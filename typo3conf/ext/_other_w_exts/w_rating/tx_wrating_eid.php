<?php


	// Exit, if script is called directly (must be included via eID in index_ts.php)
if (!defined ('PATH_typo3conf')) 	die ('Could not access this script directly!');

	// Connect to database:
tslib_eidtools::connectDB();


require_once (t3lib_extMgm::extPath('w_rating').'pi1/class.tx_wrating_pi1.php');




class tx_wrating_eid	{

	/**
	* 
	* @var tx_wsubsbox_pi1
	*/
	protected $Pi1;


	protected $content = '';

	

	public function __construct() {
		$this->init();
		$this->main();
	}


	public function init() {
		//$this->Pi1 = 		t3lib_div::makeInstance('tx_wrating_pi1');
	}


	public function main()	{

		//fb('event: ' . $eventUid, $eventUid ? FirePHP::LOG : FirePHP::WARN);

		switch(t3lib_div::_GP('method'))	{
			case 'vote':
				$call = $this->vote(t3lib_div::_GP('table'), t3lib_div::_GP('uid'), t3lib_div::_GP('note'));
				$this->content = json_encode($call);
				break;

			default:
				//fb('no method or not supported', FirePHP::ERROR);
				$call = array('res' => -1, 'notice' => 'METHOD_NOT_SUPPORTED', 'success' => false);
				$this->content = json_encode($call);
		}
	}

	public function vote($table_name, $record_uid, $note)	{

		// check if user can vote
		$ip = tx_wrating_pi1::getRealIpAddr();

		// get record
		$whereClause = ' AND table_name = "'.$table_name.'" AND  record_uid = "'.$record_uid.'" AND userdata LIKE \'%"ip":"'.$ip.'"%\'';
		if ($votes = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
		        '*',
		        'tx_wrating_vote',
		        '1=1' . $whereClause
		))	{
			$notice = 'ALREADY_VOTED';
		}
		else	{
			// insert
			$votesave = $GLOBALS['TYPO3_DB']->exec_INSERTquery(
				'tx_wrating_vote',
				array(
					'crdate' => $GLOBALS['EXEC_TIME'],
					'table_name' => $table_name,
					'record_uid' => $record_uid,
					'note' => $note,
					'userdata' => json_encode(array('ip' => $ip))
				)
			);
			$notice = 'SUCCESS';
		}
		$res = array($table_name, $record_uid, $note);

		return array(
			'success' => $votesave,
			'notice' => $notice,
			'res' => $res,
		);
	}

	
	/**
	 * Outputs the content from $this->content
	 *
	 * @return	void
	 */
	function printContent()	{
		print $this->content;
	}

}

header('Content-type: application/json');

$eid = t3lib_div::makeInstance('tx_wrating_eid');
$eid->printContent();



?>