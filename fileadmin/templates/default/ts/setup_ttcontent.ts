

# http://typo3.org/news/article/responsive-image-rendering-in-typo3-cms-62/


# is this working as expected in 6?
lib.stdheader.10.setCurrent.htmlSpecialChars = 0
lib.stdheader.10.setCurrent.wrap = <span> | </span>

tt_content {

	# 1,5,6,10,11,12,20,21

	#stdWrap.dataWrap =
	# usuń linki <a href> do lokalizowanych content-elementów
	# stdWrap.prepend.dataWrap >



	#
	# CONTENT ELEMENTS WRAPS for different layouts/frames
	#


		#  how to use this:
		#  basically it looks some like this:
		# stdWrap.innerWrap.cObject.[default|NN] {
		#       10 - OPEN TAG
		#       10.cObject.default.value = <div id="c{field:uid}"
		#       20 - CLASS
		#       20.10.value = csc-default
		#       30 - CLOSE TAG
		#       30.cObject.default.value = >|</div>
		# }
		#
		#  overwrite settings for selected frame:
		# stdWrap.innerWrap.cObject.[layout-number]
		#
		# differences from typo 4 to 6:
		# for open tag:  instead of NN.10.value  (default.10.value)   use:  NN.10.cObject.default.value   (default.10.cObject.default.value)
		# for class:     instead of NN.15.value  (default.15.value)   use:  NN.20.10.value  (default.20.10.value)
		# for close tag: instead of NN.30.value  (default.30.value)   use:  NN.30.cObject.default.value   (default.30.cObject.default.value)


	# labels for these frames: see page tsconfig TCEFORM.tt_content.section_frame.altLabels
	# examples of modify:

	# rulerAfter add as separate div
	#stdWrap.innerWrap.cObject.6.15.value = csc-default
	#stdWrap.innerWrap.cObject.6.30.value = >|</div><div class="separator"></div>
	#
	# change: Indent > layout-1
	#stdWrap.innerWrap.cObject.10.15.value = csc-default frame-layout-1
	#stdWrap.innerWrap.cObject.10.30.value = >|</div>

	# add new:
	# Box type 1
	stdWrap.innerWrap.cObject.31 < tt_content.stdWrap.innerWrap.cObject.default
	stdWrap.innerWrap.cObject.31.20.10.value = csc-default frame-box frame-box1

	# Box type 2
	stdWrap.innerWrap.cObject.32 < tt_content.stdWrap.innerWrap.cObject.default
	stdWrap.innerWrap.cObject.32.20.10.value = csc-default frame-box frame-box2

	# Box type 3
    stdWrap.innerWrap.cObject.33 < tt_content.stdWrap.innerWrap.cObject.default
    stdWrap.innerWrap.cObject.33.20.10.value = csc-default frame-box frame-box3
}



# zoom icon overlay to images

#tt_content.image.20.rendering.simple	{
#	imageStdWrap.dataWrap = <div class="csc-textpic-imagewrap csc-textpic-single-image" style="width:{register:totalwidth}px;"> | <div class="iconoverlay"></div> </div>
#	imageStdWrapNoWidth.wrap = <div class="csc-textpic-imagewrap csc-textpic-single-image"> | <div class="iconoverlay"></div> </div>
#}

# uncomment when needed. check in constants before
#tt_content.image.20 {
#	maxW = 960
#}


	#
	# MENU / SITEMAP CONTENT
	#

# seems to work as expected in 6.2

tt_content.menu	{

	20 {
		# "Menu of these pages"
		default {
			1 {
				noBlur = 1

				NO = 1
				NO.wrapItemAndSub = <li>|</li> |*||*| <li class="last">|</li>

				ACT < .NO
				ACT.wrapItemAndSub = <li class="act">|</li> |*||*| <li class="last act">|</li>

				CUR < .ACT
			}
		}

		# "Menu of subpages to these pages"
		1 {
			special = directory
			# in 6 it's not wrap, but that way:
			# stdWrap.outerWrap = <ul class="csc-menu csc-menu-1">|</ul>

			1 {
				NO = 1
				NO.wrapItemAndSub = <li>|</li> |*||*| <li class="last">|</li>

				ACT < .NO
				ACT.wrapItemAndSub = <li class="act">|</li> |*||*| <li class="last act">|</li>

				CUR < .ACT
				#expAll = 0
			}
			2 < .1
			2.wrap = <ul class="csc-menu-submenu"> | </ul>
			3 < .2
			4 < .2
			5 < .2
			6 < .2
			7 < .2
		}
		
		# "Sitemap - liststyle"
		2 {
			1 {
				NO = 1
				NO.wrapItemAndSub = <li>|</li> |*||*| <li class="last">|</li>

				ACT < .NO
				ACT.wrapItemAndSub = <li class="act">|</li> |*||*| <li class="last act">|</li>

				CUR < .ACT
			}
			2 < .1
            2.wrap = <ul class="csc-menu-submenu"> | </ul>
			3 < .2
			4 < .2
			5 < .2
			6 < .2
			7 < .2
		}

		# "Menu of subpages to these pages (with abstract)"
		4 {
			wrap = <ul class="csc-menu csc-menu-4">|</ul>
			1	{
				NO = 1
				NO {
					#wrapItemAndSub >
					#wrapItemAndSub = <li>|</li> |*||*| <li class="last">|</li>
					linkWrap = <li>|</li> |*||*| <li class="last">|</li>
					temp_____after {
						#data = field : abstract // field : description // field : subtitle
						#required = 1
						#htmlSpecialChars = 1
						wrap = <dd>|</dd> |*||*| <dd class="last">|</dd>
					}
					ATagTitle.field = description // title
				}
				ACT < .NO
				ACT {
					linkWrap = <li class="act">|</li> |*||*| <li class="last act">|</li>
					# after.wrap = <dd class="act">|</dd> |*||*| <dd class="last act">|</li>
				}
				CUR < .ACT
			}
		}
	}
}




# some snippets for sitemap

temp.tt_content.menu.20  {
	#1.1.expAll = 1
	#1.1.NO.allWrap = <p class="csc-mysitemap csc-mysitemap-level1"><span>|</span></p>
	#1.2 < tt_content.menu.20.1.1
	#1.2.NO.allWrap = <p class="csc-mysitemap csc-mysitemap-level2"><span>|</span></p>
	#1.2.itemArrayProcFunc = user_itemArrayProcFuncCat
	#1.2.IProcFunc = user_IProcFuncCat

	#1.3 < tt_content.menu.20.1.1
	#1.3.NO.allWrap = <p class="csc-mysitemap csc-mysitemap-level3"><span>|</span></p>
	#1.3 {
		#itemArrayProcFunc = user_itemArrayProcFuncCat
		#IProcFunc = user_IProcFuncCat
	#}
}



#
# UPLOADS
#

# doesn't seem to work in 6.2, settings looks different. clean on first use

temp.tt_content.uploads {
	20 {
		#outerWrap = <div class="csc-uploads">|</div>
		
		#linkProc.iconCObject.file.import.override = fileadmin/templates/default/images/fileicons/
		linkProc.iconCObject.file	{
			import = fileadmin/templates/default/images/fileicons/
			import.override = fileadmin/templates/default/images/fileicons/
			#width = 18
		}
		linkProc.icon.path = fileadmin/templates/default/images/fileicons/
		linkProc.icon.widthAttribute = 18

		itemRendering {
			20.1	{
				wrap = <p class="linkedLabel">|</p>
			}
		}
	}

	# previous
	temp.20 {
		outerWrap = <div class="csc-uploads">|</div>
		itemRendering {
			wrap = <div class="row-odd tr-first">|</div> |*| <div class="row-even">|</div> || <div class="row-odd">|</div> |*|

			10.wrap = <span class="csc-uploads-icon">|</span>

			20.wrap > 
			20.1 {
				data = register:description
				wrap = <span class="file-descr">|</span>
			}
			20.2 {
				data = register:linkedLabel
				wrap >
				dataWrap = <span class="file-label" onclick="pageTracker._trackEvent('Files', 'download', 'page-{TSFE:id}')">|</span>
			}

			30.wrap = <span class="file-size">|</span>
		}
	}
}


# Better, cleaner filelist
temp.tt_content.uploads {
	20 {
		outerWrap = <div class="csc-uploads">|</div>
		itemRendering {
			wrap = <div class="row-odd tr-first">|</div> |*| <div class="row-even">|</div> || <div class="row-odd">|</div> |*|

			10.wrap = <span class="csc-uploads-icon">|</span>

			20.wrap > 
			20.1 {
				data = register:description
				wrap = <span class="file-descr">|</span>
			}
			20.2 {
				data = register:linkedLabel
				wrap >
				dataWrap = <span class="file-label" onclick="pageTracker._trackEvent('Files', 'download', 'page-{TSFE:id}')">|</span>
			}

			30.wrap = <span class="file-size">|</span>
		}
	}
}

# remove class bodytext
# lib.parseFunc_RTE.nonTypoTagStdWrap.encapsLines.addAttributes.P.class >



# http://typo3.org/news/article/responsive-image-rendering-in-typo3-cms-62/
