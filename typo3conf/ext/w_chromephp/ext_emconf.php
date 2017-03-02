<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "w_chromephp".
 *
 * Auto generated 03-02-2013 00:36
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
	'title' => 'ChromePHP / Chrome Logger - debug in Chrome console',
	'description' => 'Chrome\'s Developer tools Console php debug. Short call - just use cp(var);. ChromePHP globally included in TYPO. Respects DevIpMask. Needs Chrome Logger ext for Chrome. @See README for details. @See https://craig.is/writing/chrome-logger',
	'category' => 'plugin',
	'author' => 'wolo.pl \'.\' studio',
	'author_email' => 'wolo.wolski@gmail.com',
	'shy' => '',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.2.1',
	'constraints' => [
		'depends' => [
			'typo3' => '4.5.0-7.6.99',
		],
		'conflicts' => [
		],
		'suggests' => [
		],
	],
	'_md5_values_when_last_written' => 'a:10:{s:9:"ChangeLog";s:4:"99b7";s:23:"class.tx_wchromephp.php";s:4:"2be4";s:21:"ext_conf_template.txt";s:4:"ee08";s:12:"ext_icon.gif";s:4:"1bdc";s:17:"ext_localconf.php";s:4:"23b2";s:14:"ext_tables.php";s:4:"e552";s:10:"README.txt";s:4:"7bba";s:23:"ChromePhp/ChromePhp.php";s:4:"f71a";s:23:"ChromePhp/composer.json";s:4:"baaa";s:16:"ChromePhp/README";s:4:"248a";}',
	'suggests' => [
	],
];

?>