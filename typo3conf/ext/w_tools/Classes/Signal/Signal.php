<?php
/**
 * wolo.pl '.' studio 2016
 * w_tools
 */

namespace WTP\WTools\Signal;


/**
 * Class old name would be: tx_wtools_signal
 *
 * Signal operator
 * it's job is to collect signals and possibly trigger it's
 *
*/
class Signal {

	protected $name = '';
	protected $value = null;
	protected $CallerObjectName;


	/**
	 * @var string set only on debug mode (only on DEV, if not forced)
	 */
	public $_debugInfo = [];
	protected $_backtrace = [];


	/**
	 * @param string $name
	 * @param object $Caller
	 * @param Signal $value
	 */
	public function __construct($name, &$Caller, $value = null) {
		$this->name = $name;
		$this->value = $value;
		$this->callerObjectClass = get_class($Caller);
		if (SignalOperator::DEBUG) 	{
			$this->_backtrace = $this->getSignalBacktrace();
			// because they are protected, thus not visible on debug. make a visible copy on DEV
			$this->_debugInfo = [
				'Name' => $this->name,
				'Value' => $this->value,
				'Backtrace' => $this->_backtrace
			];
		}
	}

	/**
	 * If SignalOperator::DEBUG = true (usually set to DEV const) signals contains clear backtrace
	 * @return array
	 */
	protected function getSignalBacktrace()	{
		// you can always use this:
		//echo "<pre>";	debug_print_backtrace(0, 7); //die(); // try

		// borrowed from ext:beko_debugster - thanks for this legendary extension ;)
		$backtrace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );	// important - save memory
		for ($i=  4  ;$i<  8  ;$i++)	{
			$bt = $backtrace[$i];
			if (!$bt) { break; }
			$hops[] = sprintf('%s -> %s()  : %d ', $bt['class'], $bt['function'], $bt['line']);
		}
		//debugster($hops);
		//debugster($backtrace);
		return $hops;
	}


}



?>