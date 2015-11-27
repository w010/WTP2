<?php
/**
 * wolo.pl '.' studio 2015
 *
 * w_tools MVC base 0.2
 */


/**
 * Class tx_wtools_mvc_page_abstract
 *
 * PAGE - z zalozenia cos jak kontroler, ale nie do konca - bardziej zbior kontrolerow, uruchamiajacy dopiero
 * odpowiednie kontrolery renderujace elementy takiej strony. posiada wlasny template, w ktorym osadza sie kontrolery-widoki
 */
//abstract class tx_wtools_mvc_page_abstract {
abstract class Tx_WTools_Mvc_Page_Abstract {

    protected $templateCode;
    protected $markers = array();
    protected $subparts = array();

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
    protected $pageName;

	/**
	 * @var array
	 */
	protected $Viewhelpers = [];




	/**
     * @param $pObj tx_wtools_pibase
     * @param $pageName string
     * @throws Exception
     * @return Tx_WTools_Mvc_Page_Abstract
     */
    public function __construct(&$pObj, $pageName)  {
        $this->pObj = $pObj;
        $this->conf = $pObj->conf;

		// are made in children
		//$this->Viewhelpers['general'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( 'Tx_WTools_Mvc_Viewhelper_General', $pObj);
		//$this->Viewhelpers['links'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( 'Tx_WTools_MVC_Viewhelper_Links', $pObj);

        $this->setPageName($pageName);
	    $this->setTemplateCode( $this->pObj->cObj->fileResource( $this->getTemplatePath() ) );
        if (!$this->getTemplateCode())
            Throw new Exception('Fatal: no template found for PAGE '.$pageName.' - looking in: '.$this->getTemplatePath());
        // set default shared markers
        $this->setMarkers();
        $this->setSubparts();
    }

    /**
     * MAIN CONTENT GENERATION
     * @throws Exception
     * @return string
     */
    public function render()   {
        Throw new Exception('Fatal: no render() defined in PAGE object '.$this->getPageName());
    }


	/**
	 * Get viewhelper - are sometimes useful in pages
	 * @param $viewhelperName
	 * @return mixed
	 */
	public function Viewhelper($viewhelperName)	{
		if (!is_object($this->Viewhelpers[$viewhelperName]))
			Throw new Exception('no viewhelper named '.$viewhelperName.' - check in Page');
		return $this->Viewhelpers[$viewhelperName];
	}

/**
 * *********************
 */

    public function handleRequests()    {

    }

    /**
     * @param $pageName string PAGE name
     */
    protected function setPageName($pageName)   {
        if (!$this->pageName)
            $this->pageName = $pageName;
    }

    /**
     * @return string
     */
    public function getPageName()   {
        return $this->pageName;
    }



/**********************
/** template methods */

    protected function getTemplatePath()    {
        if ($this->conf['templatePath'])    return $this->conf['templatePath'].'Pages/'.strtoupper($this->getPageName()).'.html';
	    Throw new Exception('Fatal: no templatePath configured for plugin '.$this->pObj->prefixId.'. Check ts setup');
    }
	public function getTemplateCode()  {
        return $this->templateCode;
    }
    public function setTemplateCode($templateCode)  {
        $this->templateCode = $templateCode;
        $this->pObj->addDebug('set template for page '.$this->getPageName());
        //$GLOBALS['TSFE']->additionalHeaderData['w_tools_pi2'] = '
        //<link rel="stylesheet" type="text/css" href="typo3conf/ext/w_tools_pi2/res/css/style.css" />
        //';
    }

    public function assign($markerName, $value)    {
        $this->markers['###'.$markerName.'###'] = $value;
    }

    public function assignSubpart($subpartName, $value)    {
        $this->subparts['###'.$subpartName.'###'] = $value;
    }

    /**
     * called always
     */
    protected function setMarkers()    {
		// czemu nie DEV?
		$this->assign('DEV_BOOL', DEV);
    }

    protected function setSubparts()    {

    }

}



?>