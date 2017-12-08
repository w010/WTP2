<?php



// compatibility
/**
 * Class tx_wtools_log
 * @package WTP\WTools
 * @deprecated
 */
class tx_wtools_log extends \WTP\WTools\Log {
    public function __construct($file) {
        \TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();
        parent::__construct($file);
    }
}

