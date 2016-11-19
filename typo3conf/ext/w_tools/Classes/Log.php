<?php

namespace WTP\WTools;


// compatibility
/**
 * Class tx_wtools_log
 * @package WTP\WTools
 * @deprecated
 */
class tx_wtools_log extends Log {
    public function __construct($file) {
        \TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();
        parent::__construct($file);
    }
}



class Log  {

    protected $file = '';



    public function __construct($file) {
        $this->file = $file;
    }


    public function log($notice)    {
        $filePointer = fopen($this->file, "a");

        $logMsg = date('Y-m-d H:i:s') . "\t\t" . $notice . "\n";

        // co to ma powodować? zapisywanie na początku pliku?
        rewind($filePointer);
        fwrite($filePointer, $logMsg);
        fclose($filePointer);
    }
    

}

?>