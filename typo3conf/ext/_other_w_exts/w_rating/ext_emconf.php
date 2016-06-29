<?php

########################################################################
# Extension Manager/Repository config file for ext "w_rating".
#
# Auto generated 16-06-2012 03:28
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Rating of records',
	'description' => 'Simple records star-like rating, with jquery ajax. Default voted is table = "pages" and uid = current page. You can set it to any table and uid, use it standalone or easy in own markers for extensions like tt_news. Cookie and IP protected. Any number of votings on one page.',
	'category' => 'fe',
	'author' => 'wolo.pl',
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
	'constraints' => array(
		'depends' => array(
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:15:{s:9:"ChangeLog";s:4:"69ff";s:10:"README.txt";s:4:"bf8e";s:12:"ext_icon.gif";s:4:"1bdc";s:17:"ext_localconf.php";s:4:"4db9";s:14:"ext_tables.php";s:4:"43e4";s:14:"ext_tables.sql";s:4:"b67b";s:24:"ext_typoscript_setup.txt";s:4:"bbc7";s:24:"icon_tx_wrating_vote.gif";s:4:"475a";s:16:"locallang_db.xml";s:4:"7b56";s:7:"tca.php";s:4:"06c4";s:18:"tx_wrating_eid.php";s:4:"156e";s:19:"doc/wizard_form.dat";s:4:"fdac";s:20:"doc/wizard_form.html";s:4:"eeb2";s:28:"pi1/class.tx_wrating_pi1.php";s:4:"e40a";s:17:"pi1/locallang.xml";s:4:"e589";}',
	'suggests' => array(
	),
);

?>