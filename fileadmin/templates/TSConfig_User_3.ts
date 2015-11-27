#
# settings for Content Admin beusergroup
# this group has a Content Editor subgroup (uid=2) so it is
# not neccessery to set Editor to user (as far as I know)


setup.override.edit_wideDocument = 1
setup.override.navFrameResizable = 1
setup.default.navFrameWidth = 300
setup.default.thumbnailsByDefault = 1

options.pageTree.showDomainNameWithTitle = 1
options.pageTree.showNavTitle = 1
options.pageTree.hideFilter = 0

mod.web_list {
	clickTitleMode = edit
	alternateBgColors = 1

	# good version
	table.static_template.hideTable = 1
	table.static_currencies.hideTable = 1
	table.static_taxes.hideTable = 1
	table.static_markets.hideTable = 1
	table.static_countries.hideTable = 1
}

# By default almost all new contents hidden
TCAdefaults {
	#pages.hidden = 1
	#tt_content.hidden = 1
	#tt_news.hidden = 1
	#tt_news_cat.hidden = 1
}




# FEEDIT (standard / advanced)
# http://wiki.typo3.org/Frontend_editing
# http://docs.typo3.org/typo3cms/extensions/feeditadvanced/Installation/Index.html

admPanel	{

	enable.edit = 1

	module.edit.forceNoPopup = 0
	module.edit.forceDisplayFieldIcons = 1
	module.edit.forceDisplayIcons = 0
	# hides admpanel itself
	hide = 1
}

#[PIDinRootline = 83]
[globalVar = TSFE|id = 83]
page.config.admPanel = 0
admPanel	{
  	enable.edit = 0
}
[global]


# background of selected branch (zmienic kolor! na inny niz ten w newsletterze)
options.pageTree.backgroundColor.2 = rgba(0, 255, 0, 0.1)


permissions.file.default {
	addFile = 1
	readFile = 1
	editFile = 1
	writeFile = 1
	uploadFile = 1
	copyFile = 1
	moveFile = 1
	renameFile = 1
	unzipFile = 1
	deleteFile = 1

	addFolder = 1
	readFolder = 1
	moveFolder = 1
	writeFolder = 1
	renameFolder = 1
	deleteFolder = 1
	deleteSubfolders = 1
}
