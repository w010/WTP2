<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43($_EXTKEY,"pi1/class.tx_bekodebugster_pi1.php","_pi1","",1);

include_once (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'pi1/class.tx_bekodebugster_pi1.php');
?>