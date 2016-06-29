<?php


return Array (

	'ctrl' => array (
		'title' => 'LLL:EXT:site_cedris/locallang_db.xml:tx_sitecedris_bglink',
		'label' => 'label',
		'tstamp' => 'tstamp',
		'delete' => 'deleted',
		'default_sortby' => 'ORDER BY label',
		'sortby' => 'sorting',
		'enablecolumns' => array (
			'disabled' => 'hidden',
		),
		'crdate' => 'crdate',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('site_cedris').'Resources/tx_sitecedris_bglink.gif',
		// new way - see https://typo3.org/api/typo3cms/class_t_y_p_o3_1_1_c_m_s_1_1_core_1_1_utility_1_1_extension_management_utility.html#a73a823dae5eb2061c685c457b9deebb1
		// @description on loadNewTcaColumnsConfigFiles
	),

	
	'interface' => Array (
		'showRecordFieldList' => 'title,link'
	),
	'columns' => Array (
		'label' => Array (
			'label' => 'LLL:EXT:site_cedris/locallang_db.xml:tx_sitecedris_bglink.label',
			'config' => Array (
				'type' => 'input',
				'size' => '40',
				'max' => '255',
			)
		),
		'link' => Array (
			'label' => 'LLL:EXT:site_cedris/locallang_db.xml:tx_sitecedris_bglink.link',
			'config' => array(
				'type' => 'input',
				'size' => '50',
				'max' => '255',
				'eval' => 'trim, required',
				'wizards' => array(
					'_PADDING' => 2,
					'link' => array(
						'type' => 'popup',
						'title' => 'LLL:EXT:site_cedris/locallang_db.xml:tx_sitecedris_bglink.link',
						'icon' => 'link_popup.gif',
						'script' => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1',
					),
				),
				'softref' => 'typolink',
			),
		),
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => Array (
				'type' => 'check',
			)
		)
	),

	'types' => Array (
		'0' => Array('showitem' => '
			link,label;;;;1-1-1,hidden;;2;;
		'),

	),
	'palettes' => Array (
		//'1' => Array('showitem' => 'url'),
		//'2' => Array('showitem' => 'starttime,endtime,fe_group'),
	)
);


?>