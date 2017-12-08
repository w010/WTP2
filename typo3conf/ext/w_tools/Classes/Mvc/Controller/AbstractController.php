<?php
/**
 * wolo.pl '.' studio 2016
 *
 * w_tools MVC base 0.5
 */

namespace WTP\WTools\Mvc\Controller;


use WTP\WTools\Registry;
use WTP\WTools\Mvc;



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
     * @var Mvc\AbstractPluginMvc
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
	 * @var bool
	 */
	protected $isAjax;

		/**
		 * @return bool
		 */
		public function isAjax() {
			return $this->isAjax;
		}

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
     * @param Mvc\Model\AbstractModel|null $Model   - optional, may be given or created in controller
     */
    public function __construct($controllerName, $displayMode, $Model)  {

	        // this way should be used, instead of passing everything in params

        $this->pObj = &Registry::Cell('wtools', 'pi1');
	    $this->conf = &Registry::Cell('wtools', 'conf');
	    $this->piVars = &Registry::Cell('wtools', 'piVars');    // seems to be updated correctly after manually set pivar in child controller
	    $this->feUser = &Registry::Cell('wtools', 'feUser');

	    // namespace: get page name from the string
	    if (strstr($controllerName, '\\'))
		    $controllerName = end(explode('\\', $controllerName));

	    $this->controllerName = $controllerName;
        $this->displayMode = $displayMode ? $displayMode : 'default';
	    // that means, even if it's ajax call, it doesn't mean the current controller is in ajax mode. it could be child controller, like comments in articles ajax load
	    $this->isAjax = $this->pObj->conf['mode'] == 'ajax'  &&  $this->pObj->piVars['controller'] == $this->getControllerName();
		$this->setModel( $Model );
	    $this->init();
    }

	/**
	 * @return void
	 */
	public function init() {

	}


	/**
	 * MAIN CONTENT
	 *
	 * @return string
	 */
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
     * @return Mvc\AbstractPluginMvc
     */
    public function getPObj()    {
        return $this->pObj;
    }

		/**
		 * Swap load button onclick after loading items, that it may load another ie. with increased or decreased offset
		 * This probably isn't the best way to achieve that, but I still don't have better idea
		 *
	     * @return string
	     */
	    public function swapLoadOnclick()  {
	        // to rewrite in child classes
	    }
}



?>