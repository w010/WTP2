<?php

//die('no access');

// WTP DUMP/BACKUP TOOL FOR TYPO3 - wolo.pl '.' studio
// 2013-2017
define ('SCRIPT_VERSION', '1.2.0');
//
// dump / ..za....ka si tr lub o dw
// you should change default password!

//
//                      WARNING!   CRITICAL
//
// THIS SCRIPT IS FOR PRIVATE DEV USE ONLY, DO NOT PUT IT ON PUBLIC UNSECURED!
// IT DOES LOW LEVEL DB / FILESYSTEM OPERATIONS AND DOESN'T HAVE ANY USER-INPUT SECURITY CHECK.
// IF THIS IS YOUR SITE AND RUNNING IN PUBLIC/PRODUCTION ENVIRONMENT AND YOU ARE
// NOT SURE IF THIS FILE SHOULD BE HERE, PLEASE DELETE THIS SCRIPT IMMEDIATELY

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_STRICT ^ E_DEPRECATED);

define('PATH_thisScript', str_replace('//', '/', str_replace('\\', '/',
	(PHP_SAPI == 'fpm-fcgi' || PHP_SAPI == 'cgi' || PHP_SAPI == 'isapi' || PHP_SAPI == 'cgi-fcgi') &&
	($_SERVER['ORIG_PATH_TRANSLATED'] ? $_SERVER['ORIG_PATH_TRANSLATED'] : $_SERVER['PATH_TRANSLATED']) ?
	($_SERVER['ORIG_PATH_TRANSLATED'] ? $_SERVER['ORIG_PATH_TRANSLATED'] : $_SERVER['PATH_TRANSLATED']) :
	($_SERVER['ORIG_SCRIPT_FILENAME'] ? $_SERVER['ORIG_SCRIPT_FILENAME'] : $_SERVER['SCRIPT_FILENAME']))));

define('PATH_dump', dirname(PATH_thisScript).'/');
define('PATH_site', realpath(PATH_dump.'../').'/');
define('TYPO3_MODE', 'BE');

if (file_exists(PATH_site.'typo3conf/LocalConfiguration.php'))	{
	$GLOBALS['TYPO3_CONF_VARS'] = require_once(PATH_site.'typo3conf/LocalConfiguration.php');

	// may be used sometimes in AdditionalConfiguration
	include_once(PATH_site.'typo3/sysext/core/Classes/Utility/GeneralUtility.php');
	include_once(PATH_site.'typo3conf/AdditionalConfiguration.php');
	// for q3i (is already included in AdditionalConfiguration)
	//if (file_exists(PATH_site.'typo3conf/AdditionalConfiguration_host.php'))
	//	include_once(PATH_site.'typo3conf/AdditionalConfiguration_host.php');


	//$localconf = array_replace_recursive($localconf, $additionalLocalconf);
	//var_dump($GLOBALS['TYPO3_CONF_VARS']);
	$typo_db_username = $GLOBALS['TYPO3_CONF_VARS']['DB']['username'];
	$typo_db_password = $GLOBALS['TYPO3_CONF_VARS']['DB']['password'];
	$typo_db_host = $GLOBALS['TYPO3_CONF_VARS']['DB']['host'];
	$typo_db = $GLOBALS['TYPO3_CONF_VARS']['DB']['database'];
	define('V4', false);
}
else {
	include_once(PATH_site . 'typo3conf/localconf.php');
	define('V4', true);
}


if (!defined('DEV'))                define('DEV',   false);
if (!defined('LOCAL'))              define('LOCAL', false);

if (!defined('TYPO3_CONTEXT'))      define('TYPO3_CONTEXT',     getenv('TYPO3_CONTEXT'));
if (!defined('INSTANCE_CONTEXT'))   define('INSTANCE_CONTEXT',  getenv('INSTANCE_CONTEXT'));


// default options for this script operation
$optionsDefault = [
	// script only displays generated command line, but doesn't exec it
	'dontExecCommands' => 0,

	// exec commands, but don't show them on PUB
	'dontShowCommands' => 0,

	// default tables for "Dump with omit" action, if not specified
	'defaultOmitTables' => ['index_rel', 'sys_log'],

	// adds docker exec on container to command line
	'docker' => false,

	// if docker=true, container name must be specified
	'docker_containerSql' => ''
];

// custom options may be included to override
$optionsCustom = [];
include_once ('conf.php');
$options = array_replace($optionsDefault, $optionsCustom);



//if (!DEV)
//	die('access denied');





$Dump = new Dump();
$Dump->main();


class Dump  {

	// define some global variables. all are public, because this script is for private use and no need to control such things.

	public $options = [];

	// html content to display before form
	public $contentHeader = '';

	// message/error to show
	public $messages = [];

	// stored commands to show and/or execute
	public $cmds = [];

	// input values
	public $projectName = '';
	public $projectVersion = '';
	public $dbFilename = '';
	public $action = '';



	function main() {

		global $options;

		$this->options = &$options;

		// old - fashion 4.x var names, should be refactored in near future and typo 4.x support should be dropped
		global $typo_db_username;
		global $typo_db_password;
		global $typo_db_host;
		global $typo_db;

		if (!$typo_db_username || !$typo_db_password || !$typo_db_host || !$typo_db)
			$this->msg('CHECK DATABASE CONFIG. Looks like some authorization data is missed. Check '.(V4?'localconf':'LocalConfiguration'));

		$this->addEnvironmentMessage();

		// set input values
		$this->projectName = $_POST['name'];
		$this->projectVersion = $_POST['v'];
		$this->action = $_POST['action'];
		$this->dbFilename = $_POST['dbFilename'] ? $_POST['dbFilename'] : $_POST['dbFilenameSel'];

		// predicted project name, taken from domain name (only when not submitted, on first run)
		if (!$_POST['submit'] && !$_POST['name'])
			list($this->projectName) = preg_split('@\.@', $_SERVER['HTTP_HOST']);


		$this->contentHeader .= '<p>- database: <span class="info"><b>' . $typo_db . '</b></span></p>';
		if (INSTANCE_CONTEXT)
			$this->contentHeader .= '<p>- instance: <span class="info"><b>' . INSTANCE_CONTEXT . '</b></span></p>';
		if ($this->options['docker_containerSql'])
			$this->contentHeader .= '<p>- docker: <span class="info"><b>' . $this->options['docker_containerSql'] . '</b></span></p>';

		if (!$_POST['submit']  ||  ($_POST['submit'] && !$this->paramsRequiredPass(['action' => $this->action])))
			return;

		$this->contentHeader .= '<p>- action: <span class="info"><b>' . $this->action . '</b></span></p>';

		// if docker is used, docker exec CONTAINER must be prepended before mysqldump
		$cmd_dockerPart = '';
		if ($this->options['docker'])
			$cmd_dockerPart = 'docker exec '.$this->options['docker_containerSql'].' ';



		// RUN
		switch ($this->action) {

			// BACKUP FILESYSTEM
			case 'backup':
				$backupDir = PATH_site . '../' . $this->projectName . '_backup' . time() . '/';
				$this->exec_control('mkdir ' . $backupDir);
				$this->exec_control('cp -R ' . PATH_site . '/* ' . $backupDir);
				break;


			// EMPTY specified backup folder. use this, if you know why to delete a backup.
			case 'backupclean':
				if (!isset($_POST['backupdir']))
					die('error: backup remove - directory namepart not given');
				$backupDir = 'backup' . $_POST['backupdir'];
				die('backup remove disabled');
				//	$this->exec_control ('rm -R '.$backupDir.'/*');
				break;

			// DUMP EXPORT SITE FILES AND DATABASE
			case 'dump':
			case 'quickdump':
			case 'databasedump':
				if (!$this->paramsRequiredPass(['projectName' => $this->projectName, 'projectVersion' => $this->projectVersion])) return;
				$dumpFilename = str_replace(' ', '_', $this->projectName);

				if ($this->action == 'quickdump') {
					/*$this->exec_control ('tar -zcf '.PATH_site.'DUMP/'.$dumpFilename.'-v'.$this->projectVersion.'.tgz ./../* --exclude="typo3temp" --exclude="DUMP" --exclude="uploads" -exclude="typo3_src-*"  ');*/
					 $this->exec_control('tar -C ' . PATH_site . ' -zcf ' . $dumpFilename . '-v' . $this->projectVersion . '.tgz typo3conf fileadmin --exclude="fileadmin/contents" ');

					// works on win, check on lin

				} else if ($this->action == 'databasedump') {
					// do nothing, don't dump filesystem
				} else {
					$this->exec_control('tar -C ' . PATH_site . ' -zcf ' . $dumpFilename . '-v' . $this->projectVersion . '.tgz . --exclude="DUMP" ');
				}

				if (defined('LOCAL') && LOCAL) {
					//d:\xampp\mysql\bin\mysqldump
					$this->exec_control('mysqldump --complete-insert --add-drop-table --no-create-db --skip-set-charset --quick --lock-tables --add-locks --default-character-set=utf8 --host=' . $typo_db_host . ' --user=' . $typo_db_username . ' --password="' . $typo_db_password . '" ' . $typo_db . ' > "' . $this->projectName . '-v' . $this->projectVersion . '.sql"');
					$this->exec_control('tar -zcf ' . $dumpFilename . '-v' . $this->projectVersion . '.sql.tgz ' . $this->projectName . '-v' . $this->projectVersion . '.sql');

				} else {
					$this->exec_control($cmd_dockerPart.'mysqldump --complete-insert --add-drop-table --no-create-db --skip-set-charset --quick --lock-tables --add-locks --default-character-set=utf8 --host=' . $typo_db_host . ' --user=' . $typo_db_username . ' --password="' . $typo_db_password . '" ' . $typo_db . ' > "' . PATH_site . 'DUMP/' . $this->projectName . '-v' . $this->projectVersion . '.sql"');
					$this->exec_control('tar -C ' . PATH_site . 'DUMP/ -zcf ./' .$dumpFilename . '-v' . $this->projectVersion . '.sql.tgz ' . $this->projectName . '-v' . $this->projectVersion . '.sql');
				}

				// display download link
				if (!$options['dontExecCommands'])
					$this->cmds[] = '<br><a href="' . $dumpFilename . '-v' . $this->projectVersion . '.sql.tgz">' . $dumpFilename . '-v' . $this->projectVersion . '.sql.tgz</a><br>';

				break;


			// zrobic tutaj porzadek, te wszystkie dumpy roznego typu
			case 'databasedump-omit':

				if (!$this->paramsRequiredPass(['projectName' => $this->projectName, 'projectVersion' => $this->projectVersion, 'omitTables' => $_POST['omitTables']]))
					return;
				$dumpFilename = str_replace(' ', '_', $this->projectName);

				$this->exec_control($cmd_dockerPart.'mysqldump --complete-insert --add-drop-table --no-create-db --skip-set-charset --quick --lock-tables --add-locks --default-character-set=utf8 --host=' . $typo_db_host . ' --user=' . $typo_db_username . ' --password="' . $typo_db_password . '" ' . $typo_db
					. ' --ignore-table=' . $typo_db . '.'
					. implode ('--ignore-table=' . $typo_db . '.', explode(chr(10), $_POST['omitTables']))
					//. '  --ignore-table=' . $typo_db . '.index_rel --ignore-table=' . $typo_db . '.index_fulltext --ignore-table=' . $typo_db . '.sys_log --ignore-table=' . $typo_db . '.tt_news_cache_tags --ignore-table=' . $typo_db . '.tx_dam
					. ' > "' . PATH_site . 'DUMP/' . $this->projectName . '-v' . $this->projectVersion . '.sql"');
				$this->exec_control('tar -C ' . PATH_site . 'DUMP/ -zcf ./' .$dumpFilename . '-v' . $this->projectVersion . '.sql.tgz ' . $this->projectName . '-v' . $this->projectVersion . '.sql');

				if (!$options['dontExecCommands'])
					$this->cmds[] = '<br><a href="' . $dumpFilename . '-v' . $this->projectVersion . '.sql.tgz">' . $dumpFilename . '-v' . $this->projectVersion . '.sql.tgz</a><br>';

				break;


			// IMPORT DATABASE
			case 'importdb':
				if (!$this->paramsRequiredPass(['dbFilename' => $this->dbFilename]))
					return;
				// slashes even on windows have to be unix-style in execute source
				$this->exec_control($cmd_dockerPart.'mysql --batch --quick --host=' . $typo_db_host . ' --user=' . $typo_db_username . ' --password="' . $typo_db_password . '" --database=' . $typo_db . ' --execute="SET NAMES \'utf8\'; SET collation_connection = \'utf8_unicode_ci\'; SET collation_database = \'utf8_unicode_ci\'; SET collation_server = \'utf8_unicode_ci\'; source ' . str_replace('\\', '/', PATH_site . 'DUMP/' . $this->dbFilename) . ';"');
				break;
		}
	}


	private function addEnvironmentMessage()    {
		$environment = '';
		if (defined('DEV') && DEV)          $environment = 'DEV';
		if (defined('LOCAL') && LOCAL)      $environment = 'LOCAL';
		if (getenv('TYPO3_CONTEXT') == 'Development')   $environment = 'Development';
		if (getenv('TYPO3_CONTEXT') && !$environment)   $environment = getenv('TYPO3_CONTEXT');
		if (!$environment)                              $environment = 'PUBLIC';
		if ($environment == 'Production' || $environment == 'PUBLIC')
			$environment = '<span class="error">'.$environment.' !!!!</span>';
		else
			$environment = '<span class="info">'.$environment.'</span>';
		$this->contentHeader .= '<h4>running on ' . $environment . '</h4>';
	}


	/**
	 * add message/notice
	 * @param string $message
	 * @param string $class - class for notice p, may be error or info
	 * @param string $index - index can be checked in tag markup, to indicate error class in form element
	 */
	function msg($message, $class = '', $index = '') {
		//$this->msg .= $message . '<br>';
		if ($index)     $this->messages[$index] = [$message, $class];
		else            $this->messages[] = [$message, $class];
	}


	/* display generated messages with class if set */
	function displayMessages()  {
		$content = '';
		foreach ($this->messages as $message) {
			$content .= '<p'.($message[1] ? ' class="'.$message[1].'">':'>') . $message[0] . '</p>';
		}
		return $content;
	}


	/* call command */
	function exec_control($cmd, $saveCmd = true) {
		global $options;
		if ($options['dontExecCommands'])
			$this->msg('command not executed - exec disabled - see @dontExecCommands', 'info');
		else
			exec($cmd);

		if ($saveCmd) {
			$this->cmds[] = $cmd;
		}
	}

	/* list files */
	function getFilesFromDirectory($dir = 'DUMP', $ext = 'sql') {
		$files = glob('*.' . $ext);
		if (!is_array($files))  $files = [];
		return $files;
	}

	/* control required params for action */
	function paramsRequiredPass($params) {
		$pass = true;
		foreach ($params as $param => $value) {
			if (!$value) {
				$this->msg('error: <b>' . $param . '</b> must be set. ', 'error', $param);
				$pass = false;
			}
		}
		return $pass;
	}

	/* prints error class on form input, if present */
	function checkFieldError($param)    {
		if (array_key_exists($param, $this->messages))
			return ' class="error"';
	}

	/* returns omit tables value to display in textarea */
	function getOmitTables()    {
		if ($_POST['omitTables'])
			return htmlspecialchars($_POST['omitTables']);
		return implode(chr(10), $this->options['defaultOmitTables']);
	}

	/* returns dump filenames, to see what version number should be next */
	function getExistingDumpsFilenames()    {
		return chr(10) . implode (chr(10), $this->getFilesFromDirectory());
	}
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
.info	{color: green;}
ul  { list-style: none; float: left; margin-top: 0;}
.actions li > label:hover	{color: red;}
.radio-sub-options { display: none; padding: 10px 20px 20px;}
input[type=radio]:checked + .radio-sub-options { display: block;}
footer  {font-size: 80%;}
	</style>
</head>
<body>

<header>
	<h2>WTP Dump tool</h2>
	<?php   print $Dump->contentHeader;     ?>
</header>

<pre>PATH_site = <?php  print PATH_site;  ?></pre>
<pre>PATH_dump = <?php  print PATH_dump;  ?></pre>
<?php  if ($Dump->cmds  &&  !$options['dontShowCommands']) print "<p>- commands executed:</p><p><pre>".implode('<br><br>', $Dump->cmds)."</pre></p>";  ?>
<?php  print $Dump->displayMessages();  ?>
<form action='' method='post'>
	<div class="actions"><h3<?php print $Dump->checkFieldError('action'); ?>>action:</h3>
		<ul>
			<li> <label> <span>export/dump DB &gt;</span><input name='action' type='radio' value='databasedump'> <br class='clear'></label> </li>
			<li> <label> <span>export/dump DB (omit tables) &gt;</span><input name='action' type='radio' value='databasedump-omit'>
					<div class="radio-sub-options selector-tables clear">
						<textarea name='omitTables' cols='32' rows='6'><?php  print $Dump->getOmitTables(); ?></textarea>
					</div>  <br class='clear'></label> </li>
			<li> <label for='action_importdb'> <span>import DB &lt;</span> </label> <input name='action' id='action_importdb' type='radio' value='importdb'>
					<div class="radio-sub-options selector-database clear">
						<h3<?php print $Dump->checkFieldError('dbFilename'); ?>><label for='dbFilenameSel'>database filename:</label></h3>
						<select name='dbFilenameSel' id='dbFilenameSel'>
							<option></option>
							<?php
							foreach ($Dump->getFilesFromDirectory() as $file)
								print "<option>".$file.'</option>';
							?>
						</select>
						or type: <input name='dbFilename' type='text'>
					</div>  <br class='clear'> </li>
			<li> <label> <span>dump WHOLE SITE</span><input name='action' type='radio' value='dump'> <br class='clear'></label> </li>
			<!--<li> <label> <span>quickdump (exclude temp,uploads,src)</span><input name='action' type='radio' value='quickdump'> <br class='clear'></label> </li>-->
			<li> <label> <span>quickdump (typo3conf, fileadmin without contents)</span><input name='action' type='radio' value='quickdump'> <br class='clear'></label> </li>
			<li> <label> <span>backup filesystem</span> <input name='action' type='radio' value='backup'> <br class='clear'></label> </li>
			<li> <label> <span>backup clean</span> <input name='action' type='radio' value='backupclean'> <br class='clear'></label> </li>
		</ul>
	</div>
	<div>
		<h3<?php print $Dump->checkFieldError('projectName'); ?>><label for='name'>project / dir name:</label></h3>
		<input name='name' id='name' type='text' size='30' value='<?php print htmlspecialchars($Dump->projectName); ?>'>
	</div>
	<div>
		<h3<?php print $Dump->checkFieldError('projectVersion'); ?>><label for='v'>version:</label></h3>
		<input name='v' id='v' type='text' size='10'>
		<i title="Existing dumps: <?php print $Dump->getExistingDumpsFilenames(); ?>">[ i ]</i>
	</div>
	<!--<div>

	</div>-->

	<br>
	<input type='submit' name='submit' value=' go! '>
</form>
<br>
<br>

<footer>
	<i>WTP DUMP/BACKUP TOOL FOR TYPO3<br> v<?php print SCRIPT_VERSION; ?> - wolo.pl '.' studio 2013-2017</i>
</footer>

</body>
</html>