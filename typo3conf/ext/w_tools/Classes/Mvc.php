<?php
/**
 * wolo.pl '.' studio 2015
 * 
 * w_tools MVC base 0.3
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;


/**
 * Main MVC loader
 *
 * v. 02
 */
class Tx_WTools_Mvc		{


    /**
     * Returns PAGE object
     * @param tx_wtools_pibase $pObj
     * @param string $pageName
     * @throws Exception
     * @return Tx_WTools_Mvc_Page_Abstract
     */
    static function getPage(&$pObj, $pageName)   {
	    $classNamePrefix = ExtensionManagementUtility::getCN($pObj->extKey);
	    $className = $classNamePrefix.'_page_'.$pageName;
        $path = ExtensionManagementUtility::extPath($pObj->extKey).'Classes/Page/'.$pageName.'.php';
        if (!file_exists($path))
            Throw new Exception('Fatal: page class file not found! Class '.$className.' for page '.$pageName);
        require_once($path);
        return new $className($pObj, $pageName);
    }

    /**
     * Returns Controller object
     * @param $pObj Tx_WTools_Mvc_Pibase
     * @param $Model Tx_WTools_Mvc_Model_Abstract
     * @param $controllerName string
     * @param $displayMode string
     * @throws Exception
     * @return Tx_WTools_Mvc_Controller_Abstract
     */
    static function getController(&$pObj, Tx_WTools_Mvc_Model_Abstract &$Model, $controllerName, $displayMode)   {
		$classNamePrefix = ExtensionManagementUtility::getCN($pObj->extKey);
	    $className = $classNamePrefix.'_controller_'.$controllerName;
        $path = ExtensionManagementUtility::extPath($pObj->extKey).'Classes/Controller/'.$controllerName.'.php';
        if (!file_exists($path))
            Throw new Exception('Fatal: controller class file not found! Class '.$className.' for controller '.$controllerName);
        require_once($path);
        return new $className($pObj, $Model, $controllerName, $displayMode);
    }

    /**
     * Returns View object
     * @param Tx_WTools_Mvc_Controller_Abstract $Controller
     * @param string $viewName
     * @param string $displayMode instead of determining from controller, can be set here
     * @throws Exception
     * @return Tx_WTools_Mvc_View_Default
     */
    static function getView(&$Controller, $viewName, $displayMode = '')   {
		$classNamePrefix = ExtensionManagementUtility::getCN($Controller->getPObj()->extKey);
	    $className = $classNamePrefix.'_view_'.$viewName;
        $path = ExtensionManagementUtility::extPath($Controller->getPObj()->extKey).'Classes/View/'.$viewName.'.php';
        if (!file_exists($path))
            Throw new Exception('Fatal: view class file not found! Class '.$className.' for view '.$viewName);
        require_once($path);
		$Model = $Controller->getModel();
		$pObj = $Controller->getPObj();
        return new $className($pObj, $Model, $Controller, $viewName, $displayMode?$displayMode:$Controller->getDisplayMode());
    }

	/**
	 * Returns MODEL object
	 *
	 * this is the first thing that need to be reworked
	 * model should represent data types, plus general model for non-standard
	 *
	 * @param Tx_WTools_Mvc_Pibase $pObj
	 * @param string $modelName
	 * @throws Exception
	 * @return Tx_WTools_Mvc_Model_Abstract
	 */
	static function getModel(&$pObj, $modelName = 'memcache')   {
		$classNamePrefix = ExtensionManagementUtility::getCN($pObj->extKey);
		$className = $classNamePrefix.'_model'.($modelName?'_'.$modelName:'');
		$path = ExtensionManagementUtility::extPath($pObj->extKey).'Classes/Model/'.$modelName.'.php';
		if (!file_exists($path))
			Throw new Exception('Fatal: model class file not found! Class <i>'.$className.'</i> for model <b>'.$modelName.'</b>. Should be in '.$path);
		require_once($path);
		return $className::Instance($pObj);
	}

	/**
	 * Returns COMPONENT object
	 *
	 * @param Tx_WTools_Mvc_Pibase $pObj
	 * @param string               $componentName
	 * @param array                $params
	 * @throws Exception
	 * @return Tx_WTools_Mvc_Component_Abstract
	 */
		static function getComponent(Tx_WTools_Mvc_Pibase &$pObj, $componentName, $params = [])   {
			$classNamePrefix = ExtensionManagementUtility::getCN($pObj->extKey);
			$className = $classNamePrefix.'_component'.($componentName?'_'.$componentName:'');
			$path = ExtensionManagementUtility::extPath($pObj->extKey).'Classes/Component/'.$componentName.'.php';
			if (!file_exists($path))
				Throw new Exception('Fatal: component class file not found! Class <i>'.$className.'</i> for component <b>'.$componentName.'</b>. Should be in '.$path);
			require_once($path);
			return new $className($pObj, $componentName, $params);
		}


	/**
	 * Returns other object - additional
	 * todo: być może obiekt AJAX jednak stanie się rodzajem page, wtedy ta metoda będzie najpewniej zbędna, bo na ten moment tylko do tego służy.
	 * jeśli to się zmieni, odnotować użycie tutaj
	 * @param tx_wtools_pibase $pObj
	 * @param string $objectName
	 * @throws Exception
	 * @return object
	 */
	static function getObject(&$pObj, $objectName)   {
		$classNamePrefix = ExtensionManagementUtility::getCN($pObj->extKey);
		$className = $classNamePrefix.'_'.$objectName;
		$path = ExtensionManagementUtility::extPath($pObj->extKey).'Classes/'.$objectName.'.php';
		if (!file_exists($path))
			Throw new Exception('Fatal: object class file not found! Class '.$className.' for object '.$objectName);
		require_once($path);
		return new $className($pObj, $objectName);
	}

	/**
	 * Returns viewhelper
	 * todo: być może obiekt AJAX jednak stanie się rodzajem page, wtedy ta metoda będzie najpewniej zbędna, bo na ten moment tylko do tego służy.
	 * jeśli to się zmieni, odnotować użycie tutaj
	 *
	 * @param tx_wtools_pibase $pObj
	 * @param string $viewhelperName
	 * @throws Exception
	 * @return Tx_WTools_Mvc_Viewhelper_General
	 */
	static function getViewhelper(&$pObj, $viewhelperName)   {
		$classNamePrefix = ExtensionManagementUtility::getCN($pObj->extKey);
		$className = $classNamePrefix.'_viewhelper_'.$viewhelperName;
		$path = ExtensionManagementUtility::extPath($pObj->extKey).'Classes/Viewhelper/'.$viewhelperName.'.php';
		if (!file_exists($path))
			Throw new Exception('Fatal: viewhelper class file not found! Class '.$className.' for viewhelper '.$viewhelperName);
		require_once($path);
		return new $className($pObj, $viewhelperName);
	}
}



?>