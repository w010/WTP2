<?php


// NOT USED FOR NOW

// maybe it will be a better idea to collect signals and enqueue clients on script end?


namespace WTP\WTools\Hooks; 
use WTP\WTools\Signal;


/**
 * Hook to generate additional tags for saving with Caching Framework
 * ts could be set for pages, to enable generating some predefined tags, like current feuser id
 * this ts could be updated with more user uids' tags commalist
 *
 * @author	Wolo Wolski <wolo.wolski@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_wtools
 */
class SignalOperatorFinalize {

	//protected $conf = [];

	/**
	 * (Without already run)
	 */
	function runAllEnqueuedClientsForSignals() {



		//debugster($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wtools.']['caching_framework.']);

		//die('stop!!!');
	}


}


?>