****
TODO
****

- extract debugbox js from cag_materialbrowser to separate file and put it here. include only in dev mode
- change the js file name
- do real model objects and rename Model to ie. DatabaseLayer




*********
CHANGELOG
*********

2017.12.08
version 2.5.0

* passing Model to newly created Controller is now optional. you can create it later in controller's init()
* passing Model to newly created View is now optional. if not given, tries to retrieve it from controller
	(Mvc::getView checks that it exists or throws exception)
* Model singleton can now be rebuilt (forceRefresh) needed when some other model inherits it


2017.12.05
version 2.4.0

* dev flag for js now uses ApplicationContext = Development or typoscript var debug = 1
* debugbox now has severity filter and wipe button
* debugbox css ts now moved to w_tools. custom styles now must use .tx-wtools-debugbox { ... } instead of .tx_myext .debugdata
* debugbox js now must use .tx-wtools-debugbox (this js will be extracted to w_tools in next version)


2017.04.19
version 2.3.0

* tx_wtools_pibase class (deprecated) renamed to AbstractPlugin, Tx_WTools_Mvc_Pibase renamed to AbstractPluginMvc
* both uses namespaces now: WTP\WTools\AbstractPlugin, WTP\WTools\Mvc\AbstractPluginMvc


2016.12.19
version 2.2.2

* Controller->isAjax() should now be used to detect ajax mode


2016.12.12
version 2.2.1

* controller now have "default" displayMode if not set (instead of blank)


2016.11.19
version 2.2.0

* breaking changes!

	- Page object parameter list has changed. Do not pass $pObj anymore (plugin instance) - it's taken from registry now;
	- Model object - as above;
	- Viewhelper - the same
	- Controller object - as above, and rest of params has changed order
	- View - as above, and rest of params has changed order

	- Tx_WTools_Mvc	now becomes WTP\WTools\Mvc and doesn't pass pObj param as well
	
	- Pages (and Abstract page) now extends \WTP\WTools\Mvc\Page\AbstractPage (previously: Tx_WTools_Mvc_Page_Abstract)	
	- Pages can use namespaces, so mvc->getPage can take pagename with namespace
	- same with all other: controller, view, model, component, object
	