page = PAGE
page.typeNum = 0
#page.bodyTag = <body id="body">
# render bodytag using EXT:hi_misc
page.bodyTagCObject = USER
page.bodyTagCObject	{
	userFunc = tx_himisc_div->getBodyTag
	additionalClasses {
		#yui = yui-skin-sam
	}
	# now can be used for fluid templates - controlled by Backend (!) layout field
	layoutMapping {
		4 = lay-home
		#2 = lay-sub-a
		#3 = lay-sub-b
		3 = lay-sub-1col
		2 = lay-sub-2col
		1 = lay-sub-3col
		# 13 = lay-sub-2col lay-sub-2colb
	}
  # enable if using fluid not templavoila
	layoutMappingToBackendLayoutField = 0
	# uid of default if not set. must be corresponding to fluidtemplate settings to make sense!
	#layoutMappingDefault = 1
}

#page.shortcutIcon = fileadmin/templates/default/images/favicon.ico


page.config {
      admPanel = 0
      sendCacheHeaders = 1
      #additionalHeaders = Cache-Control: no-cache, must-revalidate, max-age=0|Expires: Mon, 2 Jan 2006 01:00:00 GMT|Pragma: no-cache
      index_enable = 1
      headerComment = WTP2 (Wolo.pl T3 Pack) - TYPO3-based CMS by wolo.pl '.' studio / A. wolo Wolski
      message_preview = <div id="message-preview">PREVIEW</div>
}



page.includeLibs {
	# only as example. included in ext
	#hi_misc = EXT:hi_misc/class.tx_himisc_div.php

	#linkhandler_helper = EXT:site_nn/class.tx_sitenn_linkhandler_helper.php
}

page.headerData {
	#28 = TEXT
	#28.data = field : description
	#28.wrap = <meta property="og:description" content="|">

	75 = TEXT
    75.value (
        <!-- default Bootstrap settings -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!--<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0" />-->
        <!-- Adding "maximum-scale=1" fixes the Mobile Safari auto-zoom bug: http://filamentgroup.com/examples/iosScaleBug/ -->

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    )

	# analytics in head
    667 = TEXT
    667.value (
		<script>
          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
          })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
          ga('create', 'UA-xxxxx', 'auto');
          ga('send', 'pageview');
        </script>
    )
}

page.includeCSSLibs {
	# force before typo's standard stylesheet
	bootstrap = fileadmin/templates/default/css/bootstrap.min.css
	bootstrap.media = all
}
page.includeCSS {
	# reverse order when more forceOnTop!
	layout = fileadmin/templates/default/css/layout.css
	layout.media = all
	layout.forceOnTop = 1

	screen = fileadmin/templates/default/css/screen.css
    screen.media = all
    screen.forceOnTop = 1

	# rendered after typo's standard stylesheet
    content = fileadmin/templates/default/css/content.css
    content.media = all

	rte = fileadmin/templates/default/css/rte.css
 	rte.media = all

	#mobile = fileadmin/templates/default/css/mobile.css
	#mobile.media = all
}

#[useragent = *iPad*]
#	page.includeCSS.mobile = fileadmin/templates/default/css/mobile-ipad.css
#[global]


# force before standard typo's javascript
# should be includeJSLibs probably!
page.includeJSlibs	{
	# modernizr have to be here. check for:
	# "For best performance, you should have them follow after your stylesheet references." (doc)
	modernizr = fileadmin/templates/default/js/modernizr.custom.283.js
}
# try to keep all in footer
page.includeJS	{
	# example of external js settings
	#jq = http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js
	#jq.external = 1
	#jq.forceOnTop = 1
}

page.includeJSFooter {
	bootstrap = 			fileadmin/templates/default/js/bootstrap.min.js
	
	#jquery_cookie = 		fileadmin/templates/default/js/jquery.cookie.js
    #jquery_carousel =		fileadmin/templates/default/js/jquery.carousel.js
    #jquery_columnizer = 	fileadmin/templates/default/js/jquery.columnizer.js
    #jscroller = 			fileadmin/templates/default/js/jquery.simplyscroll.min.js
    #superfish = 			fileadmin/templates/default/js/superfish.js
    
    # jquery plugins
	plugins = 				fileadmin/templates/default/js/plugins.js

    # main custom js for site. just like ts setup.lib - should be on the end, when all is loaded
	code = 					fileadmin/templates/default/js/code.js
}

# auto reload css every configured time. use &autocss=1 in url
[globalVar= ENV:DEV=1] && [globalVar= GP:autocss > 0 ]
page.includeJSFooter.autoreloadcss = fileadmin/templates/default/js/autoreloadcss.js
[global]



temp.page.footerData {
	
}




page.10 = USER
page.10.userFunc = tx_templavoila_pi1->main_page




# set in setup_lib
#page.996 < lib.cookieMessage


# WTP infobox
page.1024 < lib.wtp_infobox







temp.page	{
   10 = FLUIDTEMPLATE
   10 {
      partialRootPath = fileadmin/templates/default/html/partials/
      layoutRootPath = fileadmin/templates/default/html/layouts/
      variables {
         #contentLeft < styles.content.get
         #contentLeft.select.where = colPos=1
         contentMain < styles.content.get
         contentRight = COA
         contentRight	{
         	10 =< lib.contentRight.10
         	20 < styles.content.get
	        20.select.where = colPos=3
	        30 =< lib.contentRight.30
         }
         contentAfter < styles.content.get
         contentAfter.select.where = colPos=1
      }

      # Assign the Template files with the Fluid Backend-Template
   	  file.stdWrap.cObject = CASE
	  file.stdWrap.cObject {
  	  	key.data = levelfield:-1, backend_layout_next_level, slide
  		key.override.field = backend_layout

		# Set the default Template
		default = TEXT
		default.value = fileadmin/templates/default/html/tmpl-1col.html
		1 = TEXT
		1.value = fileadmin/templates/default/html/tmpl-1col.html
		# uid of Backend Layout record
		2 = TEXT
		2.value = fileadmin/templates/default/html/tmpl-1col-a.html
		3 = TEXT
		3.value = fileadmin/templates/default/html/tmpl-1col-b.html
	  }
   }
}


[globalVar= ENV:DEV=1]
	page.config.admPanel = 0

	# include not minimized bootstrap
	page.includeCSS.reset = fileadmin/templates/default/css/bootstrap.css
[end]


