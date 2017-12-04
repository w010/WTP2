<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 - 2016 wolo.pl <wolo.wolski@gmail.com>
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
 * Pibase extended v6-registry
 *
 * @author	wolo.pl <wolo.wolski@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_wtools
 */
class tx_wtools_pibase extends \TYPO3\CMS\Frontend\Plugin\AbstractPlugin    {
	//var $extKey        = 'w_tools';	// The extension key.

	// if in your ext you need to build some urls with added parameters, so cHash doesn't meet piVars anymore, set this to false
	var $pi_checkCHash = true;

	/**
	 * @var array $feUser
	 * 			Should use Registry instead: $feUser = & \WTP\WTools\Registry::Cell('wtools', 'feUser');
	 *          but for basic use can be accesed with this var
	 *             // maybe it should stay here anyway
	 */
	public $feUser;


	public function main($content, $conf)	{

			// Registry initialize - store common used objects and data references to not have to pass pObj over and over
			// set random pluginInstanceId to separate data from second plugin instance on same page
			// this is also an example of register already set variables (AFTER value setting) - have to copy the variable.
			// think twice before using this with big data! and rather try to do this BEFORE
			$_this = & \WTP\WTools\Registry::Cell('wtools', 'pi1', mt_rand(0, 100000000));
			$_this = $this; // no & here!

			// register some other often used things, like pivars, conf or feuser row
			//$_piVars = & \WTP\WTools\Registry::Cell('wtools', 'piVars');
			//$_piVars = $this->piVars;

			// TEST NEW WAY
				$_piVars = $this->piVars;   // copy current values
				$this->piVars = & \WTP\WTools\Registry::Cell('wtools', 'piVars');   // make original var a reference
				$this->piVars = $_piVars;   // restore values, but now it's a registry reference


			// may be this way, because value set after register
			// todo: sprawdzic w sposob jak pivars - czy mozliwe jest nadpisanie czegos w conf i czy to potem jest widoczne
			$this->conf = & \WTP\WTools\Registry::Cell('wtools', 'conf');
			$this->conf = $conf;

				// todo: sprawdzic, czy np. nadpisanie cos w feuser widoczne jest zmienione dalej w pi1 wmedl
				// to obecnie przez referencje do this->feuser i nadpisanie dziala spoko..
					// mozna to w sumie zostawic jako property, moze sie przydac zreszta zerwie compatibility.
				// keep this reference, feuser can be updated with helper fields

				// add to Registry. leave this var global. there's not always need to use registry
				$this->feUser = & \WTP\WTools\Registry::Cell('wtools', 'feUser');
				$this->feUser = $this->getFeUser();

				// is this working this way....? seems ok


		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_initPIflexForm();


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
		$value = null;
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
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from shared internet
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
	public function &getFeUser() {
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
		$conf = [];
		$conf['1'] = 'RECORDS';
		$conf['1.'] = [
			'tables' => 'tt_content',
			'source' => intval($uid),
			'dontCheckPid' => 1
		];
		return $this->cObj->cObjGet($conf);
	}

	protected function redirect($linkData)	{
		//$location = 'http://'.t3lib_div::getThisUrl().$this->cObj->getTypoLink_URL($linkData);
		$location = $this->cObj->getTypoLink_URL($linkData);
		header('Location: '.$location);
		exit();
	}
}


if (!function_exists('debugster'))	{
	function debugster($var)	{	return false; }
}
