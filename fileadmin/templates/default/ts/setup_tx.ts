
plugin.tx_scriptmerger	{
	javascript.parseBody = 0
	javascript.enable = 0
	css.enable = 1
}

[globalVar= ENV:DEV=1]
plugin.tx_scriptmerger	{
	javascript.parseBody = 0
	javascript.enable = 0
	css.enable = 0
}
[end]


# jfmulticontent - also remove jq
plugin.tx_jfmulticontent_pi1 {
	#jsInFooter = 1
	jQueryLibrary >
}

# news single
plugin.tx_wtools_pi1	{
	templateFile = fileadmin/templates/default/html/tx_ttnews_singlenews.html
}

plugin.tx_macinasearchbox_pi1 {
	#pidSearchpage = 17
	#templateFile = fileadmin/templates/default/html/tx_macinasearchbox.html
}


plugin.feadmin.dmailsubscription	{
	email.from = test@example.com
	email.fromName = WTP
	templateFile = fileadmin/templates/default/html/tx_directmailsubscription_foot.html

	create	{
		fields = email
		required = email
	}

	# pid of page with sub form
	# custom option - ext src modified!
# wtp todo: patch tych zmian, opis tej opcji
	pid_subscribe = 46

	# pid of storage with subscribed addresses
# wtp todo: pid
	pid = 78
}

# on page subscribe show full form
[globalVar = TSFE:id = 46]
plugin.feadmin.dmailsubscription.templateFile = fileadmin/templates/default/html/tx_directmailsubscription.html
[global]



plugin.tx_evojqtabs_pi1	{
	template_file = fileadmin/templates/default/html/tx_evojqtabs.html
	css_file = 0
	jquery = 0
	jquery-ui = 0
}

plugin.tx_macinasearchbox_pi1 {
	pidSearchpage = 17
	templateFile = fileadmin/templates/default/html/tx_macinasearchbox.html
}
plugin.tx_ryzyflash_pi1 {
	# default required flash version
	fp = 9.0.45
	
	# default alt content (from /Global Snippets/ storage)
	altContent = 3221
}

plugin.tx_ryzyslideshow_pi1 {
	maxWidth = 700
	maxHeight = 500
	maxWidthTn = 100 # thumbnail width
	maxHeightTn = 80 # thumbnail height
	skin = gallery.xml
	
	adapterConf {
		width = 500
		height = 312
	}
}

plugin.tx_linkhandler {
	tt_news {
		parameter < plugin.tt_news.singlePid
		additionalParams = &tx_ttnews[tt_news]={field:uid}
		additionalParams.insertData = 1
		useCacheHash = 1
		forceLink = 1
	}
	tx_cal_event {
		parameter < plugin.tx_cal_controller.view.event.eventViewPid
		additionalParams = &tx_cal_controller[view]=event&tx_cal_controller[type]=tx_cal_phpicalendar&tx_cal_controller[uid]={field:uid}
		additionalParams.insertData = 1
		useCacheHash = 1
		forceLink = 1
	}
}


plugin.tx_ryzyvideoplayer_pi1 {
	# FLV preview image
	preview =
	logoURL = 116
	logoTarget = _new 
	
	# Default player config for ryzy_flash plugin
	adapterConf < plugin.tx_ryzyflash_pi1
	adapterConf {
		#swfUrl = fileadmin/flash/level0tester.swf
		swfUrl = fileadmin/flash/videoplayer.swf
		width = 454
		height = 298
		allowfullscreen = 1
		wmode = transparent
		cssClassName = flashcontent-video
		altContent = 2812
	}
	additionalParamsFunc = EXT:site_xxx/class.user_tx_sitexxx_userFunctions.php:&user_tx_sitexxx_userFunctions->goldynVideoParamsExceptionConvertUrlToName
}

plugin.tx_hihtml2pdf_pi1 {
	asdf=asdf
}

