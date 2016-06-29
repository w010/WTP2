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

require_once(t3lib_extMgm::extPath('w_gmaps').'class.tx_wgmaps_pibase.php');

/**
 * Plugin 'Map directions' for the 'w_gmaps' extension.
 *
 * @author	wolo <wolo.wolski@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_wgmaps
 */
class tx_wgmaps_pi1 extends tx_wgmaps_pibase {
	public $prefixId      = 'tx_wgmaps_pi1';		// Same as class name
	public $scriptRelPath = 'pi1/class.tx_wgmaps_pi1.php';	// Path to this script relative to the extension dir.
	public $extKey        = 'w_gmaps';	// The extension key.
	public $pi_checkCHash = TRUE;


	public $notice = array();
	public $debugData = array();

	/**
	 * The main method of the Plugin.
	 *
	 * @param string $content The Plugin content
	 * @param array $conf The Plugin configuration
	 * @return string The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;

		// plugin initialize
		$this->_initPlugin();

		// form initialize
		//$this->_initForm();

		// form, userdata, validating...
		//$this->_handleForm();

		// output display
		return $this->pi_wrapInBaseClass($this->_renderContent());
	}


	protected function _initPlugin()	{
		//$this->isAjaxRequest = $this->conf['mode']=='ajax'?true:false;

		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_initPIflexForm();

		$this->templateCode = $this->cObj->fileResource($this->conf['templateFile']);
		if (!$this->getConfVar('noDefaultCss'))	$GLOBALS['TSFE']->additionalHeaderData[$this->prefixId] = '<link rel="stylesheet" type="text/css" href="typo3conf/ext/w_gmaps/res/style.css" media="all" />';
		// set reference to pi1, to easy use in userfield and hook classes
		$_this = &tx_wgmaps_registry::Registry('wgmaps', 'instance_pi1');
		$_this = $this;	// cannot do it once
		// set instance of hook singleton class with ref to this. this also can be done with registry.
		//tx_wform_pi1_hook::instance($this);
	}


	protected function _renderContent() {
//debugster($this->piVars);
//debugster($this->conf);

		$formTemplate = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_DIRECTIONS###');
		if (!$formTemplate)	$this->debugData[] = 'NO TEMPLATE!';
		$stepTemplate = $this->cObj->getSubpart($formTemplate, '###SUB_STEP###');
		$markers = array();
		$subparts = array();

		//$this->_processInputMarkers($markers);
		$this->_buildOtherMarkers($markers, $subparts);

		// put already generated markers in registry, so it can be used in userfield class
		//$_ma = &tx_wform_pi1_registry::Registry('wform', 'markers_field_'.$idname);


		$error = false;

		$point_start = $this->getPoint($this->conf['startPointUid']);
		$point_end = $this->getPoint($this->piVars['point']);

		if ($this->piVars['point']  ||  $this->piVars['point_name'])	{

			//debugster($point);

			if (!$point_start)	{
				$this->debugData[] = "starting point not found. uid=".$this->conf['startPointUid'];
				$this->notice[] = $this->pi_getLL('error_no_startpoint', 'Starting point not found');
				$error = true;
			}
			if (!$point_end && !$this->piVars['point_name'])	{
				$this->debugData[] = "ending point not found. no uid or name specified or record not found";
				$this->notice[] = $this->pi_getLL('error_no_endpoint', 'Destination point not found');
				$error = true;
			}

			if (!$error)	{
				$origin = $point_start['coords'];
				$destination = $point_end['coords']?$point_end['coords']:$this->piVars['point_name'];
				$Res_url = 'http://maps.googleapis.com/maps/api/directions/json?origin='.urlencode($origin).'&destination='.urlencode($destination).'&sensor=false&mode=walking&language=de';
				//debugster($Res_url);
				$Res_raw = $this->getFromUrl($Res_url);
				//debugster($Res_raw);
				$Res = json_decode($Res_raw);
				//debugster($Res);

				if (!$Res_raw  ||  !$Res  ||  $Res->status!='OK')	{
					$this->debugData[] = "result empty, url: ".$Res_url;
					$this->notice[] = $this->pi_getLL('error_communication', 'Cannot communicate with maps server');
					$error = true;
				}
				else	{
					// EVERYTHING'S OK?

					$route = $Res->routes[0];
					$legs = $route->legs[0];
					$steps = $legs->steps;

					$markers['###POINT_A_NAME###'] = $legs->start_address;
					$markers['###POINT_B_NAME###'] = $legs->end_address;
					$markers['###INFO###'] = $legs->distance->text .' - '.$this->pi_getLL('approx', 'approximately').' '.$legs->duration->text;
					$markers['###TOGGLER_TEXT###'] = $this->pi_getLL('toggle', 'Schliessen');

					//debugster($Res);

					if (is_array($steps))
					foreach($steps as $i => $step)	{
						//debugster($step);
						$ma = array();
						$subpa = array();
						$ma['###NUMBER###'] = ($i+1).'.';
						$ma['###ICON###'] = '';
						$ma['###INSTRUCTIONS###'] = $step->html_instructions;
						$ma['###LENGTH###'] = $step->distance->text;
						$ma['###STEP_CLASS###'] .= $i%2?' odd':' even';
						$ma['###STEP_CLASS###'] .= $i+1==count($steps) ? ' last' : '';

						$subparts['###SUB_STEP###'] .= $this->cObj->substituteMarkerArrayCached($stepTemplate, $ma, $subpa);
					}
				}
			}

			if ($error  ||  !count($Res->routes))	{
				$subparts['###SUB_ROUTE###'] = '';
			}
		}
		else	{
			$subparts['###SUB_ROUTE###'] = '';
		}

		$markers['###MAP###'] = $this->makeMap($point_start, $destination);
		$markers['###VAL_POINT_NAME###'] = $this->piVars['point_name'];
		$markers['###POINTS_OPTIONS###'] = $this->makePointsOptions();


		//debugster($markers);

		// form result message
		if (($message = array_shift($this->notice)))   {
			$markers['###RESULT###'] = $message;
		} else  {
			$subparts['###SUB_RESULT###'] = '';
		}
		return $this->cObj->substituteMarkerArrayCached($formTemplate, $markers, $subparts) . (DEV ? $this->displayFormDebugFrame() : '');
//		return $this->cObj->substituteMarkerArrayCached($formTemplate, $markers, $subparts) . $this->displayFormDebugFrame();
	}


	protected function _buildOtherMarkers(&$markers, &$subparts)	{
		$markers['###EXT_PREFIX###'] = $this->prefixId;
		$markers['###LABEL_SUBMIT###'] = $this->pi_getLL('label_field_submit', 'Submit');
		$markers['###FORM_ID###'] = 'wmaps_'.$this->cObj->data['uid'];
		$markers['###URL_FORM_ACTION###'] =	$this->pi_getPageLink($GLOBALS['TSFE']->id, '', array('no_cache' => 1));
		$markers['###FORM_HEAD###'] = $this->pi_getLL('label_form_head', 'Routenplaner');

		foreach (explode(',', $this->getConfVar('add_lang_markers', 'sDEF')) as $markerName )
			$markers['###'.strtoupper($markerName).'###'] = $this->pi_getLL($markerName, 'no label for add_lang_markers value: '.$markerName);

		//tx_wform_pi1_hook::content_otherMarkers($markers, $subparts);
	}


	protected function displayFormDebugFrame()	{
		$content = '<div id="wmaps_debug_'.$this->formId.'" class="wmaps-debug" style="margin-top: 15px; border: 1px solid red;">Wmaps debug<br />';
		foreach ($this->debugData as $row)
			$content .= '<p>'.$row.'</p>';
		return $content . '</div>';
	}


	protected function makeMap($point_start, $destination)	{
		//$apikey = 'AIzaSyBESugQEAaY4h9jY0Q0BqIEcCzLhqMlia8';	// wolo's private key - please change it immediately
		$apikey = $this->getConfVar('apiKey');

		//$jsUrl = 'http://maps.googleapis.com/maps/api/js?key='.$apikey.'&sensor=false&callback=gmapsInitialize&language=de';
		$jsUrl = 'http://maps.googleapis.com/maps/api/js?key='.$apikey.'&sensor=false&language=de';
		$pagerender = $GLOBALS['TSFE']->getPageRenderer()->addJsFile($jsUrl);
		//$GLOBALS['TSFE']->additionalHeaderData['xxxx'] = 'test';

		$code = '
		<script>
		var directionsDisplay;
		var directionsService = new google.maps.DirectionsService();
		var map;


		function gmapsInitialize() {
			directionsDisplay = new google.maps.DirectionsRenderer();
		  	var mapOptions = {
		    	zoom: 14,
		    	center: new google.maps.LatLng('.($point_start['coords']?$point_start['coords']:'47.285750, 11.403809').'),
		    	mapTypeId: google.maps.MapTypeId.ROADMAP
		  	}
		  	map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
		  	directionsDisplay.setMap(map);
		}

 		function calcRoute()	{
 		  	directionsService.route( {
 		  			origin: "'.$point_start['coords'].'",
					destination: "'.$destination.'",
					travelMode: google.maps.DirectionsTravelMode.WALKING
 		  		}, function( response, status ) {
		  			if ( status == google.maps.DirectionsStatus.OK ) {
			  			directionsDisplay.setDirections( response );
			  			console.log( response.routes[ 0 ].legs[ 0 ] );
			 		}
		  	});
		}

		$(document).ready(function() {
			gmapsInitialize();
		});

		'.($point_start['coords']&&$destination?'
		$(document).ready(function() {
			calcRoute();
		});
		':'')
		.'
		</script>';
		$code .= '<div id="map_canvas" style="width: 100%; height: 100%"></div>';
		return $code;
	}


	protected function getPoints($whereClause = '')	{
		return $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
		        '*',
		        'tx_wgmaps_points',
		        '1=1' . $whereClause . $this->cObj->enableFields('tx_wgmaps_points')
		);
	}

	protected function getPoint($uid)	{
		if (!$uid)	return false;
		$whereClause .= ' AND uid = '.intval($uid);
		$res = $this->getPoints($whereClause);
		return array_pop($res);
	}


	protected function makePointsOptions()	{
		$points = $this->getPoints();
		array_unshift($points, array());
		foreach($points as $row)	{
			$code .= '<option value="'.$row['uid'].'"'
			.($this->piVars['point']==$row['uid']?' selected="selected"':'')
			.'>'.$row['title'].'</option>';
		}
		return $code;
	}


	public function getFromUrl($url, $sslBypass = true)	{
		ob_start();

		$ch = curl_init($url);
		@curl_setopt($ch, "CURLOPT_RETURNTRANSFER", 1);
		if ($sslBypass)	{
			// nie sprawdza certyfikatu
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		}

		$xml = curl_exec($ch);

		if (curl_errno($ch)) {
			$this->error = true;
			return false;
		}

		curl_close($ch);
		$raw = ob_get_contents();
		ob_clean();

		return $raw;
	}
}



if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/w_gmaps/pi1/class.tx_wgmaps_pi1.php'])) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/w_gmaps/pi1/class.tx_wgmaps_pi1.php']);
}

?>