<?php

namespace WTP\WTools;



/**
 * WTP - TimeTrack
 * Simple timetracking with grouping sub elements. Useful where Typo's internal timetrack is not very handy, ie. some imports etc.
 * Tracks time and keeps it in array. May be var_dumped or used in other way, displaying of this info is up to you. It only tracks time.
 * v2.1 - use error info
 *
 *
 * Use:
 *
 * - simple:
$this->TTr->start('Label');		// starts simple tracking time for labeled element
$this->TTr->stop();				// stops tracking current (on measuring) element
var_dump( $this->TTr->getTimeTable() );		// view results


 * - measuring specified elements:
$this->TTr->start('Item read', 'ITEM_X');	// tracks specified named element ITEM_X, to have possibility to decide when to stop this exact item
$this->TTr->start('Something else');
$this->TTr->stop('ITEM_X');					// stops tracking specified item
$this->TTr->stop();


 * - advanced: grouping elements:
$this->TTr->start('Overall', 'OVERALL');				// starts tracking main group of all items
  $this->TTr->start('Preparing', 'PREPARE_GROUP');		// starts tracking subgroup
    $this->TTr->start('Read directory', 'prepare_readdir', 'PREPARE_GROUP');	// starts tracking specified item from that group
 		// reading some dir
    $this->TTr->stop('prepare_readdir', 'PREPARE_GROUP');			// stops tracking specified item from specified group

	$this->TTr->start('Load data', 'prepare_load', 'PREPARE_GROUP');
		// load some data
	$this->TTr->stop('prepare_load', 'PREPARE_GROUP');
  $this->TTr->stop('PREPARE_GROUP');								// stops tracking whole group

  $this->TTr->start('Save data', 'SAVE_GROUP');						// another subgroup with items
    $this->TTr->start('save set', 'save_'.$recordType, 'SAVE_GROUP');
		// saving
	$this->TTr->stop('save_'.$recordType, 'SAVE_GROUP');
  $this->TTr->stop('SAVE_GROUP');
$this->TTr->stop('OVERALL');

var_dump( $this->TTr->getTimeTable() );

 */
class TTrack	{

	public $track = [];
	private $element = [];
	public $specifiedElements = [];
	private $onMeasuring = false;


	public function __construct() {
	}

	/**
	 * @param string $label - label to display, may be anything
	 * @param string $specified - name of an element to track
	 * @param string $group - group of items
	 */
	public function start($label, $specified = '', $group = '') {
		$element = [];
		$element['output']['label'] = $label;
		$element['measure']['start'] = $this->ms(microtime(true));
		$element['measure']['start_s'] = time();
		if (!$specified)	{
			if ($this->onMeasuring)
				$this->stop();
			$this->onMeasuring = true;
			$this->element = $element;
		}
		else if ($group)	{
			$this->specifiedElements[$group]['grouped'][$specified] = $element;
		}
		else	{
			$this->specifiedElements[$specified] = $element;
		}
	}

	/**
	 * @param string $specified - stop selected measurement
	 * @param string $group - to stop measuring grouped item
	 */
	public function stop($specified = '', $group = '') {
		$stopTime = $this->ms(microtime(true));

		// stop general item
		if (!$specified)	{
			if ($this->element['measure']['start']) {
				$this->element['measure']['stop'] = $stopTime;
				$this->element['measure']['stop_s'] = time();
				$this->element['output']['time'] = $stopTime - $this->element['measure']['start'];
				$this->track[] = $this->element['output'];
			}
			$this->onMeasuring = false;
			unset ($this->element);
		}
		// stop specified name item
		else if ( $element = $this->specifiedElements[$specified] )  {
			$element['measure']['stop'] = $stopTime;
			$element['measure']['stop_s'] = time();
			$element['output']['time'] = $stopTime - $element['measure']['start'];
			if ($element['grouped'])	$element['output']['grouped'] = $element['grouped'];
			$this->specifiedElements[$specified] = $element;
			$this->specifiedElements['_track'][$specified] = $element['output'];
			// wolo mod 2015: add also to general array to preserve order
			$this->track[$specified] = $element['output'];
		}
		// grouped item
		else if ( $element = $this->specifiedElements[$group]['grouped'][$specified])	{
			$element['measure']['stop'] = $stopTime;
			$element['measure']['stop_s'] = time();
			$element['output']['time'] = $stopTime - $element['measure']['start'];
			unset ($element['measure']);
			$this->specifiedElements[$group]['grouped'][$specified] = $element['output'];
			$this->specifiedElements['_track'][$group]['grouped'][$specified] = $element['output'];
		}

		else	{
			$this->track['__ERRORS'][] = 'Error possibly - stop() called with: "'.$specified.'" which was not started' . ($group ? ' in group: "'.$group.'")' : '');
		}
	}

	public function getTimeTable()  {
		//		return $this->track;
		//debugster($this->specifiedElements);
		return array_merge($this->track, $this->specifiedElements['_track']);
	}

	// tworzymy int milisekund z floata
	private function ms($microtime) {
		return intval($microtime * 1000);

		/*$microtime = (string) $microtime;
		$microArray = explode('.', $microtime);
		$microArray[1] = str_pad($microArray[1], 3, '0');
		$ms = implode('', $microArray);

		return intval($ms);*/
	}
}




/**
 * OLD WAY
 */







use TYPO3\CMS\Core\SingletonInterface;


/**
 * Class TTrackx
 * version 3 - totally new approach, forget the old one [said me before I rewrote it. this is now not used]
 *
 * @package WTP\WTools\TTrack
 */
class TTrackx implements  SingletonInterface	{

	public $track = [
		'label' => 'root',
		'start' => 0,
		'items' => []
	];

	/**
	 * current track level for nested measuring, like items loop
	 * @var int
	 */
	protected $measureLevel = 0;


	public function __construct() {
		$this->track['start'] = $this->getMilliseconds();
	}


	// dodajemy element do otwartego ostatnim pushem levelu. jesli nie bylo pull (czyli zakonczenie cyklu) -
	// - znaczy, ze dodajemy podrzedny
	public function push($label, $specified = '') {

		//debugster('===================================');
		//debugster($label);

		// prepare new element
		$element = [
			'label' => $label,
			'start' => $this->getMilliseconds(),
			'end' => null
		];

		// pobieramy biezacy level
		$currentLevelArray = &$this->getCurrentLevelArray_recursive($this->track, $this->measureLevel);
			//debugster($this->measureLevel);
			//debugster($currentLevelArray);
		// i jego ostatni element. to niestety nie jest referencja, wiec nadpisywanie w tym czegokolwiek nie zmienia nic w oryginale
		// wobec tego trzeba w razie dodawania childrenow iterowac oryginalna tablice
		$lastElement = end($currentLevelArray['items']);

			//debugster($lastElement);

		// jesli jest zakonczony lub w ogole nie ma poprzedniego, dodajemy kolejny po nim, do wskaznika na biezacy level trackingu
		if ($lastElement['end'] || !$lastElement)	{
			//print('dodajemy kolejny');
			$currentLevelArray['items'][] = $element;
		}
		// jesli jest otwarty, dodajemy jako jego children
		else if ($lastElement)	{
			//print('dodajemy child');
			// dodajemy child do ostatniego elementu biezacego levelu. musimy tak, bo przypisujac tu referencje nie modyfikuje oryginalu
			$this->addChildToLastCurrentLevelItem_recursive($element, $this->track, $this->measureLevel);
			// ustawiamy measureLevel na wyzszy, zeby pull konczacy parenta wiedzial, jak znalezc jego nadrzedny
			$this->measureLevel++;
		}

			//debugster($currentLevelArray);
			//debugster($this->track);
	}


	public function pull() {
		$endTime = $this->getMilliseconds();

		// pobieramy biezacy level
		$currentLevelArray = &$this->getCurrentLevelArray_recursive($this->track, $this->measureLevel);
		// i jego ostatni element
		$i = count ($currentLevelArray['items'])-1;
		$lastElement = &$currentLevelArray['items'][ $i ];

		// jesli jest otwarty, zamykamy go. wskaznika nie zmieniamy
		if ($lastElement && !$lastElement['end'])	{

			$startTime = $lastElement['start'];
			$delta = $endTime - $startTime;
			$lastElement['end'] = $endTime;
			$lastElement['MEASURE'] = $delta;
		}
		// jesli jest zamkniety, pobieramy parenta (czyli zmniejszamy wskaznik i pobieramy ostatni) i zamykamy jego
		else if ($lastElement['end'])	{
			$this->measureLevel --;
			$currentLevelParentArray = &$this->getCurrentLevelArray_recursive($this->track, $this->measureLevel);
			$j = count ($currentLevelParentArray['items'])-1;
			$lastElementOfParent = &$currentLevelParentArray['items'][$j];
			$startTime = $lastElementOfParent['start'];
			$delta = $endTime - $startTime;
			$lastElementOfParent['end'] = $endTime;
			$lastElementOfParent['MEASURE'] = $delta;
		}
	}


	// dodajemy subitem do ostatniego elementu na podanym levelu
	function &addChildToLastCurrentLevelItem_recursive($element, &$inArr, $findLevel)	{
		static $innerLevel = 0;
		if (is_array($inArr)) {
			// jesli to juz ten level, dodajemy go do ostatniego itemu
			if ($innerLevel === $findLevel  &&  $element) {
				$innerLevel = 0;
				$inArr['items'][ count($inArr['items'])-1 ] ['items'][] = $element;
			}
			// jesli nie, szukamy glebiej
			else	{
				$innerLevel++;
				return $this->addChildToLastCurrentLevelItem_recursive($element, $inArr['items'][ count($inArr['items'])-1 ], $findLevel);
			}
		}
	}

	// pobieramy ostatni element na podanym levelu
	function &getCurrentLevelArray_recursive(&$inArr, $findLevel)	{
		static $innerLevel = 0;
		if (is_array($inArr)) {
			// jesli to juz ten level, zwracamy go
			if ($innerLevel === $findLevel) {
				// important! reset inner level, because other methods gets it modified...
				$innerLevel = 0;
				return $inArr;
			}
			// jesli nie, szukamy glebiej
			else	{
				$innerLevel++;
				return $this->getCurrentLevelArray_recursive($inArr['items'][ count($inArr['items'])-1 ], $findLevel);
			}
		}
	}




	public function getTimeTable()  {
		return $this->track;
	}



	/**
	 * Gets a microtime value as milliseconds value.
	 *
	 * @param float $microtime The microtime value - if not set the current time is used
	 * @return float The microtime value as milliseconds value
	 */
	public function getMilliseconds($microtime = NULL) {
		if (!isset($microtime)) {
			$microtime = microtime(TRUE);
		}
		return round($microtime * 1000);
	}
}




/*
class StopWatch    {

    public $track = [];
    private $element = [];
    private $previousEnd = 0;


    public function start($label) {
        // on start, set end of previous run to current - coz there's no previous.
        $this->previousEnd = $this->ms(microtime(true));
    }

    public function route($label) {
        $this->element['label'] = $label;
        $this->element['time'] = $this->ms(microtime(true)) - $this->previousEnd;
        $this->track[] = $this->element;
    }

    // tworzymy int milisekund z floata
    private function ms($microtime) {
        return intval($microtime * 1000);
    }
}*/




?>