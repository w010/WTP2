<?php
/**
 * wolo.pl '.' studio 2015
 *
 * w_tools MVC base 0.4
 */



abstract class Tx_WTools_Mvc_Controller_Abstract {

    /**
     * @var Tx_WTools_Mvc_Model_Abstract
     */
    protected $Model;

		/**
		 * @return Tx_WTools_Mvc_Model_Abstract
		 */
		public function getModel() {
			return $this->Model;
		}
		/**
		 * @param Tx_WTools_Mvc_Model_Abstract $Model
		 */
		protected function setModel(&$Model) {
			$this->Model = $Model;
		}

    /**
     * @var Tx_WTools_Mvc_Pibase
     */
    protected $pObj;
    /**
     * @var array
     */
    protected $conf = [];
	/**
     * @var array
     */
    public $piVars = [];
    /**
     * @var string
     */
    protected $controllerName;
    /**
     * @var string
     */
    protected $displayMode;

	/**
	 * this should be object in future
	 * @var array reference to feuser array
	 */
	public $feuser;

		/**
		 * @return array
		 */
		public function getFeuser() {
			return $this->feuser;
		}
		/**
		 * @param array $feuser
		 */
		protected  function setFeuser(&$feuser) {
			$this->feuser = $feuser;
		}


    /**
     * @param $pObj Tx_WTools_Mvc_Pibase
     * @param $Model Tx_WTools_Mvc_Model_Abstract
	 * @param $controllerName string
     * @param $displayMode string
     */
    public function __construct(Tx_WTools_Mvc_Pibase &$pObj, Tx_WTools_Mvc_Model_Abstract &$Model, $controllerName, $displayMode)  {
        $this->pObj = $pObj;
        $this->conf = $pObj->conf;
        $this->piVars = $pObj->piVars;
        $this->controllerName = $controllerName;
        $this->displayMode = $displayMode;
		$this->setFeuser( $pObj->getFeuser() );
		$this->setModel( $Model );
    }

    public function render()   {
        return 'error: no render() defined in controller '.get_class($this).'. controller extending Tx_WTools_Mvc_Controller_Abstract must have own render() method.';
    }


    /**
     * @return string
     */
    public function getControllerName() {
        return $this->controllerName;
    }
    /**
     * @return string
     */
    public function getDisplayMode()    {
        return $this->displayMode;
    }


    /**
     * @return \tx_wtools_pibase
     */
    public function getPObj()    {
        return $this->pObj;
    }

		/**
	     * podmiana onclick przycisku load, zeby po zaladowaniu mogl odpytac o kolejne
		 * to na pewno nie jest najlepszy sposob, w jaki mozna to bylo zrobic.
	     * @return string
	     */
	    public function swapLoadOnclick()  {
	        // to rewrite in child classes
	    }
}



?>