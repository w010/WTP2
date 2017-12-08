<?php
/**
 * wolo.pl '.' studio 2015
 *
 * w_tools MVC base 0.21
 *
 * pibase with additional MVC helpers
 */

namespace WTP\WTools\Mvc;


class AbstractPluginMvc extends \WTP\WTools\AbstractPlugin {

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
	 * @var string Default active severity of log entries in debug box (may be limited in plugin)
	 */
	protected $debugboxDefaultSeveritiesDisplay = '-1,0,1,2';

	/**
	 * @var string Message to show to user
	 */
	protected $notice = '';

	public $TTr;    // timetrack TTrack


	/**
	 * @param \Exception $e
	 */
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
	 * Debug log function. Ts debug = 1 must be set to log
	 * Ts renderDebug=1 displays debug table
	 *
	 * @param string $content note content
	 * @param string $key  debug array group key, may be debug or errors
	 * @param int    $severity 0 is standard notice, -1 is less important, 1 = success, 2 = error
	 */
	public function addDebug($content, $key = 'debug', $severity = 0)   {
		if (!$this->debug)
			return;
		//$this->debugData[$key][] = $content;
		$this->debugData[$key][] = ['content' => $content, 'severity' => $severity];
	}


	/**
	 * Builds debug box
	 * @return string
	 */
	protected function renderDebug()	{
		//echo $GLOBALS['TT']->printTSlog();
		$code = '';

		if (!$this->debug  ||  !$this->conf['renderDebug']  ||  $this->conf['mode'] == 'ajax')
			return $code;

		// todo: make sure, 'errors' and 'debug' indexes are always present! or it will display error in array_merge
		$this->debugData['debug'] = array_merge($this->debugData['errors'], $this->debugData['debug']);
		foreach ($this->debugData['debug'] as $debugRow) {
			if (is_array($debugRow)) {
				$params = $debugRow['severity'] == 2 ? ' class="severity-2" style="color: red;"' :
							($debugRow['severity'] == 1 ? ' class="severity-1" style="color: green;"' :
								($debugRow['severity'] == -1 ? ' class="severity--1" style="color: lightgray;"' :
									' class="severity-0"') );
				$code .= '<p' . $params . '>' . nl2br($debugRow['content']) . '</p>';
			}
			else {
				$code .= '<p>' . nl2br($debugRow) . '</p>';
			}
		}

		/** @var $pageRenderer\TYPO3\CMS\Core\Page\PageRenderer */
		$pageRenderer = $GLOBALS['TSFE']->getPageRenderer();
		$pageRenderer->addCssInlineBlock($this->prefixId, $this->cObj->cObjGetSingle($this->conf['debugBoxCss'], $this->conf['debugBoxCss.']), false);

		/*
		 * .debugdata can have data-default-show-severities="[N[,N...]]" (set here)
		 * .debugdata can have classes: .show-severity-N (set by js)
		 * .switch-severity can have .active class (set by js)
		 */
		return '<div class="tx-wtools-debugbox" data-default-show-severities="['.$this->debugboxDefaultSeveritiesDisplay.']" style="border: 1px solid lightgray; padding: 5px; font-size: 70%;">
					<div class="drag" title="Move box"></div>
					<div class="switch-severity" data-severity="-1" title="Show entries severity = -1">-1</div>
					<div class="switch-severity" data-severity="0" title="Show entries severity = 0">0</div>
					<div class="switch-severity" data-severity="1" title="Show entries severity = 1">1</div>
					<div class="switch-severity" data-severity="2" title="Show entries severity = 2">2</div>
					<div class="wipelog" title="Wipe log"></div>
					<div class="clear"></div>'
					. LF . $code . LF
			. '</div>';
	}
}


 


?>