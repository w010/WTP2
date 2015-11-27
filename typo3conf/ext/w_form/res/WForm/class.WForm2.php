<?php
/**
*
*	WForm 2
*
*	v2.1.3
*
*	A. Wolo Wolski <wolo.wolski(at)gmail.com>
*
*/



require_once('class.WFormField2.php');
require_once('class.WFormValidator2.php');


class WForm2 {

	//protected $name;
	protected $id;
	protected $method = 'post';
	protected $action;
	protected $rawInput = array();
	protected $input = array();

	protected $defaultFieldnamePrefix = '';
	protected $submitFieldName = 'submit';
	protected $standardMessageOnValidation = 'error in form, check marked fields';
	public $disableFieldValuesRendering = false;

	protected $fields = array();   //protected

	public $debug = DEV;
	public $syslog = array();

	protected $conf;

	/**
	* fieldname-keys array of results of validating form
	* every field/key has keys: 'validators' with validators output result and 'messages' =>
	*
	* @var array
	*/
	protected $validationResults = array();

	/**
	* array of error messages collected during validation of fields
	*
	* @var array
	*/
	protected $errors = array();


	protected $cleaned = false;

	/**
	* render field tags as html5 eg. to perform js validation
	*
	* @var bool
	*/
	public $HTML5 = false;

	/**
	 * @var array Array of fields values, ie. from get/pivars
	 */
	protected $valueArray = [];




	public function __construct(array $rawInput = null, array $conf = null) {
		$this->rawInput = $rawInput;
		//debugster($rawInput);
		$this->conf = $conf;
	}

	public function setValueArray($valueArray)  {
		$this->valueArray = $valueArray;
		return $this;
	}


	/**
	* Gets an html of opening <form> tag
	*
	* @return string
	*/
	public function renderOpenTag() {
		$params = '';
		$tagParams = array(
			'action' => $this->action,
			'method' => $this->method,
			'id' => $this->id,
			'name' => $this->name,
		);
		if (is_array($this->conf['formTagParams']))
			$tagParams = array_merge($tagParams, $this->conf['formTagParams']);
		foreach ($tagParams as $paramName => $paramVal)
			$params .= ' '.$paramName.'="'.$paramVal.'"';
		return '<form'.$params.'>';
	}

	/**
	* This have to be called to check data, validate, set values etc.
	*/
	public function init() {
		$this->cleanAndSetUserInput();

		// TODO: wyjasnic, czemu tu jest wykomentowane
		//if ($this->isSubmitted()) {
			// if submitted, exec validators and fill notices
		//}
	}

	/**
	* Normally this is called from ::init() but can be used manually in some cases
	*/
	public function cleanAndSetUserInput() {
		foreach ($this->fields as $fieldName => &$Field)  {

			// czy to przypadkiem nie jest bez sensu i value / valuedefault zawsze sa takie same...?

			// value default set on addField or setValueArray (from db)
			/** @var WformField2 $Field */
			$valueDefault = $Field->getValue();
			// value from request (pivars passed to in WForm constructor)
			$value = $this->_getInputFieldValue($fieldName);
	/*debugster($value);
	debugster($valueDefault);*/
			// keep from input or set db default if not submitted
			$value = !is_null($value) ? $value : $valueDefault;

			// clean (should it really be here, or just on rendering? what else cleaning can we do here?
			//$value = htmlspecialchars($value);

			// set to Field object and quick access array
			$this->fields[$fieldName]->setValue($value);
			$this->input[$fieldName] = $value;
		}
		$this->cleaned = true;
	}


	/**
	 * Get cleaned input values
	 *
	 * @param bool $short Get indexes as idname instead of name ( 'field', not '[DATA][field]' )
	 * @throws WForm2Exception
	 * @return array
	 */
	public function getInputValues($short = false) {
		if (!$this->cleaned)	{
			throw new WForm2Exception('there are no values in fields, first ::cleanUserInput must be called to set them to fields objects. use ::init().');
//			debugster('there is no values in fields, first ::cleanUserInput must be called to set them to fields objects. use ::init().');
		}
		foreach ($this->fields as $field)	{
			/* @var $field WFormField2 */
			$input[ $short ? $field->getIdname() : $field->getName() ] = $field->getValue();
		}
		return $input;
	}

	/**
	* Get single cleaned field value
	*
	* @param string $fieldName
	* @return string
	*/
	public function getSingleInputValue($fieldName) {
		//$values = $this->getInputValues();
		//return $values[$fieldName];
		return $this->Field($fieldName)->getValue();
	}

	public function disableFieldValuesRendering() {
		$this->disableFieldValuesRendering = true;
	}


	/**
	* Get input value from GET/POST array, like [DATA][something] by field name
	* This is very weird and possibly little buggy method. I haven't any better idea to get the values from posted data
	* But I have tested this lib on many production environments with misc forms and seems to work as expected
	*
	* @param string $fieldName
	*/
	protected function _getInputFieldValue($fieldName)  {
		// to get this value, we need to follow keys of array by field name parts corresponds to this array
		$arrayKeyLevels = preg_split("/[\[\]]/", $fieldName, 0, PREG_SPLIT_NO_EMPTY); // gets an array with eg. 'DATA' and 'something'
		// on begin set next level - whole posted array. in every iterarion we get higher level branch
		$nextDataLevel = $this->rawInput;

		// crawl array
		foreach($arrayKeyLevels as $arrKey => $keyLevel)  {
			// set next level to value in iterated key
			$nextDataLevel = $nextDataLevel[$keyLevel];
		}

		// we expect there is user input string, but if there is an array, something goes wrong...
		//return is_string($nextDataLevel) ? $nextDataLevel : null;

		// but if Select is Multiple, this has to be array... let's test just return it, looks it works good
	  	return $nextDataLevel;
	}




	/**
	* FIELDS
	*
	*/

	/**
	* Add a field to form. Requires at least a 'name' and 'type' set in $conf array
	*
	* @param array $conf
	* @return WForm2
	*/
	public function addField(array $conf = null) {
		$conf['idname'] = (string) $conf['idname'];
		$conf['name'] = (string) $conf['name'];
		if (!$conf['name'] && $conf['idname'])
			$conf['name'] = '[DATA]['.$conf['idname'].']';
		if (!$conf['class']  &&  $this->conf['additionalClassAttribToAllInputs'])   // bootstrap .form-control. only text, textarea, select
			switch ($conf['type']) {
				case 'textarea': case 'text': case 'password':  case 'email': case 'url': case 'number': case 'range': case 'date': case 'month': case 'week':
				case 'time': case 'datetime': case 'datetime-local': case 'search': case 'color': case 'tel': case 'droplist': case 'select': case null:
					$conf['class'] = $this->conf['additionalClassAttribToAllInputs'];
			}
		$field = new WFormField2($conf, $this);
		if ($conf['validators'])
			$field->addValidators($conf['validators']);
		$this->fields[ $conf['name'] ] = &$field;			// WOLO: wazna zmiana, moze wplynac ta referencja
		// set value to db val (or given)
		$this->fields[ $conf['name'] ]->setValue( $conf['value'] ? $conf['value'] : $this->valueArray[ $conf['idname'] ] );
		//$this->fields[ $conf['name'] ]->setValue( $conf['value'] ? $conf['value'] : $this->data[ $conf['idname'] ] );
		//debugster($this->valueArray[ $conf['idname'] ]);
		// tu powinno brac po prostu z data, bo data jest albo z pivars albo z db. tylko data musi byc zmapowane
		// raczej - tu powinno brac z data, ktore ustawiamy wyzej na pivars lub db
		return $this;
	}


	/**
	* Remove field from form.
	*
	* @param string $fieldName
	*/
	public function removeField($fieldName) {
		unset($this->fields[$fieldName]);
	}


	/**
	* Render field html code
	*
	* @param string $fieldName
	* @param string $additionalTagParams
	*/
	public function renderField($fieldName, $additionalTagParams = array()) {
		if (!$Field = $this->_field($fieldName)) {
			throw new WForm2Exception('Field ' . $fieldName . ' not instantiated (use ::addField())');
			return false;
		}
//		debugster($this->_field($fieldName)->getValue());
//		debugster($Field->getValue());
		return $Field->render($additionalTagParams);
	}


	/**
	* Get an object of Form Field
	*
	* @param string $name
	* @return WFormField2
	*/
	public function Field($fieldName) {
		if (!$this->_field($fieldName))
			Throw new WForm2Exception('No field named: \'' . $fieldName . '\'');
		return $this->_field($fieldName);
	}


	/**
	* Method to get field object from array
	*
	* @param string $fieldName
	* @return WFormField2
	*/
	protected function _field($fieldName)   {
		return $this->fields[$fieldName];
	}


	public function getFieldNames() {
		return array_keys($this->fields);
	}


	/*public function getFieldsArray() {
		return $this->fields;
	}*/


	/**
	* VALIDATION
	*
	*/
	public function validate() {
		foreach($this->fields as $name => $Field)	{
			/* @var $Field WFormField2 */
			$validationResults = $Field->validate();
			// save validation results for each field
			//$this->errors[$name] = $result;
			$this->validationResults[$name] = $validationResults;
			// save error messages
			$this->errors = array_merge($this->errors, $validationResults['messages']);
		}
	}

	public function getValidationResults() {
		return $this->validationResults;
	}

	public function getFormErrors() {
		return $this->errors;
	}

	public function isFormValid() {
		$error = false;
		foreach($this->fields as $name => $Field)	{
			/** @var Field WFormField */
			$error = $error  ||  $Field->isError();
		}
		return !$error;
	}

	/**
	* Quick get first/last error message to show in form result
	*
	* @param bool $getFromEnd
	*/
	public function getOneError($getFromEnd = false) {
		if (!$this->isSubmitted())	  return false;
		if ($getFromEnd)	$fieldError = array_pop($this->errors);
		else				$fieldError = array_shift($this->errors);
		//debugster($fieldError);

		// if not found, that means no message configured on addField or something fucked up
		if ($fieldError === '')
			return $this->standardMessageOnValidation;	// TODO: to lang
		return $fieldError;
	}

	public function getOneMessage() {
	    throw new Exception('getOneMessage() is deprecated, use getOneError() instead');
	}

	/**
	* FORM
	*
	*/


	/**
	* @return WForm2
	*/
	public function setDefaultFieldnamePrefix($prefix) {
		$this->defaultFieldnamePrefix = $prefix;
		return $this;
	}

	public function getDefaultFieldnamePrefix() {
		return $this->defaultFieldnamePrefix;
	}


	/*public function setName($name) {
		//$this->name = $name;
	}*/

	/**
	* @return WForm2
	*/
	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	/**
	* @return WForm2
	*/
	public function setMethod($method) {
		$this->method = $method;
		return $this;
	}

	/**
	* @return WForm2
	*/
	public function setAction($action) {
		$this->action = $action;
		return $this;
	}

	/**
	* @return WForm2
	*
	* default submit-detect fieldname is "submit", here you can change it to some other field
	* (eg when button has background and must not have value)
	*/
	public function setSubmitFieldName($fieldName)	{
		$this->submitFieldName = $fieldName;
		return $this;
	}

	/**
	* @return WForm2
	*/
	public function setHTML5($enable) {
		$this->HTML5 = (bool) $enable;
		return $this;
	}

	/**
	* Check if the form was submitted
	*
	*/
	public function isSubmitted() {
		if (!$this->submitFieldName)
			Throw new WForm2Exception('Wrong use of ::is_submitted(), first ::setSubmitFieldName() must be called!');
		$submitValue = $this->_getInputFieldValue($this->submitFieldName);
		if ($submitValue)
			return true;
		return false;
	}


	public function debug() {
		if ($this->debug)   {
			foreach ($this->fields as $Field)   {
				/** @var WFormField */
				$debugInfo = $Field->debug();
				$fields[$debugInfo['name']] = $debugInfo;
			}
			debugster($fields);
			//debugster($this->rawInput);
		}
	}


	/*public function __clone()	{
		foreach ($this->fields as $key => $field)	{
			$this->fields[$key] = clone $field;
		}
	}*/
}



class WForm2Exception extends Exception	{

	protected $debugData = array();

	public function __construct($message, $code = 0, $debugData = array()) {
		$this->debugData = $debugData;
		parent::__construct($message, $code);
	}

	public function __toString() {
		return "{$this->message} \n [{$this->code}] " . __CLASS__ . " in {$this->file} : {$this->line}";
	}

	public function getDebugData() {
		return $this->debugData;
	}
}

?>