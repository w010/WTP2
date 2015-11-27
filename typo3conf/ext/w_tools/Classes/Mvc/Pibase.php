<?php
/**
 * wolo.pl '.' studio 2015
 *
 * w_tools MVC base 0.2
 *
 * pibase with additional MVC helpers
 */


class Tx_WTools_Mvc_Pibase extends tx_wtools_pibase {

	// is that used for something here?
	/*protected $action;*/

	/**
	 * @var tx_wtools_mvc_page_abstract
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
		$this->addDebug('[EXCEPTION] '. $e->getMessage() . ' - in ' .$e->getFile() . ' ['.$e->getCode().']  - in line ' . $e->getLine(), 'errors');
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
	 * @param string $note note content
	 * @param string $key debug array group key, may be debug or errors
	 */
	public function addDebug($note, $key = 'debug')   {
		$this->debugData[$key][] = $note;
	}

	protected function renderDebug()	{
		//echo $GLOBALS['TT']->printTSlog();
				    	//die();
		//debugster($this->debugData);
		if ($this->mode['mode']=='ajax')   return false;
	if (!$this->debug ||  $this->mode['mode']=='ajax')   return false;	//	? what's that?
		$code =  implode('<br>', $this->debugData['errors']);
		$code .= '<br>'.implode('<br>', $this->debugData['debug']);
		return '<div class="debugdata" style="border: 1px solid lightgray; padding: 5px; font-size: 70%;">'.$code.'</div>';
		// recru version - why can't they work same way? (now is overwritten there)
		// dbrecru is probably better - it has auto height on dblclick
		return '<div class="debugdata" style="border: 1px solid lightgray; padding: 5px; font-size: 70%;">'.$code.'</div><script>$(function() { $(\'.tx-dbrecru-pi1 .debugdata\').draggable().dblclick(function(){   $(this).css(\'height\', \'auto\'); }); });</script>';
	}
}


 


?>