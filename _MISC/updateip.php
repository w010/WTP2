<?php

// WTP devIPmask UPDATING TOOL - wolo.pl '.' studio
// v0.1
// 2016
//

error_reporting(E_ALL ^ E_NOTICE);

define('LOCAL', intval(
	preg_match('/(localhost)/', $_SERVER['HTTP_HOST'])
));



/**
 * Update devIpMask in TYPO3 installations
 */
class updateip {

	// configuration
	public $workingDir = 'D:\WORK\___projects\\';
	protected $excludes = 'MT_Absolwent';

	// internal vars
	public $message = '';
	protected $directories = [];
	protected $fields_project = '';



	function __construct() {
		// read all directories
		$this->directories = scandir($this->workingDir);
		$this->directories = array_diff($this->directories, ['.', '..']);
		$this->directories = array_diff($this->directories, explode(',', $this->excludes));
		//var_dump($this->directories);
	}


	/**
	 * Process found projects with update of devIPmask
	 * @param $ip
	 * @return bool
	 */
	function main($ip) {

		var_dump($_POST);

		if (!$ip  ||  !$this->validIP($ip))
			return false;

		// iterate and modify AdditionalConfiguration files
		foreach($this->directories as $dir)   {

			// check if it's selected or ALL is checked
			if (!in_array($dir, $_POST['projects'], true)  &&  !in_array('all', $_POST['projects'], true))
				continue;

			$file = $this->workingDir . $dir . '\typo3conf\AdditionalConfiguration.php';
			if (file_exists($file))  {
				var_dump($file);

				// get file content
				$fileContent = file_get_contents($file);
				//var_dump($fileContent);

				// find line with 'wolo-pzn' => '[*]' (omit appending comma, that will just keep it if present)
				if (preg_match("#'wolo-pzn' => '(.*)'#", $fileContent, $m))   {
					var_dump($m);

					// replace ip and save new content to file
					$fileContentNew = preg_replace("#'wolo-pzn' => '(.*)'#", "'wolo-pzn' => '".$ip."'", $fileContent);

					if ($fileContentNew)
						file_put_contents($file, $fileContentNew);
				}
			}

				//break;  // for now only first !
		}

		return true;
	}

	public function makeProjectSelector() {
		foreach($this->directories as $dir) {
			$file = $this->workingDir . $dir . '\typo3conf\AdditionalConfiguration.php';
			if (file_exists($file)) {
				$this->fields_project .= '<label><input type="checkbox" name="projects[]" value="' . $dir . '"> ' . $dir . '</label><br>';
			}
		}
		return $this->fields_project;
	}

	/**
	 * from T3 GeneralUtility
	 * @param $ip
	 * @return bool
	 */
	public function validIP($ip) {
		return filter_var($ip, FILTER_VALIDATE_IP) !== FALSE;
	}
}



print "<pre>";

if (LOCAL)  {
	$updateip = new updateip();
	$fields_project = $updateip->makeProjectSelector();
	if ($_POST['submit']  &&  $_POST['ip']  &&  $_POST['projects'])
		$updateip->main($_POST['ip']);
	$workingDir = $updateip->workingDir;
}


?>
</pre>
<html>
	<head>
		<title>WTP: devIPmask updater</title>
	</head>
	<body>
		<h1>WTP: devIPmask updater</h1>
		<p>updates devIPmask, configured in WTP way, in all projects within working dir (<?=$workingDir?>)</p>

		<a href="http://google.com/search?q=my+ip" target="_blank">GET IP</a>
		<br><br>

		<form method="post">
			<label>set this ip for devIPmask 'wolo-pzn' key: &nbsp;
				<input name="ip">
			</label>
			<br><br>

			<p>within following projects:</p>
			<label><input type="checkbox" name="projects[]" value="all"> --- ALL ---</label><br>
			<?=$fields_project?>

			<br><br>
			<input name="submit" type="submit" value="Submit">
		</form>
	</body>
</html>