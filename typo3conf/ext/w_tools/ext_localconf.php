<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

// not used on DB
/*TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43($_EXTKEY, 'pi1/class.tx_wtools_pi1.php', '_pi1', 'list_type', 1);
TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43($_EXTKEY, 'pi2/class.tx_wtools_pi2.php', '_pi2', 'list_type', 1);
TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43($_EXTKEY, 'pi3/class.tx_wtools_pi3.php', '_pi3', 'list_type', 1);*/




$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['w_tools'] = array(

	// (note that this conf differs from old DB import)
	// not used on DB
	/*'import' => array(
		'ExportDynamicsCRM' => array(
			'log' => 'fileadmin/contents/ExportDynamicsCRM/_import.log',
			'path' => 'fileadmin/contents/ExportDynamicsCRM/',  // always set
			//'file' => 'search_result.zip',    // file is expected inside path dir, if file mode
			'pid' => 79,
			'categoryUid' => 1, // default category set to all imported crm
		),
	),*/
);


//$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_wtools_import'] = array(
// crm import extends default w_tools import class
/*$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_wtools_import_crm'] = array(
	'extension'        => $_EXTKEY,
	'title'            => 'LLL:EXT:' . $_EXTKEY . '/locallang.xml:scheduler.import.crm.name',
	//'title'            => 'CRM',
	'description'      => 'LLL:EXT:' . $_EXTKEY . '/locallang.xml:scheduler.import.crm.description'
);*/


// not used on DB for now
// Page module hook / based on tt_news
/*$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info']['w_tools_pi1']['pi1'] = 'EXT:w_tools/class.tx_wtools_cms_layout.php:tx_wtools_cms_layout->getExtensionSummary';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all']['w_tools_pi2'] = 'EXT:w_tools/pi2/class.tx_wtools_pi2.php:tx_wtools_pi2_ajax->cutBoundaries';



if (TYPO3_MODE =='BE') {
    require_once(TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('w_tools').'class.tx_wtools_cms_layout.php');
}*/
