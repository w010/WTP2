<?php

// compatibility
/**
 * @deprecated, use WTP\WTools\AbstractPlugin
 */
class tx_wtools_pibase extends \WTP\WTools\AbstractPlugin    {

    public function __construct() {
        \TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();
        parent::__construct();
    }
}

