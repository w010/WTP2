<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_wrating_vote'] = array (
	'ctrl' => $TCA['tx_wrating_vote']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'table_de16108197,record_uid,note,userdata'
	),
	'feInterface' => $TCA['tx_wrating_vote']['feInterface'],
	'columns' => array (
		'table_de16108197' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:w_rating/locallang_db.xml:tx_wrating_vote.table_de16108197',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required',
			)
		),
		'record_uid' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:w_rating/locallang_db.xml:tx_wrating_vote.record_uid',		
			'config' => array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => '*',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'note' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:w_rating/locallang_db.xml:tx_wrating_vote.note',		
			'config' => array (
				'type'     => 'input',
				'size'     => '4',
				'max'      => '4',
				'eval'     => 'int',
				'checkbox' => '0',
				'range'    => array (
					'upper' => '1000',
					'lower' => '10'
				),
				'default' => 0
			)
		),
		'userdata' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:w_rating/locallang_db.xml:tx_wrating_vote.userdata',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'table_de16108197;;;;1-1-1, record_uid, note, userdata')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);
?>