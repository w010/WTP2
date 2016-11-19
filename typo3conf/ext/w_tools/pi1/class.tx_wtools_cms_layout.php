<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Dmitry Dulepov <dmitry@typo3.org>
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   46: class tx_ttnews_cms_layout
 *   54:     function getExtensionSummary($params, &$pObj)
 *
 * TOTAL FUNCTIONS: 1
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


/**
 * Hook to display verbose information about pi1 plugin in Web>Page module
 *
 * @author	Dmitry Dulepov <dmitry@typo3.org>
 * @package	TYPO3
 * @subpackage	tx_tt_news
 */
class tx_wtools_cms_layout {
	/**
 * Returns information about this extension's pi1 plugin
 *
 * @param	array		$params	Parameters to the hook
 * @param	object		$pObj	A reference to calling object
 * @return	string		Information about pi1 plugin
 */
	function getExtensionSummary($params, &$pObj) {

		if ($params['row']['list_type'] == 'w_tools_pi1') {
			$data = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($params['row']['pi_flexform']);
			//debugster($data);
			if (is_array($data) && $data['data']['sDEF']['lDEF']['newsItem']['vDEF']) {
				$uid = intval($data['data']['sDEF']['lDEF']['newsItem']['vDEF']);
				// read db
				$rowArr = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'tt_news', 'uid = '.$uid);
				$row = array_pop($rowArr);

				$info = ' <b>'.$row['title'].'</b> <br> '.($row['image']?'<img src="/uploads/pics/'.$row['image'].'" style="width: 100px; height: auto;">':'');
				if ($row['deleted'] || $row['hidden'])
					$info .= '<p><img src="/typo3/gfx/icon_warning2.gif"> <b style="color: red;">WARNING! Selected record has been '.($row['hidden']?'hidden':'deleted').' thus it will not show. Select another or restore the record.</b></p>';


				$result = $GLOBALS['LANG']->sL('LLL:EXT:w_tools/locallang_db.xml:cms_layout.mode') .
							$info;
			}
			if (!$result) {
				$result = '<p><img src="/typo3/gfx/icon_warning2.gif"> <b style="color: red;">'.$GLOBALS['LANG']->sL('LLL:EXT:w_tools/locallang_db.xml:cms_layout.not_configured').'</p>';
			}
		}
		return $result;
	}
}


?>