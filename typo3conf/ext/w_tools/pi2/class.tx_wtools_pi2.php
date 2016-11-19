<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 wolo <wolo.wolski@gmail.com>
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
/**
 * wolo.pl '.' studio 2015
 * tools plugins
 */

//require_once(t3lib_extMgm::extPath('w_tools').'class.tx_wtools_pibase.php');
require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('tt_news').'pi/class.tx_ttnews.php');

/**
 * Plugin 'ce single tt_news' for the 'w_tools' extension.
 *
 * @author	wolo <wolo.wolski@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_wtools
 */
class tx_wtools_pi2 extends tx_wtools_pibase {
	var $prefixId	  = 'tx_wtools_pi2';		// Same as class name
	var $scriptRelPath = 'pi2/class.tx_wtools_pi2.php';	// Path to this script relative to the extension dir.
	var $extKey		= 'w_tools';	// The extension key.
	var $pi_checkCHash = true;



	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		// done in other way - see ext template setup
		/*parent::main($content, $conf);

		$TtNews = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_ttnews');
		$TtNews->cObj = $this->cObj;
		$ttnewsConf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tt_news.'];
debugster($ttnewsConf);
		//$ttnewsConf['templateFile'] = $conf['templateFile'];
		//$TtNews->piVars['tt_news'] = intval($newsItemUid);
		//$ttnewsConf['code'] = 'SINGLE';

		return $TtNews->main_news('', $ttnewsConf);*/
	}
}



// http://docs.typo3.org/typo3cms/CoreApiReference/ApiOverview/Hooks/Configuration/Index.html

class tx_wtools_pi2_ajax {

/*
to moglo byc zrobione tak:
$.ajax(this.href, {
      success: function(data) {
         $('#main').html($(data).find('#main *'));

2016 - powyzsze dziala calkiem ok!


uwaga - tu jest robiona lista ttnews dla pobierania ajaxem, ale warto pamietac, ze linki do tej
listy poprzez processSingleViewLink sa czyszczone z wszystkiego po ? czyli no_debug itd.
inaczej tego sie nie da zrobic - ten hook w ttnews jest wykonywany juz po typolink.
*/

	function cutBoundaries(&$_params, &$pObj)    {
		// type to conf
		if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('type') != 950)     return;
		//		debugster($_params['pObj']->content);
		list(,$content) = preg_split('/<!--@@@AJAX_BOUNDARY@@@-->/', $_params['pObj']->content);

//		$_params['pObj']->content = $content;

		$result['res'] = $content;

		if (isset($_GET['no_debug']) && intval($_GET['no_debug'])===0)
			//return '<pre>' . var_dump($result).'</pre>';
			$_params['pObj']->content = '<pre>' . json_encode(str_replace(['<','>'],['&lt;','&gt;'], $result), JSON_PRETTY_PRINT).'</pre>';
		else
			$_params['pObj']->content = json_encode($result);
	}
}


?>