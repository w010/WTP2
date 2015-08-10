<?php
namespace DirectMailTeam\DirectMail\Scheduler;

/***************************************************************
*  Copyright notice
*
*  (c) 2010 Benjamin Mack <benni@typo3.org>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

use \TYPO3\CMS\Backend\Utility\BackendUtility;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Scheduler\Task\AbstractTask;
use \DirectMailTeam\DirectMail\DirectMailUtility;

/**
 * Class tx_directmail_Scheduler_MailFromDraft
 * takes a specific draft and compiles it again, and then creates another
 * directmail record that is ready for sending right away
 *
 * @author	Benjamin Mack <benni@typo3.org>
 * @package TYPO3
 * @subpackage	tx_directmail
 */
class MailFromDraft extends AbstractTask {

	public $draftUid = NULL;

	protected $hookObjects = array();

	/**
	 * setter function to set the draft ID that the task should use
	 * @param integer $draftUid the UID of the sys_dmail record (needs to be of type=3 or type=4)
	 * @param void
	 */
	function setDraft($draftUid) {
		$this->draftUid = $draftUid;
	}

	/**
	 * Function executed from scheduler.
	 * Creates a new newsletter record, and sets the scheduled time to "now"
	 *
	 * @return	bool
	 */
	function execute() {
		if ($this->draftUid > 0) {
			$this->initializeHookObjects();
			$hookParams = array();

			$draftRecord = BackendUtility::getRecord('sys_dmail', $this->draftUid);

				// get some parameters from tsConfig
			$tsConfig = BackendUtility::getModTSconfig($draftRecord['pid'], 'mod.web_modules.dmail');
			$defaultParams = $tsConfig['properties'];

				// make a real record out of it
			unset($draftRecord['uid']);
			$draftRecord['tstamp'] = time();
			$draftRecord['type'] -= 2;	// set the right type (3 => 1, 2 => 0)

				// Insert the new dmail record into the DB
			$GLOBALS['TYPO3_DB']->exec_INSERTquery('sys_dmail', $draftRecord);
			$this->dmailUid = $GLOBALS['TYPO3_DB']->sql_insert_id();

				// Call a hook after insertion of the cloned dmail record
				// This hook can get used to modify fields of the direct mail.
				// For example the current date could get appended to the subject.
			$hookParams['draftRecord'] = &$draftRecord;
			$hookParams['defaultParams'] = &$defaultParams;
			$this->callHooks('postInsertClone', $hookParams);

				// fetch the cloned record
			$mailRecord = BackendUtility::getRecord('sys_dmail', $this->dmailUid);

			DirectMailUtility::fetchUrlContentsForDirectMailRecord($mailRecord, $defaultParams);

			$mailRecord = BackendUtility::getRecord('sys_dmail', $this->dmailUid);
			if ($mailRecord['mailContent'] && $mailRecord['renderedsize'] > 0) {
				$updateData = array(
					'scheduled' => time(),
					'issent'    => 1
				);
					// Call a hook before enqueuing the cloned dmail record into
					// the direct mail delivery queue
				$hookParams['mailRecord'] = &$mailRecord;
				$hookParams['updateData'] = &$updateData;
				$this->callHooks('enqueueClonedDmail', $hookParams);
					// Update the cloned dmail so it will get sent upon next
					// invocation of the mailer engine
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery('sys_dmail', 'uid = ' . intval($this->dmailUid), $updateData);
			}

		}
		return TRUE;
	}

	/**
	 * Calls the passed hook method of all configured hook object instances
	 *
	 * @param $hookMethod
	 * @param $hookParams
	 * @return    void
	 */
	function callHooks($hookMethod, $hookParams) {
		foreach ($this->hookObjects as $hookObjectInstance) {
			$hookObjectInstance->$hookMethod($hookParams, $this);
		}
	}

	/**
	 * Initializes hook objects for this class
	 *
	 * @return void
	 */
	function initializeHookObjects() {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['direct_mail']['mailFromDraft'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['direct_mail']['mailFromDraft'] as $hookObj) {
				$hookObjectInstance = GeneralUtility::getUserObj($hookObj);
				if (!(is_object($hookObjectInstance) && ($hookObjectInstance instanceof \DirectMailTeam\DirectMail\Scheduler\MailFromDraftHookInterface))) {
					throw new Exception('Hook object for "mailFromDraft" must implement the "MailFromDraftHookInterface"!', 1400866815);
				}
				$this->hookObjects[] = $hookObjectInstance;
			}
		}
	}
}

