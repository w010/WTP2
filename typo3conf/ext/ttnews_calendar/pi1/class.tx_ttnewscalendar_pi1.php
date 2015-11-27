<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Semyon Vyskubov <sv@rv7.ru>
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
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

//require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'News Calendar' for the 'ttnews_calendar' extension.
 *
 * @author	Semyon Vyskubov <sv@rv7.ru>
 * @package	TYPO3
 * @subpackage	tx_ttnewscalendar
 */
class tx_ttnewscalendar_pi1 extends tslib_pibase {

    var $prefixId = 'tx_ttnewscalendar_pi1';		// Same as class name
    var $scriptRelPath = 'pi1/class.tx_ttnewscalendar_pi1.php';	// Path to this script relative to the extension dir.
    var $extKey = 'ttnews_calendar';	// The extension key.
    var $pi_checkCHash = true;

	var $extConf = [];

    /**
     * The main method of the PlugIn
     *
     * @param	string		$content: The PlugIn content
     * @param	array		$conf: The PlugIn configuration
     * @return	The content that is displayed on the website
     */
    function main ($content, $conf) {
		//debugster($conf);

		//$GLOBALS['w_ttnews_showSQL'] = 1;

        $this->conf = $conf;
        $this->pi_setPiVarDefaults();
        $this->pi_loadLL();
        $this->pi_initPIflexForm();		// Init FlexForm configuration of current plugin instance
        //$this->gpVars = t3lib_div::_GP($this->prefixId) ? t3lib_div::_GP($this->prefixId) : array();
        $this->extConf = unserialize($GLOBALS['TSFE']->TYPO3_CONF_VARS['EXT']['extConf']['ttnews_calendar']);

        $month = $this->piVars['month'] ? $this->piVars['month'] : '';
		// if month not given, but is day filter, keep the month from it
		if (!$month  &&  $_GET['tx_ttnews']['dayFilter'])	{
			// must be in reverse order, because it's done such...
			$month = implode('-', array_reverse( array_slice( explode('-', $_GET['tx_ttnews']['dayFilter']), 0, 2) ) );
		}

        // two month view
        // quick method for now
        if ($this->conf['viewTwoMonths'])	{
            $anchor = explode("-", $month);
            $anchorMonth = ((int)$anchor[0]>0)?(int)$anchor[0]:(int)date("n");
            $anchorYear = ((int)$anchor[1]>0)?(int)$anchor[1]:(int)date("Y");
            $nextMonthParam = date("n-Y", mktime(0, 0, 0, $anchorMonth+1, 1, $anchorYear));

            $content = $this->viewMonth($month) . $this->viewMonth($nextMonthParam);
        }
        else
            $content = $this->viewMonth($month);

        return $this->pi_wrapInBaseClass($content);
    }

    function viewMonth($month) {

        ////////////
        //cp($this->conf);


        $anchor = explode("-", $month);
        $anchorMonth = ((int)$anchor[0]>0)?(int)$anchor[0]:(int)date("n");
        $anchorYear = ((int)$anchor[1]>0)?(int)$anchor[1]:(int)date("Y");

        $templateFile = $this->conf['templateFile'];
        if (strlen($this->getFF("templateFile")) > 0)
            $templateFile = $this->getFF("templateFile");
        $template = $this->cObj->fileResource($templateFile);

		$storePid = (int)$this->conf['storePid'];
        if ((int)$this->getFF("storePid") > 0)
            $storePid = (int)$this->getFF("storePid");

        $newsListPid = (int)$this->conf['newsListPid'];
        if ((int)$this->getFF("newsListPid") > 0)
            $newsListPid = (int)$this->getFF("newsListPid");

        $newsSinglePid = (int)$this->conf['newsSinglePid'];
        if ((int)$this->getFF("newsSinglePid") > 0)
            $newsSinglePid = (int)$this->getFF("newsSinglePid");

        $target = $this->conf['target'];
        if (strlen($this->getFF("target")) > 0)
            $target = $this->getFF("target");

        $dowForm = $this->conf['dowForm'];
        if (strlen($this->getFF("dowForm")) > 0)
            $dowForm = $this->getFF("dowForm");

        // wolo mod
        $newsCategory = (int)$this->conf['newsCategory'];
        if (strlen($this->getFF("newsCategory")) > 0)
            $newsCategory = (int)$this->getFF("newsCategory");

        // Getting template subparts
        $tOverall = $this->cObj->getSubpart($template, '###TEMPLATE_CALENDAR###');
        $dowRow = $this->cObj->getSubpart($tOverall, '###TEMPLATE_CALENDAR_DOW_ROW###');
        $dowItem = $this->cObj->getSubpart($dowRow, '###TEMPLATE_CALENDAR_DOW_ITEM###');
        $tRow = $this->cObj->getSubpart($tOverall, '###TEMPLATE_CALENDAR_ROW###');
        $tItemEmpty = $this->cObj->getSubpart($tRow, '###TEMPLATE_CALENDAR_ITEM_EMPTY###');
        $tItemPast = $this->cObj->getSubpart($tRow, '###TEMPLATE_CALENDAR_ITEM_PAST###');
        $tItemToday = $this->cObj->getSubpart($tRow, '###TEMPLATE_CALENDAR_ITEM_TODAY###');
        $tItemFuture = $this->cObj->getSubpart($tRow, '###TEMPLATE_CALENDAR_ITEM_FUTURE###');

        // Reserving variables and setting counters
        $collect = $collectInner = $collectDow = '';
        $currentItem = 1;

        // Fill empty cells on first row if needed
        $timeNow = mktime(0, 0, 0, $anchorMonth, 1, $anchorYear);
        $monthStartFrom = (int)date("N", $timeNow);
        //$firstDayShown = (int)date("j", ($timeNow - ($monthStartFrom*24*3600))) + 1;

        for ($i=1; $i<$monthStartFrom; $i++) {
            if ($currentItem == 8) {
                $subParts['###TEMPLATE_CALENDAR_ITEMS###'] = $collectInner;
                $collect .= $this->fillSubparts($tRow, $subParts);
                $collectInner = '';
                $currentItem = 1;
            }
            $collectInner .= $tItemEmpty;
            $currentItem++;
        }

        // wolo category mod
        $queryWhere = "1=1";

        $joinCategoriesTable = (int)$this->conf['joinCategoriesTable'];

        if ($storePid > 0)
            $queryWhere .= ' AND tt_news.pid = ' . $storePid;

        if($joinCategoriesTable>0)
        if ($newsCategory)
            $queryWhere .= ' AND (tt_news_cat.uid IN (' . $newsCategory . ') OR tt_news_cat.parent_category IN ('.$newsCategory.'))';


        $categoryFilter = $GLOBALS['TYPO3_DB']->cleanIntList ($_GET['tx_ttnews']['cat']);
        if($categoryFilter){
            $queryWhere .= ' AND (tt_news_cat.uid IN (' . $categoryFilter . '))';
        }

		//debugster($queryWhere);

        if ($joinCategoriesTable > 0) {

            $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
            $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                'tt_news.*, tt_news_cat.*, tt_news.title AS newsTitle, tt_news.uid AS newsUid, GROUP_CONCAT(tt_news_cat.uid) catUids ',
                'tt_news JOIN tt_news_cat_mm AS mm ON tt_news.uid = mm.uid_local JOIN tt_news_cat ON tt_news_cat.uid = mm.uid_foreign',
                $queryWhere . $this->cObj->enableFields('tt_news') . $this->cObj->enableFields('tt_news_cat'),
				// wolo fix
				'tt_news.uid'
            );
            //debugster($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
        }else{

            $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
            $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                'tt_news.*',
                'tt_news JOIN tt_news_cat_mm AS mm ON tt_news.uid = mm.uid_local',
                $queryWhere . $this->cObj->enableFields('tt_news')
            );
            //debugster($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
        }

        // wolo end.
        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
//debugster($row);
            $newsDay = (int)date("j", (int)$row['datetime']);
            $newsMonth = (int)date("n", (int)$row['datetime']);
            $newsYear = (int)date("Y", (int)$row['datetime']);

            if ($newsMonth == $anchorMonth && $newsYear == $anchorYear)	{

                $news[$newsDay][] = $row;
                $newsStartday[$newsDay][] = true;	// collect array of startdays

            }

            // wolo mod - more days event
            if ($row['tx_ttnewscalendar_event_endtime'])	{
                // if endtime is set, find all days between
                for ($d=$row['datetime']; $d<=$row['tx_ttnewscalendar_event_endtime']; $d+=(24*3600))	{
                    $newsDay_continuous = (int)date("j", $d);
                    $newsMonth_continuous = (int)date("n", $d);
                    // ...and put event in these days, if month is right, year is right and day is next than current (to not duplicate event in first day - is already there)
                    if ($newsMonth_continuous == $anchorMonth  &&  $newsYear == $anchorYear  &&  $newsDay_continuous > $newsDay)
//					if ($newsMonth_continuous == $anchorMonth)
                        $news[$newsDay_continuous][] = $row;
                }
            }
        }

        for ($i=1; $i<=(int)date("t", mktime(0, 0, 0, $anchorMonth, 1, $anchorYear)); $i++) {
            if ($currentItem == 8) {
                $subParts['###TEMPLATE_CALENDAR_ITEMS###'] = $collectInner;
                $collect .= $this->fillSubparts($tRow, $subParts);
                $collectInner = '';
                $currentItem = 1;
            }
            $itemContent = $i;
            $link = '';

            // filtr tylko dla dni tygodnia, a nie dla każdego eventu
            if ($this->conf['linkDaysFilter'] && $news[$i] && $newsListPid > 0) {
                $dayFilterLinkParam = $anchorYear.'-'.$anchorMonth.'-'.$i;
                //				debugster($dayFilterLinkParam);
                $this->prefixId = 'tx_ttnews';
                $link = $this->cObj->addParams($this->pi_linkTP_keepPIvars_url(array('dayFilter'=>$dayFilterLinkParam), 1, 1, $newsListPid), array('target' => $target));
                $this->prefixId = 'tx_ttnewscalendar_pi1';
                //				$link = $this->cObj->addParams($this->pi_getPageLink($newsListPid, $target, array('tx_ttnews[dayFilter]' => $dayFilterLinkParam)));
                $link = '<a href="'.$link.'">';
                $title = '';
            }
            else if ($news[$i] && $newsSinglePid > 0 && $newsListPid > 0) {
                if (count($news[$i]) > 1)
                    //$itemContent = $this->cObj->addParams($this->pi_linkTP($i, array(), 1, $newsListPid), array('target' => $target));
                    $link = $this->cObj->addParams($this->pi_linkTP_keepPIvars_url(array(), 1, 1, $newsListPid), array('target' => $target));
                else if (count($news[$i]) == 1)
                    //$itemContent = $this->cObj->addParams($this->pi_linkTP($i, array('tx_ttnews[tt_news]' => $news[$i][0]['uid']), 1, $newsSinglePid), array('target' => $target));
                    //					$link = $this->cObj->addParams($this->pi_linkTP_keepPIvars_url(array('tx_ttnews[tt_news]' => $news[$i][0]['uid']), 1, 0, $newsSinglePid), array('target' => $target));
                    $link = $this->cObj->addParams($this->pi_getPageLink($newsSinglePid, $target, array('tx_ttnews[tt_news]' => $news[$i][0]['uid'])), array());
                $link = '<a href="'.$link.'">';
            }

            //riko
            $sundayClass = ($currentItem == 7) ? ' sunday' : '';
            $itemContent = '<div class="day'.$sundayClass.'">'.$link. $itemContent. ($link?'</a>':'') .'</div>';
            //riko


            if(!$joinCategoriesTable > 0){ //nie chciałem usuwać, tu link byl nadawany na title eventu, a nie na numer dnia miesiaca
                // work as date filter for list - don't display items on calendar, just make day link to filtered list
                if ($this->conf['linkDaysFilter'] && $news[$i] && $newsListPid > 0) {
                    $dayFilterLinkParam = $anchorYear.'-'.$anchorMonth.'-'.$i;
                    //				debugster($dayFilterLinkParam);
                    $this->prefixId = 'tx_ttnews';
                    $link = $this->cObj->addParams($this->pi_linkTP_keepPIvars_url(array('dayFilter'=>$dayFilterLinkParam), 1, 1, $newsListPid), array('target' => $target));
                    $this->prefixId = 'tx_ttnewscalendar_pi1';
                    //				$link = $this->cObj->addParams($this->pi_getPageLink($newsListPid, $target, array('tx_ttnews[dayFilter]' => $dayFilterLinkParam)));
                    $link = '<a href="'.$link.'">';
                    $title = '';
                }
                else if ($news[$i] && $newsSinglePid > 0 && $newsListPid > 0) {
                    if (count($news[$i]) > 1)
                        //$itemContent = $this->cObj->addParams($this->pi_linkTP($i, array(), 1, $newsListPid), array('target' => $target));
                        $link = $this->cObj->addParams($this->pi_linkTP_keepPIvars_url(array(), 1, 1, $newsListPid), array('target' => $target));
                    else if (count($news[$i]) == 1)
                        //$itemContent = $this->cObj->addParams($this->pi_linkTP($i, array('tx_ttnews[tt_news]' => $news[$i][0]['uid']), 1, $newsSinglePid), array('target' => $target));
                        //					$link = $this->cObj->addParams($this->pi_linkTP_keepPIvars_url(array('tx_ttnews[tt_news]' => $news[$i][0]['uid']), 1, 0, $newsSinglePid), array('target' => $target));
                        $link = $this->cObj->addParams($this->pi_getPageLink($newsSinglePid, $target, array('tx_ttnews[tt_news]' => $news[$i][0]['uid'])), array());
                    $link = '<a href="'.$link.'">';
                }
            }

			$categoryColorClass = '';
            for($index=0;$index < count($news[$i]);$index++){
                //debugster($news[$i]);

                if($joinCategoriesTable > 0){
                    $title = $news[$i][$index]['newsTitle'];
                    $title2 = $title;
                    if(strlen($title2)>4){
                        $title2 = substr($title, 0, 4).'...';
                    }
                    if(strlen($title)>11){
                        $title = substr($title, 0, 8).'...';
                    }
                } else
                    $title = $news[$i][$index]['title'];



                $eventLink = $this->cObj->addParams($this->pi_getPageLink($newsSinglePid, $target, array('tx_ttnews[tt_news]' => $news[$i][$index]['newsUid'])), array());
                $eventLink = '<a href="'.$eventLink.'">';
                //debugster($news[$i]);

                // wolo mod.
                // TODO
                // temporary - should have configured order / just be templated. now is simplified. you can always edit it here...
				
				$categoryColorClass = '';
				foreach(explode(',', $news[$i][$index]['catUids']) as $catUid)	{
					$categoryColorClass .= ' event-category-color-'.$catUid;
				}
				//debugster($news[$i][$index]);

                //$itemContent = '<div class="day">'.$link. $itemContent. ($link?'</a>':'') .'</div>';
                $itemContent .= '<div class="event">';
//                $itemContent .= '<div class="event-category-box event-category-color-'.$news[$i][$index]['uid'].'">'.'</div>';
                $itemContent .= '<div class="event-category-box'.$categoryColorClass.'">'.'</div>';
                if($joinCategoriesTable>0){
                    $itemContent .= '<div class="event-title visible-xs visible-sm visible-md">'.$eventLink.$title2. ($eventLink?'</a>':'').'</div>';
                    $itemContent .= '<div class="event-title visible-lg">'.$eventLink.$title. ($eventLink?'</a>':'').'</div>';
                }else
                    $itemContent .= '<div class="event-title visible-lg">'.$link. $title. ($link?'</a>':'') .'</div>';
                $itemContent .= '<div class="image">'.''.'</div>';
                $itemContent .= '</div>';
                // wolo end.

            }/////////////////////////////////////////////////////////

            $classItem = '';
            $classItem .= $link?' item-notempty':'';
            $classItem .= ($anchorYear.'-'.$anchorMonth.'-'.$i==$_GET['tx_ttnews']['dayFilter'])?' act':'';
            $classItem .= $newsStartday[$i]?' eventstart':'';

			// add event category class to toggle color indicator
			$classItem .= $categoryColorClass;


            $itemMarkers = array(
                '###ITEM###' => $itemContent,
                '###CLASS_ITEM###' => $classItem,
            );
            $tItem = $tItemPast;
            if ($anchorYear == (int)date("Y") && $anchorMonth == (int)date("n") && $i == (int)date("d"))
                $tItem = $tItemToday;
            if (($anchorYear > (int)date("Y")) ||
                ($anchorYear == (int)date("Y") && $anchorMonth > (int)date("n")) ||
                ($anchorYear == (int)date("Y") && $anchorMonth == (int)date("n") && $i > (int)date("d"))
            )
                $tItem = $tItemFuture;
            $collectInner .= $this->cObj->substituteMarkerArray($tItem, $itemMarkers);
            $currentItem++;

        }

        if ($currentItem <= 8) {
            $subParts['###TEMPLATE_CALENDAR_ITEMS###'] = $collectInner;
            $collect .= $this->fillSubparts($tRow, $subParts);
        }

        for ($i=1; $i<=7; $i++) {
            $itemMarkers = array(
                '###DOW###' => $this->pi_getLL('dow.' . $dowForm . '.' . $i)
            );
            $collectDow .= $this->cObj->substituteMarkerArray($dowItem, $itemMarkers);
        }
        $subParts['###TEMPLATE_CALENDAR_DOW_ITEM###'] = $collectDow;
        $collectDow = $this->fillSubparts($dowRow, $subParts);

        $subParts['###TEMPLATE_CALENDAR_DOW_ROW###'] = $collectDow;
        $subParts['###TEMPLATE_CALENDAR_ROW###'] = $collect;

        //dodaje link bierzacego miesiaca do tOverall
        $currentMonthParam = date("n-Y", mktime());
        $currentMonthLink = '<a href="'. $this->cObj->addParams($this->pi_linkTP_keepPIvars_url(array(), 1, 1, $newsListPid), array('target' => $target)).'" onclick="new newsCalendar().getMonth(\''.$currentMonthParam.'\', '.($categoryFilter ? $categoryFilter : 'null').'); return false;">Pokaż bieżący miesiąc'.'</a>';
        $tOverall .= "<div class='show-current-month'>";
        $tOverall .= $currentMonthLink;
        $tOverall .= "</div>";

        $content = $this->fillSubparts($tOverall, $subParts);

        // wolo month ajax mod
        $monthPrev = $monthNext = '';
        if ($anchorYear > $anchorYear - 2)	{
            $monthParam = date("n-Y", mktime(0, 0, 0, $anchorMonth-1, 1, $anchorYear));
            $monthPrev = $this->pi_linkTP($this->pi_getLL('month.prev'), array('tx_ttnewscalendar_pi1[month]' => $monthParam, 'tx_ttnews[cat]' => $categoryFilter), 1, $newsListPid);
            $monthPrev = '<a href="'.$this->cObj->lastTypoLinkUrl.'" onclick="new newsCalendar().getMonth(\''.$monthParam.'\', '.($categoryFilter ? $categoryFilter : 'null').'); return false;">'.$this->pi_getLL('month.prev').'</a>';
        }
        if ($anchorYear < $anchorYear + 2)	{
            $monthParam = date("n-Y", mktime(0, 0, 0, $anchorMonth+1, 1, $anchorYear));
            $monthNext = $this->pi_linkTP($this->pi_getLL('month.next'), array('tx_ttnewscalendar_pi1[month]' => $monthParam, 'tx_ttnews[cat]' => $categoryFilter), 1, $newsListPid);
            $monthNext = '<a href="'.$this->cObj->lastTypoLinkUrl.'" onclick="new newsCalendar().getMonth(\''.$monthParam.'\', '.($categoryFilter ? $categoryFilter : 'null').'); return false;">'.$this->pi_getLL('month.next').'</a>';
        }

		// wolo mod - czy to musi linkowac do strony z single? chyba niekoniecznie? raczej do biezacej
        //$this->pi_linkTP('ajax link', array('type' => $GLOBALS['TSFE']->tmpl->setup['page_ttnewscalendar_month.']['typeNum']), 1, $newsListPid);
        $this->pi_linkTP('ajax link', array('type' => $GLOBALS['TSFE']->tmpl->setup['page_ttnewscalendar_month.']['typeNum']), 1);
        $ajaxUrl = $this->cObj->lastTypoLinkUrl;

        $itemMarkers = array(
            '###MONTH###' => $this->pi_getLL('month.' . $anchorMonth),
            '###YEAR###' => $anchorYear,
            '###MONTH_PREV###' => $monthPrev,
            '###MONTH_NEXT###' => $monthNext,
            '###URL_AJAX###' => $ajaxUrl
        );
        $content = $this->cObj->substituteMarkerArray($content, $itemMarkers);

        return $content;

    }

    /**
     * Get FlexForm setting
     *
     * @param	string		$item: FlexForm item identifier
     */
    function getFF($item) {
        return $this->pi_getFFvalue($this->cObj->data['pi_flexform'], $item, 'sDEF');
    }

    /**
     * Fill subparts
     *
     * @param	string		$content: Content part
     * @param	array		$subParts: List of sub parts
     * @return	Rearranged content
     */
    function fillSubparts($content, $subParts) {
        foreach ($subParts as $subPart => $subContent) {
            $content = $this->cObj->substituteSubpart($content, $subPart, $subContent);
        }
        return $content;
    }

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ttnews_calendar/pi1/class.tx_ttnewscalendar_pi1.php'])
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ttnews_calendar/pi1/class.tx_ttnewscalendar_pi1.php']);

?>