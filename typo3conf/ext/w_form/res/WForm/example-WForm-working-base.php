<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2014 wolo.pl <wolo.wolski@gmail.com>
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
 * wolo.pl '.' studio 2013-2014
 * Rekrutacja
 */

require_once(t3lib_extMgm::extPath('w_tools').'class.tx_wtools_pibase.php');
//require_once(t3lib_extMgm::extPath('w_form').'pi1/class.tx_wform_pi1.php');
require_once(t3lib_extMgm::extPath('w_form').'res/WForm/class.WForm2.php');


/**
 * Plugin 'DB: Rekrutacja: Dodaj/edytuj firme' for the 'db_recru' extension.
 *
 * @author	wolo.pl <wolo.wolski@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_dbrecru
 */
class tx_dbrecru_pi5 extends tx_wtools_pibase {
	var $prefixId      = 'tx_dbrecru_pi5';		// Same as class name
	var $scriptRelPath = 'pi5/class.tx_dbrecru_pi5.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'db_recru';	// The extension key.
	var $pi_checkCHash = true;

	/**
	* Form object
	*
	* @var WForm2
	*/
	protected $Form;

	protected $templateCode = '';

	protected $notice = array();

	protected $debugData = array();

	protected $formId = '';


	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		parent::main($content, $conf);

//		debugster($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wform_pi1.']);
		/*$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wform_pi1.']['disableAjax'] = 1;
		$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wform_pi1.']['formConf'] = '
		Nazwa firmy  |  name = text  |  |  required:;"Nazwa jest wymagana", lengthMax:100;"Nazwa zbyt długa"
		Nip  |  name = text  |  |  required:;"Nip jest wymagany"
		Ulica, numer  |  name = text  |  |  required:;"Adres jest wymagany"
		Miasto  |  name = text  |  |  required:;"Miasto jest wymagane"
		Miasto  |  name = text  |  |  required:;"Miasto jest wymagane"
		Miasto  |  name = text  |  |  required:;"Miasto jest wymagane"

		E-mail  |  email = text,size:30  |  |  required:;"email is required", valid:tx_wform_pi1_userfield_email->valid;"invalid email"
		Phone  |  phone = text,size:30  |  |
		';

		$wform = tx_wform_pi1::renderFormExternal($this->cObj, '');
		return $wform;*/

		// plugin initialize
		$this->_initPlugin();

		// form initialize
		$this->_initForm();

		// form, userdata, validating...
		$this->_handleForm();

		// output display

		return $this->pi_wrapInBaseClass($this->_renderContent());
	}


	protected function _initPlugin()	{
		//$this->conf['templateFile'] = $this->conf['templateFile'] ? $this->conf['templateFile'] : $defaultTemplateFile;
		$this->templateCode = $this->cObj->fileResource($this->conf['templateFile']);
		if (!$this->getConfVar('noDefaultCss'))	    $GLOBALS['TSFE']->additionalHeaderData[$this->prefixId] = '<link rel="stylesheet" type="text/css" href="typo3conf/ext/db_recru/templates/style.css" media="all" />';
		// captcha object
        /*if (t3lib_extMgm::isLoaded('sr_freecap') ) {
            require_once(t3lib_extMgm::extPath('sr_freecap').'pi2/class.tx_srfreecap_pi2.php');
            $this->freeCap = t3lib_div::makeInstance('tx_srfreecap_pi2');
        }*/
	}


	protected function _initForm()  {
		//$valueArray = pivars albo z bazy
		$this->formId = 'firma';
		$this->Form = new WForm2($this->piVars);
		$this->Form->name = 'wform_form';
		$this->Form->setId( $this->formId )
					->setAction($this->pi_linkTP_keepPIvars_url(array(), 0, 1))
					//->setAction(($_t = $this->pi_getPageLink($GLOBALS['TSFE']->id)) . (strstr($_t,'?')?'&':'?') . 'no_cache=1')
					//->setMethod('post') // not necessary, post is default
					->setDefaultFieldnamePrefix($this->prefixId)
					->setSubmitFieldName('firma_send')

					->addField(array(
						'name' => '[DATA][name]',
						'type' => 'text',
						'idname' => 'name',
						'marker' => 'NAME',
						'value' => $valueArray['name'],
						'validators' => array(
							'required' => array('message' => $this->pi_getLL('msg.field.name.required', 'nazwa jest wymagana')),
							'lengthMax' => array('value' => 100, 'message' => $this->pi_getLL('msg.field.email.lengthMax', 'nazwa zbyt długa')),
					)))
					->addField(array(
						'name' => '[DATA][email]',
						'type' => 'text',
						'idname' => 'email',
						'marker' => 'EMAIL',
						'value' => $valueArray['name'],
						'validators' => array(
							'required' => array('message' => $this->pi_getLL('msg.field.email.required', 'email is required')),
							'valid' => array('userObj' => &$this, 'methodName' => '_validEmail', 'message' => $this->pi_getLL('msg.field.email.valid', 'invalid email')),
					)))

					->init();

					// OPTIONS
                    /*->addField(array(
                        'name' => '[DATA][receipt_date]',
                        'type' => 'text',
                        'marker' => 'RECEIPT_DATE',
                        'id' => 'date_hr',
                        'additionalTagParams' => array('onfocus' => 'document.getElementById(\'date_trigger\').onclick();'),
                        		//'onfocus' => 'document.getElementById(\'date_trigger\').onclick();'),
                        'validators' => array(
                            'required' => array('message' => 'podanie daty jest wymagane'),
                            'valid' => array('userObj' => &$this, 'methodName' => '_validReceiptdate', 'message' => 'podana data jest nieprawidłowa'),
                    ))) */

					/*
					if (is_object($this->freeCap))  {
				$this->Form->addField(array(
					'name' => '[captcha_response]',
					'type' => 'text',
					//'marker' => '__NORENDER_CAPTCHA',
					'marker' => 'CAPTCHA',
					//'prefix' => 'tx_freecap_pi2',
					'validators' => array(
						'required' => array('message' => 'wprowadź tekst z obrazka'),
						'valid' => array('userObj' => &$this->freeCap, 'methodName' => 'checkWord', 'message' => 'tekst nie zgadza się'),
				)));
		}


		// init form
		$this->Form->init();

		*/
	}

	protected function _handleForm()	{
		if ($this->Form->isSubmitted())  {

			$this->Form->validate();
			$vars = $this->Form->getInputValues();

			if ($validForm = $this->Form->isFormValid())	{
				// do something, eg save to db
				$insertArray = Array(
					'name' => $vars['[DATA][name]'],
					'email' => $vars['[DATA][email]'],
				);

				$done = true;

				if ($this->conf['saveToDb'])	{
					$done = $done && $GLOBALS['TYPO3_DB']->exec_INSERTquery(
							'tx_wform_forms',
							$insertArray
					);
				}

				if ($done)	{
					$this->notice[] = '[ll] Form has been sent!';
					// set to not render fields with their values
					$this->Form->disableFieldValuesRendering();
				} else  {
					$this->notice[] = '[ll] Cannot save form, error';
				}
			}
		}
	}


	protected function _renderContent() {
		$markers = array();
		$markers['###FORM_OPENTAG###'] = $this->Form->renderOpenTag();

		$formTemplate = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM###');
		if (!$formTemplate)	$this->debugData[] = 'NO TEMPLATE!';
		$markers = array();
		$subparts = array();

		// make input markers like ###INPUT_inputmarkername### with input html tag and ###ERROR_inputmarkername### with error classname
		$this->_processInputMarkers($markers);
		$this->_buildOtherMarkers($markers, $subparts);

//		debugster($this->conf);


		// captcha
		/*if (is_object($this->freeCap)) {
			$markers = array_merge($markers, $this->freeCap->makeCaptcha());
		}   else	{
			$subparts['###CAPTCHA_INSERT###'] = '';
		}*/



		// form result message
		if (($message = array_shift($this->notice))  ||  $message = $this->Form->getOneError())   {
			$markers['###RESULT###'] = $message;
		} else  {
			$subparts['###SUB_RESULT###'] = '';
		}
		return $this->cObj->substituteMarkerArrayCached($formTemplate, $markers, $subparts)  . (DEV ? $this->displayFormDebugFrame() : '');;
	}


	protected function _processInputMarkers(&$markers)  {

		foreach ($this->Form->getFieldNames() as $fieldName)  {
			$Field = $this->Form->Field($fieldName);
			$idname = $Field->getIdname();
			$type = $Field->getType();

			// get row template
			if (!$rowTemplate = $this->cObj->getSubpart($this->templateCode, '###FIELD__'.$idname.'###'))
				if (!$rowTemplate = $this->cObj->getSubpart($this->templateCode, '###FIELD_'.strtoupper($type).'###'))
					$rowTemplate = $this->cObj->getSubpart($this->templateCode, '###FIELD_DEFAULT###');

			$ma['###LABEL###'] = $this->pi_getLL('label_field_'.$idname, $this->fieldsConf[$idname]['label']);
			$ma['###ERROR_CLASS###'] = $Field->isError() ? ' '.$this->getConfVar('errorClass') : '';
			$ma['###REQUIRED_MARK###'] = $Field->isRequired() ? $this->cObj->getSubpart($this->templateCode, '###MARK_REQUIRED###') : '';
			$ma['###ROW_ID###'] = $this->formId.'_row_'.$idname;


			if ($type != 'user')	{
				$ma['###INPUT###'] = $this->Form->renderField($fieldName, array());
				$codeField = $this->cObj->substituteMarkerArrayCached($rowTemplate, $ma, $subpa);
			}
			else	{
				// put already generated markers in registry, so it can be used in userfield class
				$_ma = &tx_wform_pi1_registry::Registry('wform', 'markers_field_'.$idname);
				$_ma = $ma;
				$codeField = $this->Form->renderField($fieldName, array());
			}

			$markers['###FORM_FIELDS###'] .= $codeField;
		}

		return;
		$fieldNames = $this->Form->getFieldNames();
		$this->debugData[] = 'generating fieldnames: '.implode(', ', $fieldNames);
		foreach($fieldNames as $fieldName)  {
			$markerFieldName = strtoupper( $this->Form->Field($fieldName)->marker );
			// if (strstr($markerFieldName, '__NORENDER'))  continue;
			$markers['###INPUT_'.$markerFieldName.'###'] = $this->Form->renderField($fieldName, array());
			$markers['###ERROR_'.$markerFieldName.'###'] = $this->Form->Field($fieldName)->isError() ? ' error' : '';
		}
	}


	protected function _buildOtherMarkers(&$markers, &$subparts)	{
		$markers['###FORM_OPENTAG###'] = $this->Form->renderOpenTag();
		$markers['###EXT_PREFIX###'] = $this->prefixId;
		$markers['###FORM_ID###'] = $this->formId;
		$markers['###LABEL_SUBMIT###'] = $this->pi_getLL('label_field_submit', 'Submit');

		// CALENDAR
		/*include_once(t3lib_extMgm::siteRelPath('date2cal') . '/src/class.jscalendar.php');
		$JSCalendar = JSCalendar::getInstance();
		$JSCalendar->setInputField('date');
		$JSCalendar->config['calConfig']['ifFormat'] = '\'%d.%m.%Y\'';
		$JSCalendar->setDateFormat(true, $dateFormat);
		$markers['###DATE2CAL###'] = $JSCalendar->renderImages();
		if (($jsCode = $JSCalendar->getMainJS()) != '') {
			$GLOBALS['TSFE']->additionalHeaderData['powermail_date2cal'] = $jsCode;
		}*/

		foreach(t3lib_div::trimExplode(',', $this->getConfVar('renderCEuids')) as  $ceUid)	{
			$markers['###CE_'.intval($ceUid).'###'] = $this->renderCE($ceUid);
		}

		// LABELS
		// todo: to form ini conf
		$fields = 'test';
		//$this->Form->getFieldNames()
		$fieldsArr = t3lib_div::trimExplode(',', $fields);
		foreach ($fieldsArr as $fieldName)	{
			$markers['###LABEL_'.strtoupper($fieldName).'###'] = $this->pi_getLL('label.'.$fieldName);
		}
	}

	protected function displayFormDebugFrame()	{
		$content = '<div id="wform_debug_'.$this->formId.'" class="wform-debug" style="margin-top: 15px; border: 1px solid red;">WForm debug<br />';
		foreach ($this->debugData as $row)
			$content .= '<p>'.$row.'</p>';
		return $content . '</div>';
	}



	// additional (validator):
	public function _validEmail($value)   {
		return t3lib_div::validEmail($value);
	}

}



?>