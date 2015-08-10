<?php

if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

	// Register jumpurl processing hook
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['checkDataSubmission'][]='EXT:'.$_EXTKEY.'/Classes/Checkjumpurl.php:&DirectMailTeam\DirectMail\Checkjumpurl';

	// unserializing the configuration so we can use it here:
$_EXTCONF = unserialize($_EXTCONF);

/**
 * Language of the cron task:
 */
$TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]['cron_language'] = $_EXTCONF['cron_language'] ? $_EXTCONF['cron_language'] : 'en';

/**
 * Number of messages sent per cycle of the cron task:
 */
$TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]['sendPerCycle'] = $_EXTCONF['sendPerCycle'] ? $_EXTCONF['sendPerCycle'] : 50;

/**
 * Default recipient field list:
 */
$TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]['defaultRecipFields'] = 'uid,name,title,email,phone,www,address,company,city,zip,country,fax,firstname';

/**
 * Additional DB fields of the recipient:
 */
$TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]['addRecipFields'] = $_EXTCONF['addRecipFields'];

/**
 * Admin email for sending the cronjob error message
 */
$TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]['adminEmail'] = $_EXTCONF['adminEmail'];

/**
 * Direct Mail send a notification every time a job starts or ends
 */
$TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]['notificationJob'] = $_EXTCONF['notificationJob'];

/**
 * Interval of the cronjob
 */
$TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]['cronInt'] = $_EXTCONF['cronInt'];

/**
 * Use HTTP to fetch contents
 */
$TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]['UseHttpToFetch'] = $_EXTCONF['UseHttpToFetch'];

/**
 * Enable the use of News plain text rendering hook:
 */
if ($_EXTCONF['enablePlainTextNews']) {
		// Register tt_news plain text processing hook
	$TYPO3_CONF_VARS['EXTCONF']['tt_news']['extraCodesHook'][] = 'DirectMailTeam\\DirectMail\\Hooks\\TtnewsPlaintextHook';
}

/**
 * Registering class to scheduler
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['DirectMailTeam\\DirectMail\\Scheduler\\DirectmailScheduler'] = array(
	'extension' => $_EXTKEY,
	'title' => 'Direct Mail: Mailing Queue',
	'description' => 'This task invokes dmailer in order to process queued messages.',
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['DirectMailTeam\\DirectMail\\Scheduler\\MailFromDraft'] = array(
	'extension'			=> $_EXTKEY,
	'title'				=> 'Direct Mail: Create Mail from Draft',
	'description' 		=> 'This task allows you to select a DirectMail draft that gets copied and then sent to the. This allows automatic (periodic) sending of the same TYPO3 page.',
	'additionalFields'	=> 'DirectMailTeam\\DirectMail\\Scheduler\\MailFromDraftAdditionalFields'
);


/**
 * added CLI
 */
$TYPO3_CONF_VARS['SC_OPTIONS']['GLOBAL']['cliKeys']['direct_mail'] = array('EXT:direct_mail/cli/cli_direct_mail.php','_CLI_direct_mail');
?>
