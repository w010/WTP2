<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 wolo <wolo.wolski@gmail.com>
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
 * Hook class for the 'w_form' extension.
 *
 * @author	wolo <wolo.wolski@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_wform
 */
class tx_wform_pi1_hook	{

	protected static $_instance;
	protected $pObj;

	private function __construct(tx_wform_pi1 &$pObj = null) {
		$this->pObj = $pObj;
		// merge global extconf with ts hook config
		if (is_array($pObj->conf['hook.'])) 	foreach ($pObj->conf['hook.'] as $hookName => $classRefs)	{
			if (is_array($classRefs)) 	foreach ($classRefs as $key => $class)	{
				$hookName = str_replace('.','',$hookName);
				$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['w_form']['hook_'.$hookName][$key] = $class;
			}
		}
	}
    private function __clone()  {}

    /**
	 * Singleton pattern
	 * @return tx_wform_pi1_hook
	 */
	final public static function instance(&$pObj = null) {
		if (self::$_instance === null) {
			$class = __CLASS__;
			self::$_instance = new $class($pObj);
		}
		return self::$_instance;
	}





	// here we can modify fields which will be in email to admin
	static function email_fieldsToRender(&$fields)	{
			$pObj = self::instance()->pObj;
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['w_form']['hook_email_fieldsToRender'])) {
	            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['w_form']['hook_email_fieldsToRender'] as $_classRef) {
	                $_procObj = & t3lib_div::getUserObj($_classRef);
	                $_procObj->email_fieldsToRender($pObj, $fields);
	            }
	        }
	}

	// here we can change recipients, modify content, or remove recipients to prevent sending in some cases
	static function email_beforeSend(&$recipients, &$subject, &$plainContent, &$htmlContent, &$headers, &$bcc)	{
			$pObj = self::instance()->pObj;
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['w_form']['hook_email_beforeSend'])) {
	            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['w_form']['hook_email_beforeSend'] as $_classRef) {
	                $_procObj = & t3lib_div::getUserObj($_classRef);
	                $_procObj->email_beforeSend($pObj, $recipients, $subject, $plainContent, $htmlContent, $headers, $bcc);
	            }
	        }
	}

	static function content_otherMarkers(&$markers, &$subparts)	{
			$pObj = self::instance()->pObj;
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['w_form']['hook_content_otherMarkers'])) {
	            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['w_form']['hook_content_otherMarkers'] as $_classRef) {
	                $_procObj = & t3lib_div::getUserObj($_classRef);
	                $_procObj->content_otherMarkers($pObj, $markers, $subparts);
	            }
	        }
	}

	// here we can modify form fill record before storing it
	static function process_beforeDbInsert(&$insertArray)	{
			$pObj = self::instance()->pObj;
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['w_form']['hook_process_beforeDbInsert'])) {
	            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['w_form']['hook_process_beforeDbInsert'] as $_classRef) {
	                $_procObj = & t3lib_div::getUserObj($_classRef);
	                $_procObj->process_beforeDbInsert($pObj, $insertArray);
	            }
	        }
	}
}



?>