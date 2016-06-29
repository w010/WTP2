<?php

require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('site_cedris').'class.tx_sitecedris_model.php');
require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('feuser_friends').'pi1/class.tx_feuserfriends_pi1.php');

use \TYPO3\CMS\Core\Utility\GeneralUtility;


class tx_sitecedris_ttnewshooks {
	/**
	 * @var tx_ttnews
	 */
	var $tx_ttnews;

	/**
	 * @var tslib_cObj
	 */
	var $cObj;

	/**
	 * Current tt_news CODE
	 * @var string
	 */
	var $theCode;

	/**
	 * tt_news config
	 * @var Array
	 */
	var $conf;

	
	
	/**
	 * Set up local vars
	 * @param tx_ttnews $pObj
	 */
	private function _initLocal(tx_ttnews &$pObj) {
		$this->tx_ttnews = &$pObj;
		$this->theCode = $this->tx_ttnews->theCode;
		$this->cObj = &$this->tx_ttnews->local_cObj;
		$this->conf = &$this->tx_ttnews->conf;
	}


	public function processSingleViewLink(&$linkWrap, &$url, &$params, &$pObj)  {
		$this->tx_ttnews = &$pObj;
		if ($_GET['type']==950) {
			//echo "<pre>"; //echo chr(10).chr(10);
			//var_dump($url);
			// clear url - remove ajax params, which are passed to singleview links. remove all after "?"
			list ($url) = explode('?', $url);
			$linkWrap [0] = preg_replace('/href="(.*?)"/', 'href="'.$url.'"', $linkWrap[0]);
		}
	}
	
	
	public function extraItemMarkerProcessor(array $markerArray, array $row, array $lConf, tx_ttnews &$pObj) {
		$this->_initLocal($pObj);
		$this->cObj->__userNewsCount++;
		
		$markerArray['###NEWS_ITEM###'] = $this->cObj->__userNewsCount;
		//$markerArray['###NEWS_COMMENTS###'] = $this->getMMForumComments();

		if ($pObj->theCode == 'SINGLE') {
			// dossiers
			if ($GLOBALS['TSFE']->id == 5)  {    // to conf!
				$urlconf = array('parameter' => 40, 'additionalParams' => '&tx_ttnews[tt_news]=' .
					intval($this->tx_ttnews->piVars['tt_news']), 'useCacheHash' => true);
				// dossiers links from "single standalone" to standard "single" (single_detail)
				$markerArray['###URL_DETAIL###'] = $this->cObj->typoLink_URL($urlconf);
			}
			// publicaties
			else if ($GLOBALS['TSFE']->id == 13)  {    // to conf!
				$urlconf = array('parameter' => 49, 'additionalParams' => '&tx_ttnews[tt_news]=' .
					intval($this->tx_ttnews->piVars['tt_news']), 'useCacheHash' => true);
				$markerArray['###URL_DETAIL###'] = $this->cObj->typoLink_URL($urlconf);
			}
			// praktijk & dossiers detail
			else if ($GLOBALS['TSFE']->id == 33  ||  $GLOBALS['TSFE']->id == 40)  {    // to conf!
				$markerArray['###FUNCTION###'] = $row['tx_sitecedris_praktik_function'];
				$markerArray['###PHONE###'] = $row['tx_sitecedris_praktik_phone'];
				$mailto = $this->cObj->getMailTo($row['tx_sitecedris_praktik_email'] , '');
				$markerArray['###EMAIL###'] = '<a href="#" class="mail" onclick="'.$mailto[0].' return false;">'.$mailto[1].'</a>';

					/**
					 * @var $Ff tx_feuserfriends_pi1
					 */
					/*$Ff = GeneralUtility::makeInstance('tx_feuserfriends_pi1');
					$Ff->cObj = $this->cObj;
					$ffConf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_feuserfriends_pi1.'];
					$ffConf['templateFile'] = $this->conf['templateFile_swList'];
					$ffConf['pidList'] = 45;
					$ffConf['context'] = 'tt_news';	// to check where we are in select conf hook
					$ffConf['listView.']['fields'] = 'name,image';
					$ffConf['listView.']['imgWraps.']['image.']['file.']['maxW'] = '176';
					$ffConf['listView.']['imgWraps.']['image.']['file.']['maxH'] = '100';
					//debugster($Ff->main('', $ffConf));
					//tx_sitecedris_sw_bedrijven
					$markerArray['###NEWS_SW###'] = $Ff->main('', $ffConf);*/
					//debugster($ffConf);

				//debugster($lConf);
				// other way - render list of images with captions, make ttnews generate them
				$tempRow = [
					'image' => $row['tx_sitecedris_praktik_logo'],
					'imagecaption' => $row['tx_sitecedris_praktik_title']
				];
				$lConf['praktijkDetailSwImage.']['imageCount'] = count(explode(',', $row['tx_sitecedris_praktik_logo']));
				$lConf['praktijkDetailSwImage.']['listImageMode'] = 'resize';
				$tempMA = $pObj->getImageMarkers([], $tempRow, $lConf['praktijkDetailSwImage.'], 'displayList');
				//debugster($tempRow);
				//debugster($tempMA);

				$markerArray['###NEWS_SW###'] = $tempMA['###NEWS_IMAGE###'];
			}
			// blog
			else if ($GLOBALS['TSFE']->id == 37) {    // to conf!
				if ($row['tx_sitecedris_author']) {
					$res_author = $pObj->db->exec_SELECTgetRows('tx_sitecedris_image', 'be_users', 'uid = ' . intval($row['tx_sitecedris_author']));
					//debugster($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
					//debugster($res_author);
					if ($res_author[0]['tx_sitecedris_image']) {

						$tempRow = [
							'image' => $res_author[0]['tx_sitecedris_image'],
							'imagecaption' => 'Door: '.($row['author'] ? $row['author'] : $res_author[0]['realName'])
						];
						$lConf['blogDetailAuthorImage.']['imageCount'] = 1;
						$lConf['blogDetailAuthorImage.']['listImageMode'] = 'resize';
						$tempMA = $pObj->getImageMarkers([], $tempRow, $lConf['blogDetailAuthorImage.'], 'displayList');
						//debugster($tempRow);
						//debugster($tempMA);

						$markerArray['###BLOG_AUTHOR###'] = $tempMA['###NEWS_IMAGE###'];
					}
				}
			}
		}

		//		$markerArray['###ADDITIONAL_CONTENT_1###'] = $this->renderCE(581);

		$markerArray['###KEYWORDS###'] = $row['keywords'];


		/*if ($pObj->conf['renderRelatedNewsAsList']) {
			$relatedNews = $this->getRelatedNewsAsList($row['uid']);
		} else {*/
			$backgroundNews = $this->getBackground($row['uid']);
		//}


		if ($backgroundNews) {
			$rel_stdWrap = GeneralUtility::trimExplode('|', $pObj->conf['related_stdWrap.']['wrap']);
			$markerArray['###NEWS_BACKGROUND###'] = $backgroundNews;
		}
//		debugster($row['tx_sitecedris_background_links']);
//		debugster(tx_sitecedris_model::getBgLinks($row['tx_sitecedris_background_links']));
//		$links = tx_sitecedris_model::getBgLinks($row['tx_sitecedris_background_links'])
		//foreach(GeneralUtility::trimExplode("\n", $row['tx_sitecedris_background_links']) as $linkRow)   {
		foreach(tx_sitecedris_model::getBgLinks($row['tx_sitecedris_background_links']) as $bgLink)   {
			//debugster($bgLink);
			$markerArray['###NEWS_BACKGROUND###'] .= '<dd>'.$this->cObj->typoLink(
				$bgLink['label'] ? $bgLink['label'] : $bgLink['link'],  [
					'parameter' => $bgLink['link'],
					//'wrap' => '<dd>|</dd>'
				]
			) . '</dd>';
			/*list($link, $label) = GeneralUtility::trimExplode('|', $linkRow);
			if ($link)  $markerArray['###NEWS_BACKGROUND###'] .= '<dd><a href="'.$link.'" target="_blank" class="external-link">'.$label.'</a></dd>';*/
		}
		$markerArray['###NEWS_BACKGROUND###'] .=  $rel_stdWrap[1];


//		debugster($row);
//		debugster($markerArray);
		return $markerArray;
	}



	public function additionalFormSearchFields(tx_ttnews &$pObj, array $markerArray) {
		$markerArray['###PLACEHOLDER###'] = $pObj->pi_getLL('searchPlaceholder', 'Zoek naar nieuws');

		switch ($pObj->theCode) {
			// on blog list replace image with author picture
			case 'SEARCH_FILTER_PRAKTIJK':
				$markerArray['###filter_cat_options###'] = self::makeDroplistOptions( self::getCategoriesForDroplist(' AND parent_category = '.intval($pObj->actuallySelectedCategories)), false, $pObj->piVars['cat']);
				$markerArray['###filter_sw_options###'] = self::makeDroplistOptions( self::getSWForDroplist(), false, $pObj->piVars['filter']['sw']);
				$markerArray['###filter_arbeidsmarktregio###'] = self::makeDroplistOptions( self::getRegioForDroplist(), false, $pObj->piVars['filter']['arbeidsmarktregio']);
		}

		return $markerArray;
	}


	/**
	 * custom hook from modified tt_news. search for "wolo mod" in plugin source
	 * @param           $row
	 * @param           $lconf
	 * @param tx_ttnews $pObj
	 * @return mixed
	 */
	public function extraItemArrayProcessor($row, $lconf, tx_ttnews $pObj)    {
		$this->_initLocal($pObj);
		// BLOG
		if ($row['tx_sitecedris_author'])   {
			$res_author = $pObj->db->exec_SELECTgetRows('tx_sitecedris_image', 'be_users', 'uid = '.intval($row['tx_sitecedris_author']));
			//debugster($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
			if ($res_author[0]['tx_sitecedris_image'])  {
				switch ($pObj->theCode) {
					// on blog list replace image with author picture
					case 'LIST':
				        $row['image'] = $res_author[0]['tx_sitecedris_image'];   break;
					// on blog single use author picture as first image
					//case 'SINGLE':
				    //    $row['image'] = $row['image'] ? ($res_author[0]['tx_sitecedris_image'] . ',' . $row['image']) : $res_author[0]['tx_sitecedris_image'];   break;
				}
			}
		}

		// PUBLICATIES (and dossiers) INTERNAL LINK
		// because we use SINGLE on overview, we need to disable autoredirect for singleview link-type news.
		if ($GLOBALS['TSFE']->id == 13  ||  $GLOBALS['TSFE']->id == 5)   {
			$row['type'] = 0;   // reset type. on this level we don't even need this anymore
		}

		//debugster($row);
		return $row;
	}


	public function extraGlobalMarkerProcessor(tx_ttnews &$pObj, array $markerArray) {
		$this->_initLocal($pObj);


		// news page - ajax options
		$markerArray['###PAGEBROWSER_AJAX###'] = $this->makeAjaxPagebrowser();

		$markerArray['###AJAX_URL###'] = $this->makeLink_ajax_baseurl([]);
		$markerArray['###DEV###'] = DEV ? 'true' : 'false';

		if ($this->theCode == 'LIST')
			$markerArray['###COUNTER###'] = (int)$this->tx_ttnews->newsCount . ' resultaten';

		// links on (homepage) lists. cat link works only if one cat is selected
		$catRow = $this->getCategoryRow($this->tx_ttnews->catExclusive, 'uid, title');
		$markerArray['###LINK_CAT###'] = $this->makeLink($catRow['title'], ['cat'=>$catRow['uid']], 1, 1, 4);  // pid to conf!

		$markerArray['###LINK_ALL###'] = $this->makeLink('BEKIJK AL HET NIEUWS', [], 1, 1, 4);  // pid to conf! label to ll!

		return $markerArray;
	}


	public function makeAjaxPagebrowser()   {
		$pbConf = $this->conf['pageBrowser.'];
		//$pbConf['showResultCount'], $pbConf['tableParams'], $this->pointerName

		/*debugster($this->tx_ttnews->internal);
		debugster($this->tx_ttnews->internal['res_count']); // to jest puste, gdy np. pagebrowser sie nie generuje, bo jest mniej niz 1 strona. wtedy pokazuje count 0...
		debugster($this->tx_ttnews->newsCount);
		debugster($this->tx_ttnews->internal['results_at_a_time']);
		debugster($this->tx_ttnews->internal['maxPages']);*/

		$pointer = (int)$this->tx_ttnews->piVars['pointer'];
		//$count = (int)$this->tx_ttnews->internal['res_count'];
		$count = (int)$this->tx_ttnews->newsCount;
		$results_at_a_time = \TYPO3\CMS\Core\Utility\MathUtility::forceIntegerInRange($this->tx_ttnews->internal['results_at_a_time'], 1, 1000);
		$totalPages = ceil($count / $results_at_a_time);
		$maxPages = \TYPO3\CMS\Core\Utility\MathUtility::forceIntegerInRange($this->tx_ttnews->internal['maxPages'], 1, 100);

		if ($count > $results_at_a_time) {
			$_pagebrowser = 'pagina <input id="pb_ajax_page" value="'.($pointer+1).'" onchange=""> van '.$totalPages;

			$_pagebrowser .= '<nav><ul class="pagination">';
			$_pagebrowser .= '<li><a href="#" onclick="wtpAjax.ttnews_getView(this, \'LIST\', '.intval($this->tx_ttnews->piVars['cat']).', \'tx_ttnews[cat]='.intval($this->tx_ttnews->piVars['cat']).'&amp;tx_ttnews[pointer]='.($pointer?$pointer-1:'0').'\', \'#ajax-target-list\'); return false;" class="page-prev '.($pointer==0?'disabled':'').'">&nbsp;</a></li>';
			$_pagebrowser .= '<li><a href="#" onclick="wtpAjax.ttnews_getView(this, \'LIST\', '.intval($this->tx_ttnews->piVars['cat']).', \'tx_ttnews[cat]='.intval($this->tx_ttnews->piVars['cat']).'&amp;tx_ttnews[pointer]='.($totalPages>$pointer+1?$pointer+1:$pointer).'\', \'#ajax-target-list\'); return false;" class="page-next '.($pointer+1==$totalPages?'disabled':'').'">&nbsp;</a></li>';
			$_pagebrowser .= '</ul></nav>';
		}

		$tmpl = '<div class="count-all"><p class="counter">###COUNTER###</p> <div class="pagebrowser-wrap">###PAGEBROWSER###</div></div>';
		$_counter = $count.' nieuwsberichten';

		return str_replace(['###COUNTER###', '###PAGEBROWSER###'], [$_counter, $_pagebrowser], $tmpl);
	}


	
	public function extraCodesProcessor(tx_ttnews &$pObj) {
		$this->_initLocal($pObj);
		$content = '';
		//
		switch ($pObj->theCode) {

			case 'SEARCH_FORM':
			case 'SEARCH_FILTER_PRAKTIJK':
				$prefix_display = 'displayList';
				$templateName = 'TEMPLATE_LIST';

				// Make markers for the searchform
				$searchMarkers = array(
					'###FORM_URL###' => $pObj->pi_linkTP_keepPIvars_url(array('pointer' => null, 'cat' => null), 0, 1, $pObj->config['searchPid']),
					'###SWORDS###' => htmlspecialchars($pObj->piVars['swords']),
					'###PLACEHOLDER###' => $pObj->pi_getLL('searchPlaceholder', 'Zoek naar nieuws'),
					'###SEARCH_BUTTON###' => $pObj->pi_getLL('searchButtonLabel'));

				// Hook for any additional form fields
				if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['additionalFormSearchFields'])) {
					foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['additionalFormSearchFields'] as $_classRef) {
						$_procObj = & GeneralUtility::getUserObj($_classRef);
						$searchMarkers = $_procObj->additionalFormSearchFields($pObj, $searchMarkers);
					}
				}

				// Add to content
				$searchSub = $pObj->getNewsSubpart($pObj->templateCode, $pObj->spMarker('###TEMPLATE_'.$pObj->theCode.'###'));

				$renderMarkers = $pObj->getMarkers($searchSub);
				$pObj->renderMarkers = array_unique($renderMarkers);

				$content .= $pObj->cObj->substituteMarkerArray($searchSub, $searchMarkers);
				unset($searchSub);
				unset($searchMarkers);

				break;

			case 'CATMENU_SIMPLE':
				$this->conf['displayCatMenu.']['mode'] = 'w_simple';
				$content .= $pObj->displayCatMenu();
				break;

			case 'LIST_WEEK':
				$content .= $pObj->displayList();
				break;
		}

		return $content;
	}

	/**
	 * Simple category menu, with title, description and image
	 * @param array $lConf
	 * @param tx_ttnews $pObj
	 * @return string HTML code for category menu
	 */
	public function userDisplayCatmenu(array $lConf, tx_ttnews &$pObj) {
		$this->_initLocal($pObj);
		//debugster($lConf);
		// important
		switch ($lConf['mode']) {
			case 'w_ajaxCat':
				// skopiowane z klasy tx_ttnews i zmodyfikowane
				$fields = '*';
				$lConf = $pObj->conf['displayCatMenu.'];
				/*$addCatlistWhere = '';
				if ($pObj->dontStartFromRootRecord) {
					$addCatlistWhere = 'tt_news_cat.uid IN (' . implode(',', $pObj->cleanedCategoryMounts) . ')';
				}*/
				$categoryUids = $pObj->db->cleanIntList($lConf['categoryUids']);

				//$addCatlistWhere = 'tt_news_cat.parent_category IN (' . implode(',', $pObj->cleanedCategoryMounts) . ')';
				$pObj->dontStartFromRootRecord = 1;
				$addCatlistWhere = 'tt_news_cat.uid IN (' . $categoryUids . ')';
				$res = $pObj->db->exec_SELECTquery($fields, 'tt_news_cat', ($pObj->dontStartFromRootRecord ? $addCatlistWhere : 'tt_news_cat.parent_category=0') . $pObj->SPaddWhere . $pObj->enableCatFields . $pObj->catlistWhere, '', $pObj->config['catOrderBy']);   //      catOrderBy = FIELD(tt_news_cat.uid, 2,3,1)
				//debugster($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
				$content = '';
				$content .= $pObj->local_cObj->stdWrap($pObj->pi_getLL('catmenuHeader', 'Select a category:'), $lConf['catmenuHeader_stdWrap.']);

				while (($row = $pObj->db->sql_fetch_assoc($res))) {
					//$cArr[] = $row;
					$title = $row['title'];
					//list ($description) = GeneralUtility::trimExplode('position:', $row['description']);
					$icon = '';

					// wolo: buduje tu po swojemu html z catmenu. image ukradlem z ttnews_categorytree
					if ($row['image'])  {
						$iconConf['image.']['file'] = $lConf['catmenuIconPath'].$row['image'];
						if ($iconConf['image.']['file']) {
							$iconConf['image.']['file.'] = $lConf['catmenuIconFile.'];
							$icon = $GLOBALS['TSFE']->cObj->IMAGE($iconConf['image.']);
						}
					}

					// to z kolei kradzione z getCatMenuContent()
					if ($pObj->tsfe->sys_language_content) {
						// get translations of category titles
						$catTitleArr = GeneralUtility::trimExplode('|', $row['title_lang_ol']);
						$syslang = $pObj->tsfe->sys_language_content - 1;
						$title = $catTitleArr[$syslang] ? $catTitleArr[$syslang] : $row['title'];
					}
					$catSelLinkParams = ($this->conf['catSelectorTargetPid'] ? ($pObj->conf['itemLinkTarget'] ? $pObj->conf['catSelectorTargetPid'] . ' ' . $pObj->conf['itemLinkTarget'] : $pObj->conf['catSelectorTargetPid']) : $pObj->tsfe->id);
					/*$pTmp = $pObj->tsfe->ATagParams;
					if ($pObj->conf['displayCatMenu.']['insertDescrAsTitle']) {
						$pObj->tsfe->ATagParams = ($pTmp ? $pTmp . ' ' : '') . 'title="' . $description . '"';
					}*/
					if ($row['uid']) {
						$link = $pObj->pi_linkTP_keepPIvars_url(array('cat' => $row['uid']), $pObj->allowCaching, 1, $catSelLinkParams);

						if ($pObj->piVars['cat'] == $row['uid']) {
							$wrap = $lConf['catmenuItem_ACT_stdWrap.'];
						} else {
							$wrap = $lConf['catmenuItem_NO_stdWrap.'];
						}
					} else {
						$link = $pObj->pi_linkTP_keepPIvars($title, array(), $pObj->allowCaching, 1, $catSelLinkParams);
					}

					$_itemContent = $pObj->local_cObj->stdWrap(// '<a href="'.$link.'">'
						$pObj->local_cObj->stdWrap( '<a onclick="wtpAjax.controlAjaxCatButtons(this); wtpAjax.ttnews_getView(this, \'LIST\','.intval($row['uid']).', \'tx_ttnews[cat]='.intval($row['uid']).'\', \'#ajax-target-list\'); return false;" id="catSel_'.intval($row['uid']).'" href="'.$link.'">'.$title.'</a>', $lConf['catmenuItem_title_stdWrap.'])
						//. $pObj->local_cObj->stdWrap( '<a href="'.$link.'">'.$icon.'</a>', $lConf['catmenuItem_image_stdWrap.'])
						//. $pObj->local_cObj->stdWrap( '<a href="'.$link.'">'.$description.'</a>', $lConf['catmenuItem_description_stdWrap.'])
						, $wrap );

					$content .= $pObj->local_cObj->stdWrap($_itemContent, $lConf['catmenuItem_whole_stdWrap.']);
				}

				$pObj->db->sql_free_result($res);
				//print($content);
				//die();
				return $content;
				//break;

			// CATMENU_SIMPLE
			case 'w_simple':
				// skopiowane z klasy tx_ttnews i zmodyfikowane
				$fields = '*';
				$lConf = $pObj->conf['displayCatMenu.'];
				//debugster($pObj->conf);
				//debugster($pObj->actuallySelectedCategories);

				//debugster($pObj->SPaddWhere );
				//debugster( $pObj->enableCatFields );
				//debugster($pObj->catlistWhere);

				/*$addCatlistWhere = '';
				if ($pObj->dontStartFromRootRecord) {
					$addCatlistWhere = 'tt_news_cat.uid IN (' . implode(',', $pObj->cleanedCategoryMounts) . ')';
				}*/
				//$addCatlistWhere = 'tt_news_cat.parent_category IN (' . implode(',', $pObj->cleanedCategoryMounts) . ')';

				if ($pObj->actuallySelectedCategories)
					$categoryUids = $pObj->actuallySelectedCategories;
				else
					$categoryUids = $pObj->db->cleanIntList($lConf['categoryUids']);

				$pObj->dontStartFromRootRecord = 1;
				$addCatlistWhere = 'tt_news_cat.parent_category IN (' . $categoryUids . ')';
				$res = $pObj->db->exec_SELECTquery($fields, 'tt_news_cat', ($pObj->dontStartFromRootRecord ? $addCatlistWhere : 'tt_news_cat.parent_category=0') . $pObj->SPaddWhere . $pObj->enableCatFields, '', 'tt_news_cat.' . $pObj->config['catOrderBy']);
				//debugster($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
				$content = '';
				//$content .= $pObj->local_cObj->stdWrap($pObj->pi_getLL('catmenuHeader', 'Select a category:'), $lConf['catmenuHeader_stdWrap.']);

				while (($row = $pObj->db->sql_fetch_assoc($res))) {
					//$cArr[] = $row;
					$title = $row['title'];
					//list ($description) = GeneralUtility::trimExplode('position:', $row['description']);
					$icon = '';

					/*if ($row['image'])  {
						$iconConf['image.']['file'] = $lConf['catmenuIconPath'].$row['image'];
						if ($iconConf['image.']['file']) {
							$iconConf['image.']['file.'] = $lConf['catmenuIconFile.'];
							$icon = $GLOBALS['TSFE']->cObj->IMAGE($iconConf['image.']);
						}
					}*/

					// to z kolei kradzione z getCatMenuContent()
					if ($pObj->tsfe->sys_language_content) {
						// get translations of category titles
						$catTitleArr = GeneralUtility::trimExplode('|', $row['title_lang_ol']);
						$syslang = $pObj->tsfe->sys_language_content - 1;
						$title = $catTitleArr[$syslang] ? $catTitleArr[$syslang] : $row['title'];
					}
					$catSelLinkParams = ($this->conf['catSelectorTargetPid'] ? ($pObj->conf['itemLinkTarget'] ? $pObj->conf['catSelectorTargetPid'] . ' ' . $pObj->conf['itemLinkTarget'] : $pObj->conf['catSelectorTargetPid']) : $pObj->tsfe->id);
					/*$pTmp = $pObj->tsfe->ATagParams;
					if ($pObj->conf['displayCatMenu.']['insertDescrAsTitle']) {
						$pObj->tsfe->ATagParams = ($pTmp ? $pTmp . ' ' : '') . 'title="' . $description . '"';
					}*/
					if ($row['uid']) {
						$link = $pObj->pi_linkTP_keepPIvars_url(array('cat' => $row['uid']), $pObj->allowCaching, 1, $catSelLinkParams);

						if ($pObj->piVars['cat'] == $row['uid']) {
							$wrap = $lConf['catmenuItem_ACT_stdWrap.'];
						} else {
							$wrap = $lConf['catmenuItem_NO_stdWrap.'];
						}
					} else {
						$link = $pObj->pi_linkTP_keepPIvars($title, array(), $pObj->allowCaching, 1, $catSelLinkParams);
					}

					$_itemContent = $pObj->local_cObj->stdWrap(// '<a href="'.$link.'">'
						$pObj->local_cObj->stdWrap( '<a href="'.$link.'">'.$title.'</a>', $lConf['catmenuItem_title_stdWrap.'])
						//. $pObj->local_cObj->stdWrap( '<a href="'.$link.'">'.$icon.'</a>', $lConf['catmenuItem_image_stdWrap.'])
						//. $pObj->local_cObj->stdWrap( '<a href="'.$link.'">'.$description.'</a>', $lConf['catmenuItem_description_stdWrap.'])
						, $wrap );

					$content .= $pObj->local_cObj->stdWrap($_itemContent, $lConf['catmenuItem_whole_stdWrap.']);
				}

				$pObj->db->sql_free_result($res);
				//print($content);
				//die();
				return $content;
		}
	}




	public function processSelectConfHook(tx_ttnews &$pObj, array $selectConf) {
		$this->_initLocal($pObj);

		/*debugster($pObj->piVars);
		debugster($selectConf);*/

		if ($pObj->piVars['filter']['arbeidsmarktregio'] > 0)  {
			$selectConf['where'] .= ' AND tt_news.tx_sitecedris_sw_arbeidsmarktregio = ' . intval($pObj->piVars['filter']['arbeidsmarktregio']);
		}
		if ($pObj->piVars['filter']['sw'] > 0)  {
			$selectConf['where'] .= " AND ( CONCAT(',', tt_news.tx_sitecedris_sw_bedrijven, ',') LIKE '%,".intval($pObj->piVars['filter']['sw']).",%')";
		}


		if ($pObj->theCode == 'LIST_WEEK')  {
			// display only items from last week
			$selectConf['where'] .= " AND datetime BETWEEN " . strtotime('-1 week') . ' AND ' . $GLOBALS['EXEC_TIME'];
		}

		//debugster($selectConf['where']);

		/*if ($pObj->piVars['filter']['sw'])
		if ($pObj->piVars['filter']['arbeidsmarktregio'])*/
		//debugster($selectConf);


		/* wolo ttnews full query debug:
		$GLOBALS['w_ttnews_showSQL'] = 1;
		needs this code in ttnews pi class in getListContent, right after debug(...
		if (isset($GLOBALS['w_ttnews_showSQL']) && $GLOBALS['w_ttnews_showSQL'])
			debugster($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery); */


		
		// just modify config - there's no need to modify $selectConf
		/*switch ($pObj->config['orderBy']) {
			case 'sorting':
				// nothing to change, sorting is sorting
				break;
			case 'fixed_sorting':
				$pObj->config['orderBy'] = 'sorting_fixed, sorting';
				break;
			case 'fixed_datetime':
				$pObj->config['orderBy'] = 'sorting_fixed, datetime DESC';
				//$pObj->config['ascDesc'] = ''; // just for sure
				break;
		}*/
		
		return $selectConf;
	}

	/**
	 * SelectConf hook called in SINGLE view
	 *
	 * @param tx_ttnews $pObj
	 * @param array     $selectConf
	 * @return array
	 */
	public function processSViewSelectConfHook(tx_ttnews &$pObj, array $selectConf) {
		$this->_initLocal($pObj);
		
		return $selectConf;
	}
	
	/**
	 * @param unknown_type $linkWrap
	 * @param unknown_type $url
	 * @param array $params
	 * @param tx_ttnews $pObj
	 * @return void - modify refs
	 */
	public function getSingleViewLinkHook($linkWrap, $url, array $params, tx_ttnews &$pObj) {
		$this->_initLocal($pObj);
	}


	/**
	 * custom hook! manipulate rows array, after query, before displaying list
	 * @param $pObj tx_ttnews
	 * @param $rows array
	 */
	public function processListElements(&$pObj, &$rows) {
		$this->_initLocal($pObj);
	}



	public function getCategoryRow($uid, $fields = '*') {
		$catRows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows($fields,'tt_news_cat','uid='.intval($uid).' AND deleted=0');
		if (is_array($catRows))     return array_pop($catRows);
		else    return [];
	}



	/**
	 *      SYSTEM
	 */





	/* links, mainly for ajax, from w_social viewhelper */

	// GENERAL SYSTEM LINKS
	public function makeLink($label, $piVars = [], $clear = 0, $cache = 1, $pid = null)  {      //array('view'=>'single', 'mode'=>'rooms', 'uid'=>$row['uid'])
		if (!$pid)      $pid = null;
		// not sure about these params! check
		return $this->tx_ttnews->pi_linkTP_keepPIvars($label, $piVars, $clear, $cache, $pid);
	}

	/**
	 * @param array $piVars
	 * @param int $clear - is this used? linkTP doesn't uses it
	 * @param int $cache
	 * @param mixed $pid - int 0 means homepage not current page?, OR null
	 * @param array $params - last param to leave compatibility with pibase calls, where this is not provided
	 * @return string
	 */
	public function makeLink_url($piVars = [], $clear = 0, $cache = 1, $pid = null, $params = array())  {      //array('view'=>'single', 'mode'=>'rooms', 'uid'=>$row['uid'])
		if (!$pid)      $pid = null;
		$pid = intval($pid);
		// not sure about these params! check links with no_debug or something
		//return $this->pObj->pi_linkTP_keepPIvars_url($params, $clear, $cache, $pid);
		$this->tx_ttnews->pi_linkTP('|', Array($this->tx_ttnews->prefixId=>$piVars)+$params, intval($cache), $pid);
		return $this->tx_ttnews->cObj->lastTypoLinkUrl;
	}

//
// AJAX
//
	/**
	 * Make general ajax base url for non-cached interactions
	 * For cached like getting resources, build proper urls in makeLink_ methods and pass it to ajax call
	 * @param array $config
	 * @return string
	 */
	public function makeLink_ajax_baseurl($config = []) {
		return $this->makeLink_url( [], intval($config['clear']), !$config['no_cache'], $config['pid'], ['type'=>$GLOBALS['TSFE']->tmpl->setup['wtools_ttnews_ajax.']['typeNum']] );
	}





	/**
	 *  render content element
	 * @param $uid
	 * @return string
	 */
	function renderCE($uid)	{
		$conf = Array ();
		$conf['1'] = 'RECORDS';
		$conf['1.'] = Array (
			'tables' => 'tt_content',
			'source' => intval($uid),
			'dontCheckPid' => 1
		);
		return $this->cObj->cObjGet($conf);
	}




















	/**
	 * Find related news records and pages, add links to them and wrap them with stdWraps from TS.
	 *
	 * wolo: this is a copy of getRelated method from ttnews pi class
	 *
	 * @param	integer		$uid of the current news record
	 * @return	string		html code for the related news list
	 */
	function getBackground($uid) {

		$lConf = $this->tx_ttnews->conf['getRelatedCObject.'];
		$visibleCategories = '';
		$sPidByCat = array();
		if ($this->tx_ttnews->conf['checkCategoriesOfRelatedNews'] || $this->tx_ttnews->conf['useSPidFromCategory']) {
			// get visible categories and their singlePids
			$catres = $this->tx_ttnews->db->exec_SELECTquery('tt_news_cat.uid,tt_news_cat.single_pid', 'tt_news_cat', '1=1' . $this->tx_ttnews->SPaddWhere . $this->tx_ttnews->enableCatFields);

			$catTemp = array();
			while (($catrow = $this->tx_ttnews->db->sql_fetch_assoc($catres))) {
				$sPidByCat[$catrow['uid']] = $catrow['single_pid'];
				$catTemp[] = $catrow['uid'];
			}
			$this->tx_ttnews->db->sql_free_result($catres);
			if ($this->tx_ttnews->conf['checkCategoriesOfRelatedNews_background']) {
				$visibleCategories = implode($catTemp, ',');
			}
		}
		$relPages = FALSE;
		if ($this->tx_ttnews->conf['usePagesRelations']) {
			$relPages = $this->getBackgroundPages($uid);
		}
		//		$select_fields = 'DISTINCT uid, pid, title, short, datetime, archivedate, type, page, ext_url, sys_language_uid, l18n_parent, M.tablenames';
		$select_fields = ' uid, pid, title, short, datetime, archivedate, type, page, ext_url, sys_language_uid, l18n_parent, tx_sitecedris_background_mm.tablenames, image, bodytext';

		//		$where = 'tt_news.uid=M.uid_foreign AND M.uid_local=' . $uid . ' AND M.tablenames!=' . $this->tx_ttnews->db->fullQuoteStr('pages', 'tt_news_related_mm');
		$where = 'tx_sitecedris_background_mm.uid_local=' . $uid . '
					AND tt_news.uid = tx_sitecedris_background_mm.uid_foreign
					AND tx_sitecedris_background_mm.tablenames != ' . $this->tx_ttnews->db->fullQuoteStr('pages', 'tx_sitecedris_background_mm');

		$groupBy = '';
		if ($lConf['groupBy']) {
			$groupBy = trim($lConf['groupBy']);
		}
		$orderBy = '';
		if ($lConf['orderBy']) {
			$orderBy = trim($lConf['orderBy']);
		}

		if ($this->tx_ttnews->conf['useBidirectionalRelations']) {
			//			$where = '((' . $where . ') OR (tt_news.uid=M.uid_local AND M.uid_foreign=' . $uid . ' AND M.tablenames!=' . $this->tx_ttnews->db->fullQuoteStr('pages', 'tt_news_related_mm') . '))';


			$where = '((' . $where . ')
					OR (tx_sitecedris_background_mm.uid_foreign=' . $uid . '
						AND tt_news.uid = tx_sitecedris_background_mm.uid_local
						AND tx_sitecedris_background_mm.tablenames != ' . $this->tx_ttnews->db->fullQuoteStr('pages', 'tx_sitecedris_background_mm') . '))';
		}



		//		$from_table = 'tt_news,tt_news_related_mm AS M';
		$from_table = 'tx_sitecedris_background_mm, tt_news';

		$res = $this->tx_ttnews->db->exec_SELECTquery($select_fields, $from_table, $where . $this->tx_ttnews->enableFields, $groupBy, $orderBy);
		//debugster($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
		if ($res) {
			$relrows = array();
			while (($relrow = $this->tx_ttnews->db->sql_fetch_assoc($res))) {
				$currentCats = array();
				if ($this->tx_ttnews->conf['checkCategoriesOfRelatedNews_background'] || $this->tx_ttnews->conf['useSPidFromCategory']) {
					$currentCats = $this->tx_ttnews->getCategories($relrow['uid'], true);
				}
				if ($this->tx_ttnews->conf['checkCategoriesOfRelatedNews_background']) {
					if (count($currentCats)) { // record has categories
						foreach ($currentCats as $cUid) {
							if (GeneralUtility::inList($visibleCategories, $cUid['catid'])) { // if the record has at least one visible category assigned it will be shown
								$relrows[$relrow['uid']] = $relrow;

								// wolo mod - add cat row to item. see below:
								$relrows[$relrow['uid']]['cat'] = $cUid;
								break;
							}
						}
					} else { // record has NO categories
						$relrows[$relrow['uid']] = $relrow;
					}
				} else {
					$relrows[$relrow['uid']] = $relrow;
				}

				// check if there's a single pid for the first category of a news record and add 'sPidByCat' to the $relrows array.
				if ($this->tx_ttnews->conf['useSPidFromCategory'] && count($currentCats) && $relrows[$relrow['uid']]) {
					$firstcat = array_shift($currentCats);
					if ($firstcat['catid'] && $sPidByCat[$firstcat['catid']]) {
						$relrows[$relrow['uid']]['sPidByCat'] = $sPidByCat[$firstcat['catid']];
					}
				}
			}
//						debug($relrows, '$relrows ('.__CLASS__.'::'.__FUNCTION__.')', __LINE__, __FILE__, 3);
//						debugster($relrows);


			$this->tx_ttnews->db->sql_free_result($res);
			if (is_array($relPages[0]) && $this->tx_ttnews->conf['usePagesRelations']) {
				$relrows = array_merge_recursive($relPages, $relrows);
			}

			$piVarsArray = array('backPid' => ($this->tx_ttnews->conf['dontUseBackPid'] ? null : $this->tx_ttnews->config['backPid']),
			                     'year' => ($this->tx_ttnews->conf['dontUseBackPid'] ? null : ($this->tx_ttnews->piVars['year'] ? $this->tx_ttnews->piVars['year'] : null)),
			                     'month' => ($this->tx_ttnews->conf['dontUseBackPid'] ? null : ($this->tx_ttnews->piVars['month'] ? $this->tx_ttnews->piVars['month'] : null)));

			$veryLocal_cObj = GeneralUtility::makeInstance('tslib_cObj'); // Local cObj.
			$lines = array();

			// save current realUrl state
			$tmpRealUrl = (bool) $this->tx_ttnews->tsfe->config['config']['tx_realurl_enable'];
			$tmpCoolUri = (bool) $this->tx_ttnews->tsfe->config['config']['tx_cooluri_enable'];


			//
			// wolo mod - group array by item category
			//
			$relGroup = [];
			foreach ($relrows as $row) {
				// get array of categories with items in subkey
				$relGroup[ $row['cat']['catid'] ]['cat'] = $row['cat'];
				$relGroup[ $row['cat']['catid'] ]['items'][ $row['uid'] ] = $row;
				unset($relGroup[ $row['cat']['catid'] ]['items'][ $row['uid'] ]['cat']);    // clean, we got cat elsewhere
			}

			foreach ($relGroup as $groupRow) {

				if ($groupRow['cat'])   $lines[] = '<dt>'.$groupRow['cat']['title'].'</dt>';
				$relrows = $groupRow['items'];
				// wolo mod end.

				foreach ($relrows as $row) {

					if ($this->tx_ttnews->tsfe->sys_language_content && $row['tablenames'] != 'pages') {
						$OLmode = ($this->tx_ttnews->sys_language_mode == 'strict' ? 'hideNonTranslated' : '');
						$row = $this->tx_ttnews->tsfe->sys_page->getRecordOverlay('tt_news', $row, $this->tx_ttnews->tsfe->sys_language_content, $OLmode);
						if (! is_array($row))
							continue;
					}
					$veryLocal_cObj->start($row, 'tt_news');

					if ($row['type'] != 1 && $row['type'] != 2) { // only normal news
						$catSPid = false;
						if ($row['sPidByCat'] && $this->tx_ttnews->conf['useSPidFromCategory']) {
							$catSPid = $row['sPidByCat'];
						}
						$sPid = ($catSPid ? $catSPid : $this->tx_ttnews->config['singlePid']);

						// temporary disable realUrl to get raw GETvars from function getSingleViewLink()
						$this->tx_ttnews->tsfe->config['config']['tx_realurl_enable'] = 0;
						$this->tx_ttnews->tsfe->config['config']['tx_cooluri_enable'] = 0;

						// special treatment for simulatestatic because it doesn't seem possible to temporarily disable it after tslib_fe is once initialized
						if ($this->tx_ttnews->tsfe->config['config']['simulateStaticDocuments']) {

							/**
							 * TODO: 16.04.2009
							 *
							 * extract parameters from GETvars like it was done in tt_news 2.5.x
							 */

						}

						$link = $this->tx_ttnews->getSingleViewLink($sPid, $row, $piVarsArray, true);

						$linkArr = GeneralUtility::explodeUrl2Array($link, true);
						$newsAddParams = '';
						if (is_array($linkArr) && is_array($linkArr['tx_ttnews'])) {
							$newsAddParams = GeneralUtility::implodeArrayForUrl('tx_ttnews', $linkArr['tx_ttnews']);
						}

						// load the parameter string into the register 'newsAddParams' to access it from TS
						$veryLocal_cObj->LOAD_REGISTER(array('newsAddParams' => $newsAddParams, 'newsSinglePid' => $sPid), '');

						if (! $this->tx_ttnews->conf['getRelatedCObject.']['10.']['default.']['10.']['typolink.']['parameter'] || $catSPid) {
							$this->tx_ttnews->conf['getRelatedCObject.']['10.']['default.']['10.']['typolink.']['parameter'] = $sPid;
						}
					}
					// re-enable realUrl (if set) to make cObjGetSingle render the related links as realUrls
					$this->tx_ttnews->tsfe->config['config']['tx_realurl_enable'] = $tmpRealUrl;
					$this->tx_ttnews->tsfe->config['config']['tx_cooluri_enable'] = $tmpCoolUri;
					$lines[] = $veryLocal_cObj->cObjGetSingle($this->tx_ttnews->conf['getRelatedCObject'], $this->tx_ttnews->conf['getRelatedCObject.'], 'getRelatedCObject');
				}
			}
			// make sure that realUrl is set to its previous state
			$this->tx_ttnews->tsfe->config['config']['tx_realurl_enable'] = $tmpRealUrl;
			$this->tx_ttnews->tsfe->config['config']['tx_cooluri_enable'] = $tmpCoolUri;

			if ($this->tx_ttnews->debugTimes) {
				$this->tx_ttnews->hObj->getParsetime(__METHOD__);
			}

			return implode('', $lines);
		} else {
			return '';
		}
	}



	/**
	 * @param $uid
	 * @return array
	 */
	function getBackgroundPages($uid) {
		$relPages = array();

		$select_fields = 'uid,title,tstamp,description,subtitle,tt_news_related_mm.tablenames';
		$from_table = 'pages,tt_news_related_mm';
		$where = 'tt_news_related_mm.uid_local=' . $uid . '
					AND pages.uid=tt_news_related_mm.uid_foreign
					AND tt_news_related_mm.tablenames=' . $this->tx_ttnews->db->fullQuoteStr('pages', 'tt_news_related_mm') . $this->tx_ttnews->getEnableFields('pages');

		$pres = $this->tx_ttnews->db->exec_SELECTquery($select_fields, $from_table, $where, '', 'title');

		while (($prow = $this->tx_ttnews->db->sql_fetch_assoc($pres))) {
			if ($this->tx_ttnews->tsfe->sys_language_content) {
				$prow = $this->tx_ttnews->tsfe->sys_page->getPageOverlay($prow, $this->tx_ttnews->tsfe->sys_language_content);
			}

			$relPages[] = array('title' => $prow['title'], 'datetime' => $prow['tstamp'], 'archivedate' => 0, 'type' => 1, 'page' => $prow['uid'],
			                    'short' => $prow['subtitle'] ? $prow['subtitle'] : $prow['description'], 'tablenames' => $prow['tablenames']);
		}
		$this->tx_ttnews->db->sql_free_result($pres);
		return $relPages;
	}













	/**
	 * universal helper. expects data in array( array(0 => value, 1 => label), array()...)
	 *
	 * @param array $data
	 * @param bool $addEmpty
	 * @param string $setValue
	 * @return string
	 */
	function makeDroplistOptions($data, $addEmpty = true, $setValue = '')	{
		$code = '';
		$selected = false;
		$selectedIsSet = false;
		if ($addEmpty)	array_unshift( $data, Array('', $this->pObj->pi_getLL('droplist_all', 'All')) );
		if (is_array($data))
			foreach($data as $row)	{
				if ($setValue==$row[0] && !$selectedIsSet)	{
					$selected = true;
					$selectedIsSet = true;
				}
				$code .= '<option value="'.$row[0].'"'. ($selected?' selected':'') .'>'.$row[1].'</option>';
				$selected = false;
			}
		return $code;
	}


	/**
	 * adapters between model / db data and droplist generator
	 * get element array as arrays (0 => uid, 1 => name) for use in droplist builder
	 */

	/*function getStatusForDroplist()	{
		$rows = tx_sitedb_badanie_model::getStatuses();
		if (is_array($rows))
			foreach($rows as $row)	{
				$res[] = Array($row['db_overall_status'], $row['db_overall_status']);
			}
		return $res;
	}*/


	function getCategoriesForDroplist($where = '')	{
		$categories = tx_sitecedris_model::getCategories(0, $where);
		if (is_array($categories))
			foreach($categories as $category)	{
				$res[] = Array($category['uid'], $category['title']);
			}
		return $res;
	}

	function getSWForDroplist($where = '')	{
		$rows = tx_sitecedris_model::getSWForDroplist(0, $where);
		if (is_array($rows))
			foreach($rows as $row)	{
				$res[] = Array($row['uid'], $row['name']);
			}
		//debugster($rows);
		return $res;
	}

	function getRegioForDroplist($where = '')	{
		$rows = tx_sitecedris_model::getRegioForDroplist(0, $where);
		if (is_array($rows))
			foreach($rows as $row)	{
				$res[] = Array($row['uid'], $row['title']);
			}
		return $res;
	}

}



?>