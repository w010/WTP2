<?php
/**
 * wolo.pl '.' studio 2016
 *
 * w_tools MVC base 0.5
 */

namespace WTP\WTools\Mvc\Component;


use TYPO3\CMS\Core\Utility\GeneralUtility;
use WTP\WTools\Registry;
use WTP\WTools\Mvc;


//abstract class Tx_WTools_Mvc_Component_Abstract	{

/**
 * Some small (or big) component displayed in various contexts
 *
 * Class Tx_WTools_Mvc_Component_Abstract
 */
abstract class AbstractComponent	{

	/**
     * @var \Tx_WTools_Mvc_Pibase
     */
	protected $pObj;
	/**
	 * @var Mvc\View\DefaultView
	 */
    protected $View;
    /**
     * @var array
     */
    protected $conf = [];

		/**
		 * @var array
		 */
//		public $piVars = [];
		/**
		 * this should be object in future
		 * @var array reference to feUser array
		 */
//		public $feUser;

    /**
     * @var string
     */
    protected $componentName;

	/**
	 * @var array
	 */
	protected $params = [];

    
    public function __construct($componentName, $params = [])	{
		$this->componentName = $componentName;
		$this->View = $params['view'];
        $this->params = &$params;

	    $this->pObj = &Registry::Cell('wtools', 'pi1');
	    $this->conf = &Registry::Cell('wtools', 'conf');
	    //$this->piVars = &Registry::Cell('wtools', 'piVars');    // seems to be updated correctly after manually set pivar in child controller
	    //$this->feUser = &Registry::Cell('wtools', 'feUser');
    }

	public function render()	{
		return 'render abstract component [this method should be overwritten]';
	}

}

?>