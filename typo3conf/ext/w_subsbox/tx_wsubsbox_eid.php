<?php


	// Exit, if script is called directly (must be included via eID in index_ts.php)
if (!defined ('PATH_typo3conf')) 	die ('Could not access this script directly!');

	// Connect to database:
tslib_eidtools::connectDB();


require_once (t3lib_extMgm::extPath('w_subsbox').'pi1/class.tx_wsubsbox_pi1.php');




class tx_wsubsbox_eid	{

	/**
	* 
	* @var tx_wsubsbox_pi1
	*/
	protected $Pi1;


	protected $content = '';

	

	public function __construct() {
		$this->init();
		$this->main();
	}


	public function init() {
		$this->Pi1 = 		t3lib_div::makeInstance('tx_wsubsbox_pi1');
	}


	public function main()	{

		//fb('event: ' . $eventUid, $eventUid ? FirePHP::LOG : FirePHP::WARN);

		switch(t3lib_div::_GP('method'))	{
			case 'subscribe':
				$this->content = $this->Pi1->ajax_subscribe(t3lib_div::_GP('email'));
				break;

			default:
				//fb('no method or not supported', FirePHP::ERROR);
				$call = array('res' => -1, 'error' => 'method_not_supported');
				$this->content = json_encode($call);
		}
	}


	
	/**
	 * Outputs the content from $this->content
	 *
	 * @return	void
	 */
	function printContent()	{
		print $this->content;
	}

}



$eid = t3lib_div::makeInstance('tx_wsubsbox_eid');
$eid->printContent();



?>