<?php
/**
 * wolo.pl '.' studio 2015
 *
 * w_tools WTP2 - misc helper object.
 * When it gets too large, extract some functionality to own classes
 */

die('should not be used!');
// moved to Classes/Hooks/Misc


class tx_wtools_misc {



	/*
	 * Process/modify top menu in page header
	 */

	/**
	 * @var bool indicate that user has no avatar and default is displayed
	 */
	public $noAvatar = false;


	function topMenuIProcFunc($I, $lConf)	{

		// does it allow to modify this config from here before executing?

		switch ($I['uid']) {
			case 64: 	// DB: Twoje konto
			case 261: 	// ML: Konto

				//$itemRow = $lConf['parentObj']->menuArr[$I['key']];
				//debugster($itemRow);
				//debugster($conf);
				//debugster(array_keys($lConf['parentObj']));
				//debugster(get_class($lConf['parentObj']));
				//debugster($I);

				// skad to sie bierze takie polaczone "Zaloguj / zarejestruj | Twoje konto"? gdzie to sie generuje?
				// skad bierze sie wyloguj?
				$split = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode('|', $I['parts']['title'] );

				if ($GLOBALS['TSFE']->fe_user->user)    {
					$conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wtools.'];

					// link id for inserting ajax alerts and class for custom styling this position including image

					$I['parts']['notATagBeforeWrap_begin'] = str_replace('class="', 'id="social_alert_notice_login" class="menu-username ', $I['parts']['notATagBeforeWrap_begin']);

					// username

					$usernameFormatted = $this->displayUsername($GLOBALS['TSFE']->fe_user->user);
					if ($usernameFormatted)
						$I['parts']['title'] = '<b>'.$usernameFormatted.'</b>';
					else
						// old style. why with marker? where it's replaced?
						$I['parts']['title'] = $split[1]. ' <b><!--###USERNAME###--></b>';

					// avatar thumbnail

					$avatarConf = $conf['avatarTopMenu.'];

					$avatarFile = $this->getAvatar(
						$GLOBALS['TSFE']->fe_user->user,
						['defaultAvatarPath' => $I['uid'] == 64 ? 'fileadmin/templates/default/images/social/' : $I['uid'] == 261 ? 'fileadmin/templates/medlearning/images/social/' : ''],
						$conf['avatarField']);

					$conf = array(
						'file' => $avatarFile,	// without preceeding slash
						'file.' => $avatarConf
					);

					//debugster($avatarFile);
					//debugster($conf);
					//debugster($conf);

					// todo: locallang, using ts setup local_lang key
					$tagParams = [
						'title' => $this->noAvatar ? 'brak obrazka' : '',
						'alt' => 'avatar',
						'class' => $avatarConf['class'],
						'height' => $avatarConf['height'] ? intval($avatarConf['height']) : '',
						'width' => $avatarConf['width'] ? intval($avatarConf['width']) : '',
					];


					/**
					 * @var $cObj \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
					 */
					$cObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');

					$tagParams['src'] = $cObj->IMG_RESOURCE($conf);
					//debugster($tagParams);
					if ($tagParams['src']) {
						$I['parts']['title'] .= '&nbsp;&nbsp; <img';
						foreach($tagParams as $param => $val){
							$I['parts']['title'] .= ($val ? ' '.$param.'="'.$val.'"' : '');
						}

						$I['parts']['title'] .= '>';
					}
					else
						$I['parts']['title'] .= '&nbsp;&nbsp;'. (DEV?'image error @ tx_wtools_misc':'');

				} else  {
					$I['parts']['title'] = $split[0];
				}
				break;
			default:
				break;
		}
		return $I;
	}


	/**
	 * Get avatar path + filename
	 *
	 * @param array  $row
	 * @param array  $conf
	 * @param string $avatarField
	 * @return string image path
	 */
	public function getAvatar($row, $conf = [], $avatarField = 'image') {
		// sr_feuserregister avatar (first image)
		//t3lib_div::loadTCA("fe_users");
		//debugster($GLOBALS['TCA']['fe_users']['columns']['image']['config']['uploadfolder']);
		// z jakiegos powodu wchodzac przez type 945 uploadfolder w tca sie rozni...
		// todo: to musi byc konfigurowalne, dla np. avatarow grup bedzie inny
		$path = 'uploads/tx_srfeuserregister';
		if ($row[$avatarField]) {
			return $path . '/'.$row[$avatarField];
		}
		$this->noAvatar = true;
		return $conf['defaultAvatarPath'].'avatar-default-'.($row['gender']==1?'f':'m').'.png';
	}



	/**
	 * Generates user name string to display in various places as link to profile
	 *
	 * taken from w_medl viewhelper general
	 *
	 * @param array $row user row
	 * @return string
	 */
	public function displayUsername($row){
		$parts = [];
		if ($row['first_name'] || $row['last_name']) {
			$parts[] = '<span class="user_fullname text-capitalize">' . $row['first_name'] . ($row['last_name'] ? ' ' . $row['last_name'] : '') . '</span>';
			$isFullName = true;
		}
		$parts[] = $isFullName ?
			'<span class="user_username">('.$row['username'].')</span>':
			'<span class="user_fullname">'.$row['username'].'</span>';

		return  implode(' ', $parts);
	}


	/**
	 * to z jakiegos powodu nie dziala, nie wywoluje sie
	 */
	function rekrutacjaMenuIProcFunc($I,$conf)	{
		die('rekrutacjaMenuIProcFunc');
		switch ($I['uid']) {

			case 205: // menu/rejestracja usera
			case 206: // menu/rejestracja firmy

				$split = t3lib_div::trimExplode('|', $I['parts']['title'] );
				if ($GLOBALS['TSFE']->fe_user->user)    {
					$I['parts']['title'] = $split[1]. ' <b><!--###USERNAME###--></b>';
				} else  {
					$I['parts']['title'] = $split[0];
				}
				break;
			default:
				break;
		}
		return $I;
	}
}
