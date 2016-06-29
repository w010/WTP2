
# for reuse
lib.CE = RECORDS
lib.CE.tables = tt_content
lib.CE.dontCheckPid = 1

#lib.CE = CONTENT
#lib.CE.table = tt_content
#lib.CE.select	{
#	pidInList = this
#	where = 1=2
#}

# for pass ce uid in "data" param in fluid
lib.CE_fluid < lib.CE
lib.CE_fluid.source.current = 1


#
# MAIN CONTAINER
# 

lib.content = COA
#lib.content.10 < lib.breadcrumb		# breadcrumb markup zrobic z bootstrapa
lib.content.20 = RECORDS
lib.content.20.source.current = 1
lib.content.20.tables = tt_content
lib.content.20.wrap = <!--TYPO3SEARCH_begin--> | <!--TYPO3SEARCH_end-->

# Other containers

# left - referenced as slided example
lib.contentLeft < lib.content
lib.contentLeft {
	20.source >
	20.source.postUserFunc = thinkopen_at\KbTvContSlide\SlideController->main
	20.source.postUserFunc.field = main-content-left
	20.source.postUserFunc.table = tt_content
	# if only menus in left columns and use indexedsearch, use (remove search tags):
	20.wrap = |
}
lib.contentRight < lib.contentLeft
lib.contentRight.20.source.postUserFunc.field = main-content-right

lib.contentTop < lib.contentLeft
lib.contentTop.20.source.postUserFunc.field = main-content-top

# bottom - referenced as non-slided example
#lib.contentBottom < lib.content
#lib.contentHead < lib.content


#[globalVar= GP:tx_ttnews|tt_news > 0 ]
#	lib.content >
#	lib.content < plugin.tt_news
#	lib.content.code >
#	lib.content.code = SINGLE
#[global]


lib.contentTop.10 < lib.breadcrumb

# example optionally insert something before by making it COA array
# by doing this way, we doesn't copy breadcrumb to other containers and no need to reset it there

#temp.libcontent < lib.content
#lib.content >
#lib.content = COA
#lib.content.5 < lib.breadcrumb
#lib.content.10 < temp.libcontent




lib.main-before = COA
lib.main-after = COA


#
# page logo in header, linked to homepage
#
lib.head-logo < lib.CE
lib.head-logo.source = 3


temp.lib.head-logo = COA
temp.lib.head-logo {
		1 = TEXT
		1.typolink.parameter = 1
		1.value = WTP
		1.wrap = <h1>|</h1>
		1.innerWrap = <span>|</span>
		2 = TEXT
		2.typolink.parameter = 1
		2.value = description
		2.wrap = <h2>|</h2>
}




#
# additional content in div#page-head
#
lib.page-meta = COA
#lib.page-meta.10 < lib.lang-menu
lib.page-meta.15 < lib.searchbox
#temp.lib.page-meta.20 < lib.CE
#temp.lib.page-meta.20 {
#	source = 57
#	wrap = <div id="page-meta-image">|</div>
#	required = 1
#}


#[treeLevel = 1,2,3,4,5]
#lib.page-meta.20 >
#[end]


#
# footer div#page-foot
#
lib.page-foot = COA
lib.page-foot {

	# foot menu
	10 = COA
	10.wrap = <div class="col-xs-6"> | </div>
	10	{
		10 < lib.CE
		10.source = 2
	}

	#15 < lib.menu-footer

	# copyrights
	20 = COA
	20.wrap = <div class="col-xs-6"> | </div>
	20	{
		10 = TEXT
		10.value = <p class="pull-left"> wolo.pl TYPO3 pack <br> Preconfigured web system starter</p>

		20 = TEXT
		20.value = <p class="pull-right text-right"> {date:Y} - WTP 2.62.5<br>  <a href="http://wolo.pl">wolo.pl '.' studio</a> </p>
		20.insertData = 1
		
		#20 < lib.CE
		#20.source = 8
		#20.wrap = <nav> | </nav>

		99 = TEXT
		99.value = <br class="clear">
	}

	#99 = TEXT
	#99.value = <br class="clear">
}



# todo: create CE and set source uid

lib.cookieMessage = COA
lib.cookieMessage   {
	wrap = <div id="cookieMessage">|</div>
	10 = TEXT
	10.value = <button type="button" class="close pull-right" aria-hidden="true" onclick="$('#cookieMessage').hide();"><span>Close</span> ×</button>
	20 < lib.CE
	20.source = 580
	30 = TEXT
	30.value = <button type="button" class="cookie-agree" onclick="wtp.cookieAgree();">Agree</button>
}








# WTP INFOBOX v4.31
# - keep in mind, that it disables cache on DEV

lib.wtp_infobox = COA
lib.wtp_infobox {
	conf.version = 4.31
	wrap = <div id="wtp_infobox" class="well well-sm" title="double click to toggle borders (DEV only)"> | </div>

	# version display in markup
	5 = TEXT
	5.value < lib.wtp_infobox.conf.version
	5.wrap = <!-- WTP infobox v| -->
	# env info
	10 = COA
	10.wrap = <p class="env-info">|</p>
	10.default = TEXT
	10.default.wrap = <span>|</span>
	10.default.value = <b class="text-danger">PUBLIC !!</b>
	10.10 < lib.wtp_infobox.10.default
}
[globalVar= ENV:DEV=1]
	lib.wtp_infobox.10.10 < lib.wtp_infobox.10.default
	lib.wtp_infobox.10.10.value = DEV
[end]
[globalVar= ENV:LOCAL=1]
	lib.wtp_infobox.10.11 < lib.wtp_infobox.10.default
	lib.wtp_infobox.10.11.value = LOCAL
[end]

# Cache disabled for beuser
[globalVar = TSFE:beUserLogin = 1]
	config.no_cache = 1
	page.bodyTagCObject.additionalClasses.beuser = beuser
	lib.wtp_infobox.20 = TEXT
	lib.wtp_infobox.20.value = <p>Cache disabled - BE user logged in</p>
[end]


