<?php

/**
 * Hooks for the 'w_form_adds' extension.
 *
 * @author	wolo <wolo.wolski@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_wform_adds
 */
class user_wform_hooks	{

	/**
	* EXAMPLE CLASS - YOU CAN USE THIS AS DRAFT FOR YOUR WFORM ADDS
	*/

	/*function email_beforeSend(&$pObj, &$recipients, &$subject, &$plainContent, &$htmlContent, &$headers)	{
		$subject = 'hook modified subject';
	}*/

}



class user_wform_hooks_askform	{

	public $Form;
	

	function email_beforeSend(tx_wform_pi1 &$pObj, &$recipients, &$subject, &$contentPlain, &$htmlContent, &$headers, &$bcc)	{
		
		$this->Form = $pObj->getFormObj();
		

		// find recipients
		
			// get item record
		$item = tx_wzep_model::getItem($_POST['tx_wnews']['w_news']);
		
			// get it's partner and location
		$partnerLocation = tx_wzep_model::getPartnerAndLocation($item['location']);
		
		foreach($partnerLocation as $ploc)	{
			$recipients[] = trim($ploc['email_person']);
			$recipients[] = trim($ploc['email_location']);
		}

		
		// change recipients to bcc only
		$bcc = $recipients;
		$recipients = array();


		// prepare message
		
		$fieldsToRenderInEmail = $this->Form->getFieldNames();
		$contentPlain = '';

		// todo: implement this in w_form ext
		foreach ($fieldsToRenderInEmail as $fieldName)  {
			$idname = $this->Form->Field($fieldName)->getIdname();
			$value = $this->Form->Field($fieldName)->getValue();
			$contentPlain .= "{$pObj->pi_getLL('emailcontent_field_'.$idname, $idname)}: {$value}\n";
		}

		$contentPlain .=
			"\n".date('d.m.Y, H:i', $GLOBALS['EXEC_TIME'])
			."\n{$pObj->pi_getLL('emailcontent_field_ip', 'ip')}: {$pObj->getRealIpAddr()}"
			."\n{$pObj->pi_getLL('emailcontent_field_lang', 'language')}: {$pObj->LLkey}"
			;

		$urlItem = strip_tags($_POST['refurl']);
		$contentPlain .= "\n\n".$urlItem;
	}


	function content_otherMarkers(&$pObj, &$markers, &$subparts)	{
		$markers['###ADDITIONAL_INPUT###'] .= '<input type="hidden" name="tx_wnews[w_news]" value="'.intval($_GET['tx_wnews']['w_news']).'" />';
		$markers['###ADDITIONAL_INPUT###'] .= '<input type="hidden" name="refurl" value="'.'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'" />';
	}

}


?>