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



class WFormField2 {

	// main and html tag param
	protected $name;
	protected $idname;

	/**
	* WForm2 object
	*
	* @var WForm2
	*/
	protected $pObj;

	// misc
	protected $type;
	protected $prefix;
	protected $value;
	public $marker = '';	// marker name to substitute

	// html param
	protected $class;
	protected $id;
	protected $additionalTagParams = array();

	// config passed
	protected $conf = array();

	// validators
	protected $validators = array();
	public $error = false;
	public $errorClassMark = true;



	public function __construct($conf, WForm2 &$pObj = null) {
		if (!$conf['name'] && !$conf['idname'])   {
			throw new WForm2Exception('error instantiating Field - name or idname are required in conf', 2277701, array('conf' => $conf));
			//throw new Exception('error instantiating Field - name, type and marker are required in conf', 0);
		}
		$this->pObj = $pObj;
		$this->conf = $conf;
		$this->idname = (string) $conf['idname'];
		$this->name = (string) $conf['name'];
		$this->type = $conf['type'] ? (string) $conf['type'] : 'text';
		$this->class = (string) $conf['class'];
		$this->id = (string) $conf['id'];
		$this->prefix = $conf['prefix'] ? (string) $conf['prefix'] : $this->_getDefaultFieldnamePrefix($conf);
		$this->marker = $conf['marker'] ? (string) $conf['marker'] : strtoupper($conf['idname']);
		$this->value = (string) $conf['value'];
		$this->additionalTagParams = is_array($conf['additionalTagParams']) ? $conf['additionalTagParams'] : array();
	}

	protected function _getDefaultFieldnamePrefix($conf) {
		if ($conf['prefix'])
			return $conf['prefix'];
		return $this->pObj->getDefaultFieldnamePrefix();
	}

	public function getConf() {
		return $this->conf;
	}

	public function getName() {
		return $this->name;
	}
	
	 public function getIdname() {
		return $this->idname;
	}

	public function getValue() {
		return $this->value;
	}

	public function getType() {
		return $this->type;
	}

	public function setValue($value) {
		$this->value = $value;
	}


	/**
	* RENDER CODE
	*
	*/

	/**
	* Method used to get html code of input field
	*
	* @param string $additionalTagParams
	*/
	public function render($additionalTagParams = array()) {
		if ($this->pObj->disableFieldValuesRendering)   $this->value = '';

		$additionalTagParams = array_merge($additionalTagParams, $this->additionalTagParams);
		switch ($this->type) {
			case 'textarea':	return $this->_renderTypeTextarea($additionalTagParams);
			case 'text':
			case 'password':
			// html5
			case 'email':
			case 'url':
			case 'number':
			case 'range':
			case 'date':
			case 'month':
			case 'week':
			case 'time':
			case 'datetime':
			case 'datetime-local':
			case 'search':
			case 'color':
			case 'tel':
								return $this->_renderTypeText($additionalTagParams);
			case 'check':
			case 'checkbox':	return $this->_renderTypeCheck($additionalTagParams);
			case 'droplist':
			case 'select':		return $this->_renderTypeSelect($additionalTagParams);
			case 'radio':		return $this->_renderTypeRadio($additionalTagParams);
			case 'multicheck':	return $this->_renderTypeMulticheck($additionalTagParams);
			case 'user':		return $this->_renderTypeUser($additionalTagParams);
			default:			Throw new WForm2Exception('WFormField: unknown field type "'.$this->type.'", cannot render.');
		}
	}

	// TEXTAREA
	protected function _renderTypeTextarea($additionalTagParams = array())   {
		$name = $this->prefix . $this->name;
		$class = $this->_makeClassParam($this->type, $additionalTagParams);
//		$class = $this->class . ($this->errorClassMark ? ($this->error ? ' formerror invalid' : '') : '');
		$code = '<textarea name="'.$name.'"' .
			($class ? ' class="'.trim($class).'"' : '') .
			($this->id ? ' id="'.trim($this->id).'"' : '') .
			$this->makeHTML5ValidatorParams();
		foreach($additionalTagParams as $parName => $parVal)	{
			$code .= " {$parName}=\"{$parVal}\"";
		}
		$code .= '>' . $this->value . '</textarea>';
		return $code;
	}

	// TEXT
	protected function _renderTypeText($additionalTagParams = array())   {
		$name = $this->prefix . $this->name;
		// is this necessary for something now yet?
		/*// main class
		$classes[] = 'input-text';
		// additional class if given in extra params
		if ($addClass = $additionalTagParams['class'])  {
			$classes[] = $addClass;
			unset ($additionalTagParams['class']);
		}
		// error class
		if ($this->errorClassMark && $this->error)  {
			$classes[] = 'formerror';
		}
		//$class = $standardClass . ' ' . $this->class . ($addClass ? ' '.$addClass : '') . ($this->errorClassMark ? ($this->error ? ' formerror' : '') : '');
		$class = implode(' ', $classes);*/
		$class = $this->_makeClassParam($this->type, $additionalTagParams);

		$code = '<input name="'.$name.'"' .
			' type="'.$this->type.'"' .
			($class ? ' class="'.trim($class).'"' : '') .
			($this->id ? ' id="'.trim($this->id).'"' : '') .
			$this->makeHTML5ValidatorParams();
		foreach($additionalTagParams as $parName => $parVal)	{
			$code .= " {$parName}=\"{$parVal}\"";
		}
		$code .= ' value="' . ($this->type=='password' ? '' : htmlentities( $this->value) ) . '" />';
		return $code;
	}

	// CHECKBOX
	protected function _renderTypeCheck($additionalTagParams = array())   {
		$name = $this->prefix . $this->name;
		$class = $this->_makeClassParam($this->type, $additionalTagParams);

		$code = '<input name="'.$name.'" type="checkbox"' .
			($class ? ' class="'.trim($class).'"' : '') .
			($this->id ? ' id="'.trim($this->id).'"' : '');

		if ($this->value)   {
			$code .= ' checked="checked"';
			unset ($additionalTagParams['checked']);
		}

		foreach($additionalTagParams as $parName => $parVal)	{
			$code .= " {$parName}=\"{$parVal}\"";
		}
		$code .= ' value="' . ($this->value ? $this->value : 'on') . '" />';
		return $code;
	}

	// SELECT
	protected function _renderTypeSelect($additionalTagParams = array())   {
		$name = $this->prefix . $this->name;
		$class = $this->_makeClassParam($this->type, $additionalTagParams);
		$options = is_array($this->conf['options']) ? $this->conf['options'] : array();

		// compatible with Select Multiple options - assume it is always array, to easier find value for "selected"
		$value = is_string($this->value) ? array($this->value) : $this->value;
		//debugster($options);
		// process options if config given
		if (($obj = $this->conf['options_userFunc']['userObj'])  &&  ($methodName = $this->conf['options_userFunc']['methodName']))	{
			if (is_callable(array($obj, $methodName)))		$obj->$methodName($options, $this->name, $value);
			else		$this->pObj->syslog[] = 'error making options for field: ' . $this->name . ' using ' . get_class($obj) . '->' . $methodName;
		}

		// options code
		foreach ($options as $option)   {
			$codeOptions .= '<option value="' . htmlspecialchars($option[0]) . '"' . (in_array($option[0], $value) ? ' selected="selected"' : '') . '>' . htmlspecialchars($option[1]) . "</option>\n";
		}
		$code = '<select name="'.$name.'"' .
			($class ? ' class="'.trim($class).'"' : '') .
			($this->id ? ' id="'.trim($this->id).'"' : '');
		foreach($additionalTagParams as $parName => $parVal)	{
			$code .= " {$parName}=\"{$parVal}\"";
		}

		$code .= '>' . $codeOptions . '</select>';
		return $code;
	}

	// RADIO
	protected function _renderTypeRadio($additionalTagParams = array())   {
		$name = $this->prefix . $this->name;
		$class = $this->_makeClassParam($this->type, $additionalTagParams);
		$options = is_array($this->conf['options']) ? $this->conf['options'] : array();

		// process options if config given
		if (($obj = $this->conf['options_userFunc']['userObj'])  &&  ($methodName = $this->conf['options_userFunc']['methodName'])  &&  is_callable(array($obj, $methodName)))	{
			$obj->$methodName($options);
		}

		$additionalTagParamsCode = "";
		foreach($additionalTagParams as $parName => $parVal)	{
			$additionalTagParamsCode .= " {$parName}=\"{$parVal}\"";
		}
		// TODO: opcja dla ustalenia czy label przed inputem czy za
		// podawanie szablonu/wzoru
		$optionIndex = 0;
		foreach ($options as $option)   {
			$optionIndex++;
			$code .= '<label class="i'.$optionIndex.'"><input type="radio" name="'.$name.'"'.
				($class ? ' class="'.trim($class).'"' : '') .
				($this->id ? ' id="'.trim($this->id).$optionIndex.'"' : '') .
				' value="' . htmlspecialchars($option[0]) . '"' .
				($option[0] == $this->value ? ' checked="checked"' : '') . " ".$additionalTagParamsCode."/> " .
				htmlspecialchars($option[1]) . "</label>\n";
		}

		return $code;
	}

	// MULTICHECK
	protected function _renderTypeMulticheck($additionalTagParams = array())   {
		$name = $this->prefix . $this->name;
		$class = $this->_makeClassParam($this->type, $additionalTagParams);
		$options = is_array($this->conf['options']) ? $this->conf['options'] : array();

		$value = is_array($this->value) ? $this->value : array($this->value);

		// process options if config given
		if (($obj = $this->conf['options_userFunc']['userObj'])  &&  ($methodName = $this->conf['options_userFunc']['methodName'])  &&  is_callable(array($obj, $methodName)))	{
			$obj->$methodName($options);
		}

		$additionalTagParamsCode = "";
		foreach($additionalTagParams as $parName => $parVal)	{
			$additionalTagParamsCode .= " {$parName}=\"{$parVal}\"";
		}
		// TODO: opcja dla ustalenia czy label przed inputem czy za
		$optionIndex = 0;
		foreach ($options as $option)   {
			$optionIndex++;
			$code .= '<label class="i'.$optionIndex.' checkbox-inline"><input type="checkbox" name="'.$name.'[]"'.
				($class ? ' class="'.trim($class).'"' : '') .
				($this->id ? ' id="'.trim($this->id).$optionIndex.'"' : '') .
				' value="' . htmlspecialchars($option[0]) . '"' .
				(is_array($this->value) && in_array($option[0], $this->value) ? ' checked="checked"' : '') . " ".$additionalTagParamsCode."/> " .
				htmlspecialchars($option[1]) . "</label>\n";
		}

		return $code;
	}

	// USER
	protected function _renderTypeUser($additionalTagParams = array())	{
		//debugster($this->conf['field_userFunc']['userObj']);
		//debugster($this->conf['field_userFunc']['methodName']);
		//debugster($this->conf);
		
		$Obj = $this->conf['field_userFunc']['userObj'] ? $this->conf['field_userFunc']['userObj'] : $this->conf['setup']['field_userFunc']['userObj'];
		$methodName = $this->conf['field_userFunc']['methodName'] ? $this->conf['field_userFunc']['methodName'] : $this->conf['setup']['field_userFunc']['methodName'];

		try {	
			if (!is_object($Obj))				throw new WForm2Exception('no object passed.');
			if (!method_exists($Obj, $methodName))	throw new WForm2Exception('Object\'s method not callable.');
			// CALL - get content
			$content = $Obj->$methodName($this->conf, $this);
		} catch	(Exception $e)	{
			return 'error rendering field <b>'.$this->name.'</b> <i>('.get_class($Obj).'->'.$methodName.')</i><br />
			Exception: '.$e->getMessage();
		}
		return $content;
	}


	protected function _makeClassParam($type, &$additionalTagParams) {
		// main class
		$classes[] = $this->class;
		$classes[] = 'input-'.$type;
		// additional class if given in extra params
		if ($addClass = $additionalTagParams['class'])  {
			$classes[] = $addClass;
			unset ($additionalTagParams['class']);
		}
		// error class
		if ($this->errorClassMark && $this->error)  {
			$classes[] = 'formerror invalid';
		}

		return implode(' ', $classes);
	}


	// HTML 5
	protected function makeHTML5ValidatorParams()	{
		if (!$this->pObj->HTML5)	return;	// should be deprecated soon... now for compatibility

		foreach ($this->validators  as  $ValidatorName => $Validator)	{
			/** @var WFormValidator2 */
			switch ($ValidatorName)	{
				case 'required':	$content .= ' required="required"';		break;
				case 'maxlength':	$ValidatorName = 'size';	// todo: finish this
				case 'min':
				case 'max':
				case 'step':
					$Vconf = $Validator->getConf();
					$content .= ' '.$ValidatorName.'="'.$Vconf['value'].'"';
					break;
			}
		}

		return $content;
	}


	/**
	* Check if this field has userinput error in value
	*
	* @return bool
	*/
	public function isError()   {
		return (bool) $this->error;
	}


	/**
	* VALIDATION
	*
	*/

	/**
	* Add validators to check field on submit
	*
	* @param array $conf
	*/
	public function addValidators(array $conf = null) {
		foreach($conf as $key => $val)  {
			switch (is_array($val))   {
				case true:  $this->_addValidator($key, $val); break;
				default:	$this->_addValidator($val); break;	  // if only validator name is given as value, no options are passed (only if even error message is not set)
			}
		}
	}

	private function _addValidator($name, array $options = null) {
		$name = (string) $name;
		$this->validators[$name] = new WFormValidator2($name, $options, $this);
	}

	public function isRequired() {
		return (bool) $this->validators['required'];
	}


	/**
	* Check field using configured validators attached to them
	* Returns object/array:
	*   'validators' - array of validators results
	*   'messages' - collected array of messages when validators outputs such
	*
	* @return array of results for every validator
	*/
	public function validate() {
		$result = array('validators' => array(), 'messages' => array());

		foreach ($this->validators as $name => $validator)  {
			/** @var WFormValidator2 */
			try {
				$result['validators'][$name]['result'] = $validator->checkField();
				if (!$this->error  &&  !$result['validators'][$name]['result']) {
					$result['messages'][] = $validator->getMessage() ? $validator->getMessage() : '';
					$result['validators'][$name]['message'] = $validator->getMessage() ? $validator->getMessage() : '';
					$this->error = true;
				}
			} catch (Exception $e)  {
				if (function_exists('debugster'))
					debugster($e->getMessage());
				else
					var_dump($e->getMessage());
			}
		}

		return $result;
	}

	public function debug() {
		if ($this->pObj->debug) {
			return array(
				'name' => $this->name,
				'value' => $this->value,
				//'conf' => $this->conf,
				'type' => $this->type,
				'class' => $this->class,
				'prefix' => $this->prefix,
				'marker' => $this->marker,
			);
		}
	}

}

?>