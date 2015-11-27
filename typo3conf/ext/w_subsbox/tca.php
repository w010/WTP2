<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_wsubsbox_emails'] = array (
	'ctrl' => $TCA['tx_wsubsbox_emails']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'address'
	),
	'feInterface' => $TCA['tx_wsubsbox_emails']['feInterface'],
	'columns' => array (
		'address' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:w_subsbox/locallang_db.xml:tx_wsubsbox_emails.address',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'address;;;;1-1-1')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);
?>