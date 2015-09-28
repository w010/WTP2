<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

/**
 * WTP2 v2.62.4
 * Wolo TYPO3 Pack
 * 2015-09
 * wolo.pl '.' studio
 *
 * (version convention explain: vA.B.C means: A=2 because WTP2,  B=nn is like T3-branch n.n,  C is WTP version itself.)
 */

// global statics define, do not touch

define('LOCAL', intval(
	preg_match('/(localhost)/', $_SERVER['HTTP_HOST'])
));

define('TESTDEVS', intval(
	preg_match('/(test-devs\.)/', $_SERVER['HTTP_HOST'])
));

define('DEV', intval(
	preg_match('/(dev\.)/', $_SERVER['HTTP_HOST'])
	||	TESTDEVS
	||	LOCAL
));

define('CLI', intval(
	PHP_SAPI == 'cli'
));

putenv("DEV=".intval(DEV)); // make them available from TS: [globalVar= ENV:DEV=1]
putenv("TESTDEVS=".intval(TESTDEVS));
putenv("LOCAL=".intval(LOCAL));

// helps in styling in some cases
define('NO_DEBUG', (bool)$_GET['no_debug']);




// all DEV envs
if (DEV)	{
	$GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport'] = 'mbox';
	$GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport_mbox_file'] = PATH_site.'mails-out.txt';
}

// next environments should be after DEV, to overwrite own settings

// DEVS env - test-devs server. probably also in DEV mode - see define above and comment proper line if required
if (TESTDEVS)  {
	//$GLOBALS['TYPO3_CONF_VARS']['DB']['username'] = 'admin833_xx';
	//$GLOBALS['TYPO3_CONF_VARS']['DB']['password'] = '7DxxxaU';
	//$GLOBALS['TYPO3_CONF_VARS']['DB']['database'] = 'baza833_aaaa';
}

// only localhost. defined last to overwrite DEV settings - possible is also in dev mode
if (LOCAL)  {
	/*$GLOBALS['TYPO3_CONF_VARS']['DB']['host'] = '127.0.0.1';
	$GLOBALS['TYPO3_CONF_VARS']['DB']['username'] = 'admin833_';
	$GLOBALS['TYPO3_CONF_VARS']['DB']['password'] = '';
	$GLOBALS['TYPO3_CONF_VARS']['DB']['database'] = 'baza833_';*/

	$GLOBALS['TYPO3_CONF_VARS']['GFX']['im_path'] = 'D:\xampp\ImageMagick-6.8.1-9\\';	// for some reason two backslashes
	$GLOBALS['TYPO3_CONF_VARS']['GFX']['im_path_lzw'] = 'D:\xampp\ImageMagick-6.8.1-9\\';
	$GLOBALS['TYPO3_CONF_VARS']['GFX']['im_version_5'] = '6';
	$GLOBALS['TYPO3_CONF_VARS']['GFX']['colorspace'] = 'sRGB';

	$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['rsaauth'] = 'a:1:{s:18:"temporaryDirectory";s:13:"D:\xampp\tmp";}';

	$GLOBALS['TYPO3_CONF_VARS']['SYS']['binSetup'] = 'openssl=d:\xampp\apache\bin\openssl.exe';
}

// run exts only on this env
	//$GLOBALS['TYPO3_CONF_VARS']['EXT']['runtimeActivatedPackages'] = array('devlog');


$GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'] = (DEV?'DEV ':'') . (LOCAL?'LOCAL ':'') . (TESTDEVS?'TESTDEVS ':'') . $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'];
$GLOBALS['TYPO3_CONF_VARS']['SYS']['enableDeprecationLog'] = DEV?'file':false;
$GLOBALS['TYPO3_CONF_VARS']['SYS']['displayErrors'] = LOCAL? 1 : DEV? 2 : -1;
$GLOBALS['TYPO3_CONF_VARS']['SYS']['exceptionalErrors'] = E_ALL ^ E_STRICT ^ E_NOTICE ^ E_WARNING ^ E_USER_ERROR ^ E_USER_NOTICE ^ E_USER_WARNING;
$GLOBALS['TYPO3_CONF_VARS']['SYS']['sqlDebug'] = DEV ? 1 : 0;
$GLOBALS['TYPO3_CONF_VARS']['SYS']['devIPmask'] = NO_DEBUG? '' :LOCAL? '*' :implode(',', array(
	'all' => DEV?'*':'',
	'wolo-pzn' => '85.221.134.155',
));

// cli debug
if (CLI)	{
	#$GLOBALS['TYPO3_CONF_VARS']['SYS']['devIPmask'] = '*';
	#$GLOBALS['TYPO3_CONF_VARS']['SYS']['displayErrors'] = 2;
	#$GLOBALS['TYPO3_CONF_VARS']['SYS']['exceptionalErrors'] = E_ALL;
}

$GLOBALS['TYPO3_CONF_VARS']['BE']['installToolPassword'] = DEV ? md5('123') : md5('KD84nx(B34899c3C#@h&');
$GLOBALS['TYPO3_CONF_VARS']['BE']['lifetime'] = (LOCAL?7*24:3) * 3600;
$GLOBALS['TYPO3_CONF_VARS']['BE']['debug'] = DEV ? TRUE : TRUE;

$GLOBALS['TYPO3_CONF_VARS']['FE']['debug'] = DEV && NO_DEBUG==FALSE ? TRUE : FALSE;
$GLOBALS['TYPO3_CONF_VARS']['FE']['compressionDebugInfo'] = DEV ? true : false;
$GLOBALS['TYPO3_CONF_VARS']['FE']['lifetime'] = (LOCAL?7*24:3) * 3600;

// maintenance mode: set to 2 to not prevent cli/scheduler to work
//$GLOBALS['TYPO3_CONF_VARS']['BE']['adminOnly'] = 2;


// 404 page
$GLOBALS['TYPO3_CONF_VARS']['FE']['pageNotFound_handling'] = 'REDIRECT:/404.html';


	// not used in 6 anymore
	// $GLOBALS['TYPO3_CONF_VARS']['EXT']['extCache'] = $GLOBALS['TYPO3_CONF_VARS']['FE']['debug'] ? 0 : 3;
$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['beko_debugster'] = serialize(array('ip_mask'=>DEV?(NO_DEBUG?'':'*'):$GLOBALS['TYPO3_CONF_VARS']['SYS']['devIPmask'], 'steps_back'=>3, 'useDevIpMask' => DEV?1:1));
/*if (LOCAL)
	$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['beko_debugster'] = serialize(array('ip_mask'=>'*', 'steps_back'=>3, 'useDevIpMask' => 1));*/


// enable memcache in 6.2
/*
$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_pages']['backend'] = 'TYPO3\\CMS\\Core\\Cache\\Backend\\MemcachedBackend';
$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_pages']['options'] = array('servers' => array('localhost:11211'),);
$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_pagesection']['backend'] = 'TYPO3\\CMS\\Core\\Cache\\Backend\\MemcachedBackend';
$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_pagesection']['options'] = array('servers' => array('localhost:11211'),);
$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_hash']['backend'] = 'TYPO3\\CMS\\Core\\Cache\\Backend\\MemcachedBackend';
$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_hash']['options'] = array('servers' => array('localhost:11211'),);
/**/

/**
 * user func for ts condition for env static check
 * @param $env
 * @return bool
 */
/*function user_envCheck($env)    {
	if ( constant($env) )    {
			return TRUE;
	}

	return FALSE;
}
*/
?>