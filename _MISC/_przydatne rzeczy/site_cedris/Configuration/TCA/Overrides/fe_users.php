<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');



// fe_users (sw-bedrijvn)
$tempColumns = Array (

	'tx_sitecedris_sw_postadres' => Array (
		"exclude" => 1,
		"label" => "LLL:EXT:site_cedris/locallang_db.xml:fe_users.tx_sitecedris_sw_postadres",
		'config' => array(
			'type' => 'input',
			'size' => '30',
			'max' => '32',
			'eval' => 'trim'
		),
	),
	'tx_sitecedris_sw_cole' => Array (
		"exclude" => 1,
		"label" => "LLL:EXT:site_cedris/locallang_db.xml:fe_users.tx_sitecedris_sw_cole",
		'config' => array(
			'type' => 'input',
			'size' => '30',
			'max' => '8',
			'eval' => 'trim'
		),
	),
	'tx_sitecedris_sw_provincie' => Array (
		"exclude" => 1,
		"label" => "LLL:EXT:site_cedris/locallang_db.xml:fe_users.tx_sitecedris_sw_provincie",
		'config' => array(
			'type' => 'input',
			'size' => '40',
			'max' => '32',
			'eval' => 'trim'
		),
	),
	'tx_sitecedris_sw_arbeidsmarktregio' => Array (
		"exclude" => 1,
		"label" => "LLL:EXT:site_cedris/locallang_db.xml:fe_users.tx_sitecedris_sw_arbeidsmarktregio",
		'config' => Array (
			'type' => 'select',
			'foreign_table' => 'tx_sitecedris_swregio',
			'size' => '1',
			'maxitems' => '1',
			'minitems' => '0',
			'items' => Array( Array('', -1))
		),
	),
	'tx_sitecedris_sw_citysecond' => Array (
		"exclude" => 1,
		"label" => "LLL:EXT:site_cedris/locallang_db.xml:fe_users.tx_sitecedris_sw_citysecond",
		'config' => array(
			'type' => 'input',
			'size' => '100',
			'max' => '256',
			'eval' => 'trim'
		),
	)
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns("fe_users", $tempColumns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes("fe_users", "tx_sitecedris_sw_postadres,tx_sitecedris_sw_cole,tx_sitecedris_sw_provincie,tx_sitecedris_sw_arbeidsmarktregio", '', 'after:image');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes("fe_users", "tx_sitecedris_sw_citysecond", '', 'after:city');




