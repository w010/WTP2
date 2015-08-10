<?php
namespace DirectMailTeam\DirectMail;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2006 Stanislas Rolland <stanislas.rolland(arobas)fructifor.ca>
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
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * @author		Stanislas Rolland <stanislas.rolland(arobas)fructifor.ca>
 *
 * @package 	TYPO3
 * @subpackage 	tx_directmail
 * @version		$Id: class.tx_directmail_select_categories.php 6012 2007-07-23 12:54:25Z ivankartolo $
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;
use DirectMailTeam\DirectMail;

/**
 * Localize categories for backend forms
 *
 */
class SelectCategories {
	var $sys_language_uid = 0;
	var $collate_locale = 'C';

	/**
	 * Get the localization of the select field items (right-hand part of form)
	 * Referenced by TCA
	 *
	 * @param	array		$params: array of searched translation
	 * @return	void		...
	 */
	function get_localized_categories($params) {
		global $LANG;

/*
		$params['items'] = &$items;
		$params['config'] = $config;
		$params['TSconfig'] = $iArray;
		$params['table'] = $table;
		$params['row'] = $row;
		$params['field'] = $field;
*/
		$config = $params['config'];
		$table = $config['itemsProcFunc_config']['table'];

			// initialize backend user language
		if ($LANG->lang && ExtensionManagementUtility::isLoaded('static_info_tables')) {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				//'sys_language.uid,static_languages.lg_collate_locale',
				'sys_language.uid',
				'sys_language LEFT JOIN static_languages ON sys_language.static_lang_isocode = static_languages.uid',
				'static_languages.lg_typo3 = '.$GLOBALS['TYPO3_DB']->fullQuoteStr($LANG->lang,'static_languages').
					PageRepository::enableFields('sys_language').
					PageRepository::enableFields('static_languages')
				);
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$this->sys_language_uid = $row['uid'];
				$this->collate_locale = $row['lg_collate_locale'];
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
		}

		if (is_array($params['items']) && !empty($params['items'])) {
			foreach ($params['items'] as $k => $item ) {
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'*',
					$table,
						'uid='.intval($item[1])
				);
				while($rowCat = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					if($localizedRowCat = DirectMailUtility::getRecordOverlay($table,$rowCat,$this->sys_language_uid,'')) {
						$params['items'][$k][0] = $localizedRowCat['category'];
					}
				}
				$GLOBALS['TYPO3_DB']->sql_free_result($res);
			}
		}
	}
}

?>
