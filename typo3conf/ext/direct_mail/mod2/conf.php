<?php

	// DO NOT REMOVE OR CHANGE THESE 3 LINES:
define('TYPO3_MOD_PATH', '../typo3conf/ext/direct_mail/mod2/');
$BACK_PATH='../../../../typo3/';
$MCONF['name'] = 'txdirectmailM1_txdirectmailM2';

$MLANG['default']['tabs_images']['tab'] = 'mod_icon.gif';
$MLANG['default']['ll_ref'] = 'LLL:EXT:direct_mail/mod2/locallang_mod.xml';

$MCONF['access'] = 'user,group';
$MCONF['script']='index.php';

$MCONF['workspaces'] = 'online'
?>