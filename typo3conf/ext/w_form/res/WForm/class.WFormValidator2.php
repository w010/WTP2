<?php

/**
* Validator class for fields of WForm object.
*
* v2.1.3
*
* Some available validators: (for more look below in code)
* required
*   [no config]
* lengthMin
*   value (int)
* lengthMax
*   value (int)
* digits
* 	[no config]
* pattern
* 	regular expression
* [custom_name]
*   userObj (object) - passed object with callable method to perform validation
*   methodName (string) - method name which will be called
*/
class WFormValidator2 {

    /**
    * Name
    *
    * @var string
    */
    protected $name;
    /**
    * Config
    *
    * @var array
    */
    protected $conf = array();

    /**
    * Parent object - Form Field
    *
    * @var WFormField2
    */
    protected $pObj;

    /**
    * Field value to check
    *
    * @var string
    */
    protected $value;

    /**
    * Error message if validation fails
    *
    * @var string
    */
    public $protected = '';



    public function __construct($name, array $conf = null, WFormField2 &$pObj) {
        $this->name = (string) $name;
        $this->conf = $conf;
        $this->pObj = $pObj;
        $this->message = (string) $conf['message'];
    }


    /**
    * checks field validity using selected validator type
    * 
    * @return bool
    */
    public function checkField()    {
        $this->value = $this->pObj->getValue();

		//debugster($this->value);
        //debugster($this->name);
        //debugster($this->conf);

         switch ($this->name)    {
            case 'required':    return (bool) $this->_V_Required();
            case 'maxlength':
            case 'lengthMax':   return (bool) $this->_V_LengthMax();
            case 'minlength':
            case 'lengthMin':   return (bool) $this->_V_LengthMin();
            case 'integer':
            case 'number':
            case 'digits':   	return (bool) $this->_V_Digits();
            case 'min':			return (bool) $this->_V_Min();
            case 'max':			return (bool) $this->_V_Max();
            case 'step':		return (bool) $this->_V_Step();
            case 'pattern':		return (bool) $this->_V_Pattern();

            default:            return (bool) $this->_V_Custom($this->name);
        }
    }

    protected function _V_Required() {
        if (strlen($this->value))	// if user writes "0" then return true
            return true;
        return false;
    }

    protected function _V_LengthMin() {
        if (!$this->_V_Required())   // if no value, don't check it
            return true;
        if (strlen($this->value) >= $this->conf['value'])
            return true;
        return false;
    }

    protected function _V_LengthMax() {
        if (strlen($this->value) <= $this->conf['value'])
            return true;
        return false;
    }

    protected function _V_Digits() {
        if (preg_match('/[^0-9]/', $this->value, $matches))	// does this work correct?
        	 return false;
        return true;
    }

    protected function _V_Min() {
        if (intval($this->value) >= $this->conf['value'])
        	 return true;
        return false;
    }

    protected function _V_Max() {
        if (intval($this->value) <= $this->conf['value'])
        	 return true;
        return false;
    }

    protected function _V_Step() {
    	// todo: step validator
        return true;
    }

    protected function _V_Pattern() {
        if (preg_match('/'.$this->conf['value'].'/', $this->value, $matches))
        	 return true;
        return false;
    }

    protected function _V_Custom($customName)   {
    	//debugster($this->conf);
    	
        // check if userObj and methodName is given
        $obj = $this->conf['userObj'];
        $objClass = get_class($obj);
        $methodName = $this->conf['methodName'];

        if (is_object($obj)  &&  is_callable(array($obj, $methodName)))    {
            // call given method of object passed
            $res = $obj->$methodName($this->value, $this);

            if ($res)
                return true;
            return false;
        }
        $fieldName = $this->pObj->getName();
        Throw new WForm2Exception("Error with \"{$customName}\" custom validator, object passing: {$objClass}->{$methodName} in field {$fieldName}");
    }


	public function getName()	{
		return $this->name;
	}

	/**
	* returns message defined on Form->addField
	* 
	* @return string
	*/
	public function getMessage()	{
		return $this->message;
	}

	public function getConf()	{
		return $this->conf;
	}

}

?>