<?php
/**
 * wolo.pl '.' studio 2015 - 2016
 *
 * w_tools - misc hooks
 */

namespace WTP\WTools\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;



class Misc  {



	/*
	 * Process/modify top menu in page header
	 */
	function topMenuIProcFunc($I, $lConf)	{


		// UWAGA DODAWANIE TUTAJ AVATARA I GENEROWANIE USERNAME NA TYM ETAPIE POWODUJE PROBLEMY
		// CACHUJE SIE INNY USER! TRZEBA SIE WPIAC W HOOKA W TSFE TAM, GDZIE SYSTEM SAM TEN MARKER ZASTEPUJE
		// TUTAJ GENERUJE SIE JEDYNIE CUSTOM MARKER, KTORY JAKO HOOK WSTAWIA TO W POSTPROCESSINGU


		switch ($I['uid']) {
			case 64: 	// DB: Twoje konto
			case 261: 	// ML: Luxmed / Konto
			case 336: 	// ML: PZ / Konto


				//$itemRow = $lConf['parentObj']->menuArr[$I['key']];
				//debugster($itemRow);
				//debugster($conf);
				//debugster(array_keys($lConf['parentObj']));
				//debugster(get_class($lConf['parentObj']));
				//debugster($I);


				// split title if it has | in page name
				$split = GeneralUtility::trimExplode('|', $I['parts']['title'] );

				if ($GLOBALS['TSFE']->fe_user->user)    {

					// link id for inserting ajax alerts and class for custom styling this position including image
					// checking if social_alert_notice_login exists in markup may be used to determine if ajax alerts get call should be run
					$I['parts']['notATagBeforeWrap_begin'] = str_replace('class="', 'id="social_alert_notice_login" class="menu-username ', $I['parts']['notATagBeforeWrap_begin']);

					// if split succeeded (title has | char) set it to second part when logged in. if not splitted, just leave it (set it to first splitpart)
					$I['parts']['title'] = $split[1] ? $split[1] : $split[0];


					// by default in typo, the <!--###USERNAME###--> is substituted with username in tsfe postprocessing
					// @see tsref config.USERNAME_substToken
					// note that it's replaced with whole comment, not only marker!
					// here we use custom marker, which is later replaced in tsfe hook
					$I['parts']['title'] .= '<b><!--###USERNAME_AVATAR###--></b>';

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
	 * @var bool indicate that user has no avatar and default is displayed
	 */
	public $noAvatar = false;





	/**
	 * In postprocessing insert proper username and avatar into marker (usually in menu, in user page link)
	 * used for hook tslib_fe-contentStrReplace
	 * @param array $params     - 'search' and 'replace' array pairs to substitute in generated content (like markers)
	 * @param array $pObj
	 */
	public function menuUsernameAvatarMarkerReplace(&$params, &$pObj) {

		$username = '';
		$avatar = '';

		if ($GLOBALS['TSFE']->fe_user->user) {

			$conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wtools.'];

			$defaultAvatarPath = 'fileadmin/templates/default/images/social/';

			if (defined('MEDLEARNING')  &&  MEDLEARNING)
				$defaultAvatarPath = 'fileadmin/templates/medlearning/images/social/';

			//
			// user name formatted

			$username = $this->displayUsername($GLOBALS['TSFE']->fe_user->user);
			//debugster($username);


			//
			// avatar thumbnail

			$avatarConf = $conf['avatarTopMenu.'];

			$avatarFile = $this->getAvatar(
				$GLOBALS['TSFE']->fe_user->user,
				['defaultAvatarPath' => $defaultAvatarPath],
				$conf['avatarField']
			);

			$conf = [
				'file'  => $avatarFile,    // without preceeding slash
				'file.' => $avatarConf
			];

			//debugster($avatarFile);
			//debugster($conf);

			// todo: locallang, using ts setup local_lang key
			$tagParams = [
				'title'  => $this->noAvatar ? 'brak obrazka' : '',
				'alt'    => 'avatar',
				'class'  => $avatarConf['class'],
				'height' => $avatarConf['height'] ? intval($avatarConf['height']) : '',
				'width'  => $avatarConf['width'] ? intval($avatarConf['width']) : '',
			];


			/**
			 * @var $cObj \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
			 */
			$cObj = GeneralUtility::makeInstance('\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');

			$tagParams['src'] = $cObj->IMG_RESOURCE($conf);
			//debugster($tagParams);

			if ($tagParams['src']) {
				// build tag using generated parameters
				$avatar .= '<img';
				foreach ($tagParams as $param => $val) {
					$avatar .= ($val ? ' ' . $param . '="' . $val . '"' : '');
				}
				$avatar .= '>';
			} else
				$avatar .= (DEV ? 'image error @ \WTP\WTools\Hooks\Misc' : '');
		}


		$params['search'][] = '<!--###USERNAME_AVATAR###-->';
		$params['replace'][] = '&nbsp;&nbsp;' . $username . '&nbsp;&nbsp;' . $avatar;
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
		//debugster($GLOBALS['TCA']['fe_users']['columns']['image']['config']['uploadfolder']);
		// z jakiegos powodu wchodzac przez type 945 uploadfolder w tca sie rozni...
		// todo: to musi byc konfigurowalne, dla np. avatarow grup bedzie inny
		$path = 'uploads/tx_srfeuserregister';
		if ($row[$avatarField]) {
			return $path . '/'.$row[$avatarField];
		}
		$this->noAvatar = true;
		return $conf['defaultAvatarPath'] . 'avatar-default-'.($row['gender']==1?'f':'m').'.png';
	}



	/**
	 * Generates user name string to display in various places as link to profile
	 *
	 * taken from w_medl viewhelper general
	 *
	 * @param array $row user row
	 * @return string
	 */
	public function displayUsername($row)   {
		$isFullName = false;
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
