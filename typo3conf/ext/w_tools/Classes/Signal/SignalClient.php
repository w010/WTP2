<?php
/**
 * wolo.pl '.' studio 2016
 * w_tools
 */

namespace WTP\WTools\Signal;


/**
 * Interface SignalClient
 *
 * Signal client is an object instantiated using ext conf 'signalClients' key, they waits for specified signal to trigger it or are triggered immediately when instantiated and such signal already exists
 * Triggering is done by SignalOperator
 */
interface SignalClient {

	// todo: visibility. 	cannot define this in interface
	/*public $signalName;
	public $userFunc;*/
	
	
	public function trigger($conf = []);

	
}



?>