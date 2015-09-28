# should be empty, to proper generate link in fe_login - to let him get base from baseUrl
#[PIDinRootline = 87]
#config.absRefPrefix >
#[end]

plugin.tx_srfeuserregister_pi1	{
	#_CSS_DEFAULT_STYLE = @import url("fileadmin/styles/some_style_sheet_file.css");

	templateFile = fileadmin/templates/site/html/tx_srfeuserregister.html
	#tx_siteext.templateFile = fileadmin/templates/site/html/tx_sitexxx_srfeuserregister.html
	#tx_siteext.userGroupAfterConfirmation_withOrganization = {$plugin.tx_srfeuserregister_pi1.userGroupAfterConfirmation_withOrganization}

	tx_sitexxx.use_cobj_mail = 1

	# disable editing of these fields
	edit.overrideValues	{
		tx_sitexxx_organizer >
		tx_sitexxx_addevents >
	}

	#parseValues.tx_sitexxx_addevents =
}




plugin.tx_felogin_pi1	{
 	templateFile = fileadmin/templates/default/html/tx_felogin.html
 	storagePid = 12
 	showForgotPasswordLink = 0
 	showPermaLogin = 1
 	 	
 	showLogoutFormAfterLogin = 1
 	
 	#welcomeHeader_stdWrap
 	#welcomeMessage_stdWrap
 	#successHeader_stdWrap

 	#feloginBaseURL = http://dev.xxx.com
 	email_from = noreply@xxx.com
 	email_fromName = WTP

 	welcomeHeader_stdWrap {
		wrap = <p class="bodytext">|</p>
	}
	forgotHeader_stdWrap {
		wrap = <p class="bodytext">|</p>
	}
	successHeader_stdWrap {
		# it doesn't affect toploginbox username!
		#wrap = <span class="header">|</span>
	}
	errorHeader_stdWrap {
		wrap = <p class="bodytext error">|</p>
	}
	changePasswordHeader_stdWrap {
		wrap = <p class="bodytext">|</p>
	}
	changePasswordMessage_stdWrap {
		wrap = <p class="bodytext">|</p>
	}
	logoutHeader_stdWrap	{
		wrap = <span class="header">|</span>
	}

	preserveGETvars = 0

	# custom option - forget password page link - modded felogin @see in typo3conf/ext/felogin
	pid_forget = 27
}



plugin.tx_hifeuser_pi1	{
	templateFile = fileadmin/templates/site/html/tx_hifeuser.html

	# status and userdata are both in "userdata"
	boxes = userdata,newsletter

	# page cannot be Hide at login
	logoutPID = 87

	#editPID = 158
	editPID = 186
	displayEditLink = 1

	iwantaddPID = 187
	displayIwantaddLink = 1


	govUsergroupUid = 3
}

plugin.tx_hifeuserlb_pi1 {
	storagePID < plugin.tx_felogin_pi1.storagePid
	loginFormActionURL = 87
	logoutFormActionURL = 87
}





# todo: move to external ts

lib.faveventslist < plugin.tx_cal_controller
lib.faveventslist {
	#pidList
	#calendar 9

	view = list
	view.allowedViews = list

	view.list.starttime = -1 year
	view.list.endtime = +1 year

	view.list.maxEvents = 3
	view.calendar.nearbyDistance >


	view.event.eventViewPid = 201

	pageBrowser.usePageBrowser = 0

	serviceWrapper = favourites
}


ajax_calfavbox = PAGE
ajax_calfavbox.typeNum = 983
ajax_calfavbox.config {
	disableCharsetHeader = 1
	disableAllHeaderCode = 1

	# required here...   ..or maybe not?
	#includeLibs.hi_yui = EXT:hi_yui/class.tx_hiyui.php
}


ajax_calfavbox.10 < lib.faveventslist

ajax_calfavbox.15 < plugin.tx_hical_pi2
ajax_calfavbox.15	{
	notLoggedCE = 1744
	noItemsCE = 1818
}

[loginUser = *]
ajax_calfavbox.20 = TEXT
ajax_calfavbox.20.typolink.parameter = 201,0
ajax_calfavbox.20.value < lib.l10n.cal.mainpage.list.favourites.seeAllFavourites
ajax_calfavbox.20.wrap = <div class="ttnews-bottom-more"> | </div>
[global]
