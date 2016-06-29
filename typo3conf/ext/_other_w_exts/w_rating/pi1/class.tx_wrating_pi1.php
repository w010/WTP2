<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 wolo.pl <wolo.wolski@gmail.com>
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
 * Plugin 'Voting' for the 'w_rating' extension.
 *
 * @author	wolo.pl <wolo.wolski@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_wrating
 */
class tx_wrating_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_wrating_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_wrating_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'w_rating';	// The extension key.
	var $pi_checkCHash = true;
	
	var $starsNumber;
	var $rating_id;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->init();

		// object and html container id
		$this->rating_id = 'w_rating_'.$this->cObj->data['uid'];

		// check if other ratings with same id was before (may happen when using from extensions)
		$otherRatings = $GLOBALS['TSFE']->register["w_rating"]["rating_id"];
		
		if (in_array($this->rating_id, $otherRatings))
			// add some random number to prevent conflict and validate
			$this->rating_id .= '_'.mt_rand(100, 10000);

		// store for other instances to check it
		if (!is_array($otherRatings))	$otherRatings = array();
		$otherRatings[] = $this->rating_id;
		$GLOBALS['TSFE']->register["w_rating"]["rating_id"] = $otherRatings;
		
		
		// which table?
		$table_name = $this->getConfVar('table_name');
		$where .= ' AND table_name = "'.$table_name.'"';
		
		// what uid?
		$special = $this->getConfVar('record_uid.special');

		switch ($special)	{
			case 'current':
				if ($this->getConfVar('table_name') == 'pages')
					$record_uid = $GLOBALS['TSFE']->id;
				break;
			default:
				$record_uid = intval($this->getConfVar('record_uid'));
		}

		$where .= ' AND record_uid = '.intval($record_uid);

		$votes = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
		        '*',
		        'tx_wrating_vote',
		        '1=1' . $where. $this->cObj->enableFields('tx_wrating_vote')
		);
		
		//debugster($votes);
		
		$count = count($votes);
		$notes_sum = 0;
		
		foreach ($votes as $vote)
			$notes_sum += $vote['note'];

		$note = $notes_sum / $count;

		$stars = $this->_makeStars($note);

		$content = $this->getConfVar('mode') == 'full'
			? $this->_makeViewFull($stars, $note, $count)
			 	. $this->_makeJS($table_name, $record_uid, $note)
			: $stars;

			//	debugster($content);

		return $this->pi_wrapInBaseClass($content);
	}

	protected function _makeViewFull($stars, $note, $count)	{
		$template = $this->getConfVar('templateCode')?$this->getConfVar('templateCode'):
			'<div class="votable">###STARS###</div>
			<div class="count" title="note: ###NOTE###"><p> (###COUNT### ###MSG_COUNT###) </p></div> 
			<div class="clear"></div>
			<p class="message"> ###MESSAGE### </p>';
		
		return preg_replace(
			array(
				'/###STARS###/',
				'/###NOTE###/',
				'/###COUNT###/',
				'/###MSG_COUNT###/',
				'/###MESSAGE###/',
			),
			array(
				$stars,
				$note,
				$count,
				$this->pi_getLL('msg_count'),
				$this->pi_getLL('msg_canvote')
			),
			$template);
	}
	
	protected function init()	{
		$this->starsNumber = $this->getConfVar('starsNumber')?$this->getConfVar('starsNumber'):5;
	}

	protected function _makeStars($note)	{
		for($i=1;$i<=$this->starsNumber;$i++)	{
			$full = $i <= $note;
			$stars .= '<div class="star'.($full?' full':'').'"></div>';
		}
		return '<div id="'.$this->rating_id.'" class="w_rating stars">'.$stars.' </div>';
	}

	protected function _makeJS($table_name, $record_uid, $current_note = 0)	{
		$current_note = intval($current_note);
		$jsLocal = '<script type="text/javascript">
// <![CDATA[
$(document).ready(function() {
	var Wrat = new Wrating();

	Wrat.currentNote = '.$current_note.'
	Wrat.rating_id = "'.$this->rating_id.'";
	Wrat.table_name = "'.$table_name.'";
	Wrat.record_uid = "'.$record_uid.'";
	Wrat.init();
});

// ]]>
</script>
';


$GLOBALS['TSFE']->inlineJS['w_rating'] = '

var Wrating = function() 	{

  	this. currentNote = 0;
	this. rating_id = "";
	this. rating_el = null;
	this. table_name = "";
	this. record_uid = "";

	this. voted = 0;

	this. init = function()	{

		this.rating_el = $(".votable  #"+this.rating_id);
		var stars = this.rating_el.find(".star");
			//console.log(this.rating_id);
			//console.log(this.rating_el);
		
		if (this.voted = $.cookie(this.rating_id +"_"+ this.table_name +"_"+ this.record_uid))	{
			this.disableVoting();
			this. setMessage ("'.$this->pi_getLL("msg_thank", "Thank you for voting").'");
			return;
		}

		// make this available in .each scope
		var pobj = this;

		$.each(stars, function(key, star) {
				// console.log("setup: "+pobj.rating_id + " star: " +key);

			$(star).addClass("clickable");

			$(star).click(function()	{
					// console.log("click rating: "+pobj.rating_el + " note: " +key+1);
					// ajax vote call
				pobj.ajax_vote(key+1);
			});
			$(star).hover(
				function()	{
						//	console.log("hover "+pobj.rating_id + " " +key);
					pobj.starHover(star, key);
				},
				function()	{
						//	console.log("unhover "+pobj.rating_id + " " +key);
					pobj.restoreNote();
				}
			);
		});
	};

	this. starHover = function(star, index)	{
		this.setStarsNote(index+1);
	};

	this. restoreNote = function()	{
		this.setStarsNote(this.currentNote);
	};

	this. setStarsNote = function(note)	{
		var stars = this.rating_el.find(".star");

		$.each(stars, function(key, star) {
			if (note >= key+1)		$(star).addClass("full");
			else 					$(star).removeClass("full");
		});
	};

	this. disableVoting = function()	{
		this.setStarsNote(this.voted);
		$(this.rating_el).parent().removeClass(\'votable\');

		var stars = this.rating_el.find(".star");

		// make this available in .each scope
		var pobj = this;

		var stars = this.rating_el.find(".star");
		$.each(stars, function(key, star) {
				// console.log("unbind: "+pobj.rating_id + " star: " +key);

			$(star).removeClass("clickable");
			$(star).unbind ("click");
			$(star).unbind ("hover");
		});
	};

	this. ajax_vote = function(note)	{
		var pobj = this;
		$.ajax({
	        type: "GET",
	        url: "/?eID=w_rating&method=vote",
	        data: \'table=\'+this.table_name+\'&uid=\'+this.record_uid+\'&note=\'+note,
	        dataType: "json",
	        success: function(res) {
	            if(parseInt(res)!=0)    // if no errors
	            {
						// console.log(res);
					if (res.success)	{
						pobj. voted = note;
						pobj. disableVoting();
						$.cookie(pobj.rating_id +"_"+ pobj.table_name +"_"+ pobj.record_uid,  note, {
							expires: 356,
							path: "/" });
						pobj. setMessage ("'.$this->pi_getLL("msg_thank", "Thank you for voting").'");
					}
					else if (res.notice == "ALREADY_VOTED") {
						pobj. disableVoting();
						pobj. restoreNote();
						pobj. setMessage ("'.$this->pi_getLL("msg_voted", "You voted already").'");
					}
	            }
	        }
	    });
	};

	this. setMessage = function(message)	{
		this. rating_el.parent().parent().find(".message")
			.html(message);
	}
}
';

		return $jsLocal;
	}

	/**
	* standard configuration methods thanks to Ryzy :)
	*/
	public function getConfVar($var, $sheetName = 'sDEF', $lang = 'lDEF', $field = 'pi_flexform') {
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
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/w_rating/pi1/class.tx_wrating_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/w_rating/pi1/class.tx_wrating_pi1.php']);
}

?>