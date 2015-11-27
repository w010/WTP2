<?php
if (!defined('TYPO3_MODE'))
	die ('Access denied.');

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1'] = 'layout, select_key, pages';

t3lib_extMgm::addPlugin(Array(
	'LLL:EXT:ttnews_calendar/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');

$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_pi1'] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY . '_pi1', 'FILE:EXT:ttnews_calendar/flexform.xml');

t3lib_extMgm::addStaticFile($_EXTKEY,'static/', 'News Calendar');


$tempColumns = array(
	'tx_ttnewscalendar_event_endtime' => array(		
		'exclude' => 0,		
		'label' => 'LLL:EXT:ttnews_calendar/locallang_db.xml:tt_news.tx_ttnewscalendar_event_endtime',		
		'config' => array(
			'type'     => 'input',
			'size'     => '8',
			'max'      => '20',
			'eval'     => 'datetime',
			'checkbox' => '0',
			'default'  => '0'
		)
	),
);


t3lib_div::loadTCA('tt_news');
t3lib_extMgm::addTCAcolumns('tt_news',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('tt_news','tx_ttnewscalendar_event_endtime;;;;1-1-1', '', 'after:datetime');




if (TYPO3_MODE == 'BE')
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_ttnewscalendar_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_ttnewscalendar_pi1_wizicon.php';

?>