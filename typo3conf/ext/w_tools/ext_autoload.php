<?php

// Not needed anymore since TYPO3 CMS 6.1, when using namespaces.
// http://docs.typo3.org/typo3cms/CoreApiReference/ExtensionArchitecture/FilesAndLocations/Index.html

	// Register necessary classes with autoloader

$path = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('w_tools');

return [
	'tx_wtools_pibase' => $path.'Classes/Compatibility/tx_wtools_pibase.php',
	'tx_wtools_log' => $path.'Classes/Compatibility/tx_wtools_log.php',
	'wXml' => $path.'Classes/wXml.php',
	'wCsv' => $path.'Classes/wCsv.php',


	//'tx_wtools_misc' => $path.'Classes/Misc.php',
	//'tx_wtools_import' => $path.'Classes/Import.php',
	//'tx_wtools_import_crm' => $path.'Classes/Import_Crm.php',
];
