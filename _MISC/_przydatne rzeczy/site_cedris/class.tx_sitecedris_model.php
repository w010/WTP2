<?php



class tx_sitecedris_model	{


	/**
	* @var tslib_cObj
	*/
	static $cobjInstance;


	/**
	* get items or item count from given category. (note that it's NOT item getter for lists)
	* 
	* @param int $categoryUid
	* @param bool $count
	*/
	/* function getItemsFromCategory($categoryUid, $count = false) {
		// $whereClause = '';

		// $res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
		        // $count ? 'COUNT(DISTINCT '.'tt_news'.'.uid)' : '*',
		        // 'tt_news',
		        // self::TABLE_ITEM_CATEGORY_MM,
		        // 'tt_news_cat',
		        // '1=1' . $whereClause . self::getCobj()->cObj->enableFields('w_news')
		// );

		// if ($res)
		    // while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {    $rows[] = $row;    }
    }*/

	/**
	 * get available categories or category when uid specified
	 *
	 * @param int    $uid
	 * @param string $whereClause
	 * @return array
	 */
	static function getCategories($uid = 0, $whereClause = '') { // select fields edited
		if ($uid > 0)
			$whereClause .= ' AND uid = '.intval($uid);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
		        'uid, parent_category, title',
		        'tt_news_cat',
		        '1=1 ' . $whereClause . self::getCobj()->enableFields('tt_news_cat'),
		        '','crdate','',
		        'uid'
		);

		//debugster($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
		return $res;
	}

	/**
	 * get available SWs, or single SW when uid specified
	 *
	 * @param int    $uid
	 * @param string $whereClause
	 * @return array
	 */
	static function getSWForDroplist($uid = 0, $whereClause = '') {
		if ($uid > 0)
			$whereClause .= ' AND uid = '.intval($uid);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid, name',
			'fe_users',
			'1=1 ' . $whereClause . self::getCobj()->enableFields('fe_users') . ' AND pid = 45', // pid should be in conf, but is project specified and probably won't ever change.
			'','name','',
			'uid'
		);

		//debugster($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
		return $res;
	}


	/**
	 * get available arbeitsmarkt regios, or single regio when uid specified
	 *
	 * @param int    $uid
	 * @param string $whereClause
	 * @return array
	 */
	static function getRegioForDroplist($uid = 0, $whereClause = '') {
		if ($uid > 0)
			$whereClause .= ' AND uid = '.intval($uid);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid, title',
			'tx_sitecedris_swregio',
			'1=1 ' . $whereClause . self::getCobj()->enableFields('tx_sitecedris_swregio'),
			'','title','',
			'uid'
		);

//		debugster($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
		return $res;
	}


	/**
	 * get bglinks records linked to tt_news record (dossier)
	 *
	 * @param string $uids
	 * @param string $whereClause
	 * @return array
	 */
	static function getBgLinks($uids = '', $whereClause = '') { // select fields edited
		if (!$whereClause)  // if no additional where, try select by uids - prevents displaying all when not given
			$whereClause .= ' AND uid IN '.self::prepareInStatement(explode(',', $uids));
		$res = self::db()->exec_SELECTgetRows(
			'*',
			'tx_sitecedris_bglink',
			'1=1 ' . $whereClause . self::getCobj()->enableFields('tx_sitecedris_bglink'),
			'','crdate','', //todo: by sorting!
			'uid'
		);
//		debugster(self::db()->debug_lastBuiltQuery);
		return $res;
	}

	/**
	* get categories with item count
	*/
	// function getCategoriesCountItems()	{
		// $res = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
		        // 'tt_news_cat'.'.* , COUNT( DISTINCT i.uid ) AS item_count',
		        // 'tt_news_cat'.', '.self::TABLE_ITEM_CATEGORY_MM.' AS mm, '.'tt_news'.' AS i',
		        // 'mm.uid_local = i.uid AND mm.uid_foreign = '.'tt_news_cat'.'.uid' . $whereClause . self::getCobj()->enableFields('tt_news_cat'),
		        // 'tt_news_cat'.'.uid'
		// );
		// return $res;
	// }




	/*function getLocations($uid = 0)	{ // select fields edited
		if ($uid)
			$whereClause = ' AND location.uid = '.intval($uid);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
		        'location.uid, location.title, location.title_lang_ol, GROUP_CONCAT(DISTINCT uid ORDER BY location.uid) uid',
		        self::TABLE_LOCATION.' AS location',
		        '1=1' . $whereClause . str_replace(self::TABLE_LOCATION, 'location', self::getCobj()->enableFields(self::TABLE_LOCATION) ),
		        'location.title',
		        'location.title'
		);
		return $res;
	}

	function getLocationsCountItems(tx_wnews &$pObj)	{
		$selectConf = array();
		$onlyTheseJoins = array_flip(array_keys($pObj->piVars));
		$onlyTheseJoins['location'] = 0;	// set something, but nothing positive, to make join
		
		$criteria = $pObj->piVars;
		unset($criteria['location']);		// unset from search criteria, to filter all neighbour filter values
		
		self::makeDbQueryForListAndCounters($selectConf, $pObj, $criteria, $onlyTheseJoins);
		
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
		        'location.title, location.title_lang_ol , GROUP_CONCAT(DISTINCT location.uid ORDER BY location.uid) uid, COUNT( DISTINCT '.'tt_news'.'.uid ) AS item_count',
		        'tt_news' .($selectConf['leftjoin']?' LEFT OUTER JOIN '.$selectConf['leftjoin']:''),
		        //'tt_news'.'.location = location.uid' . str_replace(self::TABLE_LOCATION, 'location', self::getCobj()->enableFields(self::TABLE_LOCATION) ) . ($selectConf['where']?$selectConf['where']:''),
		        'tt_news'.'.location = location.uid' 
					. str_replace(self::TABLE_LOCATION, 'location', self::getCobj()->enableFields(self::TABLE_LOCATION) ) 
					. self::getCobj()->enableFields('tt_news') 
					. ($selectConf['where']?$selectConf['where']:''),
		        'location.title',
		        'location.title'
		);
		return $res;
	}*/
	


	
	
	
	/**
	* MAIN SELECT QUERY GENERATOR
	* 
	* @param array $selectConf
	* @param tx_ttnews $pObj
	* @param array $criteria
	* @param array $onlyTheseJoins
    * @return array
	*/
	static function makeDbQueryForListAndCounters(&$selectConf, &$pObj, $criteria, $onlyTheseJoins = array())	{
// echo "enter makdDBQuery " . time() . "<br />";
		// this have to be read and stored in cache in controller / hooks object ! that we could get it here
		//$params = self::getCache('params_for_category');

		// needs wtp-patched tt_news
		//$GLOBALS['w_ttnews_showSQL'] = true;

            //
            // SEARCH CRITERIA

            //debugster($criteria);

            // MAIN SEARCH WORDS
            if($criteria['swords']) {
                //$pObj->searchFieldList .= ',db_sponsors,db_study_type,db_study_design,db_eligibility_criteria,db_overall_official';
                //$selectConf['where'] .= $pObj->searchWhere($criteria['swords']);
                $selectConf['where'] .= ' AND (';
                foreach( explode(',','title,short,bodytext') as $i => $field){
	                $selectConf['where'] .= ($i?'OR ':'') . ' tt_news.'.$field.' LIKE "%'.  $GLOBALS['TYPO3_DB']->escapeStrForLike($GLOBALS['TYPO3_DB']->quoteStr( $criteria['swords'], 'tt_news'), 'tt_news') . '%"';
                };
                $selectConf['where'] .= ' )';
            }


			// Keywords - here it means Second category
			if ($criteria['keywords']) {
				$selectConf['where'] .= ' AND tt_news_cat_mm.uid_foreign = '.intval($criteria['keywords']);
			}

            //debugster($selectConf['where']);

			// examples, may be helpful
            /*if ($criteria['age'] == '18')               $selectConf['where'] .= ' AND CAST( tt_news.db_minimum_age AS UNSIGNED ) >= 18';
            else if ($criteria['age'] == 'not18')       $selectConf['where'] .= ' AND CAST( tt_news.db_minimum_age AS UNSIGNED ) < 18';
            if ($criteria['phase']) {
                $selectConf['where'] .= ' AND tt_news.db_phase LIKE \'%Phase ' . $GLOBALS['TYPO3_DB']->escapeStrForLike($GLOBALS['TYPO3_DB']->quoteStr($criteria['phase'], 'tt_news'), 'tt_news') . '%\'';
            }*/
			//	$selectConf['where'] .= ' AND tt_news.location IN '.self::prepareInStatement($criteria['location'], false);

            //debugster($selectConf);
		return $selectConf;
	}
	
	

	
	/**
	* for contact form or other purposes
	* 
	* @param int $uid of item
    * @return array row
	*/
	static function getItem($uid)	{
		$whereClause = ' AND i.uid = '.intval($uid);
		$res = self::db()->exec_SELECTgetRows(
				'i.*',
		        'tt_news' . ' AS i',
		        '1=1' . $whereClause . ' AND NOT i.deleted AND NOT i.hidden'
		);
		return array_pop($res);
	}
	

	


	
	

	
	
	/**
	*  HELPERS & SYS
	*/
	
	
	
	
	/**
	* Get field language overlay
	* 
	* @param array $row
	* @param string $field
    * @param string $langKey
    * @return string
	*/
	/*function _langOverlay($row, $field, $langKey)	{
		if (!is_array($row))	return;
		if ($row[$field.'_lang_ol'])	{
			foreach (explode('|', $row[$field.'_lang_ol']) as $ol)	{
				list ($lang, $value) = explode(':', $ol);
				if ($lang == $langKey)
					return $value;
			}
		}
		return $row[$field];
	}*/
	

	
	/**
	* gets cleared IN statement for select. may be intval or strings
	* 
	* @param array $dataArray
	* @param bool $integer
	* 
	* @return string
	*/
	function prepareInStatement($dataArray, $integer = true)	{
		if (!is_array($dataArray))
			return '("debug: prepareInStatement input not array!")';
		if ($integer) {
			$res = self::db()->cleanIntArray($dataArray);
		} else {
			$res = array_unique($dataArray);
			// $res = $GLOBALS['TYPO3_DB']->fullQuoteArray($dataArray, '');
		}
		return '('.implode(',', $res).')';
	}

	
	/**
	* quick cache - to not repeat some tasks on every item/hook instance
	* 
	* @param string $id
	* @param mixed $data
	* @return null
	*/
	public static function setCache($id, $data) {
		if (!$id)	return null;
        $GLOBALS['tx_wzep_cache'][$id] = $data;
	}

	
	/**
	* quick cache read
	* 
	* @param string $id
    * @return mixed data
	*/
	public static function getCache($id) {
	    if (!$id)	return null;
	    $data = isset($GLOBALS['tx_wzep_cache'][$id])
	    	? $GLOBALS['tx_wzep_cache'][$id]
            : null;
        return $data;
	}
	
	

	/**
	* get singleton cObj
	* 
	* @return tslib_cObj
	*/
	function getCobj()	{
		$instance = &self::$cobjInstance;
		if (!$instance)
			//$instance = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tslib_cObj');
			$instance = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
		return $instance;
	}


	/**
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	function db()   {
		return $GLOBALS['TYPO3_DB'];
	}
}



?>