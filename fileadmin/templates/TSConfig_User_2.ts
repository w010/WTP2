#
# settings for Content Editor beusergroup
# this is default group for every user and subgroup of Content Admin (uid=3)


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

# but on local don't hide them
[globalVar= ENV:LOCAL=1]
	TCAdefaults {
		pages.hidden = 0
		tt_content.hidden = 0
		tt_news.hidden = 0
		tt_news_cat.hidden = 0
	}
[end]


#[PIDupinRootline=275]
#admPanel {
#	hide = 1
#	override {
#		preview = 1
#		preview.showHiddenPages = 1
#		preview.showHiddenRecords = 1
#	}
#}
#[end]


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
