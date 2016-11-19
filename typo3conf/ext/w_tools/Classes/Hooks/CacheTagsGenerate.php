<?php

namespace WTP\WTools\Hooks;

/**
 * Hook to generate additional tags for saving with Caching Framework or r_memcached.
 * ts could be set for pages, to enable generating some predefined tags, like current feuser id
 * this ts could be updated with more user uids' tags commalist
 *
 * @author	Wolo Wolski <wolo.wolski@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_wtools
 */
class CacheTagsGenerate {

	protected $conf = [];


	// todo: move to CacheOperator

	// todo later: this should be reworked - it can't run on end hook, sometimes we need pivars or other values,
	// so it should be run from plugin context

	/**
	 * note: Because CF is disabled for ajax (no_cache in ts ajax page setup), this is used for now only for r_memcached
	 *
	 * @param array $tags
	 */
	function main(&$tags = []) {
		//debugster($tags);die();
		//die('hook CacheTagsGenerate executed');
		// get ext cf conf
		$this->conf = &$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wtools.']['caching_framework.'];
		//$extConf = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['w_tools']['caching_framework'];

		//debugster($this->conf);

		if (is_array($this->conf['additionalTags.']))
		foreach ($this->conf['additionalTags.'] as $tag => $values) {
			switch ($tag) {
				// no need to use this here, same functionality is built in r_memcached
				case 'user':
					$tags = array_merge($tags, $this->makeTags_user($values));
						// save generated tags to conf, may be useful to see it
					if (1) 			//	DEV)	// todo later: if works correctly, save only on dev
						$this->conf['additionalTags-debug-generated.']['feuser'] = implode(',', $tags);
					$GLOBALS['TSFE']->addCacheTags($tags);
					// can't set mc tags here, must generate them earlier, in plugin context, to get feusers uids. @see example (wmedl/Ajax.php)
					//$GLOBALS['tx_rmemcached']->addCustomTags( $tags );
					break;
				case 'section':
					$tags = array_merge($tags, $this->makeTags_section($values));
					// save generated tags to conf, may be useful to see it
					if (1) 			//	DEV)	// todo later: if works correctly, save only on dev
						$this->conf['additionalTags-debug-generated.']['section'] = implode(',', $tags);
					$GLOBALS['TSFE']->addCacheTags($tags);
					$GLOBALS['tx_rmemcached']->addCustomTags( $tags );
					break;
			}
		}

		//debugster($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wtools.']['caching_framework.']);

		//die('stop!!!');
		// could return tags array for use with r_memcached tags hook, but for consistency with CF
		// we use here addCustomTags, which basically results with the same
		return $tags;
	}


	// if called from tslib_fe contentPostProc hook
	public function cachingFramework(array &$params, \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController &$pObj) {
		return $this->main();
	}

	// if called from r_memcached tags hook
	public function rMemcached(&$tags, \tx_rmemcached $pObj)	{
		return $this->main($tags);
	}


	/**
	 * Returns commalist of tags for caching framework
	 * @param string $values commalist of values (feuser uids)
	 * @return array tags
	 */
	protected function makeTags_user($values)	{
		$tags = [];
		$valuesArray = [];
		if ($values)	$valuesArray = explode(',', $values);
		// avoid empty tags with feuser = 0
		if ($GLOBALS['TSFE']->fe_user->user['uid'])
			$valuesArray[] = $GLOBALS['TSFE']->fe_user->user['uid'];
		if (is_array($valuesArray) && count($valuesArray))
		foreach($valuesArray as $value)
			$tags[] = 'user_'.intval($value);
		return $tags;
	}

	/**
	 * Returns commalist of tags for caching framework
	 * @param string $values commalist of values (section names)
	 * @return array tags
	 */
	protected function makeTags_section($values)	{
		$tags = [];
		$valuesArray = explode(',', $values);
		if (is_array($valuesArray) && count($valuesArray))
			foreach($valuesArray as $value)
				$tags[] = 'section_'.$value;

		return $tags;
	}
}


?>