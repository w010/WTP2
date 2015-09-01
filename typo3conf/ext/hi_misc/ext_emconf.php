<?php

########################################################################
# Extension Manager/Repository config file for ext "hi_misc".
#
# Auto generated 05-03-2011 14:47
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Typo3 misc funcs / plugins',
	'description' => 'Misc plugins and functions for TYPO3...',
	'category' => 'misc',
	'author' => 'ryży,wolo',
	'author_email' => 'marcin@ryzycki.pl,wolo.wolski@gmail.com',
	'shy' => '',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => 'pages,tt_content',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.1.20',
	'constraints' => array(
		'depends' => array(
			'php' => '5.3.0-5.5.99',
			'typo3' => '6.2.0-7.9.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:8:{s:9:"ChangeLog";s:4:"9efc";s:23:"class.tx_himisc_div.php";s:4:"fd6e";s:27:"class.tx_himisc_realurl.php";s:4:"2d65";s:12:"ext_icon.gif";s:4:"a768";s:17:"ext_localconf.php";s:4:"dba4";s:14:"ext_tables.php";s:4:"03d8";s:14:"ext_tables.sql";s:4:"6221";s:24:"ext_typoscript_setup.txt";s:4:"aa69";}',
	'suggests' => array(
	),
);

?>