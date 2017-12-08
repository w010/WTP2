<?php
/**
 * wolo.pl '.' studio 2016
 *
 * w_tools MVC base 0.5
 */

namespace WTP\WTools\Mvc\Page;


use TYPO3\CMS\Core\Utility\GeneralUtility;
use WTP\WTools\Registry;
use WTP\WTools\Mvc;





/**
 * Class AbstractPage
 *
 * PAGE - something like controller, but not quite - rather collection of controllers, that runs proper controllers
 * rendering parts of such page. Has own template, in which is to be embedded controllers-views.
 *
 * PAGE - z zalozenia cos jak kontroler, ale nie do konca - bardziej zbior kontrolerow, uruchamiajacy dopiero
 * odpowiednie kontrolery renderujace elementy takiej strony. posiada wlasny template, w ktorym osadza sie kontrolery-widoki
 *
 * @package WTP\WTools\Mvc
 */
abstract class AbstractPage {

    protected $templateCode;
    protected $markers = [];
    protected $subparts = [];

    /**
     * @var Mvc\AbstractPluginMvc
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
     * @param $pageName string
     * @throws \Exception
     * @return AbstractPage
     */
    public function __construct($pageName)  {
	        // this way should be used, instead of passing everything in params

		    //$this->pObj = $pObj;
		    //$this->conf = $pObj->conf;
		    $this->pObj = &Registry::Cell('wtools', 'pi1');
		    $this->conf = &Registry::Cell('wtools', 'conf');

	    // namespace: get page name from the string
	    if (strstr($pageName, '\\'))
		    $pageName = end(explode('\\', $pageName));

		// are made in children
		//$this->Viewhelpers['general'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( 'Tx_WTools_Mvc_Viewhelper_General', $pObj);
		//$this->Viewhelpers['links'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( 'Tx_WTools_MVC_Viewhelper_Links', $pObj);

        $this->setPageName($pageName);
	    $this->setTemplateCode( $this->pObj->cObj->fileResource( $this->getTemplatePath() ) );
        if (!$this->getTemplateCode())
            Throw new \Exception('Fatal: no template found for PAGE '.$pageName.' - looking in: '.$this->getTemplatePath());
        // set default shared markers
        $this->setMarkers();
        $this->setSubparts();
    }

    /**
     * MAIN CONTENT GENERATION
     * @throws \Exception
     * @return string
     */
    public function render()   {
        Throw new \Exception('Fatal: no render() defined in PAGE object '.$this->getPageName());
    }


    /**
     * Get viewhelper - are sometimes useful in pages
     * @param $viewhelperName
     * @return mixed
     * @throws \Exception
     */
	public function Viewhelper($viewhelperName)	{
		if (!is_object($this->Viewhelpers[$viewhelperName]))
			Throw new \Exception('no viewhelper named '.$viewhelperName.' - check in Page');
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
	    Throw new \Exception('Fatal: no templatePath configured for plugin '.$this->pObj->prefixId.'. Check ts setup');
    }
	public function getTemplateCode()  {
        return $this->templateCode;
    }
    public function setTemplateCode($templateCode)  {
        $this->templateCode = $templateCode;
        $this->pObj->addDebug('set template for page <b>'.$this->getPageName().'</b>');
        //$GLOBALS['TSFE']->additionalHeaderData['w_tools_pi2'] = '
        //<link rel="stylesheet" type="text/css" href="typo3conf/ext/w_tools_pi2/res/css/style.css">
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
		// why not 'DEV'? - because it's easier to find
	    $this->assign('DEV_BOOL', (
	            (defined('DEV') && DEV)
			    ||  GeneralUtility::getApplicationContext() == 'Development'
				||  $this->conf['debug']
	        ) ? 'true' : 'false');

		$this->assign('EXT_PREFIX', $this->pObj->prefixId);
		//$this->assign('BASE_URL', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . '/');
	    //$this->assign('BASE_URL', ($_SERVER['HTTPS'] ? 'https' : 'http') . '://' . $_SERVER['SERVER_NAME'] . '/');
	    // todo: make this configurable
	    $this->assign('BASE_URL', $GLOBALS['TSFE']->tmpl->setup['config.']['baseURL']
		    . (substr($GLOBALS['TSFE']->tmpl->setup['config.']['baseURL'], -1) != '/' ? '/' : '') );
    }

    protected function setSubparts()    {

    }


    /**
     * build page using template, markers and subparts
     * use on finish of render() method
     */
    protected function make() {
        return $this->pObj->cObj->substituteMarkerArrayCached($this->templateCode, $this->markers, $this->subparts);
    }
}



?>