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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */


/**
 * Hook class for the 'w_gmaps' extension.
 *
 * @author	wolo <wolo.wolski@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_wgmaps
 */
class tx_wgmaps_pi1_hook	{

	protected static $_instance;
	protected $pObj;

	private function __construct(tx_wform_pi1 &$pObj = null) {
		$this->pObj = $pObj;
	}
    private function __clone()  {}

    /**
	 * Singleton pattern
	 * @return tx_wgmaps_pi1_hook
	 */
	final public static function instance(&$pObj = null) {
		if (self::$_instance === null) {
			$class = __CLASS__;
			self::$_instance = new $class($pObj);
		}
		return self::$_instance;
	}

	static function email_fieldsToRender(&$fields)	{
		// here we can modify fields which will be in email to admin
	}

	static function email_beforeSend(&$recipients, &$subject, &$plainContent, &$htmlContent, &$headers)	{
		// here we can change recipients, modify content, or remove recipients to prevent sending in some cases
	}

	static function content_otherMarkers(&$markers, &$subparts)	{
		$pObj = self::instance()->pObj;
		
		// to finish - instantiate hooks
	}

	static function process_beforeDbInsert(&$insertArray)	{
		
	}
}



?>