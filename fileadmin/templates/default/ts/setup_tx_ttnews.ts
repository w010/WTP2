#includeLibs.newsImageMarkerFunc = EXT:mynews_ext/class.tx_ttnews_itemMarkerArrayFunc.php

plugin.tt_news {

	# singlePid i backPid - domyślnie na bieżącą stronę
	singlePid.data = TSFE:id
	backPid.data = TSFE:id
# wtp todo: pid
  searchPid = 35

	recursive = 255
	substitutePagetitle = 1
	listOrderBy = datetime desc
	dontUseBackPid = 1

	usePiBasePagebrowser = 1
	pageBrowser {
		showFirstLast = 0
		showRange = 0
		showResultCount = 0
		maxPages = 10
		alwaysPrev = 0
		pageFloat = center
		dontLinkActivePage = 0
		
		#check these, try to uncomment 
		browseBoxWrap.wrap = <div class="browseBoxWrap">|</div>
		showResultsWrap.wrap = <div class="showResultsWrap">|</div>
		browseLinksWrap.wrap = <div class="browseLinksWrap">|</div>
		showResultsNumbersWrap.wrap = <span class="showResultsNumbersWrap">|</span>
		disabledLinkWrap = <span class="disabledLinkWrap">|</span>
		inactiveLinkWrap = <span class="inactiveLinkWrap">|</span>
		activeLinkWrap = <span class="activeLinkWrap">|</span>
		
		# for eg. images or spans
		# hscText = 0
	}

	# categories selection
  # it'd best to set this using custom codes for each
	displayCatMenu.mode = w_ajaxCat

  #displayCatMenu.includeList = 1,2,3,26

	# don't set this to automatically link to proper pages on lists. have to be manually in single views.
	# if you use only one news list, set it here to list pid
# wtp todo: pid
	#catSelectorTargetPid = 4

	# catTextModes: 0-no, 1-not linked, 2-as shortcut, 3-as cat selector
	catTextMode = 3
	catImageMode = 0
	categoryDivider =,
	# categoryDivider_stdWrap = |
	category_stdWrap =
	catmenuHeader_stdWrap.wrap =  

   

	displayLatest {
		subheader_stdWrap.crop = 100 | ...
		subheader_stdWrap.stripHtml = 0
		subheader_stdWrap.ifEmpty.field >
  # subheader_stdWrap.append >
		date_stdWrap.strftime = %e.%m.%Y
			# full content on latest view
		#content_stdWrap.parseFunc < lib.parseFunc_RTE
		#content_stdWrap.crop = 110 | ...&nbsp;
		#content_stdWrap.stripHtml = 0
		#content_stdWrap.ifEmpty.field >
		#content_stdWrap.append < plugin.tt_news.displayList.subheader_stdWrap.append

      	image {
          file {
            maxW = 205m
            maxH = 146m
            minW >
            minH >
            #width = 210c
            #height = 150
          }
          noImage_stdWrap {
			cObject = IMAGE
			#cObject.file = fileadmin/templates/default/images/news_default.png
			cObject.file = fileadmin/templates/default/images/clear.gif
      	  }
        }
	}
	latestLimit = 5

	displayList {
		#author_stdWrap = #bug w tt_news - nie da sie usunac domyslnego markupu p[bodytext]
		subheader_stdWrap.crop >
		subheader_stdWrap.ifEmpty.field >
		# %e probably doesn't work at win server. @see condition below
		date_stdWrap.strftime = %e-%m-%Y
		#date_stdWrap.strftime = e.%m.%Y

		# gdy na liscie musimy wyswietlic tresc !
		# content_stdWrap.parseFunc < lib.parseFunc_RTE

		#image.file < tt_news.displayLatest.image.file

		image {
          file {
            maxW = 210m
            maxH = 150
            minW > 
            minH >
            #width = 210
            #height = 150
          }
		  noImage_stdWrap {
			wrap = <span class="default-img">|</span>
			cObject = IMAGE
			#cObject.file = fileadmin/templates/default/images/news_default.png
			cObject.file = fileadmin/templates/default/images/clear.gif
      	  }
   		}
	}

	displaySingle {
		#author_stdWrap.wrap = |
		#subheader_stdWrap.wrap = <p class="subheader">|</p>
		subheader_stdWrap.wrap >

		image {
      	  file {
            maxW = 210
            maxH = 150
            minW >
            minH >
            #width = 210c
            #height = 150
         }
         
         imageLinkWrap {
			# ? wyglada, jakby nie bylo brane pod uwage. czemu? patrz constants
            width = 800m
            height = 600
         }
      }
      date_stdWrap.strftime = %e.%m.%Y
   }

   # example: options for custom image rendering in hooks
   temp.blogDetailAuthorImage {
			image.file {
				maxW = 200m
				maxH = 200
				minW >
				minH >
			}
			listImageMode = resize
			caption_stdWrap.wrap = <p class="author">|</p>
			imageWrapIfAny = <div class="image">|</div>
		}

	# removing some parts from related lists, like icon, date
	getRelatedCObject {
		10.default.5 >
		10.default.20 >
		10.1.5 >
		10.1.20 >
		10.2.5 >
		10.2.20 >

    # 10.default.10.typolink.parameter = 34
	}

	newsFiles	{
		icon = 0
		size.wrap = &nbsp;(|)
		size.bytes.labels =  | KB| MB| GB

			#labelStdWrap.cObject = TEXT
            #labelStdWrap.cObject.dataWrap = DB:tt_news:{GPvar:tx_ttnews|tt_news}:title
            #labelStdWrap.cObject.wrap3 = {|}
            #labelStdWrap.cObject.insertData = 1

            labelStdWrap = TEXT
            labelStdWrap.field= title
            labelStdWrap.wrap = |

	}

	related_stdWrap.wrap = <dl class="ttnews-related">|</dl>
	newsFiles_stdWrap.wrap = <dl class="ttnews-files">|</dl>

	date_stdWrap.strftime = %e.%m.%Y
	noNewsIdMsg_stdWrap.wrap = <div class="noNewsIdMsg"> | </div>
#	noNewsToListMsg_stdWrap.wrap = <div class="noNewsToListMsg"> | </div>

  noNewsToListMsg_stdWrap.wrap = <!--@@@AJAX_BOUNDARY@@@--><div class="noNewsToListMsg"><br> | </div><!--@@@AJAX_BOUNDARY@@@-->


	sys_language_mode = strict
	showNewsWithoutDefaultTranslation = 1

	excludeAlreadyDisplayedNews = 1 

	_CSS_DEFAULT_STYLE >  
	
	
	#someCustomFunctionality {
		# value used in hook
	#}
}


plugin.tt_news.code = LIST

# not always good idea, depends on project

[globalVar = GP:tx_ttnews|tt_news > 0]
plugin.tt_news.code >
plugin.tt_news.code = SINGLE
[global]



# on windows %e doesn't work and causes not rendering date at all

[globalVar= ENV:LOCAL=1]
plugin.tt_news.displayLatest.date_stdWrap.strftime = e-%m-%Y
plugin.tt_news.displayList.date_stdWrap.strftime = e-%m-%Y
plugin.tt_news.displaySingle.date_stdWrap.strftime = e-%m-%Y
[global]








# moze sie przydac

#[PIDinRootline = 95]

#plugin.tx_ttnewscalendar_pi1 {
#	templateFile = fileadmin/templates/default/html/tx_ttnews_calendar.html
#	newsCategory = 2
#	storePid = 2
#	newsListPid = 23
#	newsSinglePid = 45
#}

#page_ttnewscalendar_month	{
#	10	{
#		templateFile = fileadmin/templates/default/html/tx_ttnews_calendar.html
#		storePid = 2
#		newsListPid = 23
#		newsSinglePid = 45
#	}
#}




#plugin.tx_ttnews {
	# [ryży]: co to za ustawienie?
#	overridePageId = 56
#}

