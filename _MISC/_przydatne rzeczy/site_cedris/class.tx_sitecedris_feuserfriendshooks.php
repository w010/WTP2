<?php


require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('site_cedris').'class.tx_sitecedris_ttnewshooks.php');


class tx_sitecedris_feuserfriendshooks {
	/**
	 * @var tx_feuserfriends_pi1
	 */
	var $pObj;

	/**
	 * @var tslib_cObj
	 */
	var $cObj;

	/**
	 * tt_news config
	 * @var Array
	 */
	var $conf;

	
	
	/**
	 * Set up local vars
	 * @param tx_feuserfriends_pi1 $pObj
	 */
	private function _initLocal(tx_feuserfriends_pi1 &$pObj) {
		$this->pObj = &$pObj;
		$this->cObj = $this->pObj->cObj;
		$this->conf = $this->pObj->conf;
	}

	
	
	
	//public function extraItemMarkerProcessor(array $markerArray, array $row, array $lConf, tx_feuserfriends_pi1 &$pObj) {
	public function listViewPreSubstitute(tx_feuserfriends_pi1 &$pObj, array &$markerArray, &$template ) {
		$this->_initLocal($pObj);
		//debugster($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);

		$row = &$pObj->internal['currentRow'];
//		debugster($row);

		// fix - don't display empty items
		if (!$row['name'])
			$template = '';

		$regio = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid, title, url',
			'tx_sitecedris_swregio',
			'uid = '.intval($row['tx_sitecedris_sw_arbeidsmarktregio']) . $pObj->cObj->enableFields('tx_sitecedris_swregio'),
			'','title','',
			''
		);
		//debugster($regio);
		//debugster($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);

		$markerArray['FIELD_tx_sitecedris_sw_arbeidsmarktregio'] = '';
		if ($regio[0]['title'])
			$markerArray['FIELD_tx_sitecedris_sw_arbeidsmarktregio'] = 'Regio: '.$regio[0]['title'].'';
		if ($regio[0]['title'] && $regio[0]['url'])
			$markerArray['FIELD_tx_sitecedris_sw_arbeidsmarktregio'] = 'Regio: <a href="'.htmlspecialchars($regio[0]['url']).'">'.$regio[0]['title'].'</a>';

	//	debugster($row);

//		debugster($markerArray);
		//return $markerArray;
	}


	public function extraGlobalMarkerProcessor(tx_feuserfriends_pi1 &$pObj, array $markerArray) {
		$this->_initLocal($pObj);


		$markerArray['DEV'] = DEV ? 'true' : 'false';

		// filters

		$tmpl_filters = $this->cObj->getSubpart($this->pObj->templateFile,'###SUB_TEMPLATE_FILTERS###');
//		debugster($tmpl_filters);
		//debugster($ma);

		$ma = [];
		$ma['form_action'] = $this->pObj->pi_linkTP_keepPIvars_url([], 0, 1);
		$ma['filter_name'] = $this->pObj->piVars['filter']['name'];
		$ma['filter_city'] = $this->pObj->piVars['filter']['city'];
		$ma['filter_region'] = $this->pObj->piVars['filter']['region'];
//		$ma['filter_arbeidsmarktregio'] = $this->pObj->piVars['filter']['arbeidsmarktregio'];

		$TtnewsHooks = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_sitecedris_ttnewshooks');

		$ma['filter_arbeidsmarktregio'] = $TtnewsHooks->makeDroplistOptions( $TtnewsHooks->getRegioForDroplist(), false, $pObj->piVars['filter']['arbeidsmarktregio']);


		$markerArray['FILTER'] = $this->cObj->substituteMarkerArray($tmpl_filters, $ma, '###|###', 0);

		if (!$markerArray['ITEMS'])
			$markerArray['ITEMS'] = 'Geen items gevonden.';

//debugster($markerArray);
//debugster($markerArray['FILTER']);

		return $markerArray;
	}



	function processSelectConfHook(tx_feuserfriends_pi1 &$pObj, $where)    {
		//debugster($where);
		//debugster($pObj->piVars);
		if ($pObj->piVars['filter']['name'])
			//$where .= ' AND name like '.$GLOBALS['TYPO3_DB']->fullQuoteStr($pObj->piVars['filter']['name']);  // does it wrong. maybe it changes in 6.x
			$where .= ' AND name like "%'.$GLOBALS['TYPO3_DB']->escapeStrForLike($pObj->piVars['filter']['name']).'%"';
		if ($pObj->piVars['filter']['city'])
			$where .= ' AND  ( city like "%'.$GLOBALS['TYPO3_DB']->escapeStrForLike($pObj->piVars['filter']['city']).'%"'
//					. '     OR  FIND_IN_SET ("'.$GLOBALS['TYPO3_DB']->escapeStrForLike($pObj->piVars['filter']['city']).'", tx_sitecedris_sw_citysecond) )';
					. '     OR  CONCAT(\',\', tx_sitecedris_sw_citysecond, \',\') LIKE \'%,%'.$GLOBALS['TYPO3_DB']->escapeStrForLike($pObj->piVars['filter']['city']).'%,%\')';

		//debugster($where);
		if ($pObj->piVars['filter']['region'])
			$where .= ' AND tx_sitecedris_sw_provincie like "%'.$GLOBALS['TYPO3_DB']->escapeStrForLike($pObj->piVars['filter']['region']).'%"';
		if ($pObj->piVars['filter']['arbeidsmarktregio'] > 1)
			$where .= ' AND tx_sitecedris_sw_arbeidsmarktregio = '.intval($pObj->piVars['filter']['arbeidsmarktregio']);
		
		//debugster($pObj->conf);
		
		// praktijk single vorbeeld box
		if ($pObj->conf['context'] == 'tt_news')	{
			if ($pObj->cObj->data['tx_sitecedris_sw_bedrijven'])
				$where .= ' AND uid IN ('. $GLOBALS['TYPO3_DB']->cleanIntList($pObj->cObj->data['tx_sitecedris_sw_bedrijven']).')';
			else
				$where .= ' AND 1=2';	// prevent displaying all records when no selected
		}

//		debugster($where);
		return $where;
	}


	/**
	 *  render content element
	 * @param $uid
	 * @return string
	 */
	function renderCE($uid)	{
		$conf = Array ();
		$conf['1'] = 'RECORDS';
		$conf['1.'] = Array (
			'tables' => 'tt_content',
			'source' => intval($uid),
			'dontCheckPid' => 1
		);
		return $this->cObj->cObjGet($conf);
	}


}



?>