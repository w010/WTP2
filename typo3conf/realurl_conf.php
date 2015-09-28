<?php

$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT'] = array(
	'init' => array(
		'doNotRawUrlEncodeParameterName' => 0,
		'appendMissingSlash' => 'ifNotFile',
		'postVarSet_failureMode' => DEV ? '' : 'ignore',
		'enableUrlDecodeCache' => 1,
		'enableUrlEncodeCache' => 1,
		'enableCHashCache' => 1,
		'emptyUrlReturnValue' => '/',
	),
	'pagePath' => array(
		'rootpage_id' => 1,
		'type' => 'user',
		'userFunc' => 'EXT:realurl/class.tx_realurl_advanced.php:&tx_realurl_advanced->main',
		'spaceCharacter' => '-',
		'languageGetVar' => 'L',
		'expireDays' => 90,
		'disablePathCache' => 0,
		'segTitleFieldList' => 'tx_realurl_pathsegment,alias,nav_title,title',
	),
	'redirects' => Array (
	),
	'preVars' => Array (
		Array (
			'GETvar' => 'L',
			'valueMap' => array (
				'en' => 1,
				'de' => 2,
			),
			'noMatch' => 'bypass'
		),
	),
	'fixedPostVars' => Array(
		// user
		'20' => Array(
			Array (
				'GETvar' => 'tx_felogin_pi1[forgot]',
				'valueMap' => array (
					'forgot' => 1,
				),
				'noMatch' => 'bypass'
			),
			array( 'GETvar' => 'tx_felogin_pi1[user]' ),
			array( 'GETvar' => 'tx_felogin_pi1[forgothash]' )
		),
		// register
		/*'62' => array(
			array( 'GETvar' => 'tx_srfeuserregister_pi1[regHash]' )
		),*/


		// TwojeKonto / rejestracja
		/*'86' => 'reg_form', // registration user
		'87' => 'reg_form', // registration company
		'reg_form' => array(
			array (
				'GETvar' => 'tx_srfeuserregister_pi1[cmd]',
				'valueDefault' => 'create',
			),
			// array ( 'GETvar' => 'tx_srfeuserregister_pi1[token]' ) // problem, don't enable!
		),*/


/*
		'newstag' => array (
				array(
					'GETvar' => 'tx_ttnews[tag]',
				),
		),
		'185' => 'newstag'*/
	),


	'postVarSets' => array(
		'_DEFAULT' => Array (
			'article' => Array (
				Array (
					'GETvar' => 'tx_ttnews[tt_news]',
					'lookUpTable' => Array (
						'table' => 'tt_news',
						'id_field' => 'uid',
						'alias_field' => 'title',
						'addWhereClause' => ' AND NOT deleted AND title <> "" ',
						'useUniqueCache' => 1,
						'useUniqueCache_conf' => Array (
							'strtolower' => 1,
							'spaceCharacter' => '-',
							//'encodeTitle_userProc' => 'EXT:hi_misc/class.tx_himisc_realurl.php:&tx_himisc_realurl->uidToTitle',
						),
					),
				),
			),
			'cat' => array (
				array(
					'GETvar' => 'tx_ttnews[cat]',
					'lookUpTable' => Array (
						'table' => 'tt_news_cat',
						'id_field' => 'uid',
						'alias_field' => 'title',
						'addWhereClause' => ' AND NOT deleted AND title <> "" ',
						'useUniqueCache' => 1,
						'useUniqueCache_conf' => Array (
							'strtolower' => 1,
							'spaceCharacter' => '-',
							//'encodeTitle_userProc' => 'EXT:hi_misc/class.tx_himisc_realurl.php:&tx_himisc_realurl->uidToTitle',
						),
					),
				),
			),
			'p' => Array (
				Array (
					'GETvar' => 'tx_ttnews[pointer]',
					'userFunc' => 'EXT:hi_misc/class.tx_himisc_realurl.php:&tx_himisc_realurl->recalculatePageNumber'
				),
			),
			/* what extension makes tags for tt_news? */
			/*'tagname' => array (
					array(
						'GETvar' => 'tx_ttnews[tag]',
					),
			),*/
		),
	),
	'fileName' => Array (
		'defaultToHTMLsuffixOnPrev' => 1,
		'index' => Array(
			'nocache.html' => Array (
				'keyValues' => Array (
					'no_cache' => 1,
				),
			),
			'rss.xml' => Array (
				'keyValues' => Array (
					'type' => 100,
				),
			),
            'sitemap.xml' => array(
                'keyValues' => array(
                    'type' => 200,
                ),
            ),
			'_DEFAULT' => Array(
				'keyValues' => Array(
				)
			),
		)
	)
);
