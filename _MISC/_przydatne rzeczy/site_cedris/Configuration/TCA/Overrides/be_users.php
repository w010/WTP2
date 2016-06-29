<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');



// be_users
$tempColumns = Array (

	'tx_sitecedris_image' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:site_cedris/locallang_db.xml:be_users.tx_sitecedris_image',
		'config' => Array (
			'type' => 'group',
			'internal_type' => 'file',
			'allowed' => 'jpg,png,gif',
			'uploadfolder' => 'uploads/pics',
			'size' => '1',
			'maxitems' => '1',
			'minitems' => '0',
			'show_thumbs' => '1',
		)
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns("be_users", $tempColumns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes("be_users", "tx_sitecedris_image", '', 'after:email');

