<?php



class TTrack    {

    public $track = array();
    private $element = array();
    public $specifiedElements = array();
    private $onMeasuring = false;


    public function __construct() {
    }

    public function start($label, $specified = '') {
        $element = array();
        $element['output']['label'] = $label;
        $element['measure']['start'] = $this->ms(microtime(true));
        $element['measure']['start_s'] = time();

        if (!$specified)    {
            if ($this->onMeasuring)
                $this->stop();
            $this->onMeasuring = true;
            $this->element = $element;
        }
        else    {
            $this->specifiedElements[$specified] = $element;
        }
    }

    public function stop($specified = '') {
        $stopTime = $this->ms(microtime(true));

        if (!$specified)    {
            if ($this->element['measure']['start']) {
                $this->element['measure']['stop'] = $stopTime;
                $this->element['measure']['stop_s'] = time();
                $this->element['output']['time'] = $stopTime - $this->element['measure']['start'];
                $this->track[] = $this->element['output'];
            }
            $this->onMeasuring = false;
            unset ($this->element);
        }
        else    {
            if (!$element = $this->specifiedElements[$specified])
                return;
            $element['measure']['stop'] = $stopTime;
            $element['measure']['stop_s'] = time();
            $element['output']['time'] = $stopTime - $element['measure']['start'];
            $this->specifiedElements[$specified] = $element;
            $this->specifiedElements['_track'][$specified] = $element['output'];
            //$this->track[] = $element['output'];
        }
    }

    public function getTimeTable()  {
       //return $this->track;
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

?>