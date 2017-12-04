
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
	