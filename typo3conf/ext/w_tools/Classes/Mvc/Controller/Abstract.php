<?php
/**
 * wolo.pl '.' studio 2016
 *
 * w_tools MVC base 0.5
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
		 * @var Tx_WTools_Mvc_View_Default
		 */
		protected $View;
		/**
		 * @return Tx_WTools_Mvc_View_Default
		 */
		public function getView() {
			return $this->View;
		}
		/**
		 * @param Tx_WTools_Mvc_View_Default $View
		 */
		protected function setView(&$View) {
			$this->View = $View;
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
	 * @var array reference to feUser array
	 */
	public $feUser;

		/**
		 * @return array
		 */
		public function getFeUser() {
			return $this->feUser;
		}
		/**
		 * @param array $feUser
		 */
		protected  function setFeUser(&$feUser) {
			$this->feUser = $feUser;
		}


    /**
     * @param deprecated    Tx_WTools_Mvc_Pibase    $pObj
     * @param Tx_WTools_Mvc_Model_Abstract  $Model
     * @param string    $controllerName
     * @param string    $displayMode
     */
    public function __construct(Tx_WTools_Mvc_Pibase &$pObj, Tx_WTools_Mvc_Model_Abstract &$Model, $controllerName, $displayMode)  {

	        // this way should be used, instead of passing everything in params

            //$this->pObj = $pObj;
	        //$this->conf = $pObj->conf;
	        //$this->piVars = &$pObj->piVars; // must be reference, pivar sometimes can be set to default if not present (seems not needed to be like this anymore)
	        //$this->setFeUser( $pObj->getFeUser() );
        $this->pObj = &\WTP\WTools\Registry::Cell('wtools', 'pi1');
	    $this->conf = &\WTP\WTools\Registry::Cell('wtools', 'conf');
	    $this->piVars = &\WTP\WTools\Registry::Cell('wtools', 'piVars');    // seems to be updated correctly after manually set pivar in child controller
	    $this->feUser = &\WTP\WTools\Registry::Cell('wtools', 'feUser');

	    $this->controllerName = $controllerName;
        $this->displayMode = $displayMode;
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