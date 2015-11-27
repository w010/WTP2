config  {
	cache_period = 3600
	no_cache = 0
	sendCacheHeaders = 0
	cache_clearAtMidnight = 1

	tx_realurl_enable = 1
	#baseURL = http://wtp.aw.test-devs.com/
	# see ext:hi_basetag
	baseURL = auto
	#absRefPrefix = /
	prefixLocalAnchors = all

	#doctype = xhtml_trans
	doctype = html5
	# see setup_lang
	htmlTag_setParams = class="no-js"
	metaCharset = utf-8
	renderCharset = utf-8
	xmlprologue = none
	#minifyJS = 0
	compressJs = 1
	removeDefaultJS = external
	inlineStyle2TempFile = 1
	meaningfulTempFilePrefix = 9
	spamProtectEmailAddresses = 1
	spamProtectEmailAddresses_atSubst = @
	extTarget =
	disablePrefixComment = 1
	content_from_pid_allowOutsideDomain = 1
	typolinkEnableLinksAcrossDomains = 1
	noScaleUp = 0
	linkVars = L,no_debug,view

  # pageTitleFirst = 1
}

# note! contains marker which causes to not memcache this message page with r_memcached
#config.message_page_is_being_generated = <strong>Strona jest aktualnie generowana przez serwer.</strong></br>Jeśli ten komunikat nie zniknie automatycznie w ciągu paru sekund, przeładuj stronę.<span style="display: none;">@@@NO_MEMCACHE@@@</span>



# page title tag

temp.pageTitle = COA
temp.pageTitle	{
	10 = TEXT
	10.value = WTP
	10.stdWrap.noTrimWrap = ||: |

	20 = TEXT
	20.field = subtitle // title
}
temp.pageTitle.wrap = <title> | </title>

[globalVar = GP:tx_ttnews|tt_news > 0]

config.noPageTitle = 2
temp.pageTitle.20.field >
temp.pageTitle.30 = RECORDS
temp.pageTitle.30 {
	source = {GP:tx_ttnews|tt_news}
	source.insertData = 1
	tables = tt_news
	conf.tt_news >
	conf.tt_news = TEXT
	conf.tt_news.field = title
}

# set to page in setup_page.ts

[end]



[globalVar= ENV:DEV=1]
	config.no_cache = 1
	config.disablePrefixComment = 0
	#config.baseURL = http://dev.wtp.aw.test-devs.com/

	config.noPageTitle = 2
	page.headerData.6666 = TEXT
	page.headerData.6666.field = subtitle // title
	page.headerData.6666.wrap = <title>DEV WTP:&nbsp;|</title>
[end]
[globalVar= ENV:LOCAL=1]
	#config.baseURL = http://wtp.localhost/

	config.noPageTitle = 2
    page.headerData.6666 = TEXT
    page.headerData.6666.field = subtitle // title
    page.headerData.6666.wrap = <title>LOC WTP:&nbsp;|</title>
[end]



