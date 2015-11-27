page.config.index_enable = 1

# domyslnie wszystkie, ale powinno byc nadpisane dla danego drzewa zeby ograniczyc wyniki do niego
# czy ta opcja jest konieczna, zeby search w ogole dzialal?
# plugin.tx_indexedsearch.search.rootPidList = 1,116,91

# dodaje pelna domene, psuje realurl
#plugin.tx_indexedsearch.search.detect_sys_domain_records = 1


plugin.tx_indexedsearch {
  show.advancedSearchLink = 0
  show.rules=0
  tableParams{
    secHead=border=0 cellpadding=0 cellspacing=0 width="100%" class="text"
    searchBox=border=0 cellpadding=0 cellspacing=0 class="text"
    searchRes=border=0 cellpadding=0 cellspacing=0 width="100%" class="text"
  }
  templateFile = fileadmin/templates/default/html/tx_indexedsearch.html
  search {
		page_links = 5
		# if searchbox from indexed is located somewhere else than list result. not used since macina_searchbox
		#targetPid.data = TSFE:id
	}

	# _CSS_DEFAULT_STYLE >
}

### LOCALIZATION
#plugin.tx_indexedsearch._LOCAL_LANG = LLL:EXT:indexed_search/pi/locallang.xml
#plugin.tx_indexedsearch._LOCAL_LANG.ue = LLL:EXT:indexed_search/pi/locallang.xml


plugin.tx_macinasearchbox_pi1 {
	pidSearchpage = 74
	templateFile = fileadmin/templates/default/html/tx_macinasearchbox.html

	# for some reason not working if set in normal way
	# must be updated if languages are added
	_LOCAL_LANG.pl {
		headline < lib.l10n.search.macinabox_headline
	}
	_LOCAL_LANG.en {
		headline < lib.l10n.search.macinabox_headline
	}
	_LOCAL_LANG.fr {
		headline < lib.l10n.search.macinabox_headline
	}

}


#lib.searchbox < plugin.tx_macinasearchbox_pi1

lib.searchbox = COA
lib.searchbox {
	#10 = TEXT
	#10.value < lib.l10n.search.box_wrap_label
	#10.wrap = <h3 class="search-header">|</h3>

	20 < plugin.tx_macinasearchbox_pi1
	
	# wrap =  </div> <div class="csc-default searchBox">|</div> <div>
}




### poprawka dla pagebrowser`a w wynikach wyszukiwania <patrz>  setup.ts

#indexed_search: make exception for search results at indexed_search, otherwise Strona1, Strona2 will link to index.php not siteurl
#[PIDinRootline = 74]
#	config.absRefPrefix >
#[end]