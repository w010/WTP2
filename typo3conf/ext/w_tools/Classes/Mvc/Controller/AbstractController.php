<?php
/**
 * wolo.pl '.' studio 2016
 *
 * w_tools MVC base 0.5
 */

namespace WTP\WTools\Mvc\Controller;


use WTP\WTools\Registry;
use WTP\WTools\Mvc;


//abstract class Tx_WTools_Mvc_Controller_Abstract {

/**
 * Class AbstractController
 * @package WTP\WTools\Mvc
 */
abstract class AbstractController   {

    /**
     * @var Mvc\Model\AbstractModel
     */
    protected $Model;

	/**
	 * @return Mvc\Model\AbstractModel
	 */
	public function getModel() {
		return $this->Model;
	}
	/**
	 * @param Mvc\Model\AbstractModel $Model
	 */
	protected function setModel(&$Model) {
		$this->Model = $Model;
	}


		/**
		 * @var Mvc\View\DefaultView
		 */
		protected $View;
		/**
		 * @return Mvc\View\DefaultView
		 */
		public function getView() {
			return $this->View;
		}
		/**
		 * @param Mvc\View\DefaultView $View
		 */
		protected function setView(&$View) {
			$this->View = $View;
		}


    /**
     * @var \Tx_WTools_Mvc_Pibase
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
     * @param string    $controllerName
     * @param string    $displayMode
     * @param Mvc\Model\AbstractModel $Model
     */
    public function __construct($controllerName, $displayMode, Mvc\Model\AbstractModel &$Model)  {

	        // this way should be used, instead of passing everything in params

            //$this->pObj = $pObj;
	        //$this->conf = $pObj->conf;
	        //$this->piVars = &$pObj->piVars; // must be reference, pivar sometimes can be set to default if not present (seems not needed to be like this anymore)
	        //$this->setFeUser( $pObj->getFeUser() );
        $this->pObj = &Registry::Cell('wtools', 'pi1');
	    $this->conf = &Registry::Cell('wtools', 'conf');
	    $this->piVars = &Registry::Cell('wtools', 'piVars');    // seems to be updated correctly after manually set pivar in child controller
	    $this->feUser = &Registry::Cell('wtools', 'feUser');

	    // namespace: get page name from the string
	    if (strstr($controllerName, '\\'))
		    $controllerName = end(explode('\\', $controllerName));

	    $this->controllerName = $controllerName;
        $this->displayMode = $displayMode;
		$this->setModel( $Model );
    }

    public function render()   {
        return 'error: no render() defined in controller '.get_class($this).'. controller extending WTP\WTools\Mvc\Controller\AbstractController must have own render() method.';
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