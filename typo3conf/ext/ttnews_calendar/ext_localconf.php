<?php
if (!defined('TYPO3_MODE'))
	die ('Access denied.');

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_ttnewscalendar_pi1.php', '_pi1', 'list_type', 1);

$TYPO3_CONF_VARS['EXTCONF']['tt_news']['extraCodesHook'][] = 'tx_ttnewscalendar_ttnews_hooks';
$TYPO3_CONF_VARS['EXTCONF']['tt_news']['extraItemMarkerHook'][] = 'tx_ttnewscalendar_ttnews_hooks';
$TYPO3_CONF_VARS['EXTCONF']['tt_news']['extraGlobalMarkerHook'][] = 'tx_ttnewscalendar_ttnews_hooks';
//$TYPO3_CONF_VARS['EXTCONF']['tt_news']['extraGlobalSubpartsHook'][] = 'tx_ttnewscalendar_ttnews_hooks';	  // custom
//$TYPO3_CONF_VARS['EXTCONF']['tt_news']['additionalFormSearchFields'][] = 'tx_ttnewscalendar_ttnews_hooks';
$TYPO3_CONF_VARS['EXTCONF']['tt_news']['what_to_display'][] = array('CALENDAR_LIST_DAY', 'CALENDAR_LIST_DAY');		// list events, don't show beyond enddate, filter by day or category
$TYPO3_CONF_VARS['EXTCONF']['tt_news']['selectConfHook'][] = 'tx_ttnewscalendar_ttnews_hooks';
$TYPO3_CONF_VARS['EXTCONF']['tt_news']['userDisplayCatmenuHook'][] = 'tx_ttnewscalendar_ttnews_hooks';
//$TYPO3_CONF_VARS['EXTCONF']['tt_news']['sViewSelectConfHook'][] = 'tx_ttnewscalendar_ttnews_hooks';


$TYPO3_CONF_VARS['EXTCONF']['ttnews_calendar'] = array();

if (TYPO3_MODE!='BE') {
	require_once(t3lib_extMgm::extPath('ttnews_calendar').'class.tx_ttnewscalendar_ttnews_hooks.php');
}



?>