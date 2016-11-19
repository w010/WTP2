<?php

/**
 * wCsv
 * WTP CSV little helper
 * version 0.0.2
 * wolo.pl TYPO3 pack
 *
 * Note that this can be a standalone class, so it doesn't fit into typo3 naming standards and doesn't use any typo3 functions
 */
class wCsv	{

	protected $error = false;


	/**
	 * @param string $url
	 * @param bool $sslBypass
	 * @return string
	 */
	public function getCsvFromUrl($url, $sslBypass = true)	{
		ob_start();

		$ch = curl_init($url);

		//curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		if ($sslBypass)	{
			// nie sprawdza certyfikatu
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		}

		$xml = curl_exec($ch);

		if (curl_errno($ch)) {
			$this->error = true;
			ob_clean();
			return false;
		}

		curl_close($ch);
		$rawCsv = ob_get_contents();
		ob_clean();

		return $rawCsv;
	}


	/**
	 * import xml from file
	 *
	 * @param string $path
	 * @param array  $options parameters for fgetcsv
	 * @param bool   $utf8 - probably not needed anymore in newer php installations
	 * @return SimpleXMLElement
	 */
	public function getCsvFromFile($path, $options = [], $utf8 = true)	{
		if ($utf8)
			$file_handle = $this->utf8_fopen_read($path, 'r');
		else
			$file_handle = fopen($path, 'r');

		while (!feof($file_handle) ) {
			$line_of_text[] = fgetcsv($file_handle, $options['length']?$options['length']:1024, $options['delimiter']?$options['delimiter']:',', $options['enclosure']?$options['enclosure']:'"');
		}
		fclose($file_handle);
		return $line_of_text;
	}

	function utf8_fopen_read($fileName) {
		$fc = iconv('windows-1250', 'utf-8', file_get_contents($fileName));
		$handle = fopen("php://memory", "rw");
		fwrite($handle, $fc);
		fseek($handle, 0);
		return $handle;
	}




		/**
		 * @param $csvFile
		 * @deprecated
		 * @return string
		 */
		function readCSV($csvFile){
			die ('readCSV() is deprecated, use getCsvFromFile() instead');
			//return $this->getCsvFromFile($csvFile);
		}
}


?>