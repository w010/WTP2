<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_wgmaps_points'] = array(
	'ctrl' => $TCA['tx_wgmaps_points']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,title,description,coords'
	),
	'feInterface' => $TCA['tx_wgmaps_points']['feInterface'],
	'columns' => array(
		'hidden' => array(		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'title' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:w_gmaps/locallang_db.xml:tx_wgmaps_points.title',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required,trim',
			)
		),
		'description' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:w_gmaps/locallang_db.xml:tx_wgmaps_points.description',		
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => array(
					'_PADDING' => 2,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly'       => 1,
						'type'          => 'script',
						'title'         => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon'          => 'wizard_rte2.gif',
						'script'        => 'wizard_rte.php',
					),
				),
			)
		),
		'coords' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:w_gmaps/locallang_db.xml:tx_wgmaps_points.coords',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, description;;;richtext[]:rte_transform[mode=ts];3-3-3, coords')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);
?>