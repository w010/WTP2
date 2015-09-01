<?php

class tx_himisc_div {

	/**
	 * Easily print TimeTracking data
	 *
	 * How to use it:
	 * put somewhere in typo3conf/localconf.php
	 * $TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['hook_eofe'][1001] = 'EXT:hi_misc/class.tx_himisc_div.php:tx_himisc_div->printTT';
	 *
	 * @return void
	 */
	public function printTT() {
		$content  = $GLOBALS['TT']->printTSlog();
		if (!$content) return $content;

		$content = str_replace(
			'</table>',
			'<tr><td colspan="2" align="right">Total parsetime:</td><td>'.$GLOBALS['TSFE']->scriptParseTime.'</td><td></td></tr></table>',
			$content
		);

		echo $content;
	}


	/**
	 * Gets <BODY> tag with classes mainpage|subpage level-X page-UID
	 * Used in TS: page.bodyTagCObject
	 *
	 * @param $p
	 * @param $conf
	 * @return string
	 */
	public function getBodyTag($p, $conf) {
		//debugster($GLOBALS['TSFE']->rootLine);
		$classes = array();

		foreach ($GLOBALS['TSFE']->rootLine as $k=>$v) {
			if (!$classes["level"]) {
				if (count($GLOBALS['TSFE']->rootLine) > 1)
					$classes['level'] = 'level-sub';
				else
					$classes["level"] = "level-main";
			}
			if (!$classes['TO']) {
				$TO = $GLOBALS['TSFE']->register['tx_himisc_div.templavoila.to'];
				$classes['TO'] = $conf['layoutMapping.'][$TO] ? $conf['layoutMapping.'][$TO] : "lay-$TO";
			}
		}

		$lang = $GLOBALS['TSFE']->config['config']['language'];
		$classes['lang-code'] = $lang ? $lang : 'ts-config-language-value';

		if (($pageCssClass = $GLOBALS['TSFE']->rootLine[count($GLOBALS['TSFE']->rootLine)-1]['tx_himisc_cssclass'])) {
			$classes['page-class'] = $pageCssClass;
		}
		if (DEV)    {
			$classes['dev'] = 'dev';
		}
		if (LOCAL)    {
			$classes['local'] = 'local';
		}

		$id = 'page-'.$GLOBALS['TSFE']->id;
		$classes = $this->getBodyTagAdditionalClasses($classes, $conf);
		$params = array();

		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['hi_misc']['getBodyTag-postProc']))	{
			$_params = array( 'id' => &$id, 'classes' => &$classes, 'params'=>&$params, 'conf'=>$conf );
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['hi_misc']['getBodyTag-postProc'] as $_funcRef) {
				\TYPO3\CMS\Core\Utility\GeneralUtility::callUserFunction($_funcRef, $_params, $this, '');
			}
		}

//		debugster($GLOBALS['TSFE']->register);
		//debugster($classes);
		return '<body id="'.$id.'" class="'.implode(' ',$classes).'" '.implode(' ',$params).'>';
	}

	/**
	 * Try to read additional <body> classes from TS config
	 *
	 * Example usage:
	 *
	 * page.bodyTagCObject.additionalClasses = myClass,myClass2
	 * OR
	 * page.bodyTagCObject.additionalClasses {
	 *    10 = myClass
	 *    20 = my2ndClass
	 * }
	 *
	 * @param array $classes
	 * @param       $conf
	 * @return string modified $classes
	 */
	protected function getBodyTagAdditionalClasses($classes, $conf) {
		$conf = $GLOBALS['TSFE']->tmpl->setup['page.']['bodyTagCObject.'];

		// try to read additionalClasses (coma-separated string) or additionalClasses. (array)
		$arr = is_array($conf['additionalClasses.'])
			? $conf['additionalClasses.']
			: ($conf['additionalClasses'] ? explode(',', $conf['additionalClasses']) : array());

		foreach ($arr as $k=>$v) $classes[$k] .= $v;

		return $classes;
	}

	/**
	 * Called from hook ['tslib/class.tslib_fe.php']['determineId-PostProc'], when the rootLine is already redy
	 */
	public function storeTemplaVoilaTO() {
		$GLOBALS['TSFE']->register['tx_himisc_div.templavoila.to'] = $this->getCurrentTemplaVoilaTO();
	}

	public function getCurrentTemplaVoilaTO() {
		reset($GLOBALS['TSFE']->rootLine);

		foreach ($GLOBALS['TSFE']->rootLine as $k=>$v) {
			$currentLevel = $currentLevel === null ? $k : $currentLevel;

			if ((($TO = intval($v['tx_templavoila_next_to'])) && $k != $currentLevel ) ||
				($TO = intval($v['tx_templavoila_to']))) {
					return $TO;
			}
		}
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/hi_misc/class.tx_himisc_div.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/hi_misc/class.tx_himisc_div.php']);
}