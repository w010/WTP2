<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 wolo.pl <wolo.wolski@gmail.com>
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




class tx_ttnewscalendar_ttnews_hooks  {

	/**
	* parent
	*
	* @var tx_ttnews
	*/
	protected $pObj;

	const TABLE_ITEMS = 'tt_news';
	
	protected $itemCounter = 0;
	protected $list_order = array();

	

	public function init(tx_ttnews &$pObj) {
		$this->pObj = $pObj;
		
		/*debugster($this->pObj->piVars);
		debugster($_GET);*/


		/*if ($pObj->theCode == 'LIST'  ||  $pObj->theCode == 'SEARCH')	{

		}
		
		if ($pObj->theCode == 'SINGLE')	{
			 //$GLOBALS['TSFE']->tmpl->setup['page.']['10.']['userFunc'] = 'abcd';
			 //if ($this->pObj->LLkey=='en')
		}
		
		if ($pObj->theCode == 'COMPARE')	{
			// always display full description in compare mode
			//$pObj->conf['displayList.']['content_stdWrap.']['crop'] = null;
		}*/
	}


	/**
	* 
	* 		ITEM MarkerArray
	* 
	* @param array $markerArray
	* @param array $row
	* @param array $lConf
	* @param tx_ttnews $pObj
    * @return array markerArray
	*/
	function extraItemMarkerProcessor($markerArray, $row, $lConf, tx_ttnews &$pObj)	{
		$this->init($pObj);
		$this->itemCounter++;
		//var_dump('extraItemMarkerProcessor');

        // wolo: to nie powinno sie wszedzie wykonywac, starczy w naszym code
        if ($pObj->theCode == 'CALENDAR_LIST_DAY'  ||  $pObj->theCode == 'SINGLE'  )  {
		    $markerArray['###NEWS_EVENT_ENDDATE###'] = $this->pObj->cObj->stdWrap($row['tx_ttnewscalendar_event_endtime'], $lConf['tx_ttnewscalendar_event_endtime_stdWrap.']);
			// old - sprawdza sie tylko w przypadku jednej kategorii
            $markerArray['###COLOR_CLASS###'] = $row['category_uid'];

			$markerArray['###CLASS_ITEM###'] = '';
			foreach (explode(',', $row['catUids']) as $catUid)
				$markerArray['###CLASS_ITEM###'] .= ' event-category-color-'.$catUid;
			$markerArray['###EVENT_DATE_SHORT###'] = '<div class="dayname">'.date('D', $row['datetime']).'</div><div class="daynumber catColor">'.date('j', $row['datetime']).'</div>';

			$beginDate = date('j', $row['datetime']) . ' ' . $this->_formatMonthPolish($row['datetime']);
			$beginTime = 'godz. ' . date('H:i', $row['datetime']);
			$endDate = date('j', $row['tx_ttnewscalendar_event_endtime']) . ' ' . $this->_formatMonthPolish($row['tx_ttnewscalendar_event_endtime']);
			$endTime = date('H:i', $row['tx_ttnewscalendar_event_endtime']);

			$markerArray['###EVENT_DATE_FORMATTED###'] = '<div class="date">'.$beginDate . ', '.$beginTime .' - '. ($row['tx_ttnewscalendar_event_endtime'] && $beginDate!=$endDate ? $endDate.', godz. ' : '') . $endTime .'</div>';
        }
		

		return $markerArray;
	}


	/**
	 * poprawny miesiąc w dopełniaczu
	 * @param $stamp
	 * @return string
	 */
	protected function _formatMonthPolish($stamp)	{
		switch (date('n', $stamp))	{
			case 1:	return 'stycznia';
			case 2:	return 'lutego';
			case 3:	return 'marca';
			case 4:	return 'kwietnia';
			case 5:	return 'maja';
			case 6:	return 'czerwca';
			case 7:	return 'lipca';
			case 8:	return 'sierpnia';
			case 9:	return 'września';
			case 10:	return 'października';
			case 11:	return 'listopada';
			case 12:	return 'grudnia';
			default:	return $stamp;
		}
	}


	/**
	* 
	* 		GLOBAL MarkerArray
	* 
	* @param tx_ttnews $pObj
	* @param array $markerArray
    * @return array markerArray
	*/
	function extraGlobalMarkerProcessor(tx_ttnews &$pObj, $markerArray)	{
		$this->init($pObj);

		//$markerArray['###NEWS_EVENTS_HEADER###'] = $this->pObj->pi_getLL('header_events_default', 'Seminars');

		// czy to jest uzywane gdzies poza kalendarzem? albo w ogole?
		if ($this->pObj->piVars['dayFilter'])
			$markerArray['###NEWS_EVENTS_HEADER###'] = $this->pObj->pi_getLL('header_events_date', 'Wydarzenia z') . date(' d.m.Y', intval(strtotime($this->pObj->piVars['dayFilter'])) );

		if ($pObj->theCode == 'CALENDAR_LIST_DAY') {
			$markerArray['###HEADER_FILTER###'] = $pObj->piVars['dayFilter'] ? ' dnia ' . htmlspecialchars($pObj->piVars['dayFilter']) : '';
		}
		
		return $markerArray;
	}


	/**
	* list helper
	* 
	* @param string $column
	* @param string $customName
	* @return string
	*/
	/*function makeSortLink($column, $customName = '')	{
		 $label = $customName ? $customName : $this->pObj->pi_getLL('col_'.$column, $column);
		 $sortDirection = $this->list_order['key'] == $column ? 'DESC' : '';
		 return $this->pObj->pi_linkTP_keepPIvars($label, array('sort' => $column, 'sortDirection' => $sortDirection, 'pointer' => 0));
	}*/


	


	/**
	* global subparts hook
	* test, not to use
	* 
	* @param tx_ttnews $pObj
	* @param array $subpartArray
	*/
	/*function extraGlobalSubpartsProcessor(tx_ttnews &$pObj, $subpartArray)	{
		return;
		$this->init($pObj);

		debugster($subpartArray);
	}*/


	
	/**
	* 
	* 		SEARCH MarkerArray
	* 
	* @param tx_ttnews $pObj
	* @param array $markerArray
    * @return array markerArray
	*/
	function additionalFormSearchFields(tx_ttnews &$pObj, $markerArray)	{
        return;
		$this->init($pObj);

		
		return $markerArray;
	}


	
	/**
	* CODES process
	* 
	* @param tx_ttnews $pObj
    * @return string content
	*/
    function extraCodesProcessor(tx_ttnews &$pObj)	{
		$this->init($pObj);
		$theCode = $pObj->theCode;

        $content = '';
        switch ($theCode) {

			case 'CALENDAR_LIST_DAY' :
            	//$prefix_display = 'displayList';

                $pObj->theCode = $theCode;
                $content .= $pObj->displayList();
                break;
            /*case 'LIST_WITHOUT_ENDED' :
            	$prefix_display = 'displayList';

                $pObj->theCode = $theCode;
                $content .= $pObj->displayList();
                break;*/
                
		}

		return $content;
    }



    /**
    * SELECT conf
    * 
    * @param tx_ttnews $pObj parent object (plugin instance)
    * @param array $selectConf
    * @return array selectConf processed
    */
    function processSelectConfHook(tx_ttnews &$pObj, $selectConf)	{
        $this->init($pObj);

    	switch ($pObj->theCode)	{
			case 'CALENDAR_LIST_DAY':
                //debugster($selectConf);
                $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = 1;
                //$GLOBALS['w_ttnews_showSQL'] = 1;
				// first only
                $selectConf['selectFieldsFromJoin'] = 'tt_news_cat_mm.uid_foreign AS category_uid,  GROUP_CONCAT(tt_news_cat_mm.uid_foreign) catUids';

				// filter by day
				// show only those, which have selected day between date and event-endtime
				if ($this->pObj->piVars['dayFilter'])	{
					
					$dayTstamp = intval(strtotime($this->pObj->piVars['dayFilter']));
					$dayTstampNext = $dayTstamp + 3600*24;
					// date between datetime & endtime,
					// or date in datetime day, if not between
					$selectConf['where'] .= ' AND ( ('.self::TABLE_ITEMS.'.datetime <= '.$dayTstamp. '  AND  '.self::TABLE_ITEMS.'.tx_ttnewscalendar_event_endtime >= '.$dayTstamp.') '
											. 'OR (  '.self::TABLE_ITEMS.'.datetime >= '.$dayTstamp. '  AND  '.self::TABLE_ITEMS.'.datetime < '.$dayTstampNext.') )';
				}
				else	{
					// if day not selected,
					// show only current and future events, hide ended	
					$selectConf['where'] .= ' AND ('.self::TABLE_ITEMS.'.tx_ttnewscalendar_event_endtime > '.time()
											. ' OR ( NOT '.self::TABLE_ITEMS.'.tx_ttnewscalendar_event_endtime  AND  '.self::TABLE_ITEMS.'.datetime > '.time().' ) )';
				}

				// filter by selected category
				if ($pObj->piVars['cat'])	{
					$selectConf['where'] .= ' AND tt_news_cat_mm.uid_foreign = '.intval($pObj->piVars['cat']);
				}

				// avoid duplicates from multiple cat join
				$selectConf['groupBy'] = 'tt_news.uid';

				break;

		//	case 'LIST_WITHOUT_ENDED' :
				
				// filter by day
				// show only those, which have selected day between date and event-endtime
			/*	if ($this->pObj->piVars['dayFilter'])	{
					
					$dayTstamp = intval(strtotime($this->pObj->piVars['dayFilter']));
					$dayTstampNext = $dayTstamp + 3600*24;
					// date between datetime & endtime,
					// or date in datetime day, if not between
					$selectConf['where'] .= ' AND ( ('.self::TABLE_ITEMS.'.datetime <= '.$dayTstamp. '  AND  '.self::TABLE_ITEMS.'.tx_ttnewscalendar_event_endtime >= '.$dayTstamp.') '
											. 'OR (  '.self::TABLE_ITEMS.'.datetime >= '.$dayTstamp. '  AND  '.self::TABLE_ITEMS.'.datetime < '.$dayTstampNext.') )';
				}

				// filter by selected category
				if ($pObj->piVars['cat'])	{
					$selectConf['where'] .= ' AND tt_news_cat_mm.uid_foreign = '.intval($pObj->piVars['cat']);
				}

				// show only current and future events, hide ended	
				$selectConf['where'] .= ' AND ('.self::TABLE_ITEMS.'.tx_ttnewscalendar_event_endtime > '.time()
										. ' OR ( NOT '.self::TABLE_ITEMS.'.tx_ttnewscalendar_event_endtime  AND  '.self::TABLE_ITEMS.'.datetime > '.time().' ) )';
		
				break;*/
			//case 'LIST':

				// this is normally ignored... so have to mod tt_news/tt_news on "build query for display:"
				// $selectConf['selectFields'] = 'manuf.title AS manufacturer_name';

				//tx_wzep_model::makeDbQueryForListAndCounters($selectConf, $this->pObj, $this->pObj->piVars);

			//	break;
		}
		
		
		//
		// SORT

		//$selectConf['orderBy'] = $this->list_order['orderBy'].' '.$this->list_order['direction'] . $this->list_order['secondary'];

		

// debugster($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);



/*
selectFields
where
pidInList
leftjoin
groupBy
orderBy*/

		// debugster($selectConf);
		return $selectConf;
    }


    function processSViewSelectConfHook(tx_ttnews &$pObj, $selectConf)	{
    	//$this->init($pObj);
    	//
		// LOCALIZE
		//if ($this->langKey == 'e')
			// overwrite bodytext field
			//$selectConf['selectFields'] .= ', '.self::TABLE_ITEMS.'.bodytext_en AS bodytext';
		return $selectConf;
    }

    
    function userDisplayCatmenu($lConf, &$pObj)	{
        //debugster('wolo: custom category menu! z ttnews_calendar');
    	$this->init($pObj);

    	// important
		if ($lConf['mode']!='ttnews_calendar') return;

        // todo: skad wziac uid kategorii wydarzen
        $newsCategory = (int)$pObj->conf['newsCategory'];
        $newsCategory = 14;

		$catPidList = $GLOBALS['TYPO3_DB']->cleanIntList( $lConf['catPidList'] );

        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, title, title_lang_ol', 'tt_news_cat', 'tt_news_cat.parent_category='.$newsCategory . ' AND pid IN ('.$catPidList.')' , '', 'tt_news_cat.' . $pObj->config['catOrderBy']);

        $cArr = array();
        while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
            $cArr[] = $row;
        }

        $GLOBALS['TYPO3_DB']->sql_free_result($res);

        $content = '<div class="divider" ></div>';
        $content .= '<div class="categoryFilterLinks">';

        foreach ($cArr as $item){

            //wybranej opcji dodatkowo nadaje klase 'chosen-category'
            $chosen = false;
            if($this->pObj->piVars['cat'] == $item['uid'])
                $chosen = true;

            $content .= '<div class="show-event-from-category-uid-'.$item['uid'].' '.($chosen ? 'chosen-category' : '').'">';
            $content .= $this->pObj->pi_linkTP_keepPIvars('Pokaż '.$item['title'],array('cat'=>$item['uid']), 1, 1);
            $content .= '</div>';
        }

        $chosen = false;
        if($this->pObj->piVars['cat'] == null)
            $chosen = true;

        $content .= '<div class="show-all-events '.($chosen ? 'chosen-category':'').'">';
        $content .= $this->pObj->pi_linkTP_keepPIvars('Pokaż wszystkie wydarzenia',array(), 1, 1);
        $content .= '</div>';

        $content .= '</div>';
        $content .= '<div class="divider" ></div>';

        //debugster($pObj->data['pi_flexform']);
        //debugster($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'categorySelection', 'sDEF'));

        /*
		$mode = $lConf['mode'] ? $lConf['mode'] : 'tree';
		if ($mode == 'droplist')	{

            debugster($pObj->conf);
            $addCatlistWhere = 'tt_news_cat.parent_category IN (' . $pObj->conf['categorySelection'] . ')';
            $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;

			//$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, title', 'tt_news_cat', ($pObj->conf['categorySelection'] ? $addCatlistWhere : 'tt_news_cat.parent_category=0') . $pObj->enableCatFields . $pObj->catlistWhere, '', 'tt_news_cat.' . $pObj->config['catOrderBy']);
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, title, title_lang_ol', 'tt_news_cat', ($pObj->conf['categorySelection'] ? $addCatlistWhere : 'tt_news_cat.parent_category=0') . $pObj->enableCatFields , '', 'tt_news_cat.' . $pObj->config['catOrderBy']);
			$cArr = array();
			while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
				$cArr[] = $row;
				$subcats = $pObj->hObj->getSubCategoriesForMenu($row['uid'], $fields, $pObj->catlistWhere);
				if (count($subcats)) {
					$cArr[] = $subcats;
				}
			}
//debugster($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
//			debugster($cArr);
			
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
			$content = '<form action="'.$this->pObj->pi_linkTP_keepPIvars_url(array(), 1, 1).'" method="get" id="countryFairsForm">';
			$content .= '<div class="searchDropdown countryFairs"><a href="" class="selectedOption">' . $this->pObj->pi_getLL('all_options') . '</a><a href="" class="arrowOption"></a>';
			$content .= '<ul class="selectbox"><li><a href="#" rel="default">' . $this->pObj->pi_getLL('all_options') . '</a></li>';
			foreach ($cArr as $item)	{
				$catTitleArr = t3lib_div::trimExplode('|', $item['title_lang_ol']);
				$catTitle = $catTitleArr[($GLOBALS['TSFE']->sys_language_content - 1)];
				$catTitle = $catTitle ? $catTitle : $item['title'];
							
				$content .= '<li><a href="#" rel="'.$item['uid'].'"'.($pObj->piVars['cat']==$item['uid']?' class="selected"':'').'>'.$catTitle.'</a></li>';
			}
			$content .= '</ul><input type="hidden" id="ttNewsCat" name="tx_ttnews[cat]" value=""/><input type="hidden" name="tx_ttnews[dayFilter]" value="'.$pObj->piVars['dayFilter'].'"/></div></form>';
			/*$content = '<form action="'.$this->pObj->pi_linkTP_keepPIvars_url(array(), 1, 1).'" method="get">
				<select onchange="this.form.submit();" name="tx_ttnews[cat]">
					<option value="">alle</option>';
			foreach ($cArr as $item)	{
				$content .= '<option value="'.$item['uid'].'"'.($pObj->piVars['cat']==$item['uid']?' selected="selected"':'').'>'.$item['title'].'</option>';
			}
			
			$content .= '</select>
			<input type="hidden" name="tx_ttnews[dayFilter]" value="'.$pObj->piVars['dayFilter'].'"/>
			</form><br>'; tu bylo koniec komentarza
		}
			*/
		return $content;
    }
    
    
	    /**
	    * 
	    * 	SECTION: contents, headers, droplist fill
	    * 
	    */
    
    

    /**
    * universal helper
    * 
    * @param array $data
    * @param bool $addEmpty
    * @param string $setValue
    * @return string content
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


	function getSortForDroplist()	{
		$options = array();
		foreach ($this->pObj->conf['sortConf'] as $key => $conf)	{
			$options[] = array($key, $conf['label']);
		}
		return $options;
	}
	
	
	
	
	/*function redirect()	{

		$wnewsConf = &$GLOBALS['TSFE']->tmpl->setup['plugin.']['tt_news.'];
		$pi8Conf = &$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wzep_pi8.'];

		// check if this is page with front_cat assigned, but it's not in url

		$GP = t3lib_div::_GPmerged('tx_ttnews');

		if (!$GP['cat_front'])	{
			// find cat_front uid for current page and redirect if found
			$pageUid = $this->conf['categoryToPage.'][intval($GP['cat_front'])];
        }

        if (!$pageUid)	{
			$pageUid = $this->conf['categoryToPage.']['all'];
        }

		unset ($_GET['id']);

		$redirectUrl = $this->cObj->getTypoLink_URL(
            $pageUid,
            $_GET
        );

die($redirectUrl);
        t3lib_utility_Http::redirect($redirectUrl);
	}*/
	
	

	function renderCE($uid)	{
		$conf = Array ();
		$conf['1'] = 'RECORDS';
		$conf['1.'] = Array (
			'tables' => 'tt_content',
			'source' => intval($uid),
			'dontCheckPid' => 1
		);
		return $this->pObj->cObj->cObjGet($conf);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ttnews_calendar/class.tx_ttnewscalendar_ttnews_hooks.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ttnews_calendar/class.tx_ttnewscalendar_ttnews_hooks.php']);
}

?>