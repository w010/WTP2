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


/**
 * Pibase extended v5
 *
 * @author	wolo.pl <wolo.wolski@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_wtools
 */
class tx_wtools_pibase extends \TYPO3\CMS\Frontend\Plugin\AbstractPlugin    {
	//var $extKey        = 'w_tools';	// The extension key.
	var $pi_checkCHash = true;

	var $feUser;


	public function main($content, $conf)	{
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_initPIflexForm();

		// keep this reference, feuser can be updated with helper fields
		$this->feUser = &$this->getFeuser();

		// plugin initialize
		$this->_initPlugin();
	}


	protected function _initPlugin() {

	}

	/**
	* standard configuration methods thanks to Ryzy :)
	*/
	public function getConfVar($var, $sheetName = 'sDEF', $lang = 'lDEF', $field = 'pi_flexform') {
		if (!is_array($this->cObj->data[$field]))
			return $this->_getTSConfVar($var);
		$lang = 'l'.strtoupper($lang);
		// check if field is filled up in given language - switch to default if not
		$lang = ! empty($this->cObj->data[$field]['data'][$sheetName][$lang][$var]['vDEF']) ? $lang : 'lDEF';
		return ($val=$this->pi_getFFvalue($this->cObj->data[$field], $var, $sheetName, $lang)) ? $val : $this->_getTSConfVar($var);
	}

	protected function _getTSConfVar($var) {
		if (isset($this->conf[$var]) && isset($this->conf[$var.'.'])) {
			return $this->cObj->cObjGetSingle($this->conf[$var], $this->conf[$var.'.']);
		} else if (isset($this->conf[$var])) {
			return $this->conf[$var];
		} else	{
			// try to explode path by dot (key.option.myvalue=...) and try to find in next levels of conf
			$nextlvl = $this->conf;
			foreach(explode('.', $var) as $_seg)	{
				if (is_array($nextlvl[$_seg.'.']))	{ $nextlvl = $nextlvl[$_seg.'.']; continue;	}
				if (is_string($nextlvl[$_seg]))		{ $value = $nextlvl[$_seg];		break;	}
			}
			return $value;
		}
	}



	function pi_wrapInBaseClass($content)	{
		if (!$this->conf['noBaseClassWrap'])
			return parent::pi_wrapInBaseClass($content);
		return $content;
	}

	function getRealIpAddr() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

	/**
	 * todo: opcja na force https
	 * @return string
	 */
	public function getBaseUrl() {
		$baseUrl =  GeneralUtility::getIndpEnv('TYPO3_SSL') ? 'https://' : 'http://';
		$baseUrl .= GeneralUtility::getIndpEnv('HTTP_HOST').($GLOBALS['TSFE']->absRefPrefix?$GLOBALS['TSFE']->absRefPrefix:'/');
		return $baseUrl;
	}

	/**
	 * @return array
	 */
	public function &getFeuser() {
		$feuser = &$GLOBALS['TSFE']->fe_user->user;
        return $feuser;
    }


	/**
	 * shorthand for database with code completion
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	public function db()   {
		return $GLOBALS['TYPO3_DB'];
	}


	/**
     * Render content element
     * @param $uid of CE
     * @return string html
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

	protected function redirect($linkData)	{
		//$location = 'http://'.t3lib_div::getThisUrl().$this->cObj->getTypoLink_URL($linkData);
		$location = $this->cObj->getTypoLink_URL($linkData);
		header('Location: '.$location);
		exit();
	}
}

