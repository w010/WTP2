<?php
/**
 * wolo.pl '.' studio 2016
 *
 * w_tools MVC base 0.5
 */

namespace WTP\WTools\Mvc\Model;


use WTP\WTools\Registry;
use WTP\WTools\Mvc;


//abstract class Tx_WTools_Mvc_Model_Abstract	{

/**
 * Class AbstractModel
 * @package WTP\WTools\Mvc
 */
abstract class AbstractModel	{

    /**
     * @var \WTP\WTools\Mvc\AbstractPluginMvc
     */
    protected $pObj;


	/**
	 * shorthand for database with code completion
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	public function db()   {
		return $this->pObj->db();
	}
    
    
    protected function init()	{
	    $this->pObj = &Registry::Cell('wtools', 'pi1');
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
	 * Log query to debug
	 *
	 * @param string $methodName
	 */
	protected function debugQuery($methodName) {
		if ($GLOBALS['wtools_showSQL'])
			$this->pObj->addDebug('<b>'.$methodName.'</b> query: '.$this->db()->debug_lastBuiltQuery, 'debug', -1);
	}




	/**
     * SINGLETON PATTERN
     */

    static protected $_instance = null;

	/**
	 * @param bool $forceRefresh
	 * @return AbstractModel
	 */
	static public function & Instance($forceRefresh = false) {

		if ($forceRefresh)  {
			static::$_instance = null;
		}

        /*
            new singleton, which properly inherits in newer php versions
        */
        if (!isset(static::$_instance)) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }

	/**
	 * @throws \Exception
	 */
    private function __construct()  {
		//if (!$pObj) throw new Exception('no pObj passed to model constructor!');
        // Do normal instance initialisation here
        // Nothing singleton-related should be present
        $this->init();
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