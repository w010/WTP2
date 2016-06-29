<?php


/**
 * Display be_users selector for non-admin users
 * Cedris
 * wtp 2015
 */
class tx_sitecedris_TCAform		{
	//var $divObj;
	//var $selectedItems = array();
	var $confArr = array();
	var $PA = array();
	var $recID;
	//var $useAjax = FALSE;

	function init(&$PA) {
		$this->confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['site_cedris']);

		$this->PA = &$PA;
		$this->table = $PA['table'];
		$this->field = $PA['field'];
		$this->row = $PA['row'];
		$this->fieldConfig = $PA['fieldConf']['config'];
		$this->setDefVals();
		$this->setSelectedItems();
	}


	function itemsBlogAuthor($conf)  {
		//$conf['items'][] = ['label', 'value'];

		$conf['items'][] = ['', ''];

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid, username, realName',
			'be_users',
			'1=1 AND NOT disable',
			'','realName ASC, username ASC','',
			'uid'
		);
		foreach ($res as $row)  {
			$conf['items'][] = [
				($row['realName'] ? $row['realName'] . ' ('.$row['username'].')' : $row['username']),
				$row['uid']
			];
		}

		return $conf;
	}

	/**
	 * Custom be_user selector for non-admin users, which can't see them in standard way
	 * (For blog author)
	 *
	 * @param	array		$PA: the parameter array for the current field
	 * @param	object		$fobj: Reference to the parent object
	 * @return	string		the HTML code for the field
	 */
	/*function renderBlogAuthor(&$PA, &$fobj)    {

		$this->init($PA);

		$table = $this->table;
		$field = $this->field;
		$row = $this->row;
		$this->recID = $row['uid'];
		$itemFormElName = $this->PA['itemFormElName'];

	}*/


}

?>