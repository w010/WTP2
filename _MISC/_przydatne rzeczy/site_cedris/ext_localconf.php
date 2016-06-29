<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');




//\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43($_EXTKEY, 'pi1/class.tx_sitecedris_pi1.php', '_pi1', 'list_type', 1);


$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_cedris'] = array(
    'search' => array(
        // naming of keys! should be like fieldnames -> look filters. to rebuild in future
        //'yearOptions' => array('range', '1990-'.date('Y') ),
    ),
);



$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['extraCodesHook'][] = 'EXT:site_cedris/class.tx_sitecedris_ttnewshooks.php:&tx_sitecedris_ttnewshooks';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['extraItemMarkerHook'][] = 'EXT:site_cedris/class.tx_sitecedris_ttnewshooks.php:&tx_sitecedris_ttnewshooks';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['extraItemArrayHook'][] = 'EXT:site_cedris/class.tx_sitecedris_ttnewshooks.php:&tx_sitecedris_ttnewshooks';   // custom hook! look for 'wolo mod'
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['extraGlobalMarkerHook'][] = 'EXT:site_cedris/class.tx_sitecedris_ttnewshooks.php:&tx_sitecedris_ttnewshooks';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['additionalFormSearchFields'][] = 'EXT:site_cedris/class.tx_sitecedris_ttnewshooks.php:&tx_sitecedris_ttnewshooks';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['getSingleViewLinkHook'][] = 'EXT:site_cedris/class.tx_sitecedris_ttnewshooks.php:&tx_sitecedris_ttnewshooks';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['selectConfHook'][] = 'EXT:site_cedris/class.tx_sitecedris_ttnewshooks.php:&tx_sitecedris_ttnewshooks';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['what_to_display'][] = array('SEARCH_FORM', 'SEARCH_FORM');
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['what_to_display'][] = array('SEARCH_FILTER_PRAKTIJK', 'SEARCH_FILTER_PRAKTIJK');
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['what_to_display'][] = array('CATMENU_SIMPLE', 'CATMENU_SIMPLE');
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['what_to_display'][] = array('LIST_WEEK', 'LIST_WEEK');
//$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['sViewSelectConfHook'][] = 'EXT:site_cedris/class.tx_sitecedris_ttnewshooks.php:&tx_sitecedris_ttnewshooks';
/*
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['getSingleViewLinkHook']
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['searchWhere']
*/


$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['userDisplayCatmenuHook'][] = 'EXT:site_cedris/class.tx_sitecedris_ttnewshooks.php:&tx_sitecedris_ttnewshooks';



// hook, ktory pozwala manipulowac elementami przed wyswietleniem ich na liscie. UWAGA, nie wystepuje w standardzie - dopisany do kodu ttnews
//$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['processListElementsHook'][] = 'EXT:site_cedris/class.tx_sitecedris_ttnewshooks.php:&tx_sitecedris_ttnewshooks';
// hook, ktory pozwala dodawac kod miedzy gotowymi elementami (np. dodawac inne itemy) UWAGA, nie wystepuje w standardzie - dopisany do kodu ttnews
//$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['processListElementsRenderedHook'][] = 'EXT:site_cedris/class.tx_sitecedris_ttnewshooks.php:&tx_sitecedris_ttnewshooks';




// feuser_friends (sw-userlist)

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['feuser_friends']['listViewPreSubstitute'][] = 'EXT:site_cedris/class.tx_sitecedris_feuserfriendshooks.php:&tx_sitecedris_feuserfriendshooks'; // native
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['feuser_friends']['extraGlobalMarkerHook'][] = 'EXT:site_cedris/class.tx_sitecedris_feuserfriendshooks.php:&tx_sitecedris_feuserfriendshooks'; // ttnews style hooks mod
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['feuser_friends']['selectConfHook'][] = 'EXT:site_cedris/class.tx_sitecedris_feuserfriendshooks.php:&tx_sitecedris_feuserfriendshooks';
//$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['feuser_friends']['listViewPreSubstitute'][] = 'EXT:site_cedris/class.tx_sitecedris_feuserfriendshooks.php:&tx_sitecedris_feuserfriendshooks';



$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['linkhandler']['generateLink'][] = 'EXT:site_cedris/class.tx_sitecedris_linkhandler_helper.php:&tx_sitecedris_linkhandler_helper->main'; // proper pid for linking ttnews from different categories
