<?php
// WTP DUMP/BACKUP TOOL FOR TYPO3 - wolo.pl '.' studio
// 2013-2017

if (!defined('TYPO3_MODE'))
	die('no access');



// options for this script operation
$optionsCustom = [
	// script only displays generated command line, but doesn't exec it
	'dontExecCommands' => defined('LOCAL') && LOCAL ? 0 :   0,

	// exec commands, but don't show them on PUB
	'dontShowCommands' => defined('LOCAL') && LOCAL ? 0 :   !DEV?1: 0,

	//'defaultOmitTables' => [],

	'docker' => INSTANCE_CONTEXT == 'local-docker' ? 'true' : false,

	'docker_containerSql' => INSTANCE_CONTEXT == 'local-docker' ?  'berglanddev_mysql_1'  : ''
];



// here you may hardcode db access data if not using typo3 config

/*
$typo_db = 'baza129_xx';
$typo_db_host = 'xx.m.tld.pl';
$typo_db_host = 'localhost';
$typo_db_password = '';
$typo_db_username = 'admin129xx';
*/
