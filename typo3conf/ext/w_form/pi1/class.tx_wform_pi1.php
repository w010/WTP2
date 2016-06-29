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


//require_once(PATH_tslib.'class.tslib_pibase.php');
//require_once(t3lib_extMgm::extPath('w_tools').'class.tx_wtools_pibase.php');

require_once(t3lib_extMgm::extPath('w_form').'res/WForm/class.WForm2.php');
require_once(t3lib_extMgm::extPath('w_form').'pi1/class.tx_wform_pi1_hook.php');
require_once(t3lib_extMgm::extPath('w_form').'pi1/class.tx_wform_pi1_userfield.php');


/**
 * Plugin 'W Form' for the 'w_form' extension.
 *
 * @author	wolo <wolo.wolski@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_wform
 */
class tx_wform_pi1 extends tx_wtools_pibase {
	var $prefixId	  = 'tx_wform_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_wform_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey		= 'w_form';	// The extension key.

	/**
	* Form object
	*
	* @var WForm2
	*/
	protected $Form;

	public $templateCode = '';

	protected $notice = array();

	protected $fieldsConf = array();

	protected $isAjaxRequest = false;

	protected $debugData = array();

	public $formId = 'firma';


	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		parent::main($content, $conf);
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
		//$this->isAjaxRequest = $this->conf['mode']=='ajax'?true:false;
		$this->isAjaxRequest = t3lib_div::_GP('type') == $this->conf['AjaxTypeNum'] || t3lib_div::_GP('type') == $this->conf['AjaxTypeNum_spec']?true:false;

		/* specified instance is for embeding plugin using TS, not as CE */
		if ($this->isAjaxRequest  &&  $_GET['specifiedInstance'])
			$this->conf['specifiedInstance'] = $_GET['specifiedInstance'];

		if ($this->conf['specifiedInstance'])
			// ewentualnie zrobic lepszy merge, bo recursive ze stringow robi tablice...
			$this->conf = array_merge($this->conf,  (array) $this->conf['specifiedConf.'][$this->conf['specifiedInstance'].'.']);

		$this->ajaxEnabled = !$this->getConfVar('disableAjax');
		//$this->ajaxEnabled = false;
		$this->templateCode = $this->cObj->fileResource($this->conf['templateFile']);
		if (!$this->getConfVar('noDefaultCss'))	$GLOBALS['TSFE']->additionalHeaderData[$this->prefixId] = '<link rel="stylesheet" type="text/css" href="typo3conf/ext/w_form/res/style.css" media="all" />';
		// set reference to pi1, to easy use in userfield and hook classes
		// TODO: sprawdzic, to sie chyba gryzie przy kilku instancjach plugina? raczej dodac uid
		$_this = &tx_wform_pi1_registry::Registry('wform', 'instance_pi1');
		$_this = $this;	// cannot do it once
		// set instance of hook singleton class with ref to this. this also can be done with registry.
		tx_wform_pi1_hook::instance($this, $this->Form);
		//debugster($this->getConfVar('formConf'));
	}


	protected function _initForm()  {
		$this->_parseFormSetup();

		$this->formId = $this->getConfVar('form_id') ? $this->getConfVar('form_id')
			: ($this->getConfVar('specifiedInstance') ? $this->getConfVar('specifiedInstance')
				: $this->cObj->data['uid'] );
		if (!$this->formId)	throw new Exception('When WForm inserted not as content element, option plugin.tx_wform_pi1.form_id has to be set.');

		$this->formId = $this->isAjaxRequest ? $_GET['formId'] : 'wform_'.$this->formId;

		$action = $this->getConfVar('action');
		if (!$action)	{
			$action = $this->pi_getPageLink( $this->getConfVar('action_pid') ? $this->getConfVar('action_pid') : $GLOBALS['TSFE']->id );
			$action .= $this->getConfVar('dont_force_nocache') ? '' : ( (strstr($action,'?')?'&':'?') . 'no_cache=1' );	// force no cache
		}


		try {
			$this->Form = new WForm2($this->piVars);
			$this->Form->setId($this->formId)
						//->setAction($this->pi_linkTP_keepPIvars_url(array(), 0, 1))
						->setAction($action)
						->setMethod($this->getConfVar('method') ? $this->getConfVar('method') : 'post')
						->setDefaultFieldnamePrefix($this->prefixId)
						->setSubmitFieldName($this->formId.'_send');

			// quick workaround, wform has no name property, is required? for form tag, or disable it in makeformopentag
			$this->Form->name = 'wform_form';

			// TODO: userfunc for options
			foreach ($this->fieldsConf as $fieldConf)	{

				$this->Form->addField(array(
					'name' => ($fieldConf['DATAkey']?'[DATA]':'') . '['.$fieldConf['idname'].']',
					'idname' => $fieldConf['idname'],
					'id' => $this->formId.'_'.$fieldConf['idname'],
					'type' => $fieldConf['type'],
					'marker' => strtoupper($fieldConf['idname']),
					'validators' => $fieldConf['validators']?$fieldConf['validators']:array(),
					'value' => $fieldConf['value'],
					'additionalTagParams' => $fieldConf['additionalTagParams'],
					'setup' => $fieldConf['setup'],
				));
			}

			$this->Form->init();

		} catch (WForm2Exception $e)	{
			$this->debugData[] = $e->getMessage();
			$this->debugData[] = json_encode($e->getDebugData());
		}
	}


	protected function _handleForm()	{

	  try	{

		if ($this->Form->isSubmitted())  {

			$this->Form->validate();
			$vars = $this->Form->getInputValues();

			if ($validForm = $this->Form->isFormValid()  &&  $this->Form->getFieldNames())	{
				// PROCESS DATA
				// do something, eg save to db

				$done = true;

				if ($this->getConfVar('saveToDb'))	{
					$formdata = '';
					foreach ($this->Form->getFieldNames() as $fieldName)  {
						$idname = $this->Form->Field($fieldName)->getIdname();
						$value = $this->Form->Field($fieldName)->getValue();
						$formdata .= "{$idname}: {$value}\n";
					}

					$formdata .= "ip: {$this->getRealIpAddr()}\n"
							   . "lang: {$this->LLkey}\n";

					$insertArray = Array(
						'name' => $vars['[DATA][name]'],
						'email' => $vars['[DATA][email]'],
						'formdata' => $formdata,
						'tstamp' => $GLOBALS['EXEC_TIME'],
						'crdate' => time(),
						'pid' => $this->getConfVar('recordStoragePid')?$this->getConfVar('recordStoragePid'):array_shift(explode(',', $this->cObj->data['pages'])),
					);
					tx_wform_pi1_hook::process_beforeDbInsert($insertArray);

					$done = $done && $GLOBALS['TYPO3_DB']->exec_INSERTquery(
							'tx_wform_forms',
							$insertArray
					);
				}

				if ( $this->getConfVar('sendEmailAdmin', 'sMail') )	{
					$done = $done && $this->sendMailToAdmin($vars);
				}

				switch ($done)	{
					case true:
						if ($_t = $this->getConfVar('redirect.after_success', '')  &&  !$this->isAjaxRequest)
							$this->redirect($_t);
						$this->notice[] = $this->pi_getLL('msg_formSend', 'Form has been sent!');
						// set to not render fields with their values
						$this->Form->disableFieldValuesRendering();
						// TODO: opcja - ukrycie formularza, sam komunikat (wplyw jej tez na ajax)
						break;
					case false:
//						if (DEV) $this->notice[] = $this->pi_getLL('msg_formSendError', 'Cannot save form, error');	// todo: komunikaty tech przy savedb i mailtoadmin
						$this->notice[] = $this->pi_getLL('msg_formSendError', 'Cannot save form, error');
				}
			}
		}
	  } catch (Exception $e)	{
	  	  	// NOTE: this causes exception messages show as form result notice
	  	  	$this->notice[] = '[error, if you see this, contact administrator]<br />' . $e->getMessage();
			$this->debugData[] = $e->getMessage();
	  }
	}


	protected function _renderContent() {
		if ($this->isAjaxRequest)
			return $this->ajax_response();

		$formTemplate = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM###');
		if (!$formTemplate)	$this->debugData[] = 'NO TEMPLATE!';
		$markers = array();
		$subparts = array();

		//$this->_processInputMarkers($markers);
		$this->_buildOtherMarkers($markers, $subparts);


		// make input rows
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

		if (!$markers['###FORM_FIELDS###'])	{
			$markers['###FORM_FIELDS###'] = '';
			$this->debugData[] = "form fields empty! check your config: <br />".nl2br($this->getConfVar('formConf'));
		}

		// aktualnie nic nie zwraca, bo piszemy js do headera. ale w jakiejs sytuacji moze byc potrzebne
		$markers['###JS_VALIDATOR###'] = $this->ajax_renderCode();
		$markers['###FORM_ID###'] = $this->formId;

		foreach(t3lib_div::trimExplode(',', $this->getConfVar('renderCEuids')) as  $ceUid)	{
			$markers['###CE_'.intval($ceUid).'###'] = $this->renderCE($ceUid);
		}

//debugster($markers);

		// form result message
		if (($message = array_shift($this->notice))  ||  $message = $this->Form->getOneError())   {
			$markers['###RESULT###'] = $message;
		} else  {
			$subparts['###SUB_RESULT###'] = '';
		}
		
		return $this->cObj->substituteMarkerArrayCached($formTemplate, $markers, $subparts) . (DEV ? $this->displayFormDebugFrame() : '');
	}


	protected function ajax_response()	{
		$formTemplate = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM###');
		$resultTemplate = $this->cObj->getSubpart($formTemplate, '###SUB_RESULT###');
		$GLOBALS['TYPO3_CONF_VARS']["FE"]["debug"] = '0';
		$this->conf['noBaseClassWrap'] = true;

		foreach ($this->Form->getFieldNames() as $fieldName)  {
			$errors[$this->Form->Field($fieldName)->getIdname()] = $this->Form->Field($fieldName)->isError();
		}
//debugster($this->Form->debug);
//debugster($this->Form->debug());


		$notice = ($_t = array_shift($this->notice)) ? $_t : $this->Form->getOneError();
		$notice = $notice ? str_replace('###RESULT###', $notice , $resultTemplate) : '';

		return json_encode(array(
			'success' => $this->Form->isFormValid(),
			'notice' => $notice,
			'errors' => $errors
		));
	}


	protected function ajax_renderCode()	{
		if (!$this->ajaxEnabled)	return;
		$pageType = $this->conf['specifiedInstance'] ? $this->getConfVar('AjaxTypeNum_spec') : $this->getConfVar('AjaxTypeNum');
		$url = $this->pi_getPageLink($GLOBALS['TSFE']->id, '', array('type' => $pageType, 'uid' => $this->cObj->data['uid'] ));
		if ($this->conf['includeJqFormInPlugin'])
			$GLOBALS['TSFE']->additionalHeaderData['tx_wform_pi1_static'] = '<script src="'.$this->conf['includeJqFormInPlugin'].'" type="text/javascript"></script>';
		$GLOBALS['TSFE']->additionalHeaderData['tx_wform_pi1_local__'.$this->formId] =
		"
<script type=\"text/javascript\">
// <![CDATA[
var jQwf = jQuery.noConflict();
jQwf(document).ready(function() {

	var options_".$this->formId." = {
		//target:			'#output1',		// target element to update
		beforeSubmit:	wform.f_".$this->formId.".ajax.beforeSubmit,  		// pre-submit callback
		success:		wform.f_".$this->formId.".ajax.handleResponse,		// post-submit callback
		error:			wform.f_".$this->formId.".ajax.handleError,
		url:			'".(  $url . (strstr($url,'?')? '&':'?') . 'formId='.$this->formId.'&no_cache=1' ) . ( $this->conf['specifiedInstance']?'&specifiedInstance='.$this->conf['specifiedInstance']:'' ) . "'
	};

    // bind wform and provide a callback functions
    jQwf('#".$this->formId."').ajaxForm(
    	options_".$this->formId."
    );
});


if (!wform) var wform = {};
wform.f_".$this->formId." = {}
wform.f_".$this->formId.".ajax = {

	// pre-submit callback
	beforeSubmit: function(formData, jqForm, options)	{
		wform.f_".$this->formId.".ajax.debuglog('- Ajax request...');
		//console.log(options);
		// start animation and loader
		jQwf('#".$this->formId."_container').addClass('loading');

		var inner = jQwf('#".$this->formId."_container').find('.wform-inner');
		jQwf(inner).fadeTo('fast', 0.3);

		/*
		jQwf(inner).animate({
    		opacity: 0.3,
    	}, 5000, function() {
    		// Animation complete.
  		});/**/


		//return true;
	},


	handleResponse: function(responseText, statusText)  {
		//wform.f_".$this->formId.".ajax.debuglog('response:<br/>'+responseText);
		//console.log(responseText);
		//responseText = strip_tags(responseText);

		var inner = jQwf('#".$this->formId."_container').find('.wform-inner');
		jQwf(inner).fadeTo('fast', 1);
		jQwf('#".$this->formId."_container').removeClass('loading');

		// res pobierany przez explode, bo przez regexp nie udalo sie replace parsetime...
		var resArr = responseText.split('<!');
		var res = jQwf.parseJSON(resArr[0]);
		//var res = jQwf.parseJSON(responseText.split('<!')[0]);
		//console.log(res);

		var debuglog_errors = '';
		wform.f_".$this->formId.".ajax.debuglog('success: '+res.success);

		".($this->getConfVar('hideFormAfterSuccess') ? "
		// hide form after success
		if (res.success)
			jQwf('#".$this->formId."_container').find('.form-body').hide();
		" : "") . "

		// set notice
		jQwf('#".$this->formId."_result').html(res.notice);

            //console.log(res.errors);
		// set error for input rows
        if(res.errors)
            jQwf.each(res.errors, function(idname, iserror){
                if (iserror)	{
                    jQwf('#".$this->formId."_row_'+idname).addClass('".$this->getConfVar('errorClass')."');
                    debuglog_errors += idname+', ';
                }
                else
                    jQwf('#".$this->formId."_row_'+idname).removeClass('".$this->getConfVar('errorClass')."');

                //console.log(idname);
                //console.log(iserror);
            });
            
		if (debuglog_errors)	wform.f_".$this->formId.".ajax.debuglog('errors in: '+ debuglog_errors);
		".$this->getConfVar('jshook_handleResponseEnd', 'sTech')."
	},


	handleError: function(x)	{
		wform.f_".$this->formId.".ajax.debuglog('error:<br/>'+x);
	},

	debuglog: function(data)	{
		jQwf('#wform_debug_".$this->formId."')
		.append('<p>'+data+'</p>');
	}
}

// strip tags from json response (parsetimes or debug)
function strip_tags (input, allowed) {
	allowed = (((allowed || \"\") + \"\").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join(''); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
	var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
		commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
	return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
		return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
	});
}
// ]]>
</script>";

/*
$code = "
<script type=\"text/javascript\">
	/*
	submitForm: function(form)	{
  	   //$('.button').click(function() {    }
  	}

	$.ajax({
	  type: 'POST',
	  url: 'bin/process.php',
	  data: dataString,
	  success: function() {
	    $('#message').html('').append('').hide().fadeIn(500, function() {
	      //$('#message').append('');
	    });
	  }
	});
</script> ";*/
//return $code;
	}


	protected function _buildOtherMarkers(&$markers, &$subparts)	{
		$markers['###FORM_OPENTAG###'] = $this->Form->renderOpenTag();
		$markers['###EXT_PREFIX###'] = $this->prefixId;
		$markers['###LABEL_SUBMIT###'] = $this->pi_getLL('label_field_submit', 'Submit');

		foreach (explode(',', $this->getConfVar('add_lang_markers', 'sDEF')) as $markerName )
			$markers['###'.strtoupper($markerName).'###'] = $this->pi_getLL($markerName, 'no label for add_lang_markers value: '.$markerName);

		tx_wform_pi1_hook::content_otherMarkers($markers, $subparts);

		// deprecated - budujemy rowy z inputami, wiec w globalnych markerach takich rzeczy nie budujemy
		/*foreach ($this->Form->getFieldNames() as $fieldName)  {
			$idname = $this->Form->Field($fieldName)->getIdname();
			$markers['###LABEL_'.strtoupper( $this->Form->Field($fieldName)->marker).'###'] = $this->pi_getLL('label_field_'.$idname, $this->fieldsConf[$idname]['label']);
		}*/
	}



	/**
	* (deprecated) standard, do not touch
	*
	* For compatibility and for using in other extensions
	* Here in this ext we copy these markers to make input rows, to not calling it twice when this is here
	*
	* @param array $markers
	*/
	protected function _processInputMarkers(&$markers)  {
		foreach ($this->Form->getFieldNames() as $fieldName)  {
			//$markerFieldName = strtoupper( $this->Form->Field($fieldName)->marker );
			// if (strstr($markerFieldName, '__NORENDER'))  continue;
			//$markers['###INPUT_'.$markerFieldName.'###'] = $this->Form->renderField($fieldName, array());
			//$markers['###ERROR_'.$markerFieldName.'###'] = $this->Form->Field($fieldName)->isError() ? ' error' : '';
		}
	}



	/**
	* Prepare fields config array from user setup
	*
	* Parse setup to config array to use for addField()
	*/
	protected function _parseFormSetup()  {

		// TODO: sprawdzic, co sie dzieje na typach hidden
		// TODO: sprawdzic, co sie dzieje gdy nie podasz labela i w ll nie ma
		// TODO: opcja - clear button w input text - to jako klasa user rozszerzajaca standard text input
		// TODO: inline walidacja np pass strength

		$____formConfigExample = '
# Default Name  |  idname = type, [option:value,...]  |  defaultValue  |  validator:value;"Replace default message", otherValidator
Message  |  message = textarea,rows:5,cols:40  |  |  required:;"message is required", lengthMax:100;"name is too long"
E-mail  |  email = text,size:30  |  |  required:;"email is required", valid:tx_wform_pi1_userfield_email->valid;"invalid email"
Phone  |  phone = text,size:30  |  |
Captcha  |  captcha = user, userFunc:tx_wform_pi1_userfield_captcha->getField, type:srfreecap, opt1:abc, opt2:xyz  |  |  required:;"captcha is required", valid:tx_wform_pi1_userfield_captcha->valid;"invalid captcha"
		';

		// do not touch unless you know what will happen next
		foreach (t3lib_div::trimExplode("\n", $this->getConfVar('formConf')) as $confRow)	{
			if (preg_match('/^#/', $confRow)  ||  !$confRow)	continue;	//comments, blank lines
			$field = array();
			list($field['label'],  $field['idname_type_setup'],  $field['value'],  $field['validator_']) = t3lib_div::trimExplode('|', $confRow);
			$field = array_reverse($field, 1);	// for better orientation in debug..

			//  I part
			list ($field['idname'], $field['type_setup']) = t3lib_div::trimExplode('=', $field['idname_type_setup'], 0);
			// set [type = settings,opts] - explode limit 2
			list ($field['type'], $field['setup_']) = t3lib_div::trimExplode(',', $field['type_setup'], false, 2);	// rozbijamy na 2 czesci
			//  field additional setup array
			foreach (t3lib_div::trimExplode(',', $field['setup_']) as $_setupRow)	{
				$_t = t3lib_div::trimExplode(':', $_setupRow);
				$field['setup'][$_t[0]] = $_t[1];
			}

			//  0 part
			$field['label'] = $this->pi_getLL('label_field_'.$field['idname'], $field['label']);	// need be after I, to get idname

			//  II part
			$field['value'] = $field['value'];	// just let it be here

			//  III part - validators
			foreach(t3lib_div::trimExplode(',', $field['validator_']) as $validator)	{
				if (!$validator)	continue;

				list($_v['name'], $_v['value'], $_v['message']) = preg_split('/[:;]/', $validator);
				$_val = array(
					'value' => $_v['value'],
					'message' => $this->pi_getLL('msg_field_'.$field['idname'].'_'.$_v['name'], $_v['message'])
				);
				// if value contains -> it means it is userfunc. wform2 required it splitted
				if (strstr($_v['value'], '->'))	{
					list($_userObjName, $_val['methodName']) = t3lib_div::trimExplode('->', $_v['value']);
					$_val['userObj'] = t3lib_div::makeInstance($_userObjName, $confRow, $this);
				}
				$field['validators'][$_v['name']] = $_val;
			}

			// to najlepiej jakos inaczej rozwiazac, moze jako opcja w setup
			$field['DATAkey'] = 1;

			// TODO: settings - dla innych typow sprawdzic co tu moze przyjsc
			switch ($field['type'])	{
				case 'textarea':
					$field['additionalTagParams'] = $field['setup'];
					break;
				case 'user':
					//$field['userConf'] = $field['setup'];
					list($_userObjName, $field['setup']['methodName']) = t3lib_div::trimExplode('->', $field['setup']['userFunc']);
					$field['setup']['userObj'] = t3lib_div::makeInstance($_userObjName);
					$field['DATAkey'] = 0;
					break;
				default:
					$field['additionalTagParams'] = array('size' => $field['setup']['size']);
			}

			$this->fieldsConf[$field['idname']] = $field;
		}
	}



	protected function sendMailToAdmin($vars)   {

		$subject = $this->getConfVar('mail.subject', 'sMail');
		$recipients = t3lib_div::trimExplode(',', $this->getConfVar('mail.recipients', 'sMail') );
		$bcc = t3lib_div::trimExplode(',', $this->getConfVar('mail.bcc', 'sMail') );

		//$headers = (($_t = $this->getConfVar('mail.from')) ? "From: {$_t}" . "\r\n" : '') .
		//	(($_t = $this->getConfVar('mail.reply-to')) ? "Reply-To: {$_t}" . "\r\n" : '') .
		//    'X-Mailer: PHP/' . phpversion();

		$fieldsToRenderInEmail = $this->Form->getFieldNames();

		tx_wform_pi1_hook::email_fieldsToRender($fieldsToRenderInEmail);

		foreach ($fieldsToRenderInEmail as $fieldName)  {
			$idname = $this->Form->Field($fieldName)->getIdname();
			$value = $this->Form->Field($fieldName)->getValue();
			$contentPlain .= "{$idname}: {$value}\n";
		}

		$contentPlain .=
			"\n".date('d.m.Y, H:i', $GLOBALS['EXEC_TIME'])
			."\nip: {$this->getRealIpAddr()}"
			."\nlang: {$this->LLkey}"
			;

		tx_wform_pi1_hook::email_beforeSend($recipients, $subject, $contentPlain, $contentPlain, $headers, $bcc);
		//tx_wform_pi1_hook::email_beforeSend($recipients, $subject, $contentPlain, $contentPlain, $headers, $bcc, $this->Form);


		//$sent = mail($recipients, $subject, $contentPlain, $headers);

		//$mail = t3lib_div::makeInstance('t3lib_mail_Message');
		$mail = new t3lib_mail_Message;
		$sent = $mail
			->setTo( $recipients )
			->setBcc( $bcc )

			//->setFrom( array( $this->getConfVar('mail.from', 'sMail') => $this->getConfVar('mail.fromName', 'sMail') ) )
			->setFrom( $this->getConfVar('mail.from', 'sMail') , $this->getConfVar('mail.fromName', 'sMail') )
		  	->setReplyTo( $this->getConfVar('mail.reply-to', 'sMail') )

		  	->setSubject($subject)
		  	->setBody($contentPlain, 'text/plain')
		  	->send();

		return $sent;

		/*

		// send
		$Mail = new t3lib_htmlmail();
		$Mail->start();
		//$Mail->use8Bit();
		$Mail->useQuotedPrintable();
		$Mail->setHtml($contentHtml);
		$Mail->setPlain($contentPlain);
		$Mail->from_email = $this->getConfVar('mail.from', 'sMail');
		$Mail->replyto_email = $this->getConfVar('mail.reply-to', 'sMail');
		$Mail->subject = $subject;
		$Mail->setRecipient($recipients);
		//$Mail->add_header($contentPlain);
		//$Mail->addPlain($contentPlain);
		//$Mail->add_message($contentPlain);
		//$Mail->extractHtmlInit($contentHtml);
		//$Mail->preview();
		$sent = $Mail->send();

		return $sent;*/
	}
	
	
	public function &getFormObj() {
	    return $this->Form;
	}


	protected function displayFormDebugFrame()	{
		$content = '<div id="wform_debug_'.$this->formId.'" class="wform-debug" style="margin-top: 15px; border: 1px solid red;">WForm debug<br />';
		foreach ($this->debugData as $row)
			$content .= '<p>'.$row.'</p>';
		return $content . '</div>';
	}



	/**
	* Used to easy render plugin from other extension
	* 
	* @param tslib_content $cObj
	* @param string $specifiedInstance
	*/
	public static function renderFormExternal(&$cObj, $specifiedInstance = '')	{
		$conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wform_pi1.'];
		$conf['specifiedInstance'] = $specifiedInstance;
		$Pi1 = t3lib_div::makeInstance(__CLASS__);
		$Pi1->cObj = $cObj;
		return $Pi1->main('', $conf);
	}
}



/**
* borrowed from ext:cal
*/
class tx_wform_pi1_registry	{

	/**
	 * Usage:
	 *   $myfoo = & Registry('MySpace', 'Foo');
	 *   $myfoo = 'something';
	 *
	 *   $mybar = & Registry('MySpace', 'Bar');
	 *   $mybar = new Something();
	 *
	 * @param  string $namespace  A namespace to prevent clashes
	 * @param  string $var        The variable to retrieve.
	 * @return mixed  A reference to the variable. If not set it will be null.
	 */
	function &Registry($namespace, $var) {
		static $instances = array();
		// remove to get case-insensitive namespace
		$namespace = strtolower($namespace);
		$var = strtolower($var);
		return $instances[$namespace][$var];
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/w_form/pi1/class.tx_wform_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/w_form/pi1/class.tx_wform_pi1.php']);
}

?>