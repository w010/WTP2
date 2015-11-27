<?php

// It's perfectly possible to work without that file by not defining it at all or unsetting existing definitions.
// Usage of that file is deprecated since TYPO3 CMS 6.2. Use the "extension method" described above instead.
// http://docs.typo3.org/typo3cms/TCAReference/ExtendingTca/StoringChanges/Index.html


// tt_news folder icon patch
// https://forge.typo3.org/issues/62648

$TCA['pages']['columns']['module']['config']['items'][] = array('tt-News', 'tt_news', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('tt_news').'res/gfx/ext_icon_ttnews_folder.gif');
\TYPO3\CMS\Backend\Sprite\SpriteManager::addTcaTypeIcon('pages', 'contains-tt_news', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('tt_news') . 'res/gfx/ext_icon_ttnews_folder.gif');


// add rte button inserttag - doesnt work!
//$TCA['tt_news']['types']['0']['showitem'] = 'hidden, type;;;;1-1-1,title;;;;2-2-2,short,bodytext;;2;richtext:rte_transform[flag=rte_enabled|mode=ts|cut|copy|paste|inserttag];4-4-4,			--div--;LLL:EXT:tt_news/locallang_tca.xml:tt_news.tabs.special, datetime;;;;2-2-2,archivedate,author;;3;; ;;;;2-2-2,				keywords;;;;2-2-2,sys_language_uid;;1;;3-3-3,			--div--;LLL:EXT:tt_news/locallang_tca.xml:tt_news.tabs.media, image;;;;1-1-1,imagecaption;;5;;,links;;;;2-2-2,news_files;;;;4-4-4,			--div--;LLL:EXT:tt_news/locallang_tca.xml:tt_news.tabs.catAndRels, category;;;;3-3-3,related;;;;3-3-3, xxx_links,			--div--;LLL:EXT:tt_news/locallang_tca.xml:tt_news.tabs.access, starttime,endtime,fe_group,editlock,			--div--;LLL:EXT:tt_news/locallang_tca.xml:tt_news.tabs.extended';


// where should it be placed, if not here?
//$TBE_STYLES['logo_login'] = '/images/default/logo.png';

//$GLOBALS['TCA']['tt_news_cat']['columns']['image']['config']['max_size'] = 3*1024;
//$GLOBALS['TCA']['be_users']['ctrl']['adminOnly'] = 0;

