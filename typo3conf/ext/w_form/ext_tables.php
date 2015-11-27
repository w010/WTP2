<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
/*
$TCA['tx_wform_forms'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:w_form/locallang_db.xml:tx_wform_forms',
		'label'     => 'uid',
		'label_alt'     => 'email,name,formdata',
		'label_alt_force'     => true,
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',
		'delete' => 'deleted',
		'enablecolumns' => array (
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_wform_forms.gif',
	),
);
*/

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] = 'pi_flexform';

t3lib_extMgm::addPlugin(array(
	'LLL:EXT:w_form/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:w_form/pi1/flexform_ds.xml');

if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_wform_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY)."pi1/class.tx_wform_pi1_wizicon.php";
}

?>