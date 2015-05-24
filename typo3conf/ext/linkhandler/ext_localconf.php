<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// Add linkhandler for "record"
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_content.php']['typolinkLinkHandler']['record'] = 'Aoe\\Linkhandler\\LinkHandler';

// Register hooks
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/rtehtmlarea/mod3/class.tx_rtehtmlarea_browse_links.php']['browseLinksHook'][] = 'Aoe\\Linkhandler\\Browser\\ElementBrowserHook';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/class.browse_links.php']['browseLinksHook'][] = 'Aoe\\Linkhandler\\Browser\\ElementBrowserHook';

// Register signal slots
/** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
$signalSlotDispatcher->connect('TYPO3\\CMS\\Core\\Database\\SoftReferenceIndex', 'getTypoLinkParts', 'Aoe\\Linkhandler\\SoftReferenceHandler', 'getTypoLinkParts', FALSE);
$signalSlotDispatcher->connect('TYPO3\\CMS\\Core\\Database\\SoftReferenceIndex', 'setTypoLinkPartsElement', 'Aoe\\Linkhandler\\SoftReferenceHandler', 'setTypoLinkPartsElement', FALSE);

// This hook is needed until https://review.typo3.org/27680/ is merged to
// open the correct tab in the link browser.
if (!\Aoe\Linkhandler\Utility\LegacyUtility::externalLinkFixIsImplemented()) {
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_parsehtml_proc.php']['modifyParams_LinksRte_PostProc'][] = 'Aoe\\Linkhandler\\RteParserHook';
}

$linkhandlerExtConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['linkhandler']);

if (
	is_array($linkhandlerExtConf)
	&& $linkhandlerExtConf['includeDefaultTsConfig']
) {
	if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('tt_news')) {
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
			<INCLUDE_TYPOSCRIPT: source="FILE: EXT:linkhandler/Configuration/TypoScript/tt_news/setup.txt">
			mod.tx_linkhandler.tx_tt_news_news < plugin.tx_linkhandler.tx_tt_news_news
			RTE.default.tx_linkhandler.tx_tt_news_news < plugin.tx_linkhandler.tx_tt_news_news
		');
	}

	if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('news')) {
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
			<INCLUDE_TYPOSCRIPT: source="FILE: EXT:linkhandler/Configuration/TypoScript/news/setup.txt">
			mod.tx_linkhandler.tx_news_news < plugin.tx_linkhandler.tx_news_news
			RTE.default.tx_linkhandler.tx_news_news < plugin.tx_linkhandler.tx_news_news
		');
	}
}

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['linkvalidator']['checkLinks']['tx_linkhandler'] = 'Aoe\\Linkhandler\\Linkvalidator\\LinkhandlerLinkType';

unset($linkhandlerExtConf);