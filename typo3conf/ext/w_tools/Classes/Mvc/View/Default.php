<?php
/**
 * wolo.pl '.' studio 2015
 *
 * w_tools MVC base 0.3
 */



class Tx_WTools_Mvc_View_Default {

    /**
     * @var Tx_WTools_Mvc_Pibase
     */
    public $pObj;
    /**
     * @var array
     */
    public $conf;

    /**
     * @var string
     */
    protected $viewName;

    /**
     * @var string
     */
    protected $displayMode;

    protected $templateCode;
    protected $markers = array();
    protected $subparts = array();

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
     * @var Tx_WTools_Mvc_Controller_Abstract
     */
    protected $Controller;
    /**
     * @var array
     */
    protected $Viewhelpers = [];

    /**
     * @var array - data to display, set by controller
     */
    protected $data = array();

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
	 * @var string - view container class attribute
	 */
	protected $viewClassName = 'view';


	/**
	 * @param $pObj        Tx_WTools_Mvc_Pibase
	 * @param $Model       Tx_WTools_Mvc_Model_Abstract
	 * @param $Controller  Tx_WTools_Mvc_Controller_Abstract
	 * @param $viewName    string
	 * @param $displayMode string - part of model method name, could be like 'userComments'
	 * @throws Exception
	 * @return \Tx_WTools_Mvc_View_Default
	 */
    public function __construct(&$pObj, Tx_WTools_Mvc_Model_Abstract &$Model, Tx_WTools_Mvc_Controller_Abstract &$Controller, $viewName, $displayMode)  {
        $this->pObj = $pObj;
        $this->conf = $pObj->conf;
		$this->setFeuser( $pObj->getFeuser() );
		$this->setModel( $Model );
        $this->Controller = $Controller;
		$this->viewName = $viewName;
        $this->displayMode = $displayMode;
        $this->Viewhelpers['general'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( 'Tx_WTools_Mvc_Viewhelper_General', $pObj);
        $this->Viewhelpers['links'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( 'Tx_WTools_Mvc_Viewhelper_Links', $pObj);
		// don't use yet viewhelpers in construct, because they can be overwritten in child views

	    // merge displaymode conf into view conf & set
	    //if (is_array($this->conf['view.'][$viewName.'.']) && $this->conf['displayMode.'][$displayMode.'.'])
	      //  $this->conf['view.'][$viewName] = array_replace_recursive($this->conf['view.'][$viewName.'.'], $this->conf['displayMode.'][$displayMode.'.']);
	    // that means, even if it's ajax call, it doesn't mean the current controller is in ajax mode. it could be child controller, like comments in articles ajax load
        $this->setTemplateCode( $this->pObj->cObj->fileResource( $this->getTemplatePath() ),  $this->pObj->conf['mode']=='ajax' && $pObj->piVars['ajaxType'] == 'getResults' && $pObj->piVars['controller'] == $this->Controller->getControllerName() );
        if (!$this->getTemplateCode())      Throw new Exception('Fatal: no template found for View '.$viewName.' - looking in: '.$this->getTemplatePath($viewName));
        //$this->pObj->addDebug('-- template for view '.$viewName.' ready');
        $this->addViewClassName( $this->viewName );
	    if ($this->displayMode != $this->viewName)      $this->addViewClassName( $this->displayMode );
    }

	/**
     * MAIN CONTENT GENERATION
     * @return string
     */
    public function render()   {
        // use controller and model to set data to template
        $this->assign('VIEW_CLASS', $this->viewClassName );
        $this->setMarkers();
        $this->pObj->addDebug('-- markers for view '.$this->viewName.' ready, make content');
        return $this->pObj->cObj->substituteMarkerArrayCached($this->templateCode, $this->markers, $this->subparts);
    }

/** some g/setters */

    public function getViewName()   {
        return $this->viewName;
    }
    public function getDisplayMode()   {
        return $this->displayMode;
    }
    public function addViewClassName($className)	{
		$this->viewClassName .= ' '.$className;
    }

	/**
	 * @param $viewhelperName
	 * @return Tx_WTools_Mvc_Viewhelper_Links
	 * @throws Exception
	 */
	public function Viewhelper($viewhelperName)	{
		if (!is_object($this->Viewhelpers[$viewhelperName]))
			Throw new Exception('no viewhelper named '.$viewhelperName.' - check in View');
		return $this->Viewhelpers[$viewhelperName];
	}

    /**
     * DATA TO DISPLAY IN VIEW. Called in controller.
     * @param array $data
     */
    public function setData($data)  {
        $this->data = $data;
    }



/**********************
/** template methods */

    protected function getTemplatePath()    {
        return $this->conf['templatePath'].'Partials/'.$this->getViewName().'.html';
    }
    public function getTemplateCode()  {
        return $this->templateCode;
    }
    public function setTemplateCode($templateCode, $ajax)  {
        $this->templateCode = $templateCode;
	    //$this->pObj->addDebug('set template for view');
	    // if items loaded with ajax, only show items, without context, load buttons, headers etc.
	    if ($ajax)  {
		    $this->templateCode = '###SUB_SINGLE###' . $this->pObj->cObj->getSubpart($this->templateCode, '###SUB_SINGLE###') . '###SUB_SINGLE###';
		    $this->pObj->addDebug('- template in ajax mode - only SUB_SINGLE subpart');
	    }
        //$GLOBALS['TSFE']->additionalHeaderData['w_medl_pi2'] = '
        //<link rel="stylesheet" type="text/css" href="typo3conf/ext/w_medl/res/css/w_medl.css" />
        //';
    }
    protected function getSubpart($subpartName) {
        return $this->pObj->cObj->getSubpart($this->templateCode, '###'.$subpartName.'###');
    }

    protected function makeTemplate($templateCode, $markers, $subparts) {
        return $this->pObj->cObj->substituteMarkerArrayCached($templateCode, $markers, $subparts);
    }

	/**
	 *
	 * @param string $markerName
	 * @param string $value
	 * @param bool $append append value instead of overwrite
	 */
    public function assign($markerName, $value, $append = false)    {
	    if ($append)
            $this->markers['###'.$markerName.'###'] .= $value;
        else
	        $this->markers['###'.$markerName.'###'] = $value;
    }
    public function assignSubpart($subpartName, $value)    {
        $this->subparts['###'.$subpartName.'###'] = $value;
    }

    protected function setMarkers()    {
        // to rewrite in child class
    }
    
    /**
     *  just to call it yet shorter, old-style just to use this->pi_getLL
     */
    public function pi_getLL($label, $default){
        return $this->Viewhelper('general')->pi_getLL($label, $default);
    }



    
}




?>