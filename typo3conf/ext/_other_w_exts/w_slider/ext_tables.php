<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Slider',
	'Carrousel Slider'
);

t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'jQuery Carrousel / Slider Content Elements');


$pluginSignature = str_replace('_','',$_EXTKEY) . '_slider';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_slider.xml');

/*$tmp_w_slider_columns = array(

);

t3lib_extMgm::addTCAcolumns('tt_content',$tmp_w_slider_columns);*/

$TCA['tt_content']['columns'][$TCA['tt_content']['ctrl']['type']]['config']['items'][] = array('LLL:EXT:w_slider/Resources/Private/Language/locallang_db.xml:tt_content.tx_extbase_type.Tx_WSlider_ContentElement','Tx_WSlider_ContentElement');

$TCA['tt_content']['types']['Tx_WSlider_ContentElement']['showitem'] = $TCA['tt_content']['types']['1']['showitem'];
$TCA['tt_content']['types']['Tx_WSlider_ContentElement']['showitem'] .= ',--div--;LLL:EXT:w_slider/Resources/Private/Language/locallang_db.xml:tx_wslider_domain_model_contentelement,';
$TCA['tt_content']['types']['Tx_WSlider_ContentElement']['showitem'] .= '';

?>