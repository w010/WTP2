<?php
namespace DirectMailTeam\DirectMail;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 1999-2004 Kasper Skaarhoj (kasperYYYY@typo3.com)
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
 * @author		Kasper Sk�rh�j <kasperYYYY>@typo3.com>
 * @author		Stanislas Rolland <stanislas.rolland(arobas)fructifor.ca>
 *
 * @package 	TYPO3
 * @subpackage 	tx_directmail
 * @version		$Id: class.tx_directmail_checkjumpurl.php 30935 2010-03-09 18:12:41Z ivankartolo $
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * JumpUrl processing hook on class.tslib_fe.php
 */
class Checkjumpurl {

	/**
	 * Get the url to jump to as set by Direct Mail
	 *
	 * @param	tslib_fe	$feObj: reference to invoking instance
	 * * @throws Exception
	 * @return	void
	 */

	function checkDataSubmission (&$feObj) {
		$jumpUrlVariables = GeneralUtility::_GET();

		$mid = $jumpUrlVariables['mid'];
		$rid = $jumpUrlVariables['rid'];
		$aC  = $jumpUrlVariables['aC'];

		$jumpurl = $feObj->jumpurl;
		$responseType = 0;
		if ($mid && is_array($GLOBALS['TCA']['sys_dmail'])) {
				// overwrite the jumpUrl with the one from the &jumpurl= get parameter
			$jumpurl = $jumpUrlVariables['jumpurl'];

				// this will split up the "rid=f_13667", where the first part
				// is the DB table name and the second part the UID of the record in the DB table
			$recipientTable = '';
			$recipientUid = '';
			if (!empty($rid)) {
				list($recipientTable, $recipientUid) = explode('_', $rid);
			}


			$url_id = 0;
			$isInt = MathUtility::canBeInterpretedAsInteger($jumpurl);

			if ($isInt) {

					// fetch the direct mail record where the mailing was sent (for this message)
				$resMailing = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'mailContent, page, authcode_fieldList',
					'sys_dmail',
					'uid = ' . intval($mid)
				);

				if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resMailing)) {
					$temp_unpackedMail = unserialize(base64_decode($row['mailContent']));
					$url_id = $jumpurl;
					if ($jumpurl >= 0) {
							// Link (number)
						$responseType = 1;
						$jumpurl = $temp_unpackedMail['html']['hrefs'][$url_id]['absRef'];
					} else {
							// Link (number, plaintext)
						$responseType = 2;
						$jumpurl = $temp_unpackedMail['plain']['link_ids'][abs($url_id)];
					}
					$jumpurl = htmlspecialchars_decode(urldecode($jumpurl));
					switch ($recipientTable) {
						case 't':
							$theTable = 'tt_address';
						break;
						case 'f':
							$theTable = 'fe_users';
						break;
						default:
							$theTable = '';
						break;
					}

					if ($theTable) {
						$recipRow = $feObj->sys_page->getRawRecord($theTable, $recipientUid);
						if (is_array($recipRow)) {
							$authCode = GeneralUtility::stdAuthCode($recipRow, ($row['authcode_fieldList'] ? $row['authcode_fieldList'] : 'uid'));

							// check if supplied aC identical with counted authCode
							if ( ($aC != '') && ($aC == $authCode) ) {
								$rowFieldsArray = explode(',', $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['direct_mail']['defaultRecipFields']);
								if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['direct_mail']['addRecipFields']) {
									$rowFieldsArray = array_merge($rowFieldsArray, explode(',', $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['direct_mail']['addRecipFields']));
								}

								reset($rowFieldsArray);
								foreach ($rowFieldsArray as $substField) {
									$jumpurl = str_replace('###USER_'.$substField.'###', $recipRow[$substField], $jumpurl);
								}
								// Put in the tablename of the userinformation
								$jumpurl = str_replace('###SYS_TABLE_NAME###', substr($theTable, 0, 1), $jumpurl);
								// Put in the uid of the mail-record
								$jumpurl = str_replace('###SYS_MAIL_ID###', $mid, $jumpurl);

								// If authCode is provided, keep it.
								$jumpurl = str_replace('###SYS_AUTHCODE###', $aC, $jumpurl);

								// Auto Login an FE User, only possible if we're allowed to set the $_POST variables and
								// in the authcode_fieldlist the field "password" is computed in as well
								// TODO: add a switch in Direct Mail configuration to decide if this option should be enabled by default
								if ($theTable == 'fe_users' && $aC != '' && $aC == $authCode && GeneralUtility::inList($row['authcode_fieldList'], 'password')) {
									$_POST['user'] = $recipRow['username'];
									$_POST['pass'] = $recipRow['password'];
									$_POST['pid']  = $recipRow['pid'];
									$_POST['logintype'] = 'login';
									$GLOBALS['TSFE']->initFEuser();
								}
							} else {
								throw new Exception('authCode: Calculated authCode did not match the submitted authCode.', 1376899631);
							}
						}
					}
				}
				$GLOBALS['TYPO3_DB']->sql_free_result($resMailing);
				if (!$jumpurl) {
					die('Error: No further link. Please report error to the mail sender.');
				} else {
					// jumpurl has been validated by lookup of id in direct_mail tables
					// for this reason it is save to set the juHash
					// set juHash as done for external_url in core: http://forge.typo3.org/issues/46071
					GeneralUtility::_GETset(GeneralUtility::hmac($jumpurl, 'jumpurl'), 'juHash');
				}
			} else {
					// jumpUrl is not an integer -- then this is a URL, that means that the "dmailerping"
					// functionality was used to count the number of "opened mails" received (url, dmailerping)

					// Check if jumpurl is a valid link to a "dmailerping.gif"
					// Make $checkPath an absolute path pointing to dmailerping.gif so it can get checked via ::isAllowedAbsPath()
					// and remove an eventual "/" at beginning of $jumpurl (because PATH_site already contains "/" at the end)
				$checkPath = PATH_site . preg_replace('#^/#', '', $jumpurl);

					// Now check if $checkPath is a valid path and points to a "/dmailerping.gif"
				if (preg_match('#/dmailerping\\.(gif|png)$#', $checkPath) && GeneralUtility::isAllowedAbsPath($checkPath)) {
						// set juHash as done for external_url in core: http://forge.typo3.org/issues/46071
					GeneralUtility::_GETset(GeneralUtility::hmac($jumpurl, 'jumpurl'), 'juHash');
					$responseType = -1;
				} elseif (GeneralUtility::isValidUrl($jumpurl) && preg_match('#^(http://|https://)#', $jumpurl)) {
						// Also allow jumpurl to be a valid URL
					GeneralUtility::_GETset(GeneralUtility::hmac($jumpurl, 'jumpurl'), 'juHash');
					$responseType = -1;
				}

				// to count the dmailerping correctly, we need something unique
				$recipientUid = $aC;

			}

			if ($responseType != 0) {
				$insertFields = array(
					'mid'           => intval($mid),	// the message ID
					'tstamp'        => time(),
					'url'           => $jumpurl,
					'response_type' => intval($responseType),
					'url_id'        => intval($url_id),
					'rtbl'			=> $recipientTable,
					'rid'			=> $recipientUid
				);

				$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('sys_dmail_maillog', $insertFields);
			}
		}

		// finally set the jumpURL to the TSFE object
		$feObj->jumpurl = $jumpurl;
	}

}

?>