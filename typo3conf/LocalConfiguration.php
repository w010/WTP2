<?php
return array(
	'BE' => array(
		'debug' => TRUE,
		'explicitADmode' => 'explicitAllow',
		'fileCreateMask' => '0660',
		'folderCreateMask' => '2770',
		'installToolPassword' => '$P$Cts5iKL9psOsIjubFnms3.WybLwwKy.',
		'loginSecurityLevel' => 'rsa',
	),
	'DB' => array(
		'database' => 'typo3_wtp2',
		'extTablesDefinitionScript' => 'extTables.php',
		'host' => '127.0.0.1',
		'password' => 'krakersy',
		'port' => 3306,
		'username' => 'wolo',
	),
	'EXT' => array(
		'extConf' => array(
			'crawler' => 'a:19:{s:9:"sleepTime";s:4:"1000";s:16:"sleepAfterFinish";s:2:"10";s:11:"countInARun";s:3:"100";s:14:"purgeQueueDays";s:2:"14";s:12:"processLimit";s:1:"1";s:17:"processMaxRunTime";s:3:"300";s:14:"maxCompileUrls";s:5:"10000";s:12:"processDebug";s:1:"0";s:14:"processVerbose";s:1:"0";s:16:"crawlHiddenPages";s:1:"0";s:7:"phpPath";s:12:"/usr/bin/php";s:14:"enableTimeslot";s:1:"1";s:11:"logFileName";s:0:"";s:9:"follow30x";s:1:"0";s:18:"makeDirectRequests";s:1:"0";s:16:"frontendBasePath";s:1:"/";s:22:"cleanUpOldQueueEntries";s:1:"1";s:19:"cleanUpProcessedAge";s:1:"2";s:19:"cleanUpScheduledAge";s:1:"7";}',
			'direct_mail' => 'a:9:{s:12:"sendPerCycle";s:2:"50";s:13:"cron_language";s:2:"en";s:14:"addRecipFields";s:0:"";s:10:"adminEmail";s:17:"admin@website.com";s:7:"cronInt";s:1:"5";s:15:"notificationJob";s:1:"1";s:19:"enablePlainTextNews";s:1:"1";s:14:"UseHttpToFetch";s:1:"0";s:13:"updateMessage";s:1:"0";}',
			'direct_mail_subscription' => 'a:0:{}',
			'direct_mail_userfunc' => 'a:2:{s:17:"makeEntriesUnique";s:1:"1";s:13:"enableSamples";s:1:"0";}',
			'formhandler' => 'a:0:{}',
			'hi_basetag' => 'a:0:{}',
			'hi_misc' => 'a:0:{}',
			'indexed_search' => 'a:18:{s:8:"pdftools";s:9:"/usr/bin/";s:8:"pdf_mode";s:2:"20";s:5:"unzip";s:9:"/usr/bin/";s:6:"catdoc";s:9:"/usr/bin/";s:6:"xlhtml";s:9:"/usr/bin/";s:7:"ppthtml";s:9:"/usr/bin/";s:5:"unrtf";s:9:"/usr/bin/";s:9:"debugMode";s:1:"0";s:18:"fullTextDataLength";s:1:"0";s:23:"disableFrontendIndexing";s:1:"0";s:21:"enableMetaphoneSearch";s:1:"1";s:6:"minAge";s:2:"24";s:6:"maxAge";s:1:"0";s:16:"maxExternalFiles";s:1:"5";s:26:"useCrawlerForExternalFiles";s:1:"0";s:11:"flagBitMask";s:3:"192";s:16:"ignoreExtensions";s:0:"";s:17:"indexExternalURLs";s:1:"0";}',
			'ods_plaintext' => 'a:0:{}',
			'realurl' => 'a:5:{s:10:"configFile";s:26:"typo3conf/realurl_conf.php";s:14:"enableAutoConf";s:1:"1";s:14:"autoConfFormat";s:1:"0";s:12:"enableDevLog";s:1:"0";s:19:"enableChashUrlDebug";s:1:"0";}',
			'rsaauth' => 'a:1:{s:18:"temporaryDirectory";s:0:"";}',
			'saltedpasswords' => 'a:2:{s:3:"BE.";a:4:{s:21:"saltedPWHashingMethod";s:41:"TYPO3\\CMS\\Saltedpasswords\\Salt\\PhpassSalt";s:11:"forceSalted";i:0;s:15:"onlyAuthService";i:0;s:12:"updatePasswd";i:1;}s:3:"FE.";a:5:{s:7:"enabled";i:1;s:21:"saltedPWHashingMethod";s:41:"TYPO3\\CMS\\Saltedpasswords\\Salt\\PhpassSalt";s:11:"forceSalted";i:0;s:15:"onlyAuthService";i:0;s:12:"updatePasswd";i:1;}}',
			'scheduler' => 'a:5:{s:11:"maxLifetime";s:4:"1440";s:11:"enableBELog";s:1:"1";s:15:"showSampleTasks";s:1:"1";s:11:"useAtdaemon";s:1:"0";s:30:"listShowTaskDescriptionAsHover";s:1:"1";}',
			't3jquery' => 'a:14:{s:15:"alwaysIntegrate";s:1:"1";s:17:"integrateToFooter";s:1:"0";s:17:"enableStyleStatic";s:1:"1";s:18:"dontIntegrateOnUID";s:0:"";s:23:"dontIntegrateInRootline";s:0:"";s:13:"jqLibFilename";s:23:"jquery-###VERSION###.js";s:9:"configDir";s:19:"uploads/tx_t3jquery";s:13:"jQueryVersion";s:5:"1.9.x";s:15:"jQueryUiVersion";s:5:"1.9.x";s:18:"jQueryTOOLSVersion";s:0:"";s:22:"jQueryBootstrapVersion";s:0:"";s:16:"integrateFromCDN";s:1:"0";s:11:"locationCDN";s:6:"jquery";s:13:"updateMessage";s:1:"0";}',
			'templavoila' => 'a:3:{s:7:"enable.";a:3:{s:13:"oldPageModule";s:1:"0";s:19:"selectDataStructure";s:1:"0";s:15:"renderFCEHeader";s:1:"0";}s:9:"staticDS.";a:3:{s:6:"enable";s:1:"0";s:8:"path_fce";s:27:"fileadmin/templates/ds/fce/";s:9:"path_page";s:28:"fileadmin/templates/ds/page/";}s:13:"updateMessage";s:1:"0";}',
			'tt_address' => 'a:2:{s:24:"disableCombinedNameField";s:1:"0";s:21:"backwardsCompatFormat";s:9:"%1$s %3$s";}',
		),
	),
	'EXTCONF' => array(
		'lang' => array(
			'availableLanguages' => array(),
		),
	),
	'FE' => array(
		'activateContentAdapter' => FALSE,
		'addAllowedPaths' => 'images/',
		'debug' => TRUE,
		'loginSecurityLevel' => 'rsa',
	),
	'GFX' => array(
		'colorspace' => 'sRGB',
		'im' => 1,
		'im_mask_temp_ext_gif' => 1,
		'im_v5effects' => 1,
		'im_version_5' => 'im6',
		'image_processing' => 1,
		'jpg_quality' => '80',
	),
	'HTTP' => array(
		'ssl_verify_peer' => 1,
	),
	'INSTALL' => array(
		'wizardDone' => array(
			'TYPO3\CMS\Install\Updates\FilemountUpdateWizard' => 1,
			'TYPO3\CMS\Install\Updates\TceformsUpdateWizard' => 'tt_content:image,pages:media,pages_language_overlay:media',
			'TYPO3\CMS\Install\Updates\TruncateSysFileProcessedFileTable' => 1,
		),
	),
	'SYS' => array(
		'UTF8filesystem' => '1',
		'caching' => array(
			'cacheConfigurations' => array(
				'extbase_object' => array(
					'backend' => 'TYPO3\\CMS\\Core\\Cache\\Backend\\Typo3DatabaseBackend',
					'frontend' => 'TYPO3\\CMS\\Core\\Cache\\Frontend\\VariableFrontend',
					'groups' => array(
						'system',
					),
					'options' => array(
						'defaultLifetime' => 0,
					),
				),
			),
		),
		'clearCacheSystem' => TRUE,
		'compat_version' => '6.2',
		'devIPmask' => '*',
		'displayErrors' => 1,
		'enableDeprecationLog' => 'file',
		'encryptionKey' => '205a2327aae995761d9b0811843ef95f47c976dc92afe49acdd25b45910115ca0f464b2c68ef2ba8f0941bbec6030f36',
		'exceptionalErrors' => 28674,
		'isInitialInstallationInProgress' => FALSE,
		'sitename' => 'WTP2',
		'sqlDebug' => 0,
		'systemLogLevel' => 0,
		't3lib_cs_convMethod' => 'mbstring',
		't3lib_cs_utils' => 'mbstring',
	),
);
?>