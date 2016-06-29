<?php


/**
 * Linkhandler -
 * Cedris News single pid dependency from category
 *
 * wtp 2015
 */
 
class tx_sitecedris_linkhandler_helper		{

	function main(&$hookParams, &$pObj)	{
		//debugster($hookParams['typolinkConfiguration']);
		$uid = $hookParams['recordRow']['uid'];
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
				'tt_news_cat.single_pid',
				'tt_news LEFT JOIN tt_news_cat_mm ON (tt_news.uid = tt_news_cat_mm.uid_local) LEFT JOIN tt_news_cat ON (tt_news_cat_mm.uid_foreign = tt_news_cat.uid)',
				'tt_news.uid = ' . intval($uid),
				'',
				'tt_news_cat_mm.sorting',
				1
		);
		if ($res) $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		//debugster($row);
		if (is_array($row) && intval($row['single_pid']) > 0) {
			$hookParams['typolinkConfiguration']['parameter'] = intval($row['single_pid']);
		}
	}
}

?>