<?php
/**
 * wolo.pl '.' studio 2016
 *
 * w_tools MVC base 0.5
 */

namespace WTP\WTools\Mvc\View;


use TYPO3\CMS\Core\Utility\GeneralUtility;
use WTP\WTools\Registry;
use WTP\WTools\Mvc;


//class Tx_WTools_Mvc_View_Default {

/**
 * Class DefaultView
 * @package WTP\WTools\Mvc
 */
class DefaultView {

	/**
	 * @var \Tx_WTools_Mvc_Pibase
	 */
	public $pObj;

	/**
	 * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 */
	public $cObj;

	/**
	 * @var array
	 */
	public $conf;

	/**
	 * conf of selected view
	 * @var array
	 */
	public $lConf;

	/**
	 * @var string
	 */
	protected $viewName;

	/**
	 * @var string
	 */
	protected $displayMode;

	protected $templateCode;
	protected $markers = [];
	protected $subparts = [];

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
	 * @var Mvc\Controller\AbstractController
	 */
	protected $Controller;
	/**
	 * @var array
	 */
	protected $Viewhelpers = [];

		/** @var Mvc\Viewhelper\Links */
		public $ViewhelperLinks;

		/** @var Mvc\Viewhelper\General */
		public $ViewhelperGeneral;

	/**
	 * @var array - data to display, set by controller
	 */
	protected $data = [];

	/**
	 * this should be object in future
	 * @var array reference to feuser array
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
	 * @var string - view container class attribute
	 */
	protected $viewClassName = 'view';


	/**
	 * @param $viewName	string
	 * @param $displayMode string - part of model method name, could be like 'userComments'
	 * @param $Model	   Mvc\Model\AbstractModel
	 * @param $Controller  Mvc\Controller\AbstractController
	 * @throws \Exception
	 * @return DefaultView
	 */
	public function __construct($viewName, $displayMode, Mvc\Model\AbstractModel &$Model, Mvc\Controller\AbstractController &$Controller)  {
		$this->pObj = &Registry::Cell('wtools', 'pi1');
		$this->cObj = $this->pObj->cObj;
		$this->conf = $this->pObj->conf;
		$this->setFeUser( $this->pObj->getFeUser() );
		$this->setModel( $Model );
		$this->Controller = $Controller;

		// namespace: get page name from the string
		if (strstr($viewName, '\\'))
			$viewName = end(explode('\\', $viewName));

		$this->viewName = $viewName;
		$this->displayMode = $displayMode;
		// todo later: these should be made using Tx_WTools_Mvc::getViewhelper, but for now for compatibility it only allow to create lowercase-named objects
		// it should be reworked in future when migrating w_social and related to camelcase
		$this->ViewhelperGeneral = $this->Viewhelpers['general'] = GeneralUtility::makeInstance( 'WTP\WTools\Mvc\Viewhelper\General');
		$this->ViewhelperLinks = $this->Viewhelpers['links'] = GeneralUtility::makeInstance( 'WTP\WTools\Mvc\Viewhelper\Links');
		// note: don't use yet viewhelpers in construct, because they can be overwritten in child views

		// conf of current view
		$this->lConf = &$this->pObj->conf['view.'][$viewName.'.'];  // also see below - should be merged with displaymode, and also todo: make _default and first merge with it

		// merge displaymode conf into view conf & set
		//if (is_array($this->conf['view.'][$viewName.'.']) && $this->conf['displayMode.'][$displayMode.'.'])
		  //  $this->conf['view.'][$viewName] = array_replace_recursive($this->conf['view.'][$viewName.'.'], $this->conf['displayMode.'][$displayMode.'.']);
		// that means, even if it's ajax call, it doesn't mean the current controller is in ajax mode. it could be child controller, like comments in articles ajax load
		$this->setTemplateCode( $this->pObj->cObj->fileResource( $this->getTemplatePath() ),  $this->pObj->conf['mode']=='ajax' && $this->pObj->piVars['ajaxType'] == 'getResults' && $this->pObj->piVars['controller'] == $this->Controller->getControllerName() );
		if (!$this->getTemplateCode())	  Throw new \Exception('Fatal: no template found for View '.$viewName.' - looking in: '.$this->getTemplatePath());
		//$this->pObj->addDebug('-- template for view '.$viewName.' ready');
		$this->addViewClassName( $this->viewName );
		if ($this->displayMode != $this->viewName)	  $this->addViewClassName( $this->displayMode );
	}

	/**
	 * MAIN CONTENT GENERATION
	 * @return string
	 */
	public function render()   {
		// is this ok to be here? sometimes there's no data
		/*if (!$this->data)	{
			$this->pObj->addDebug('no data set when rendering view '.$this->getViewName(), 'debug', 2);
			return 'view render error, check debug';
		}*/
		// use controller and model to set data to template
		$this->assign('VIEW_CLASS', $this->viewClassName );
		$this->assign('EXT_PREFIX', $this->pObj->prefixId );


		// universal additional labels to be read from ll. can be used in single item view or in sub loop
		// call from own view
		//$this->makeUniversalLabelsFromLocallang($fieldNames);

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
	 * @return Mvc\Viewhelper\AbstractViewhelper, \WTP\WTools\Mvc\Viewhelper\Links, \WTP\WTools\Mvc\Viewhelper\General
	 * @throws \Exception
	 */
	public function Viewhelper($viewhelperName)	{
		if (!is_object($this->Viewhelpers[$viewhelperName]))
			Throw new \Exception('no viewhelper named '.$viewhelperName.' - check in View');
		return $this->Viewhelpers[$viewhelperName];
	}

	/**
	 * DATA TO DISPLAY IN VIEW. Called in controller.
	 * @param array $data
	 */
	public function setData($data)  {
		if (is_array($data))
			$this->data = $data;
		else
			// possible reason is database select failure and false is returned from query exec
			$this->pObj->addDebug('data set to view is not an array as expected! please check why. (db query error?)', 'debug', 2);
	}



/**********************
/** template methods */

	protected function getTemplatePath()	{
		return $this->conf['templatePath'].'Partials/'.$this->getViewName().'.html';
	}
	public function getTemplateCode()  {
		return $this->templateCode;
	}
	public function setTemplateCode($templateCode, $ajax)  {
		$this->templateCode = $templateCode;
		//$this->pObj->addDebug('set template for view');
		// if items loaded with ajax, only show items, without context, load buttons, headers etc. (getResults)
		if ($ajax)  {
			$this->templateCode = '###SUB_SINGLE###' . $this->pObj->cObj->getSubpart($this->templateCode, '###SUB_SINGLE###') . '###SUB_SINGLE###';
			$this->pObj->addDebug('- template in ajax mode - only SUB_SINGLE subpart');
		}
			//$GLOBALS['TSFE']->additionalHeaderData['w_tools'] = '
			//<link rel="stylesheet" type="text/css" href="typo3conf/ext/w_tools/res/css/w_tools.css" />
			//';
	}
	protected function getSubpart($subpartName) {
		return $this->pObj->cObj->getSubpart($this->templateCode, '###'.$subpartName.'###');
	}

	protected function makeTemplate($templateCode, $markers, $subparts = []) {
		return $this->pObj->cObj->substituteMarkerArrayCached($templateCode, $markers, $subparts);
	}

	/**
	 *
	 * @param string $markerName
	 * @param string $value
	 * @param bool $append append value instead of overwrite
	 */
	public function assign($markerName, $value, $append = false)	{
		if ($append)
			$this->markers['###'.$markerName.'###'] .= $value;
		else
			$this->markers['###'.$markerName.'###'] = $value;
	}
	public function assignSubpart($subpartName, $value)	{
		$this->subparts['###'.$subpartName.'###'] = $value;
	}

	protected function setMarkers()	{
		// to rewrite in child class
	}


	/**
	 * LABELS - universal additional labels to be read from ll
	 * can be used for view main part or for subparts item iteration
	 *
	 * @param string $fields - commalist of field names
	 * @param bool $returnArray - return array with markers instead of assigning them - to use in sub loops
	 */
	protected function makeUniversalLabelsFromLocallang($fields, &$returnArray = null)   {
		$fieldsArr = GeneralUtility::trimExplode(',', $fields, true);
		foreach ($fieldsArr as $fieldName)	{
			// (w starszych wersjach bylo label.xxx, ale kropki sa problematyczne przy ustawianiu przez ts)
			if (is_array($returnArray))
				$returnArray['###LABEL_'.strtoupper($fieldName).'###'] = $this->pObj->pi_getLL('label_'.$fieldName, 'default label_'.$fieldName);
			else
				$this->assign( 'LABEL_'.strtoupper($fieldName), $this->pObj->pi_getLL('label_'.$fieldName, 'default label_'.$fieldName));
		}
	}

	/**
	 * Just to call it yet shorter, old-style to use like this->pi_getLL
	 * @param $label
	 * @param $default
	 * @return string
	 */
	public function pi_getLL($label, $default){
		return $this->pObj->pi_getLL($label, $default);
	}


	/**
	 * quick wrap function. same as in viewhelper
	 *
	 * @param string $item
	 * @param mixed  $wrap
	 * @return string
	 */
	public function wrap($item, $wrap)	{
		$wrapA = '';
		$wrapB = '';
		if (is_string($wrap))
			list ($wrapA, $wrapB) = explode('|', $wrap);

		return $wrapA . $item . $wrapB;
	}


	/**
	 * Renders Content Element
	 * @param $uid
	 * @return string
	 */
	public function renderCE($uid)	{
		return $this->pObj->renderCE($uid);
	}
}




?>