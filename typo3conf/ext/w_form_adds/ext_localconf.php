<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}


// hooks can be also set by typoscript - it can be handy especially when using specifiedInstance.
// you can then set hooks only for some instance, not globally like here.
// @see ext_typoscript_setup.txt

//$TYPO3_CONF_VARS['EXTCONF']['w_form']['hook_email_beforeSend'][] = 'user_wform_hooks';




if (TYPO3_MODE!='BE') {
	require_once(t3lib_extMgm::extPath('w_form_adds').'class.user_wform_hooks.php');
}

?>