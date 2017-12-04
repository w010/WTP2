<?php
/**
 * wolo.pl '.' studio 2015
 *
 * w_tools MVC base 0.21
 *
 * pibase with additional MVC helpers
 */


class Tx_WTools_Mvc_Pibase extends tx_wtools_pibase {

	// is that used for something here?
	/*protected $action;*/

	/**
	 * @var \WTP\WTools\Mvc\Page\AbstractPage
	 */
	protected $PAGE;

	/**
	 * @var bool
	 */
	public $debug;
	protected $debugData = [];

	/**
	 * @var string Message to show to user
	 */
	protected $notice = '';

	public $TTr;    // timetrack TTrack





	protected function catch_exception($e)  {
		$this->addDebug('[EXCEPTION] '. $e->getMessage() . ' - in ' .$e->getFile() . ' ['.$e->getCode().']  - in line ' . $e->getLine(), 'errors', 2);
	}



	/**
	 * Set notify message to user
	 * @param string $notice
	 */
	public function setNotice($notice)  {
		if ($this->notice)  $this->notice .= '<br>';
		$this->notice .= $notice;
	}


	/**
	 * debug log function. if DEV, there will be debug table
	 *
	 * @param string $content note content
	 * @param string $key  debug array group key, may be debug or errors
	 * @param int    $severity 0 is standard notice, -1 is less important, 1 = success, 2 = error
	 */
	public function addDebug($content, $key = 'debug', $severity = 0)   {
		//$this->debugData[$key][] = $content;
		$this->debugData[$key][] = ['content' => $content, 'severity' => $severity];
	}

	protected function renderDebug()	{
		//echo $GLOBALS['TT']->printTSlog();
				    	//die();
		//debugster($this->debugData);
		//if ($this->mode['mode']=='ajax')   return false;
		if (!$this->debug  ||  $this->conf['mode']=='ajax')   return false;
			// this old way doesn't work anymore
			//$code =  implode('<br>', $this->debugData['errors'][0]);
			//debugster($code);
			//$code .= '<br>'.implode('<br>', $this->debugData['debug']);
			//$code .= '<br>';
		$code = '';
		// todo: make sure, 'errors' and 'debug' indexes are always present! or it will display error in array_merge
		$this->debugData['debug'] = array_merge($this->debugData['errors'], $this->debugData['debug']);
		foreach ($this->debugData['debug'] as $debugRow) {
			if (is_array($debugRow)) {
				$style = $debugRow['severity'] == 2 ? ' style="color: red;"' :
							($debugRow['severity'] == 1 ? ' style="color: green;"' :
								($debugRow['severity'] == -1 ? ' style="color: lightgray;"' : '') );
				$code .= '<p' . $style . '>' . nl2br($debugRow['content']) . '</p>';
			}
			else
				$code .=  '<p>'.nl2br($debugRow).'</p>';
		}

		return '<div class="debugdata" style="border: 1px solid lightgray; padding: 5px; font-size: 70%;">'.$code.'</div>';
	}
}


 


?>