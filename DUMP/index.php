<?php

// WTP DUMP/BACKUP TOOL - wolo.pl '.' studio
// v0.8
// 2015
//
// dump / zapalniczka73
// you should change default password

// WARNING! THIS SCRIPT IS FOR PRIVATE DEV USE ONLY, DO NOT PUT IT ON PUBLIC!
// IT DOES LOW LEVEL FILE OPERATIONS AND HAS NO USER-INPUT SECURITY CHECK.
// IF THIS IS YOUR SITE AND RUNNING IN PUBLIC/PRODUCTION ENVIRONMENT AND YOU NOT SURE IF THE FILE HAS TO BE HERE, PLEASE DELETE IT

error_reporting(E_ALL ^ E_NOTICE);

define('PATH_thisScript', str_replace('//', '/', str_replace('\\', '/',
	(PHP_SAPI == 'fpm-fcgi' || PHP_SAPI == 'cgi' || PHP_SAPI == 'isapi' || PHP_SAPI == 'cgi-fcgi') &&
	($_SERVER['ORIG_PATH_TRANSLATED'] ? $_SERVER['ORIG_PATH_TRANSLATED'] : $_SERVER['PATH_TRANSLATED']) ?
	($_SERVER['ORIG_PATH_TRANSLATED'] ? $_SERVER['ORIG_PATH_TRANSLATED'] : $_SERVER['PATH_TRANSLATED']) :
	($_SERVER['ORIG_SCRIPT_FILENAME'] ? $_SERVER['ORIG_SCRIPT_FILENAME'] : $_SERVER['SCRIPT_FILENAME']))));

define('PATH_site', dirname(PATH_thisScript).'/');
define('PATH_work', realpath(PATH_site.'../').'/');
define('TYPO3_MODE', 'BE');

if (file_exists(PATH_work.'typo3conf/LocalConfiguration.php'))	{
	define('V6', true);
	$GLOBALS['TYPO3_CONF_VARS'] = require_once(PATH_work.'typo3conf/LocalConfiguration.php');
	include_once(PATH_work.'typo3conf/AdditionalConfiguration.php');
	//$localconf = array_replace_recursive($localconf, $additionalLocalconf);
	//var_dump($GLOBALS['TYPO3_CONF_VARS']);
	$typo_db_username = $GLOBALS['TYPO3_CONF_VARS']['DB']['username'];
	$typo_db_password = $GLOBALS['TYPO3_CONF_VARS']['DB']['password'];
	$typo_db_host = $GLOBALS['TYPO3_CONF_VARS']['DB']['host'];
	$typo_db = $GLOBALS['TYPO3_CONF_VARS']['DB']['database'];
}
else /**/
	include_once(PATH_work.'typo3conf/localconf.php');
	//require_once(PATH_work.'typo3conf/localConfiguration.php');

/*var_dump(LOCAL);
var_dump(DEV);
var_dump(DEV2);
var_dump(DEV47);
var_dump(DEV62);*/


// options for this script operation
// old type array to be compatible with older installations
$options = array(
	// script only displays generated command line, but doesn't exec it
	'dontExecCommands' => LOCAL ? 0 :   1,

	// exec commands, but don't show them
	'dontShowCommands' => LOCAL ? 0 :   0,

	// 'password'
	 'version' => '0.8',
);


//if (!DEV)
//	die('access denied');

// you may here hardcode db data if not using t3 config 
/*		$typo_db = 'baza1298_petrol';
		$typo_db_host = '1298.m.tld.pl';
		$typo_db_host = 'localhost';
		$typo_db_password = '';
		$typo_db_username = 'admin1298_petrol';
*/

// remember that undefined constants returns true / string with its name - should use defined()

//print '<h5>running on '.(DEV62?'DEV62':(DEV2?'DEV2':(DEV?'DEV':'PUBLIC !!!!'))).(LOCAL?' LOCAL':'').'</h5>';
print '<h5>running on '. (DEV?'DEV':'<span class="error">PUBLIC !!!!</span>') . (LOCAL?' LOCAL':'').'</h5>';


// define some global variables

// message/error to show
$msg = '';
// stored commands to show and/or execute
$cmds = '';

if (!$_POST['name'])    list($projectNamePredict) = preg_split('@\.@', $_SERVER['HTTP_HOST']);

main();


function main()	{

	global $options;

	global $typo_db_username;
	global $typo_db_password;
	global $typo_db_host;
	global $typo_db;

	if (!$typo_db_username || !$typo_db_password || !$typo_db_host || !$typo_db)
		msg('CHECK DATABASE CONFIG. Looks like some authorization data is missed. Check localconf');

	$projectName = 		$_POST['name'];
	$projectVersion = 	$_POST['v'];
	$action = 			$_POST['action'];
	$dbFilename = 		$_POST['dbFilename']?$_POST['dbFilename']:$_POST['dbFilenameSel'];

	//$projectDirectory = $projectName?$projectName:'public_html';

	/*if (!is_dir(PATH_work.$projectDirectory.'/'))	{
		msg('error: no "'.$projectDirectory.'" directory found in PATH_work (not running from root?)');
		return;
	}*/

	print "database name: ".$typo_db."<br>";

	if (!paramsRequiredPass(array('action'=>$action))) return;


	// RUN
	switch ($action) {

		// BACKUP FILESYSTEM
		case 'backup':
			$backupDir = PATH_work.'../'.$projectName.'_backup'.time().'/';
			exec_control('mkdir '.$backupDir);
			echr('mkdir '.$backupDir);
			exec_control('cp -R '.PATH_work.'/* '.$backupDir);
			echr('cp -R '.PATH_work.'/* '.$backupDir);
			break;


	// EMPTY specified backup folder. use this, if you know why to delete a backup.
		case 'backupclean':
			if (!is_set($_POST['backupdir']))
				die('error: backup remove - directory namepart not given');
			$backupDir = 'backup'.$_POST['backupdir'];
			die('backup remove disabled');
		//	exec_control ('rm -R '.$backupDir.'/*');
			echr ('rm -R '.$backupDir.'/* [DISABLED FOR NOW]');
			break;

	// DUMP EXPORT SITE FILES AND DATABASE
		case 'dump':
		case 'quickdump':
		case 'databasedump':
			if (!paramsRequiredPass(array('projectName'=>$projectName, 'projectVersion'=>$projectVersion)))	return;
			$dumpFilename = str_replace(' ', '_', $projectName);

			if ($action == 'quickdump')	{
				/*exec_control ('tar -zcf '.PATH_work.'DUMP/'.$dumpFilename.'-v'.$projectVersion.'.tgz ./../* --exclude="typo3temp" --exclude="DUMP" --exclude="uploads" -exclude="typo3_src-*"  ');
				echr ('tar -zcf '.PATH_work.'DUMP/'.$dumpFilename.'-v'.$projectVersion.'.tgz ./../* --exclude="typo3temp" --exclude="DUMP" --exclude="uploads" -exclude="typo3_src-*"  ');*/
				
				//exec_control ('tar -zcf '.PATH_work.'DUMP/'.$dumpFilename.'-v'.$projectVersion.'.tgz ./../typo3conf ./../fileadmin --exclude="fileadmin/contents"  ');
				exec_control ('tar -zcf '.$dumpFilename.'-v'.$projectVersion.'.tgz ./../typo3conf ./../fileadmin --exclude="fileadmin/contents"  ');
				echr ('tar -zcf '.$dumpFilename.'-v'.$projectVersion.'.tgz ./../typo3conf ./../fileadmin --exclude="fileadmin/contents"  ');
				
				// working on win, check on lin

			} else if ($action == 'databasedump')	{
				// do nothing, don't dump filesystem
			} else	{
				exec_control ('tar -zcf '.PATH_work.'DUMP/'.$dumpFilename.'-v'.$projectVersion.'.tgz ./../* --exclude="DUMP" ');
				echr ('tar -zcf '.PATH_work.'DUMP/'.$dumpFilename.'-v'.$projectVersion.'.tgz ./../* --exclude="DUMP" ');
			}
			
			if (LOCAL)	{
				//d:\xampp\mysql\bin\mysqldump 
				exec_control ('mysqldump --complete-insert --add-drop-table --no-create-db --skip-set-charset --quick --lock-tables --add-locks --default-character-set=utf8 --host='.$typo_db_host.' --user='.$typo_db_username.' --password="'.$typo_db_password.'" '.$typo_db.' > "'.$projectName.'-v'.$projectVersion.'.sql"');
				echr ('mysqldump --complete-insert --add-drop-table --no-create-db --skip-set-charset --quick --lock-tables --add-locks --default-character-set=utf8 --host='.$typo_db_host.' --user='.$typo_db_username.' --password="'.$typo_db_password.'" '.$typo_db.' > "'.$projectName.'-v'.$projectVersion.'.sql"');
				
				exec_control ('tar -zcf '.$dumpFilename.'-v'.$projectVersion.'.sql.tgz '.$projectName.'-v'.$projectVersion.'.sql');
				echr ('tar -zcf '.$dumpFilename.'-v'.$projectVersion.'.sql.tgz '.$projectName.'-v'.$projectVersion.'.sql');
			}
			else	{
				exec_control ('mysqldump --complete-insert --add-drop-table --no-create-db --skip-set-charset --quick --lock-tables --add-locks --default-character-set=utf8 --host='.$typo_db_host.' --user='.$typo_db_username.' --password="'.$typo_db_password.'" '.$typo_db.' > "'.PATH_work.'DUMP/'.$projectName.'-v'.$projectVersion.'.sql"');
				echr ('mysqldump --complete-insert --add-drop-table --no-create-db --skip-set-charset --quick --lock-tables --add-locks --default-character-set=utf8 --host='.$typo_db_host.' --user='.$typo_db_username.' --password="'.$typo_db_password.'" '.$typo_db.' > "'.PATH_work.'DUMP/'.$projectName.'-v'.$projectVersion.'.sql"');
				
				exec_control ('tar -zcf '.PATH_work.'DUMP/'.$dumpFilename.'-v'.$projectVersion.'.sql.tgz '.PATH_work.'DUMP/'.$projectName.'-v'.$projectVersion.'.sql');
				echr ('tar -zcf '.PATH_work.'DUMP/'.$dumpFilename.'-v'.$projectVersion.'.sql.tgz '.PATH_work.'DUMP/'.$projectName.'-v'.$projectVersion.'.sql');
			}

			

			if (!$options['dontExecCommands']) echr( '<br><a href="'.$dumpFilename.'-v'.$projectVersion.'.sql.tgz">'.$dumpFilename.'-v'.$projectVersion.'.sql.tgz</a>' );
			break;

		// IMPORT DATABASE
		case 'importdb':
			if (!paramsRequiredPass(array('dbFilename'=>$dbFilename))) return;
			//if (!$dbFilename)	$dbFilename = 'wtpack_dump.sql';
			// slashes even on windows have to be unix-style in execute source
			exec_control ('mysql --batch --quick --host='.$typo_db_host.' --user='.$typo_db_username.' --password="'.$typo_db_password.'" --database='.$typo_db.' --execute="SET NAMES \'utf8\'; SET collation_connection = \'utf8_unicode_ci\'; SET collation_database = \'utf8_unicode_ci\'; SET collation_server = \'utf8_unicode_ci\'; source '.str_replace('\\', '/', PATH_work.'DUMP/'.$dbFilename).';"');
			echr ('mysql --batch --quick --host='.$typo_db_host.' --user='.$typo_db_username.' --password="'.$typo_db_password.'" --database='.$typo_db.' --execute="SET NAMES \'utf8\'; SET collation_connection = \'utf8_unicode_ci\'; SET collation_database = \'utf8_unicode_ci\'; SET collation_server = \'utf8_unicode_ci\'; source '.str_replace('\\', '/', PATH_work.'DUMP/'.$dbFilename).';"');
			break;
		}
}

/* put message/notice */
function msg($message)	{
	global $msg;
	$msg .= $message.'<br>';
}

/* collect called commands */
function echr($str)	{
	global $cmds;
	$cmds .= $str.'<br>';
}

/* call command */
function exec_control($cmd) {
	global $options;
	if ($options['dontExecCommands'])
		msg('command not executed - exec disabled - see @dontExecCommands');
	else
		exec($cmd);
}

/* list files */
function getFilesFromDirectory($dir = 'DUMP', $ext = 'sql')	{
	return glob('*.'.$ext);
}

/* control required params for action */
function paramsRequiredPass($params)	{
	$message = '';
	foreach ($params as $param => $value)	{
		if (!$value)
			$message .= 'error: <b>'.$param.'</b> must be given. ';
	}
	if ($message)	{
		msg($message);
		return false;
	}
	return true;
}


?>
<html>
<head>
<style type='text/css'>
label	{display: block; clear: both;}
label span	{float: left; width: 260px;}
label input	{float: left;}
.clear	{clear: both;}
.error	{color: red;}
.actions label:hover	{color: red;}
footer  {font-size: 80%;}
</style>
</head>
<body>
<p><pre>PATH_site = <?php  print PATH_site;  ?></pre></p>
<p><pre>PATH_work = <?php  print PATH_work;  ?></pre></p>
<?php  if ($cmds  &&  !$options['dontShowCommands']) print "<p>commands executed:</p><p><pre>".$cmds."</pre></p>";  ?>
<p class='error'><?php  print $msg;  ?></p>
<form action='' method='post'>
	<div class="actions"><h3>action:</h3>
		<label> <span>export/dump DB &gt;</span><input name='action' type='radio' value='databasedump'> <br class='clear'></label>
		<label> <span>import DB &lt;</span> <input name='action' type='radio' value='importdb'> <br class='clear'></label>
		<label> <span>dump WHOLE SITE</span><input name='action' type='radio' value='dump'> <br class='clear'></label>
		<!--<label> <span>quickdump (exclude temp,uploads,src)</span><input name='action' type='radio' value='quickdump'> <br class='clear'></label>-->
		<label> <span>quickdump (typo3conf, fileadmin without contents)</span><input name='action' type='radio' value='quickdump'> <br class='clear'></label>
		<label> <span>backup filesystem</span> <input name='action' type='radio' value='backup'> <br class='clear'></label>
		<label> <span>backup clean</span> <input name='action' type='radio' value='backupclean'> <br class='clear'></label>
	</div>
	<div>
		<h3>project / dir name:</h3>
		<input name='name' type='text' value='<?php print $_POST['name'] ? $_POST['name'] : $projectNamePredict; ?>'>
	</div>
	<div>
		<h3>version:</h3>
		<input name='v' type='text'>
	</div>
	<div>
		<h3>database filename:</h3>
		<select name='dbFilenameSel'>
			<option></option>
		<?php
		foreach ((array)getFilesFromDirectory() as $file)
			print "<option>".$file.'</option>';
		?>
		</select>
		or type: <input name='dbFilename' type='text'>
	</div>
	
	<br>
	<input type='submit' value='go!'>
</form>
<br>
<br>

<footer>
	<i>WTP DUMP/BACKUP TOOL v<?php print $options['version']; ?> - wolo.pl '.' studio 2015</i>
</footer>

</body>
</html>