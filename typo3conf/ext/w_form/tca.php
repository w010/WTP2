<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_wform_forms'] = array (
	'ctrl' => $TCA['tx_wform_forms']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,name,email,formdata'
	),
	'feInterface' => $TCA['tx_wform_forms']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'name' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:w_form/locallang_db.xml:tx_wform_forms.name',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'email' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:w_form/locallang_db.xml:tx_wform_forms.email',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'formdata' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:w_form/locallang_db.xml:tx_wform_forms.formdata',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, name, email, formdata')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);
?>