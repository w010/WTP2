<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 wolo <wolo.wolski@gmail.com>
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

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Subscription ajax box' for the 'w_subsbox' extension.
 *
 * @author	wolo <wolo.wolski@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_wsubsbox
 */
class tx_wsubsbox_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_wsubsbox_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_wsubsbox_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'w_subsbox';	// The extension key.
	var $pi_checkCHash = true;


	function init($conf)	{
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
	}


	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->init($conf);

		$content = '
			<div id="subscribe_box">
				<form action="'.$this->pi_getPageLink($GLOBALS['TSFE']->id).'" method="post">
					<input type="submit" name="'.$this->prefixId.'[submit]" value="&nbsp;" onclick="callAJAX_subscribe(); return false;" />
					<div class="inputlabel-wrap">
						<label><span id="label_notify">'.htmlspecialchars($this->pi_getLL('label_subscribe')).'</span>
							<input id="input_email" type="text" name="'.$this->prefixId.'[email]" value="'.htmlspecialchars($this->piVars['input_field']).'" />
						</label>
						<div class="ajax_loader"></div>
					</div>
				</form>
			</div>

			<script type="text/javascript">
				//	<![CDATA[


			  function callAJAX_subscribe()	{
				var email = document.getElementById(\'input_email\').value;

				var sUrl = "http://' . t3lib_div::getThisUrl() . '?eID=w_subsbox&method=subscribe&email="+email+"&lang='.$this->LLkey.'";
				var callback = {
					success: function(o) {
						YAHOO.util.Dom.removeClass("subscribe_box", \'ajax_loading\');
						var container = document.getElementById("label_notify");
						//var curHeight = container.offsetHeight;

						container.innerHTML = o.responseText;

						// animate resize
						//var inner_height = document.getElementById("hint-content").offsetHeight;
						//expandElement(container, curHeight, inner_height);
					},
					failure: function(o) {
						alert("AJAX error"); //FAILURE
					}
				}


				// clear container, set loader and wait for callback
				document.getElementById("label_notify").innerHTML = "";
				YAHOO.util.Dom.addClass("subscribe_box", \'ajax_loading\');

				// send request
				var transaction = YAHOO.util.Connect.asyncRequest(\'GET\', sUrl, callback, null);
			  }
			  // ]]>
			</script>
		';

		return $this->pi_wrapInBaseClass($content);
	}



	public function ajax_subscribe($email)	{
		$this->init();
		$this->initAjax();
		
		$email = mysql_real_escape_string($email);


		if (!t3lib_div::validEmail($email))	{
			return $this->ajaxGetLL('notify_error_invalid');
		}

		// sprawdzanie czy istnieje
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
		        '*',
		        'tx_wsubsbox_emails',
		        '1=1 AND address = "' . $email . '"'
		);


		if ($res)
		    while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {	$exists = true;    }

		if ($exists)	{
			return $this->ajaxGetLL('notify_error_exists');
		}

		
		// dodawanie
		$data = array(
			'address' => $email,
			'crdate' => time(),
		);
		$success = $GLOBALS['TYPO3_DB']->exec_INSERTquery(
		        'tx_wsubsbox_emails',
		        $data
		);

		if ($success)	{
			return $this->ajaxGetLL('notify_success');
		}
		else	{
			return $this->ajaxGetLL('notify_error');
		}
	}


	protected function ajaxGetLL($phrase)	{
		return $this->LOCAL_LANG[$this->lang][$phrase];
	}

	protected function initAjax()	{
		$this->lang = t3lib_div::_GET('lang');
		if($this->lang == '')	$this->lang = 'default';
		$this->LOCAL_LANG = t3lib_div::readLLfile('EXT:w_subsbox/pi1/locallang.xml', $this->lang);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/w_subsbox/pi1/class.tx_wsubsbox_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/w_subsbox/pi1/class.tx_wsubsbox_pi1.php']);
}

?>