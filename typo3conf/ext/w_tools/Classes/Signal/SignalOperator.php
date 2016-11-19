<?php
/**
 * wolo.pl '.' studio 2016
 * w_tools
 */

namespace WTP\WTools\Signal;
use TYPO3\CMS\Core\Utility\GeneralUtility;


/**
 * Class tx_wtools_signalOperator  /  now: WTP\WTools\Signal\SignalOperator
 *
 * Signal operator
 * it's job is to collect signals and possibly trigger it's callbacks if registered, (or final check on plugin exit, to call signals sent after
 */
class SignalOperator {

	/**
	 * only static calls
	 */
	protected function __construct()	{}

	/**
	 * @var array of namespaces of signals (\WTP\WTools\Signal\Signal objects)
	 */
	static protected $signals = [ 'default'=>[] ];

	const DEBUG = TRUE; // = DEV;

	/**
	 * @var array - queue, arrays with 'SignalName' => SignalClientConf
	 */
	static protected $queue = [];


	/**
	 * @param string $signalName  name or namespace|name. default space is "default"
	 * @param object $Caller  mostly for trace / debug purposes, but maybe in future will be checked in some cases
	 * @param mixed  $params  optional - is this helpful for something, that we provide a value?
	 * @return bool
	 */
	static public function sendSignal($signalName, $Caller, &$params = null)	{

		/**
		 * @var \Tx_WTools_Mvc_Pibase $pObj
		 */
		$pObj = &\WTP\WTools\Registry::Cell('wtools', 'pi1');

			$pObj->addDebug('* SIGNAL sent: '.$signalName);


		// add to given namespace
		if (2 >= count($signalNameParts = explode(':', $signalName))) {
			self::$signals[ $signalNameParts[0] ][ $signalNameParts[1] ] =
					//$signal = GeneralUtility::makeInstance('\WTP\WTools\Signal\Signal', $signalNameParts[1], $Caller, ['uid'=>$value]);
					$signal = GeneralUtility::makeInstance('\WTP\WTools\Signal\Signal', $signalNameParts[1], $Caller, $params);
		}
		// or add to default
		else	{
			self::$signals['default'][$signalName] =
					$signal = GeneralUtility::makeInstance('\WTP\WTools\Signal\Signal', $signalName, $Caller, $params);
		}

		// TRIGGER, if signal has queue of clients awaiting for it
		if ($queue = &self::getQueueForSignal($signalName))	{
				debugster($queue);
				self::getPobj()->addDebug('-- SIGNAL: process queue for: '.$signalName);

				if (is_array($queue))
				foreach ($queue as $key => $signalClientConf)	{
					if (!$queue[$key]['triggered']) {
						$SignalClientResponse = GeneralUtility::callUserFunction($signalClientConf['userFunc'], $params, self::getPobj());
						// mark it as already executed
						$queue[$key]['triggered'] = TRUE;
						self::getPobj()->addDebug('---- SIGNAL: Triggered SignalClient: ' . $signalClientConf['userFunc'] . ', result: ' . $SignalClientResponse);
					}
				}
		};
		//$queue = &self::getQueueForSignal($signalName);
		//debugster($queue);

		return true;
	}

	/**
	 * @param string $namespace
	 * @param bool   $_debug_getAll [for debug purposes only]
	 * @return array [or array of namespaces]
	 */
	static public function getSignals($namespace = 'default', $_debug_getAll = false)	{
		if (DEV && $_debug_getAll)
			return self::$signals;
		return self::$signals[ $namespace ];
	}


	/**
	 * @param string $signalName	"namespace:name  OR  name
	 * @return Signal
	 */
	static public function getSignal($signalName)	{
		if (2 >= count($signalNameParts = explode(':', $signalName))) {
			$namespace = $signalNameParts[0];
			$signalName = $signalNameParts[1];
		}
		// should be done better.. but not now
		if (!$signalName)	{
			$signalName = $namespace;
			$namespace = 'default';
		}
		/*debugster($signalName);
		debugster($namespace);
		debugster(self::$signals);*/

		return self::$signals[ $namespace ][ $signalName ];
	}



			/**
			 * Register client on signal queue
			 * check if the signal is already sent. If so, trigger it and mark as "triggered" + "delayed"
			 *
			 * @param string $signalName "namespace:signalName"
			 * @param array $clientConf
			 */
			static public function enqueueSignalClient($signalName, $clientConf)	{
				self::$queue[$signalName][] = $clientConf;

					//debugster(self::$queue);

					// if already sent such signal, trigger the client
					if (self::getSignal($signalName))	{
						// self::getPobj()->addDebug('client enqueued for existing signal, trigger immediately');
						die('enqueue client: SIGNAL already sent! trigger it, as DELAYED');
						//$queue =
						//$SignalClientResponse = GeneralUtility::callUserFunction($signalClientConf['userFunc'], $value);

						self::getPobj()->addDebug('--- SIGNAL: enqueue Client. Signal already sent! Triggered as DELAYED, SignalClient: '.$signalClientConf['userFunc']);
						// finish if needed
						$queue[$key]['triggered'] = true;
						$queue[$key]['delayed'] = true;	// it means it runs when client was enqueued, not when signal was sent - the signal was waiting for client. may be useful later
					}
			}


	/**
	 * Enqueue all configured clients to wait for specified signal
	 * @param array $clientsConf
	 */
	static public function enqueueConfiguredSignalClients($clientsConf)	{
		// add signalClients to queue as conf array
		// iterate signals
		if (is_array($clientsConf))
		foreach($clientsConf as $signalName => $conf)	{
			//debugster($signalName);
			// iterate clients for this signal
			if (is_array($conf))
			foreach($conf as $clientConf) {
				//debugster($clientConf);
				self::enqueueSignalClient($signalName, $clientConf);
			}
		}
	}

	/**
	 * get whole Queue conf array
	 *
	 * @param string $signalName
	 * @return array
	 */
	static protected function &getQueueForSignal($signalName)	{
		return self::$queue[$signalName];
	}



	/**
	 * Get parent / plugin instance
	 * @return \Tx_WTools_Mvc_Pibase (it has addDebug method)
	 */
	static public function &getPobj()	{
		return \WTP\WTools\Registry::Cell('wtools', 'pi1');
	}
}



?>