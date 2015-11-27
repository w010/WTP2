<?php
/**
 * wolo.pl '.' studio 2015
 * Social plugin
 */


/**
 * Some small (or big) component displayed in various contexts
 *
 * Class Tx_WTools_Mvc_Component_Abstract
 */
abstract class Tx_WTools_Mvc_Component_Abstract	{

	/**
     * @var Tx_WTools_Mvc_Pibase
     */
	protected $pObj;
	/**
	 * @var Tx_WTools_Mvc_View_Default
	 */
    protected $View;
    /**
     * @var array
     */
    protected $conf = [];
    /**
     * @var string
     */
    protected $componentName;

	/**
	 * @var array
	 */
	protected $params = [];

    
    public function __construct(Tx_WTools_Mvc_Pibase &$pObj, $componentName, $params = [])	{
		$this->componentName = $componentName;
		$this->pObj = $pObj;
		$this->View = $params['view'];
        $this->conf = &$pObj->conf;
        $this->params = &$params;
    }

	public function render()	{
		return 'render abstract component [this method should be overwritten]';
	}

}

?>