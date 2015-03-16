<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

// Define the timestamp for the current run
// TODO: move to tx_devlog constructor (as static variables)

if (!$TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]['mstamp']) {
	$parts = explode(' ', microtime());
		// Timestamp with microseconds to make sure 2 log runs can always be distinguished
		// even when happening very close to one another
		// TODO: improve with microtime(true), but requires PHP > 5
	$TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]['mstamp'] = (string)$parts[1] . (string)intval((float)$parts[0] * 10000.0);
		// Normal timestamp
	$TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]['tstamp'] = $parts[1];
}

// Register the logging method with the appropriate hook

$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_div.php']['devLog'][$_EXTKEY] = 'EXT:'.$_EXTKEY.'/class.tx_devlog.php:&tx_devlog->devLog';
?>