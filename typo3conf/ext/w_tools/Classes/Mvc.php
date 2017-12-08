<?php
/**
 * wolo.pl '.' studio 2016
 * 
 * w_tools MVC base 0.5
 */


namespace WTP\WTools;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;



/**
 * Main MVC loader
 *
 * v. 05
 */
class Mvc		{


    /**
     * Returns PAGE object
     * @param string $pageName
     * @throws \Exception
     * @return object, Mvc\Page\AbstractPage
     */
    static function getPage($pageName)   {
	    $pObj = &Registry::Cell('wtools', 'pi1');

	    if (strstr($pageName, '\\'))
	    	return GeneralUtility::makeInstance($pageName, $pageName);  // pass it's name to itself

	    $classNamePrefix = ExtensionManagementUtility::getCN($pObj->extKey);
	    $className = $classNamePrefix.'_page_'.$pageName;
        $path = ExtensionManagementUtility::extPath($pObj->extKey).'Classes/Page/'.$pageName.'.php';
        if (!file_exists($path))
            Throw new \Exception('Fatal: page class file not found! Class '.$className.' for page '.$pageName);
        require_once($path);
        return new $className($pageName);
    }

    /**
     * Returns Controller object
     * @param $controllerName string
     * @param $displayMode string
     * @param $Model Mvc\Model\AbstractModel|null - you can pass Model directly ie. in Page, or not if you create specific Model inside your Controller
     * @throws \Exception
     * @return object, Mvc\Controller\AbstractController
     */
    static function getController($controllerName, $displayMode, $Model = null)   {
	    $pObj = &Registry::Cell('wtools', 'pi1');

	    if (strstr($controllerName, '\\'))
		    return GeneralUtility::makeInstance($controllerName, $controllerName, $displayMode, $Model);  // pass its name to itself

		$classNamePrefix = ExtensionManagementUtility::getCN($pObj->extKey);
	    $className = $classNamePrefix.'_controller_'.$controllerName;
        $path = ExtensionManagementUtility::extPath($pObj->extKey).'Classes/Controller/'.$controllerName.'.php';
        if (!file_exists($path))
            Throw new \Exception('Fatal: controller class file not found! Class '.$className.' for controller '.$controllerName);
        require_once($path);
        return new $className($controllerName, $displayMode, $Model);
    }

    /**
     * Returns View object
     * @param Mvc\Controller\AbstractController $Controller
     * @param string $viewName
     * @param string $displayMode instead of determining from controller, can be set here
     * @throws \Exception
     * @return object, Mvc\View\DefaultView
     */
    static function getView(Mvc\Controller\AbstractController &$Controller, $viewName, $displayMode = '')   {

	    $Model = $Controller->getModel();

	    // if Model is not found in Controller, you must pass it when creating Controller or create later in its init() method
	    if (!$Model)
		    Throw new \Exception('Fatal: View '.$viewName.' cannot retrieve Model from Controller '.$Controller->getControllerName());

	    if (strstr($viewName, '\\'))
		    return GeneralUtility::makeInstance($viewName, $viewName, $displayMode?$displayMode:$Controller->getDisplayMode(), $Model, $Controller);  // pass it's name to itself

		$classNamePrefix = ExtensionManagementUtility::getCN($Controller->getPObj()->extKey);
	    $className = $classNamePrefix.'_view_'.$viewName;
        $path = ExtensionManagementUtility::extPath($Controller->getPObj()->extKey).'Classes/View/'.$viewName.'.php';
        if (!file_exists($path))
            Throw new \Exception('Fatal: view class file not found! Class '.$className.' for view '.$viewName);
        require_once($path);
        return new $className($viewName, $displayMode?$displayMode:$Controller->getDisplayMode(), $Model, $Controller);
    }

	/**
	 * Returns MODEL object
	 *
	 * this is the first thing that need to be reworked
	 * model should represent data types, plus general model for non-standard
	 *
	 * @param string $modelName
	 * @param bool $forceRefresh - rebuild singleton on demand
	 * @return Mvc\Model\AbstractModel
	 * @throws \Exception
	 */
	static function getModel($modelName, $forceRefresh = false)   {
		$pObj = &Registry::Cell('wtools', 'pi1');

		if (strstr($modelName, '\\'))
			return $modelName::Instance($forceRefresh);


		$classNamePrefix = ExtensionManagementUtility::getCN($pObj->extKey);
		$className = $classNamePrefix.'_model'.($modelName?'_'.$modelName:'');
		$path = ExtensionManagementUtility::extPath($pObj->extKey).'Classes/Model/'.$modelName.'.php';
		if (!file_exists($path))
			Throw new \Exception('Fatal: model class file not found! Class <i>'.$className.'</i> for model <b>'.$modelName.'</b>. Should be in '.$path);
		require_once($path);
		return $className::Instance($forceRefresh);
	}

	/**
	 * Returns COMPONENT object (ie. advanced ajax button)
	 *
	 * @param string               $componentName
	 * @param array                $params
	 * @throws \Exception
	 * @return object, Mvc\Component\AbstractComponent
	 */
	static function getComponent($componentName, $params = [])   {
		$pObj = &Registry::Cell('wtools', 'pi1');

		if (strstr($componentName, '\\'))
			return GeneralUtility::makeInstance($componentName, $componentName, $params);  // pass it's name to itself

		$classNamePrefix = ExtensionManagementUtility::getCN($pObj->extKey);
		$className = $classNamePrefix.'_component'.($componentName?'_'.$componentName:'');
		$path = ExtensionManagementUtility::extPath($pObj->extKey).'Classes/Component/'.$componentName.'.php';
		if (!file_exists($path))
			Throw new \Exception('Fatal: component class file not found! Class <i>'.$className.'</i> for component <b>'.$componentName.'</b>. Should be in '.$path);
		require_once($path);
		return new $className($componentName, $params);
	}



	/**
	 * Returns viewhelper
	 * todo: być może obiekt AJAX jednak stanie się rodzajem page, wtedy ta metoda będzie najpewniej zbędna, bo na ten moment tylko do tego służy.
	 * jeśli to się zmieni, odnotować użycie tutaj
	 *
	 * @param string $viewhelperName
	 * @throws \Exception
	 * @return object, Mvc\Viewhelper\AbstractViewhelper
	 */
	static function getViewhelper($viewhelperName)   {
		$pObj = &Registry::Cell('wtools', 'pi1');

		if (strstr($viewhelperName, '\\'))
			return GeneralUtility::makeInstance($viewhelperName, $viewhelperName);  // pass it's name to itself

		$classNamePrefix = ExtensionManagementUtility::getCN($pObj->extKey);
		$className = $classNamePrefix.'_viewhelper_'.$viewhelperName;
		$path = ExtensionManagementUtility::extPath($pObj->extKey).'Classes/Viewhelper/'.$viewhelperName.'.php';
		if (!file_exists($path))
			Throw new \Exception('Fatal: viewhelper class file not found! Class '.$className.' for viewhelper '.$viewhelperName);
		require_once($path);
		return new $className($viewhelperName);
	}





		/**
		 * Returns other object - additional
		 * todo: być może obiekt AJAX jednak stanie się rodzajem page, wtedy ta metoda będzie najpewniej zbędna, bo na ten moment tylko do tego służy.
		 * jeśli to się zmieni, odnotować użycie tutaj
		 * @param \tx_wtools_pibase $pObj
		 * @param string $objectName
		 * @throws \Exception
		 * @return object
		 */
		static function getObject(&$pObj, $objectName, $params)   {
			$pObj = &Registry::Cell('wtools', 'pi1');

			if (strstr($objectName, '\\'))
				return GeneralUtility::makeInstance($objectName, $objectName, $params);  // pass it's name to itself

			$classNamePrefix = ExtensionManagementUtility::getCN($pObj->extKey);
			$className = $classNamePrefix.'_'.$objectName;
			$path = ExtensionManagementUtility::extPath($pObj->extKey).'Classes/'.$objectName.'.php';
			if (!file_exists($path))
				Throw new \Exception('Fatal: object class file not found! Class '.$className.' for object '.$objectName);
			require_once($path);
			return new $className($pObj, $objectName, $params);
		}
}



?>