<?php
/**
 * wolo.pl '.' studio 2016
 *
 * w_tools MVC base 0.5
 *
 * Viewhelper abstract
 */

namespace WTP\WTools\Mvc\Viewhelper;



class AbstractViewhelper    {

    /**
     * @var \WTP\WTools\AbstractPlugin
     */
    protected $pObj;

    public function __construct()   {
        $this->pObj = &\WTP\WTools\Registry::Cell('wtools', 'pi1');
    }




	// why not cobj/stdwrap?
	public function wrap($string, $wrap)    {
		$wrapParts = explode('|', $wrap);
		return $wrapParts[0] . $string . $wrapParts[1];
	}

    public function pi_getLL($label, $default = '')  {
        return $this->pObj->pi_getLL($label, $default);
    }

	
}


?>