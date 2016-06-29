<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');


/*$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] = 'pi_flexform';
TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(array(
	'LLL:EXT:site_cedris/locallang.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	\TYPO3\CMS\Core\Utility\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:site_cedris/pi/flexform_ds.xml');
*/



//moved to Configuration/TCA 

/*
$TCA['tx_sitecedris_swregio'] = array (
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
		'iconfile' => TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY).'res/tx_sitecedris_swregio.gif',
		// new way - see https://typo3.org/api/typo3cms/class_t_y_p_o3_1_1_c_m_s_1_1_core_1_1_utility_1_1_extension_management_utility.html#a73a823dae5eb2061c685c457b9deebb1
		// @description on loadNewTcaColumnsConfigFiles
		//'dynamicConfigFile' => TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'tca.php'
	)
);
*/




// not moved to Configuration/TCA/Overrides, because it needs to be defined in tt_news in new way in order to work like this

// tt_news
$tempColumns = Array (

	'tx_sitecedris_background' => Array (
		'exclude' => 1,
		'l10n_mode' => 'exclude',
		'label' => 'LLL:EXT:site_cedris/locallang_db.xml:tt_news.tx_sitecedris_background',
		'config' => Array (
			'type' => 'group',
			'internal_type' => 'db',
			'allowed' => 'tt_news,pages',
			'MM' => 'tx_sitecedris_background_mm',
				//'MM_opposite_field' => '' // to by przeszlo, gdyby nie pages. trzeba by dodac kolumne do pages
			'size' => '3',
			'autoSizeMax' => 10,
			'maxitems' => '200',
			'minitems' => '0',
			'show_thumbs' => '1',
			'wizards' => array(
				'suggest' => array(
					'type' => 'suggest'
				)
			)
		)
	),

	/*"tx_sitecedris_background_links" => Array (
		"exclude" => 1,
		"label" => "LLL:EXT:site_cedris/locallang_db.xml:tt_news.tx_sitecedris_background_links",
		'config' => array(
			'type' => 'input',
			'size' => '50',
			'max' => '256',
			'eval' => 'trim',
			'wizards' => array(
				'_PADDING' => 2,
				'link' => array(
					'type' => 'popup',
					'title' => 'LLL:EXT:site_cedris/locallang_ttc.xml:tx_sitecedris_background_links',
					'icon' => 'link_popup.gif',
					'script' => 'browse_links.php?mode=wizard',
					'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1',
				),
			),
			'softref' => 'typolink',
		),
	),*/

	"tx_sitecedris_background_links" => Array (
		"exclude" => 1,
		"label" => "LLL:EXT:site_cedris/locallang_db.xml:tt_news.tx_sitecedris_background_links",
		'config' => array(
			'type' => 'inline',
			'foreign_table' => 'tx_sitecedris_bglink',
			'foreign_table_where' => 'AND tx_sitecedris_bglink.pid = ###CURRENT_PID###',
			'foreign_sortby' => 'sorting',
			'maxitems' => '50',
		),
	),

	'tx_sitecedris_author' => Array (
		'exclude' => 1,
		'l10n_mode' => 'exclude',
		'label' => 'LLL:EXT:site_cedris/locallang_db.xml:tt_news.tx_sitecedris_author',
		'config' => Array (
			'type' => 'select',
			//'internal_type' => 'db',
			//'allowed' => 'be_users',
				//'form_type' => 'user',
				//'userFunc' => 'tx_sitecedris_TCAform->renderBlogAuthor',
			'itemsProcFunc' => 'tx_sitecedris_TCAform->itemsBlogAuthor',
			'size' => '1',
			'maxitems' => '1',
			'minitems' => '0',
			'show_thumbs' => '1',
			/*'wizards' => array(
				'suggest' => array(
					'type' => 'suggest'
				)
			)*/
		)
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

	'tx_sitecedris_sw_bedrijven' => Array (
		'exclude' => 1,
		'l10n_mode' => 'exclude',
		'label' => 'LLL:EXT:site_cedris/locallang_db.xml:tt_news.tx_sitecedris_sw_bedrijven',
		'config' => Array (
			'type' => 'group',
			'internal_type' => 'db',
			'allowed' => 'fe_users',
			'size' => '5',
			'maxitems' => '99',
			'minitems' => '0',
			'show_thumbs' => '1',
			'wizards' => array(
				'suggest' => array(
					'type' => 'suggest'
				)
			)
		)
	),

	'tx_sitecedris_praktik_function' => Array (
		"exclude" => 1,
		"label" => "LLL:EXT:site_cedris/locallang_db.xml:tt_news.tx_sitecedris_praktik_function",
		'config' => array(
			'type' => 'input',
			'size' => '30',
			'max' => '32',
			'eval' => 'trim'
		),
	),
	'tx_sitecedris_praktik_phone' => Array (
		"exclude" => 1,
		"label" => "LLL:EXT:site_cedris/locallang_db.xml:tt_news.tx_sitecedris_praktik_phone",
		'config' => array(
			'type' => 'input',
			'size' => '30',
			'max' => '32',
			'eval' => 'trim'
		),
	),
	'tx_sitecedris_praktik_email' => Array (
		"exclude" => 1,
		"label" => "LLL:EXT:site_cedris/locallang_db.xml:tt_news.tx_sitecedris_praktik_email",
		'config' => array(
			'type' => 'input',
			'size' => '30',
			'max' => '32',
			'eval' => 'trim'
		),
	),

	'tx_sitecedris_praktik_title' => Array (
		"exclude" => 1,
		"label" => "LLL:EXT:site_cedris/locallang_db.xml:tt_news.tx_sitecedris_praktik_title",
		'config' => array(
			'type' => 'text',
			'size' => '30',
			'max' => '32',
			'eval' => 'trim'
		),
	),
	'tx_sitecedris_praktik_logo' => Array (
		'exclude' => 1,
		'l10n_mode' => $l10n_mode_image,
		"label" => "LLL:EXT:site_cedris/locallang_db.xml:tt_news.tx_sitecedris_praktik_logo",
		'config' => Array (
			'type' => 'group',
			'internal_type' => 'file',
			'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
			'max_size' => '1000',
			'uploadfolder' => 'uploads/pics',
			'show_thumbs' => '1',
			'size' => 3,
			'autoSizeMax' => 15,
			'maxitems' => '99',
			'minitems' => '0'
		)
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns("tt_news", $tempColumns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes("tt_news", "tx_sitecedris_background,tx_sitecedris_background_links,tx_sitecedris_sw_arbeidsmarktregio,tx_sitecedris_sw_bedrijven", '', 'after:related');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes("tt_news", "tx_sitecedris_praktik_function,tx_sitecedris_praktik_phone,tx_sitecedris_praktik_email,tx_sitecedris_author,tx_sitecedris_praktik_title,tx_sitecedris_praktik_logo", '', 'after:author');
// dodajemy do palety 3, w ktorej jest author/copyright. patrz tt_news/tca
//\TYPO3\CMS\Core\Utility\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette("tt_news", "3", "tx_sitecedris_background,tx_sitecedris_background_links", 'after:related');


// custom tca fields
include_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'class.tx_sitecedris_TCAform.php');




/*

//moved to Configuration/TCA/Overrides

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
	)
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns("fe_users", $tempColumns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes("fe_users", "tx_sitecedris_sw_postadres,tx_sitecedris_sw_cole,tx_sitecedris_sw_provincie,tx_sitecedris_sw_arbeidsmarktregio", '', 'after:image');


*/


// adds new type of news "Badanie" (95)
//$GLOBALS['TCA']['tt_news']['columns']['type']['config']['items'][] = Array('LLL:EXT:site_cedris/locallang_db.xml:tt_news.type.news-badanie', 95);
//$GLOBALS['TCA']['tt_news']['ctrl']['typeicons']['95'] = $thisExtRelPath.'res/icons/icon_tt_news_ext_icon_extended.gif';
//$GLOBALS['TCA']['tt_news']['types']['95']['showitem'] = $GLOBALS['TCA']['tt_news']['types']['0']['showitem'];
  
//$TCA['tt_news']['types']['95']['showitem'] = t3lib_div::rmFromList('somefield', $GLOBALS['TCA']['tt_news']['types']['95']['showitem']);


