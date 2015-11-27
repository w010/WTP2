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

abstract class tx_wform_pi1_userfield	{

	protected $conf = array();
	protected $pObj;
	/**
	* @var tx_wform_pi1
	*/
	protected $pi1;

	public function __construct($conf, &$pObj) {
	    $this->conf = $conf;
	    $this->pObj = $pObj;
	    $this->pi1 = & tx_wform_pi1_registry::Registry('wform', 'instance_pi1');
	}
}



/**
 * User Field class for the 'w_form' extension.
 * 
 * Checks if user is 18 years old
 *
 * @author	wolo <wolo.wolski@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_wform
 */
class tx_wform_pi1_userfield_mature extends tx_wform_pi1_userfield	{
	
	const age = 18;

	function valid($value, &$pObj)	{
		$vars = $this->pi1->piVars['DATA'];

		// invalid date
		if (!checkdate( intval($vars['birthdate_month']), intval($vars['birthdate']), intval($vars['birthdate_year']) ))
			return False;

		$today = new DateTime( date('Y-m-d') );
		$birth = new DateTime( intval($vars['birthdate_year']).'-'.intval($vars['birthdate_month']).'-'.intval($vars['birthdate']) );

		// date is in past
		if ($today > $birth)
			return True;

		return False;
	}

	function age($value, &$pObj)	{
		$vars = $this->pi1->piVars['DATA'];

		// check before making datetime objects
		if (!checkdate( intval($vars['birthdate_month']), intval($vars['birthdate']), intval($vars['birthdate_year']) ))
			return False;

		$today = new DateTime( date('Y-m-d') );
		$birth = new DateTime( intval($vars['birthdate_year']).'-'.intval($vars['birthdate_month']).'-'.intval($vars['birthdate']) );

		$diff = $birth->diff($today);

		// is at least specified age
		if ($diff->y  >=  self::age)
			return True;

		return False;
	}
}



/**
 * User Field class for the 'w_form' extension.
 *
 * @author	wolo <wolo.wolski@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_wform
 */
class tx_wform_pi1_userfield_captcha extends tx_wform_pi1_userfield	{

	/**
	* @param array $conf
	* @param WFormField2 $pObj
	* @return string
	*/
	function getField($conf, &$pObj) {
		require_once(t3lib_extMgm::extPath('sr_freecap').'pi2/class.tx_srfreecap_pi2.php');
		
		$tmpl = $this->pi1->cObj->getSubpart($this->pi1->templateCode, '###FIELD__captcha###');
		if (!$tmpl)	throw new Exception('Template for captcha row not found. set ###FIELD__captcha### in main wform template.');

		if (t3lib_extMgm::isLoaded('sr_freecap') ) {
            $this->freeCap = t3lib_div::makeInstance('tx_srfreecap_pi2');
			$markers = $this->freeCap->makeCaptcha();
			$markersGlobal = & tx_wform_pi1_registry::Registry('wform', 'markers_field_captcha');
			$markers = array_merge($markersGlobal, $markers);
			//$markers['###INPUT###'] = '<input type="text" class="input-text" id="input_captcha_response" name="tx_srfreecap_pi2[captcha_response]" title="'.$markers['###SR_FREECAP_NOTICE###'].'" value="" />';
			$markers['###INPUT###'] = '<input type="text" class="input-text" id="'.$this->pi1->formId.'_input_captcha" name="'.$this->pi1->prefixId.'[captcha]" title="'.$markers['###SR_FREECAP_NOTICE###'].'" value="" />';
			$markers['###LABEL_INFO###'] = $this->pi1->pi_getLL('info_field_captcha', 'type the text from image');
			$content = $this->pi1->cObj->substituteMarkerArrayCached($tmpl, $markers);
			return $content;
		}
		throw new Exception('extension sr_freecap is not loaded');
	}

	function valid($value, &$pObj)	{
		require_once(t3lib_extMgm::extPath('sr_freecap').'pi2/class.tx_srfreecap_pi2.php');

		// before validation, rewrite pivar for captcha
		if (t3lib_extMgm::isLoaded('sr_freecap') ) {
            $this->freeCap = t3lib_div::makeInstance('tx_srfreecap_pi2');
            return $this->freeCap->checkWord($this->pi1->piVars['captcha']);
		}
		throw new Exception('extension sr_freecap is not loaded');
		return false;
	}
}


class tx_wform_pi1_userfield_email extends tx_wform_pi1_userfield	{

	public function valid($value, &$pObj)   {
		return t3lib_div::validEmail($value);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/w_form/pi1/class.tx_wform_pi1_userfield.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/w_form/pi1/class.tx_wform_pi1_userfield.php']);
}

?>