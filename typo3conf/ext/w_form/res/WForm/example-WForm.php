<?php

//require_once(t3lib_extMgm::extPath('w_form').'res/WForm/class.WForm2.php');
require_once(PATH_typo3conf.'library/WForm/class.WForm2.php');


class tx_myplugin_pix extends tslib_pibase  {
	/**
	* Form object
	* 
	* @var WForm2
	*/
	protected $Form;

	protected $templateCode = '';

	protected $notice = array();

	protected $debugData = array();

	
	
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		
		// plugin initialize
		$this->_initPlugin();
		
		// form initialize				   
		$this->_initForm();

		// form, userdata, validating...
		$this->_handleForm();

		// output display

		return $this->_renderContent();
	}

	protected function _initPlugin()	{
		//$this->conf['templateFile'] = $this->conf['templateFile'] ? $this->conf['templateFile'] : $defaultTemplateFile;
		$this->templateCode = $this->cObj->fileResource($this->conf['templateFile']);
		
		// captcha object
        if (t3lib_extMgm::isLoaded('sr_freecap') ) {
            require_once(t3lib_extMgm::extPath('sr_freecap').'pi2/class.tx_srfreecap_pi2.php');
            $this->freeCap = t3lib_div::makeInstance('tx_srfreecap_pi2');
        }
	}


	protected function _initForm()  {
		$this->Form = new WForm2($this->piVars);
		$this->Form->setId('my_form')
					->setAction($this->pi_linkTP_keepPIvars_url(array(), 0, 1))
					//->setAction(($_t = $this->pi_getPageLink($GLOBALS['TSFE']->id)) . (strstr($_t,'?')?'&':'?') . 'no_cache=1')
					->setMethod('post') // not necessary, post is default 
					->setDefaultFieldnamePrefix($this->prefixId)
					->setSubmitFieldName('send')

					->addField(array(
						'name' => '[DATA][name]',
						'type' => 'text',
						'marker' => 'NAME',
						'validators' => array(
							'required' => array('message' => $this->pi_getLL('msg.field.name.required', 'name is required')),
							'lengthMax' => array('value' => 100, 'message' => $this->pi_getLL('msg.field.email.lengthMax', 'name is too long')),
					)))
					->addField(array(
						'name' => '[DATA][email]',
						'type' => 'text',
						'marker' => 'EMAIL',
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
					'email' => $vars['[DATA][email]']
					//....
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

		//$this->_processInputMarkers($markers);
		$this->_buildOtherMarkers($markers, $subparts);
		
		// make input markers like ###INPUT_inputmarkername### with input html tag and ###ERROR_inputmarkername### with error classname
		$this->_processInputMarkers($markers);
		
		
		// captcha
		if (is_object($this->freeCap)) {
			$markers = array_merge($markers, $this->freeCap->makeCaptcha());
		}   else	{
			$subparts['###CAPTCHA_INSERT###'] = '';
		}

		
		
		// form result message
		if (($message = array_shift($this->notice))  ||  $message = $this->Form->getOneMessage())   {
			$markers['###RESULT###'] = $message;
		} else  {
			$subparts['###SUB_RESULT###'] = '';
		}
		return $this->cObj->substituteMarkerArrayCached($formTemplate, $markers, $subparts);
	}


	protected function _processInputMarkers(&$markers)  {
		$fieldNames = $this->Form->getFieldNames();
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
		$markers['###VAL_SUBMIT###'] = $this->pi_getLL('val.submit', 'submit');

		// CALENDAR
		include_once(t3lib_extMgm::siteRelPath('date2cal') . '/src/class.jscalendar.php');
		$JSCalendar = JSCalendar::getInstance();
		$JSCalendar->setInputField('date');
		$JSCalendar->config['calConfig']['ifFormat'] = '\'%d.%m.%Y\'';
		$JSCalendar->setDateFormat(true, $dateFormat);
		$markers['###DATE2CAL###'] = $JSCalendar->renderImages();
		if (($jsCode = $JSCalendar->getMainJS()) != '') {
			$GLOBALS['TSFE']->additionalHeaderData['powermail_date2cal'] = $jsCode;
		}

		// LABELS
		// todo: to form ini conf
		$fields = 'name,email,submit';
		$fieldsArr = t3lib_div::trimExplode(',', $fields);
		foreach ($fieldsArr as $fieldName)	{
			$markers['###LABEL_'.strtoupper($fieldName).'###'] = $this->pi_getLL('label.'.$fieldName);
		}
	}
	


	// additional (validator):
	public function _validEmail(string $value)   {
		//return (bool)
	}
}
	
?>