<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 - 2015 wolo.pl <wolo.wolski@gmail.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
//namespace WTP\WTools;

use \TYPO3\CMS\Core\Utility\GeneralUtility;


require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('w_tools').'Classes/wXml.php');





/**
 * WTools Universal Import v5
 *
 * usage: extend this class
 *
 * @author	wolo.pl <wolo.wolski@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_wtools
 */
class tx_wtools_import extends \TYPO3\CMS\Scheduler\Task\AbstractTask {


	/**
	 * @var array - it shouldn't be named extConf, it's a part of extConf ("import" subkey)
	 */
	public $extConf = [];
	protected $scriptStartStamp = 0;


	/**
	* @var wXml - xml helper
	*/
	protected $XmlObj;
	/**
	* @var WTP\WTools\Log - logger
	*/
	protected $Log;
	/**
	 * @var WTP\WTools\TTrack - timetrack object
	 */
	public $TTr;	// timetrack TTrack

	protected $_pathDir = '';	   // directory with data files, if directory mode
	protected $_pathFiles = [];	 // file name if file mode, or (probably) filenames in directory mode. (mode is not set anywhere, it's child object's matter to do what is needed)
	protected $_workingDir = '';	// sys - full working dir path, set internally, not configured
	protected $data = [];		   // this should be an array with keys named as table to insert to - because there could be more record types in one import, like categories
	protected $counter = [];		// same, array of counters for every record type

	protected $existingInDb = [];	// same as above. can set ids or whole records, ie. categories read from db to set relations
	protected $alreadyExistsId = []; // same here. it stores ids of records that are found in db thus not inserted (probably updated, if implemented)

	protected $importConfKey = 'default'; 	 // index of array configuration of import from localconf



	public function __construct() {
		parent::__construct();	// important!
		// init() has to be moved to execute() beacause it doesn't call constructor on run...
	}

	/**
	 * @param string $key - when called from outside, config index may be set here before execute()
	 */
	public function setImportConfKey($key) {
		$this->importConfKey = $key;
	}



	/**
	 * @param string $key - select config set
	 */
	protected function init($key)   {
		$this->scriptStartStamp = $GLOBALS['EXEC_TIME'];
		// note - that this may lead to some misunderstanding - $this->extConf never contains full extconf array, only given key!
		$this->extConf = &$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['w_tools']['import'][$key];
		$this->XmlObj =	 	GeneralUtility::makeInstance('wXml');
		$this->TTr =		GeneralUtility::makeInstance('WTP\WTools\TTrack');
		//$this->Log =		GeneralUtility::makeInstance('tx_wtools_log', PATH_site.$this->extConf['log']);
		$this->Log =		GeneralUtility::makeInstance('WTP\WTools\Log', PATH_site.$this->extConf['log']);
		//$this->TTs = new StopWatch();
//new WTP\WTools\TTrack\TTrack();
		$this->_pathDir = $this->extConf['path'];
	}


	public function execute() {
		$this->init($this->importConfKey);
		$res = self::import();
		//debugster($res);
		// todo later: write this res notice to scheduler list window
		if ($res['success'] === TRUE) {
			/*$flashMessage = GeneralUtility::makeInstance(
				'\TYPO3\CMS\Core\Messaging\FlashMessage',
				'Import success',
				'OK',
				\TYPO3\CMS\Core\Messaging\FlashMessage::OK);
			$this->getDefaultFlashMessageQueue()->enqueue($flashMessage);*/
			return TRUE;
		}
		return false;
	}

	
	/* main method */
	public function import()	{

			$this->log("\n\n".'START IMPORT');
			$this->TTr->start('Overall', 'OVERALL');

		// check if the data/archive has changed
		//	$this->TTr->start('check if should import');
		//$this->checkIfShouldImport();
		//$this->log('data not changed, no import.');
		// if positive, prepare data in work dir

			$this->TTr->start('PREPARING WORKING DIR, FIND FILES, UNPACK, ETC', 'PREPARE_GROUP');  // automatic stop previous ttr run

		if (!$this->_prepareWorkingDirAndGetFiles())
			return ['result' => false, 'notice' => 'not importing, no files'];
			//$this->TTr->stop('prepare');

		$this->log('found '.count($this->_pathFiles) .' files in work dir');

			$this->TTr->start('Load and parse data', 'prepare_load', 'PREPARE_GROUP'); // sub

		// load all files, if entities are not in single files. else rewrite prepareData in child class and simply return true.
		if (!$this->_prepareData()) {
			$this->log('files found, but data cannot be read or at least one has no data');
			return ['result' => FALSE, 'notice' => 'files found, but data cannot be read or at least one has no data'];
		}

			$this->TTr->stop('prepare_load', 'PREPARE_GROUP'); // sub end
			$this->TTr->stop('PREPARE_GROUP');


						//- iterowac users, w poszukiwaniu relacji do group
						//- wczytac uprzednio istniejace
						// okreslic, co jest id

		//	$this->TTr->start('get existing records records and ids');  // ? is this measured?

		// read existing records' ids (not uids) to compare
		$this->_readExistingData();


			$this->TTr->start('Save data', 'SAVE_GROUP');   // use specified later as ttrack group

				// temp - leave only first
				//$this->data['bedrijven']->BusinessEntities[0]->BusinessEntity = array_slice($this->data['bedrijven']->BusinessEntities[0]->BusinessEntity, 0, 1);

		// todo later: if (file mode)   don't parse data, iterate through files, not entities, and then parse

		$result = $this->_saveData();


			$this->TTr->stop('SAVE_GROUP');
			$this->TTr->stop('OVERALL');

		if (LOCAL  ||  (DEV && $GLOBALS['TSFE']->id == 82)) {
			print "Time tracking (stopwatch):\n";
			debugster($this->TTr->getTimeTable());
		}

			$this->log('END import, time taken: '.$this->TTr->getTimeTable()['OVERALL']['time'] . ' ms'.chr(10));

		GeneralUtility::devLog('Wtools xml import done, see logs', 'w_tools', 0, $this->TTr->getTimeTable());

		return ['success' => true, 'notice' => $result['notice']];
	}



	/**
	 * prepare data - init directory, find and set files
	 * in child class you can do here for example clean working dir from previous files, unpack there new archive, read files etc.
	 *
	 * @return array files
	 */
	protected function _prepareWorkingDirAndGetFiles()	{

			$this->TTr->start('Read directory', 'prepare_readdir', 'PREPARE_GROUP');

		// set working dir
		//$this->workingDir = dirname(PATH_site.$this->file).'/import/';
		$this->_workingDir = PATH_site.$this->_pathDir;
		// debugster($this->_workingDir);

			//$this->TTr->start('unzip file');
			/*$zip = new ZipArchive();*/
			/*		if ($zip->open(PATH_site.$this->file) === TRUE) {
				$zip->extractTo($this->workingDir);
				$zip->close();
				echo 'ok';
			} else {
				echo 'failed';
			}*/

		// file mode
		if ($this->extConf['file'])
			$this->_pathFiles[] = $this->extConf['file'];
		// get array of files in working directory
		else
			$this->_pathFiles = array_diff(scandir($this->_workingDir), ['..', '.']);

		// remove unneccessary log file entry
		if ( $keyUnset = array_search( basename($this->extConf['log']), $this->_pathFiles ))
			unset ($this->_pathFiles[$keyUnset]);

		//debugster($this->_pathFiles);
			$this->TTr->stop('prepare_readdir', 'PREPARE_GROUP');

		return (bool) count($this->_pathFiles);
	}


		/**
		 * prepare and parse data from file(s)
		 * example, see child classes for details
		 * obviously you parse here the xml from files to data array
		 */
		protected function _prepareData()	{
			// here xml from file(s) should be parsed and set to array
			// example:
			$success = false;
			foreach ($this->_pathFiles as $file) {
				if (preg_match('/something/', $file, $m1)) {
					$this->data['mydatafromsomething'] = $this->XmlObj->getXmlFromFile($this->_workingDir . $file);
					if ($this->data['mydatafromsomething']) $success = true;
				}
			}
			return ['success' => $success];
		}


		/**
		 * can be overwritten in child classes if needed to control this
		 */
		protected function _checkIfShouldImport()	{
			// todo later: option to force import
			// todo later: check if archive hash has changed to import or not? do we really need this situation?
			return true;
		}



		/**
		 * save data from input files
		 * example, should be rewritten in child class to match xml structure
		 * @return array result
		 */
		protected function _saveData()  {
			return;
			$success = true;
			// iterate record types, if more than one, ie. categories
			// example structure, modify to fit your xml
			foreach($this->data as $recordType => $xmlItem)	{
				foreach($xmlItem->BusinessEntities[0]->BusinessEntity as $entity) {
					//debugster($entity);
					$this->_saveRecord($entity, $recordType);
				}
				$this->log('SAVED items: ' . $recordType . ' - ' . $this->counter[$recordType]);
				$this->log('UPDATED items: ' . $recordType . ' - ' . $this->counter[$recordType.'_updated']);
				$this->log('already existed: '.$recordType. ' - '.count($this->alreadyExistsId[$recordType]));
				if ($this->counter[$recordType.'_mm'])
					$this->log('MM category relations: ' . $recordType . ' - ' . $this->counter[$recordType.'_mm']);
			}
			return ['success' => $success, 'notice' => 'data saved'];
		}



		/**
		 * save method
		 * example - to rewite in child classes
		 * must include db insert (and probably update control)
		 *
		 * @param $entity - array or simplexmlobject
		 * @param string $recordType
		 * @return array result
		 */
		protected function _saveRecord($entity, $recordType)	{

			//$xml = $this->XmlObj->getXmlFromFile($this->_workingDir.$file);
			//debugster($xml);

			// todo: make this method universal, add existing control/update method to rewrite

			// don't save, if id is already in database
			/*if (in_array((string) $xml->id_info->nct_id, $this->idsExistingInDb))	{
				$this->alreadyExists[] = $xml->id_info->nct_id;
				return ['result' => false, 'notice' => 'exists, not inserting'];
			}*/
			// better way:
			/*if (array_search((string) $entity->new_groupid, array_column($this->existingInDb[$recordType], 'description'))) {
				$this->alreadyExists[$recordType][] = (string) $entity->new_groupid;
				break;
			}*/

			$row = $this->_mapRow($entity);
			//$row = $this->mapRow_tablename($xml);
			//		debugster($row);

			// insert record
			$result = $this->_insertItem($row);

			return $result;
		}


		/**
		 * maps xml structure to row array to insert into database
		 *
		 * @param SimpleXMLElement $xml
		 * @return array
		 */
		protected function _mapRow(SimpleXMLElement &$xml)	{
			// example, should be rewritten in child class
			return [
				// system
				'tstamp' => $this->scriptStartStamp,
				'datetime' => strtotime($xml->start_date),
				'pid' => $this->extConf['pid'],
				'hidden' => 1,
				//'db_id' => (string) $xml->id_info->nct_id,	// main id used from this field!
				//'db_rank' => (string) $xml['rank'],
				//'db_overall_official' => json_encode($xml->xpath('overall_official')),
			];
		}


	/**
	 * @param array  $row - item to insert into db
	 * @param string $table
	 * @param string $recordType
	 * @param string $mmTable
	 * @param array $mmUidsForeign - uids of foreign table, ie. categories (typo uids!)
	 * @param int $updateExistingId - id (possible not uid) of current record
	 * @return mixed
	 */
	protected function _insertItem($row, $table = 'tt_news', $recordType = 'default', $mmTable = 'tt_news_cat_mm', $mmUidsForeign = [], $updateExistingId = 0)	{

		$result = false;
			// this measurement is not neccessary unless some performance problems
			//$this->TTr->start('insert item');


		// if using method with duplicate key update, omit this
		if (!$updateExistingId) {


			// neccessary to make valid query - only if manual build
			$row = self::db()->fullQuoteArray($row, $table, false);
			// query build
			$insert_fields = implode(',', array_keys($row));
			$insert_values = implode(',', $row);

			// na opcje Force - uzyc tego. na ten moment nie przewidujemy.
			/*foreach($row as $field => $value)	{
				$update_pairs_array[] = "{$field} = {$value}";
			}*/
			// $update_pairs = implode(',', $update_pairs_array);
			// If you specify ON DUPLICATE KEY UPDATE, and a row is inserted that would cause a duplicate value in a UNIQUE index or PRIMARY KEY, an UPDATE of the old row is performed.
			// INSERT INTO table (`uid`, `pid`) VALUES (1, 1) ON DUPLICATE KEY UPDATE uid = 1, pid = 1
			// $query = "INSERT INTO {$this->table} ({$insert_fields}) VALUES ({$insert_values}) ON DUPLICATE KEY UPDATE {$update_pairs}";
			$query = "INSERT INTO {$table} ({$insert_fields}) VALUES ({$insert_values})";

			/*	insert many at one query:
			  $sql = "INSERT INTO beautiful (name, age)
			  VALUES
			  ('Samia', 22),
			  ('Yumie', 29)";
			*/

			//debugster($query);
			$result = self::db()->sql_query($query);
			if ($uidLocal = self::db()->sql_insert_id())
				$this->counter[$recordType]++;


			// insert mm relation records
			//debugster(self::db()->sql_insert_id());
			if (is_array($mmUidsForeign)  &&  $uidLocal)
				//foreach (explode(',', $mmUidsForeign) as $mmUidForeign)
				foreach ($mmUidsForeign as $mmUidForeign)
					if (intval($mmUidForeign)) {
						$this->_insertMmRelation($mmTable, $uidLocal, $mmUidForeign);
						$this->counter[$recordType.'_mm'] ++;
					}
		}

		// update record
		else	{
			//debugster($row);

			// FIT TO YOUR NEEDS IN CHILD CLASS
			//$result = self::db()->exec_UPDATEquery($table, 'id = "'.$updateExistingId.'"', $row);
		}



		// clean up
		unset($row);
		unset($insert_fields);
		unset($insert_values);
		//unset($update_pairs);
		unset($query);



			// $this->TTr->stop();

		return $result;
	}


	/**
	 * @param string $table
	 * @param int $uid_local - uid of item to set category (or other relation)
	 * @param int $uid_foreign - uid of category / related table record
	 * @param string $tablenames - tablenames column value. not as in TCA (but it doesn't complicate anything), used here to know which relations to remove on clear without reading all records and iterate. deprecated?
	 * @param int $mmSorting - sorting column value. not as in TCA (but it doesn't complicate anything), used here to know which relations to remove on clear without reading all records and iterate
	 * @return query result
	 */
	protected function _insertMmRelation($table = 'tt_news_cat_mm', $uid_local = 0, $uid_foreign = 0, $tablenames = '', $mmSorting = 1)		{
		$query = 'INSERT INTO '.$table.' (uid_local, uid_foreign, tablenames, sorting) VALUES ('.intval($uid_local).', '.intval($uid_foreign).', "'.$tablenames.'", '.intval($mmSorting).')';
		return self::db()->sql_query($query);
	}


		/**
		 * example, to rewrite
		 * read current data from database, like ids or whole categories to compare and set proper relations
		 */
		protected function _readExistingData()   {
			// get some records to compare ids on insert to perform update
			//$this->idsExistingInDb[$recordType] = $this->_getExistingRecordsIds();
			//$this->log('total '.$recordType. ' records in db: '.count($this->idsExistingInDb[$recordType]));
		}

	/**
	 * get unique identifiers of already existing records
	 * note that's not record's system uid!
	 *
	 * @param string $field - field which keeps identifier
	 * @param string $table
	 * @param string $where
	 * @return array
	 */
	protected function _getExistingRecordsIds($field = '', $table = 'tt_news', $where = '')	{
		$rows = [];
		$res = self::db()->exec_SELECTquery(
				$field,
				$table,
				'1=1 '.$where
		);
		if ($res)
			while($row = self::db()->sql_fetch_assoc($res)) {	$rows[] = $row[$field];	}
		return $rows;
	}



	/**
	 * @param string $text - text to translate
	 * @return string
	 */
	protected function _translate($text) {
		$text = (string) $text;
		if ($this->extConf['translate'])	{
			/*$apiKey = '';
			$url = 'https://www.googleapis.com/language/translate/v2?q='.urlencode($text).'&target=pl&source=en&key=' . $apiKey;
			debugster($url);
			$handle = curl_init($url);
			curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);	 //We want the result to be saved into variable, not printed out
			$response = curl_exec($handle);
			curl_close($handle);

			debugster(json_decode($response, true));*/
		}

		return $text;
	}



	/**
	 * here you can do something with notice before sending to logger
	 * @param string $notice
	 */
	protected function log($notice)	{
		$this->Log->log($notice);
		if (LOCAL)  print ($notice.'<br>');
		/*$notices = $notice . ",\t" . ($this->error[0] ? "no data: " . implode(", ", $this->error) : '');
		$row = date("Y-m-d \tH:i,", time()) . "\t days: {$this->backNumber} to " . ($this->backNumber + $this->daysNumber - 1) . ",\t " . ($_GET['set'] ? "set id: " . $_GET['set'] . "." : "all sets. ") . $notices . "\n";
		$logDir = '/data/stor/www/pub/fileadmin/__log/';
		$fp = fopen($logDir . 'import.log', 'a+');
		rewind($fp);
		fwrite($fp, $row);
		fclose($fp);*/
	}


	/**
	 * called by scheduler to display in backend task list
	 * @return string
	 */
	public function getAdditionalInformation()  {
		$this->init();
		$this->_prepareWorkingDirAndGetFiles();
		return 'files in directory: '.implode(', ', $this->_pathFiles);
	}


	/**
	 * shorthand for database with code completion
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	function db()   {
		return $GLOBALS['TYPO3_DB'];
	}
}










/**
 * WTP TimeTrack
 * v2 - grouping submeasurements
 * TODO: remove and use classes/TTrack
 */
class TTrack	{

	public $track = [];
	private $element = [];
	public $specifiedElements = [];
	private $onMeasuring = false;


	public function __construct() {
		die('don\'t use from here, use WTP\WTools\TTrack');
	}

	public function start($label, $specified = '', $group = '') {
		$element = [];
		$element['output']['label'] = $label;
		$element['measure']['start'] = $this->ms(microtime(true));
		$element['measure']['start_s'] = time();
		if (!$specified)	{
			if ($this->onMeasuring)
				$this->stop();
			$this->onMeasuring = true;
			$this->element = $element;
		}
		else if ($group)	{
			$this->specifiedElements[$group]['grouped'][$specified] = $element;
		}
		else	{
			$this->specifiedElements[$specified] = $element;
		}
	}

	/**
	 * @param string $specified - stop selected measurement
	 * @param string $group - to stop measuring grouped item
	 */
	public function stop($specified = '', $group = '') {
		$stopTime = $this->ms(microtime(true));

		// stop general item
		if (!$specified)	{
			if ($this->element['measure']['start']) {
				$this->element['measure']['stop'] = $stopTime;
				$this->element['measure']['stop_s'] = time();
				$this->element['output']['time'] = $stopTime - $this->element['measure']['start'];
				$this->track[] = $this->element['output'];
			}
			$this->onMeasuring = false;
			unset ($this->element);
		}
		// stop specified name item
		else if ( $element = $this->specifiedElements[$specified] )  {
			$element['measure']['stop'] = $stopTime;
			$element['measure']['stop_s'] = time();
			$element['output']['time'] = $stopTime - $element['measure']['start'];
			if ($element['grouped'])	$element['output']['grouped'] = $element['grouped'];
			$this->specifiedElements[$specified] = $element;
			$this->specifiedElements['_track'][$specified] = $element['output'];
			// wolo mod 2015: add also to general array to preserve order
			$this->track[$specified] = $element['output'];
		}
		// grouped item
		else if ( $element = $this->specifiedElements[$group]['grouped'][$specified])	{
			$element['measure']['stop'] = $stopTime;
			$element['measure']['stop_s'] = time();
			$element['output']['time'] = $stopTime - $element['measure']['start'];
			unset ($element['measure']);
			$this->specifiedElements[$group]['grouped'][$specified] = $element['output'];
			$this->specifiedElements['_track'][$group]['grouped'][$specified] = $element['output'];
		}
	}

	public function getTimeTable()  {
//		return $this->track;
		//debugster($this->specifiedElements);
		return array_merge($this->track, $this->specifiedElements['_track']);
	}

	// tworzymy int milisekund z floata
	private function ms($microtime) {
		return intval($microtime * 1000);

		/*$microtime = (string) $microtime;
		$microArray = explode('.', $microtime);
		$microArray[1] = str_pad($microArray[1], 3, '0');
		$ms = implode('', $microArray);

		return intval($ms);*/
	}
}





if (!function_exists('array_column')) {
	/**
	 * Returns the values from a single column of the input array, identified by
	 * the $columnKey.
	 *
	 * Optionally, you may provide an $indexKey to index the values in the returned
	 * array by the values from the $indexKey column in the input array.
	 *
	 * @param array $input A multi-dimensional array (record set) from which to pull
	 *					 a column of values.
	 * @param mixed $columnKey The column of values to return. This value may be the
	 *						 integer key of the column you wish to retrieve, or it
	 *						 may be the string key name for an associative array.
	 * @param mixed $indexKey (Optional.) The column to use as the index/keys for
	 *						the returned array. This value may be the integer key
	 *						of the column, or it may be the string key name.
	 * @return array
	 */
	function array_column($input = null, $columnKey = null, $indexKey = null)
	{
		// Using func_get_args() in order to check for proper number of
		// parameters and trigger errors exactly as the built-in array_column()
		// does in PHP 5.5.
		$argc = func_num_args();
		$params = func_get_args();
		if ($argc < 2) {
			trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
			return null;
		}
		if (!is_array($params[0])) {
			trigger_error(
				'array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given',
				E_USER_WARNING
			);
			return null;
		}
		if (!is_int($params[1])
			&& !is_float($params[1])
			&& !is_string($params[1])
			&& $params[1] !== null
			&& !(is_object($params[1]) && method_exists($params[1], '__toString'))
		) {
			trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
			return false;
		}
		if (isset($params[2])
			&& !is_int($params[2])
			&& !is_float($params[2])
			&& !is_string($params[2])
			&& !(is_object($params[2]) && method_exists($params[2], '__toString'))
		) {
			trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
			return false;
		}
		$paramsInput = $params[0];
		$paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;
		$paramsIndexKey = null;
		if (isset($params[2])) {
			if (is_float($params[2]) || is_int($params[2])) {
				$paramsIndexKey = (int) $params[2];
			} else {
				$paramsIndexKey = (string) $params[2];
			}
		}
		$resultArray = [];
		foreach ($paramsInput as $row) {
			$key = $value = null;
			$keySet = $valueSet = false;
			if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
				$keySet = true;
				$key = (string) $row[$paramsIndexKey];
			}
			if ($paramsColumnKey === null) {
				$valueSet = true;
				$value = $row;
			} elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
				$valueSet = true;
				$value = $row[$paramsColumnKey];
			}
			if ($valueSet) {
				if ($keySet) {
					$resultArray[$key] = $value;
				} else {
					$resultArray[] = $value;
				}
			}
		}
		return $resultArray;
	}
}