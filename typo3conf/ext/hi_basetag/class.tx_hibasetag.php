<?php

class tx_hibasetag {
	
	public function setConfig() {
		$baseUrl = $this->getBaseUrl();

		if ('auto' == strtolower($GLOBALS['TSFE']->config['config']['baseURL'])) {
			$GLOBALS['TSFE']->baseUrl = $baseUrl;
			$GLOBALS['TSFE']->config['config']['baseURL'] = $baseUrl;
		}
		
		// the same value as baseUrl - all links will be prefixed with this value
		if ('auto' == strtolower($GLOBALS['TSFE']->config['config']['absRefPrefix'])) {
			$GLOBALS['TSFE']->absRefPrefix = $baseUrl;
			$GLOBALS['TSFE']->config['config']['absRefPrefix'] = $baseUrl;
		}
	}
	
	public function setBaseUrl() {
		//debugster($GLOBALS['TSFE']->config['config']['baseURL']);
		if ('auto' == strtolower($GLOBALS['TSFE']->config['config']['baseURL'])) {
			$baseUrl = $this->getBaseUrl();
		//	debugster($baseUrl);
		//	die();
			$GLOBALS['TSFE']->content = str_replace('base href="auto"', 'base href="'.$baseUrl.'"', $GLOBALS['TSFE']->content);
		}
	}
	
	public function getBaseUrl() {
		$baseUrl =  \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_SSL') ? 'https://' : 'http://';
		$baseUrl .= \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('HTTP_HOST').($GLOBALS['TSFE']->absRefPrefix?$GLOBALS['TSFE']->absRefPrefix:'/');
		return $baseUrl;
	}
}