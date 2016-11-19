<?php
/**
 * wolo.pl '.' studio 2016
 * Social plugin
 */
 
namespace WTP\WTools\Cache;

use WTP\WTools\Signal;

/**
 * @author	Wolo Wolski <wolo.wolski@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_wtools
 */
class CacheOperator	 implements Signal\SignalClient {

			//protected $conf = [];

	/**
	 * @var \Tx_WTools_Mvc_Pibase|null
	 */
	protected $pObj = null;


	public function __construct() {
		$this->pObj = \WTP\WTools\Registry::Cell('wtools', 'pi1');
	}

			// todo: is this needed / used?
			public function trigger($conf = []) {
				die('not used');
				//$this->conf = &$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wtools.']['caching_framework.'];
			}



	// TODO: tutaj params moze sie roznic, uid, useruid, itd. ujednolicic to
	/**
	 * Standard for other methods - get feuser tags based on uids given
	 *
	 * @param array $params
	 * @param bool $excludeCurrent - don't include current user in flush tags
	 * @return array
	 */
	protected function getTags_user(&$params, $excludeCurrent = FALSE) {
		$tags = [];
		$valuesArray = [];
		if (!$excludeCurrent  &&  $GLOBALS['TSFE']->fe_user->user['uid'])
			$valuesArray[] = $GLOBALS['TSFE']->fe_user->user['uid'];
		// interacted feuser uid is expected here
		if ($params['uid'])
			$valuesArray[] = intval($params['uid']);

		if (is_array($valuesArray) && count($valuesArray))
			foreach ($valuesArray as $value)
				$tags[] = 'user_' . intval($value);
		return $tags;
	}




	/**
	 * Flush caches friendslist-related pages
	 *
	 * @param mixed $params
	 * @return bool
	 */
	public function flushUserFriendsLists(&$params)	{
		//debugster($params);
		//debugster('flush user friends lists caches!');

		$tags = $this->getTags_user($params);

		//debugster($tags);

			// INFO:
			// Caching Framework is currently disabled on ajax calls, because ajax urls are not generated
			// with proper cHash, but built manually. This must be reworked in order to work this way
			// So for now cache ajax only using r_memcache

		// clear page caches with uid configured to pages contains friendslist
		// (with tags user_x, user_y)

			/*$cacheManager = GeneralUtility::makeInstance(CacheManager::class);
			$cacheManager->getCache('cache_core')->flush();*/

		// todo: clear same memcache tags

		$flush = false;

		$this->pObj->addDebug('--- SIGNAL: Cache->flushUserFriendsLists called, tags: '.implode(', ', $tags), 'debug', $flush ? 1 : 2);

		return $flush;
	}


	/**
	 * Flush caches Conversation related
	 *
	 * @param mixed $params
	 * @return bool
	 */
	public function flushConversation(&$params) {

		$tags = $this->getTags_user($params);
		$tags[] = 'section_messages';

		//debugster($tags);

		$flush = \ZC_clean::cleanTagsMatch('memcached', $tags);

		$this->pObj->addDebug('--- SIGNAL: Cache->flushConversation called, tags: '.implode(', ', $tags), 'debug', $flush ? 1 : 2);
		return $flush;
	}

	/**
	 * Flush caches Alerts related
	 *
	 * @param mixed $params
	 * @return bool
	 */
	public function flushAlerts(&$params)	{
		$tags = $this->getTags_user($params, true);	// only clear interacted users alerts, no need to refresh own now
		$tags[] = 'section_alerts';

		$flush = \ZC_clean::cleanTagsMatch('memcached', $tags);

		$this->pObj->addDebug('--- SIGNAL: Cache->flushAlerts called, tags: '.implode(', ', $tags), 'debug', $flush ? 1 : 2);
	}
}


?>