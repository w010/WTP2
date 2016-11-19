<?php
/**
 * wolo.pl '.' studio 2015
 *
 * w_tools MVC base 0.3
 */


abstract class Tx_WTools_Mvc_Model_Abstract	{

    /**
     * @var Tx_WTools_Mvc_Pibase
     */
    protected $pObj;


	/**
	 * shorthand for database with code completion
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	public function db()   {
		return $this->pObj->db();
	}
    
    
    protected function init(&$pObj)	{
		$this->pObj = $pObj;
    }



	/**
	 * gets cleared IN statement for select. may be intval or strings
	 *
	 * @param array $dataArray
	 * @param bool $integer
	 *
	 * @return string
	 */
	function prepareInStatement($dataArray, $integer = true)	{
		if (!is_array($dataArray))
			return '("debug: prepareInStatement input not array!")';
		if ($integer) {
			$res = $this->db()->cleanIntArray($dataArray);
		} else {
			$res = array_unique($dataArray);
			// $res = $GLOBALS['TYPO3_DB']->fullQuoteArray($dataArray, '');
		}
		return '('.implode(',', $res).')';
	}



	/**
	 * Replaces table name with short in enableColumns
	 * todo: check if works correct and move to wtools model abstract
	 *
	 * @param string $table table name
	 * @param string $short short from table
	 * @return string
	 */
	function enableColumnsShort($table, $short)  {
		return str_replace($table, $short, $this->pObj->cObj->enableFields($table));
	}





	/**
	 * quick cache - to not repeat some tasks on every item/hook instance
	 *
	 * @param string $id
	 * @param mixed $data
	 * @return bool | null
	 */
	public function setQuickCache($id, $data) {
		if (!$id)	return false;
		$GLOBALS[$this->pObj->extKey.'_cache'][$id] = $data;
	}


	/**
	 * quick cache read
	 *
	 * @param string $id
	 * @return mixed data
	 */
	public function getQuickCache($id) {
		if (!$id)	return null;
		$data = isset($GLOBALS[$this->pObj->extKey.'_cache'][$id])
			? $GLOBALS[$this->pObj->extKey.'_cache'][$id]
			: null;
		return $data;
	}




	/**
     * SINGLETON PATTERN
     */

    static protected $_instance = null;

	/**
	 * @param Tx_WTools_Mvc_Pibase $pObj
	 * @return Tx_WTools_Mvc_Model_Abstract
	 */
	static public function & Instance(Tx_WTools_Mvc_Pibase &$pObj) {
        /*if (is_null(self::$_instance))  {
			self::$_instance = new self($pObj);
        }
        return self::$_instance;*/
        
        /*
        nowa wersja singleton, która poprawnie dziedziczy w nowych wersjach php
        */
        if (!isset(static::$_instance)) {
            static::$_instance = new static($pObj);
        }

        return static::$_instance;
    }

	/**
	 * @param $pObj Tx_WTools_Mvc_Pibase
	 * @throws Exception
	 */
    private function __construct(&$pObj)  {
		if (!$pObj) throw new Exception('no pObj passed to model constructor!');
        // Do normal instance initialisation here
        // Nothing singleton-related should be present
        $this->init($pObj);
    }

    public function __destruct()    {
        // This is just here to remind you that the
        // destructor must be public even in the case
        // of a singleton.
    }

    public function __clone()   {
        trigger_error('Cloning instances of this class is forbidden.', E_USER_ERROR);
    }

    public function __wakeup()  {
        trigger_error('Unserializing instances of this class is forbidden.', E_USER_ERROR);
    }
}

?>