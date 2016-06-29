mod.SHARED.defaultLanguageLabel = Polski
mod.SHARED.defaultLanguageFlag = pl
mod.web_layout.defaultLanguageLabel = Polski
#mod.SHARED.defaultLanguageLabel = Deutsch
#mod.SHARED.defaultLanguageFlag = de
#mod.web_layout.defaultLanguageLabel = Deutsch
#mod.SHARED.defaultLanguageLabel = Nederlandse
#mod.SHARED.defaultLanguageFlag = nl
#mod.web_layout.defaultLanguageLabel = Nederlandse

#TCEFORM.pages.backend_layout.PAGE_TSCONFIG_ID = 2
#TCEFORM.pages.backend_layout_next_level.PAGE_TSCONFIG_ID = 2


TCEMAIN.translateToMessage = %s

templavoila.wizards.newContentElement.renderMode = tabs


TCEFORM {
	pages {
		layout.disabled = 1
		layout.altLabels {
			0 = standard
			#1 = my layout 1
			#2 = my layout 2
			#3 = my layout 3
		}
		#subtitle.disabled = 1
		#lastUpdated.disabled = 1
		newUntil.disabled = 1
		#no_search.disabled = 1
		alias.disabled = 1
		#author.disabled = 1
		#author_email.disabled = 1
		#abstract.disabled = 1
		#description.disabled = 1
		php_tree_stop.disabled = 1
		media.disabled = 1
		#l18n_cfg.disabled = 1
		module.disabled = 1
		lastUpdated.disabled = 1
		target.disabled = 1
		#cache_timeout.disabled = 1
		#fe_group.disabled = 1
		#extendToSubpages.disabled = 1
	}
	tx_templavoila_tmplobj {
		rendertype.addItems	{
			simple = Simple (for widgets)
		}
	}
	pages_language_overlay {
		subtitle.disabled = 1
		author.disabled = 1
		author_email.disabled = 1
		abstract.disabled = 1
		media.disabled = 1
	}
	tt_content {
		#sys_language_uid.disabled = 1
		#l18n_parent.disabled = 1
		header_layout.altLabels {
			0 = Default (H2)
			1 = H1
			2 = H2
			3 = H3
			4 = H4
			5 = H5
		}
		#header_layout.removeItems = 5,6
		section_frame.altLabels {
			#1 = Add space after
			#10 = Layout 1
			#11 = Layout 2
			#12 = 3cols
			#20 = 3cols+RulerAfter
			#21 = No wrap
		}
		section_frame.addItems {
			31 = Box type 1
			32 = Box type 2
			33 = Box type 3

			# 66 is used by default and means No frame
		}
		CType.removeItems = multimedia,mailform,search
		colPos.disabled = 1
		layout.disabled = 1
		sectionIndex.disabled = 1
		linkToTop.disabled = 1
		date.disabled = 1
		text_align.disabled = 1
		text_face.disabled = 1
		text_size.disabled = 1
		text_color.disabled = 1
		text_properties.disabled = 1
		longdescURL.disabled = 1
		#fe_group.disabled = 1
	}
	tt_news {
		archivedate.disabled = 1
		editlock.disabled = 1
		#author.disabled = 1
		#links.disabled = 1
		fe_group.disabled = 1

		#type.disabled = 1
		#keywords.disabled = 1
		#imagecaption.disabled = 1
		#imagealttext.disabled = 1
		#imagetitletext.disabled = 1
		news_files.disabled = 1
		#related.disabled = 1
		starttime.disabled = 0
		endtime.disabled = 0
		no_auto_pb.disabled = 1
	}
}



RTE.classes {
	yellowLink {
		name = Yellow Link
		value = font-weight: bold; color: yellow;
	}
	phone	{
		name = Phone
		value = color: #749377;
	}
	more {
		name = Button - More
		value = font-weight: bold; color: #749377;
	}
	fancybox {
		name = Fancybox class
		value = text-decoration: underline;
	}
}

RTE.default {
	
	buttons	{
		blockstyle.tags.div.allowedClasses := addToList(yellowLink,phone)
		textstyle.tags.span.allowedClasses := addToList(yellowLink,phone)
		textstyle.tags.p.allowedClasses := addToList(yellowLink, phone)
		blockstyle.tags.all.allowedClasses := addToList(yellowLink, phone)
		link.properties.class.allowedClasses := addToList(mail, more, fancybox)
		#blockstyle.tags.div.allowedClasses := removeFromList(csc-frame-frame1, csc-frame-frame2)
		#link.properties.class.allowedClasses := removeFromList(external-link,external-link-new-window,internal-link-new-window,internal-link,download,mail)

		#formatblock.removeItems = h5,h6
	}

	#buttons.textstyle.postfixLabelWithClassName = 1
	
	#classesTable := addToList(sometable) # old, check this
	
	proc.allowedClasses := addToList(yellowLink, more, contact-phone, fancybox)
	
	# proc.allowTags := addToList(img)
	# proc.entryHTMLparser_db.allowTags := addToList(img)
	# proc.allowTagsOutside := addToList(img)
	
	#config.tt_content.bodytext.proc.allowedClasses := addToList(left, right, center, justify)
	
	showButtons := addToList(image)
	
	showTagFreeClasses = 1
	ignoreMainStyleOverride = 1
	contentCSS = fileadmin/templates/default/css/rte.css
}



# linkhandler tu kiedys byl - to jest do wstawiania linkow do ttnews w rte. odzyskac to
# https://axelerant.com/easily-link-to-news-articles-with-the-typo3-rte/

# nie wiadomo, czemu to nie dziala w taki sposob
# ale dziala jako taki bez tych ustawien
temp.plugin.tx_linkhandler {
	tx_tt_news_news {
		forceLink = 1
		#parameter = 15
		parameter >
		parameter.stdWrap.cObject = USER
		parameter.stdWrap.cObject {
			userFunc = tx_siteNN_linkhandler_helper->main
			userFunc {
				# tt_news uid
				uid = TEXT
				uid.field = uid

				# Default pid if no pid given
				pid = TEXT
				pid.value = 5
			}
		}
		additionalParams = &tx_ttnews[tt_news]={field:uid}
		additionalParams.insertData = 1
		useCacheHash = 1
	}
}
