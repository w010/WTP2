<?php


//$TCA['tx_sitecedris_swregio'] = Array (

return Array (
	//'ctrl' => $TCA['tx_sitecedris_swregio']['ctrl'],
	
	
	//$TCA['tx_sitecedris_swregio'] = array (
	'ctrl' => array (
		'title' => 'LLL:EXT:site_cedris/locallang_db.xml:tx_sitecedris_swregio',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'delete' => 'deleted',
		'default_sortby' => 'ORDER BY title',
		'enablecolumns' => array (
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
		),
		'crdate' => 'crdate',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('site_cedris').'Resources/tx_sitecedris_swregio.gif',
		// new way - see https://typo3.org/api/typo3cms/class_t_y_p_o3_1_1_c_m_s_1_1_core_1_1_utility_1_1_extension_management_utility.html#a73a823dae5eb2061c685c457b9deebb1
		// @description on loadNewTcaColumnsConfigFiles
		//'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'tca.php'
	),
//);

	
	'interface' => Array (
		'showRecordFieldList' => 'title,description'
	),
	'columns' => Array (
		'title' => Array (
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.title',
			'config' => Array (
				'type' => 'input',
				'size' => '40',
				'max' => '256',
				'eval' => 'required'
			)
		),
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => Array (
				'type' => 'check',
			)
		),
		'fe_group' => Array (
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.fe_group',
			'config' => Array (
				'type' => 'select',
				'size' => 5,
				'maxitems' => 20,
				'items' => Array (
					Array('LLL:EXT:lang/locallang_general.php:LGL.hide_at_login', -1),
					Array('LLL:EXT:lang/locallang_general.php:LGL.any_login', -2),
					Array('LLL:EXT:lang/locallang_general.php:LGL.usergroups', '--div--')
				),
				'exclusiveKeys' => '-1,-2',
				'foreign_table' => 'fe_groups'
			)
		),
		'starttime' => Array (
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.starttime',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'max' => '20',
				'eval' => 'datetime',
				'checkbox' => '0',
				'default' => '0'
			)
		),
		'endtime' => Array (
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.endtime',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'datetime',
				'checkbox' => '0',
				'default' => '0',
				'range' => Array (
					'upper' => mktime(0,0,0,12,31,2020),
					'lower' => mktime(0,0,0,date('m')-1,date('d'),date('Y'))
				)
			)
		),
		'description' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:site_cedris/locallang_db.xml:tx_sitecedris_swregio.description',
			'config' => Array (
				'type' => 'text',
				'cols' => '40',
				'rows' => '3'
			)
		),
		'url' => Array (
			'label' => 'LLL:EXT:site_cedris/locallang_db.xml:tx_sitecedris_swregio.url',
			'config' => Array (
				'type' => 'input',
				'size' => '40',
				'max' => '256',
			)
		),
	),

	'types' => Array (
		'0' => Array('showitem' => '
			title;;;;,description;;;;,url;;;;1-1-1,hidden;;2;;
		'),

	),
	'palettes' => Array (
		//'1' => Array('showitem' => 'url'),
		'2' => Array('showitem' => 'starttime,endtime,fe_group'),
	)
);


?>