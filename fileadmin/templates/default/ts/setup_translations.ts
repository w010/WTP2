
### localizations for extensions and ts modules


plugin.tx_wform_pi1._LOCAL_LANG	{
	default		{
		label_field_name = Name: [default ts]
		label_field_message = Message:
		label_field_email = Email:
		label_field_phone = Phone:
		label_field_captcha = Captcha:
		label_field_submit = Submit
		info_field_captcha = Type above the text from image:
		msg_formSend = Form was successfuly sent
		msg_formSendError = Error sending form!
		msg_field_email_required = Email is required!
		msg_field_email_valid = Email is not valid!
		msg_field_message_required = Message is required!
		msg_field_name_required = Name is required!
		msg_field_name_lengthMax = Name is too long!
		msg_field_captcha_required = Captcha is required!
		msg_field_captcha_valid = Captcha is invalid!
	}
	pl		{
		label_field_name = Imię i nazwisko:
		label_field_message = Wiadomość:
		label_field_email = Email:
		label_field_phone = Telefon:
		label_field_captcha = Captcha:
		label_field_submit = Wyślij
		info_field_captcha = Wpisz tekst z obrazka:
		msg_formSend = Formularz został wysłany
		msg_formSendError = Błąd wysyłania formularza!
		msg_field_email_required = Email jest wymagany!
		msg_field_email_valid = Email jest nieprawidłowy!
		msg_field_message_required = Wiadomość jest wymagana!
		msg_field_name_required = Imię i nazwisko jest wymagane!
		msg_field_name_lengthMax = Imię i nazwisko jest zbyt długie!
		msg_field_captcha_required = Captcha jest wymagana!
		msg_field_captcha_valid = Captcha jest wymagana!
	}
	de		{
		label_field_name = Name: [default ts]
		label_field_message = Nachricht:
		label_field_email = E-Mail:
		label_field_phone = Telefon:
		label_field_captcha = Captcha:
		label_field_submit = Übermitteln
		info_field_captcha = Typ über den Text vom Bild abschreiben:
		msg_formSend = Formular wurde erfolgreich gesendet
		msg_formSendError = Fehler beim Senden Formular!
		msg_field_email_required = E-Mail ist erforderlich!
		msg_field_email_valid = Email ist nicht gültig!
		msg_field_message_required = Nachricht ist erforderlich!
		msg_field_name_required = Name ist erforderlich!
		msg_field_name_lengthMax = Name ist zu lang!
		msg_field_captcha_required = Captcha ist Pflicht!
		msg_field_captcha_valid = Captcha ist ungültig!
	}
}

plugin.tt_news._LOCAL_LANG	{

	nl	{
		catmenuHeader = Nieuwsoverzicht
		noNewsToListMsg = Geen nieuws in deze lijst
		#backToList =
		textFiles = Download
	}

	pl {
   		more = więcej >
		preAuthor =
		preAuthor = |&nbsp;
      	textRelated = Zobacz także:
		textFiles = Pobierz pliki:
		textLinks = Linki:
		textNewsAge =
		tags = Tagi:
		backToList = < zobacz wszystkie informacje
		goToArchive = przejdź do archiwum
		pi_list_browseresults_prev = poprzednia
		pi_list_browseresults_page =
		pi_list_browseresults_next = następna
		pi_list_browseresults_displays = Wyświetlanie newsów od ###FROM### do ###TO### z ###OUT_OF###

		textCatLatest = Kategoria:&nbsp;
		textCat = Kategoria:&nbsp;
		catmenuHeader = Wszystkie
	}

	en {
		more = more >
		tags = Tags:
		backToList = < show all news

		textCatLatest = Category:&nbsp;
		textCat = Category:&nbsp;
		catmenuHeader = Show all
	}
	
	de {
		more = Weiter lesen
	}

	fr {
		more = plus >
		tags = Tags:
		textCatLatest = Catégorie:&nbsp;
		textCat = Catégorie:&nbsp;
	}
}



plugin.tx_wgmaps_pi1._LOCAL_LANG	{
	default		{
		approx = approximately
		toggle = Toggle
		error_no_startpoint = Starting point not found
		error_no_endpoint = Destination point not found
		error_communication = Cannot communicate with maps server
		label_field_submit = Route
		label_form_head = Route planner
	}
	de	{
		approx = Ungefähr
		toggle = Schliessen
		error_no_startpoint = Starting point not found
		error_no_endpoint = Destination point not found
		error_communication = Cannot communicate with maps server
		label_field_submit = Route anzeigen
		label_form_head = Sehenswärdigkeiten Routenplaner
	}
}


plugin.tx_felogin_pi1._LOCAL_LANG	{
	de	{
		logout = abmelden
	}
}

#[globalVar=GP:L=0]
plugin.feadmin.dmailsubscription	{
	#evalErrors.email.uniqueLocal = Dit e-mailadres is al geregistreerd
	#evalErrors.email.email = Dit is geen correct e-mailadres
	#evalErrors.captcha.captcha = Captcha code is not correct.
}

plugin.tx_indexedsearch._LOCAL_LANG	{
	de	{
			submit_button_label = Suche
			opt_type_0 = Distinct word
			opt_type_1 = Part of word
			opt_type_2 = First part of word
			opt_type_3 = Last part of word
			opt_type_10 = Sounds like
			opt_type_20 = Sentence
			opt_defOp_0 = All words (AND)
			opt_defOp_1 = Any words (OR)
			opt_sections_0 = Whole site
			opt_sections_-1 = Only this page
			opt_sections_-2 = Top + level 1
			opt_sections_-3 = Level 2 and out
			opt_freeIndexUid_-1 = All, mixed
			opt_freeIndexUid_-2 = All, categorized
			opt_freeIndexUid_0 = Pages
			opt_freeIndexUid_header_-1 = Mixed categories
			opt_freeIndexUid_header_0 = Website Pages
			opt_media_-1 = All media
			opt_media_0 = Internal pages
			opt_media_-2 = All External
			opt_order_rank_flag = Weight/Frequency
			opt_order_rank_freq = Frequency
			opt_order_rank_first = Close to top
			opt_order_rank_count = Number of words
			opt_order_mtime = Date Modified
			opt_order_title = Document title
			opt_order_crdate = Creation date
			opt_group_sections = Section hierarchy
			opt_group_flat = Flat list
			opt_lang_-1 = All languages
			opt_lang_0 = Default
			opt_desc_0 = Highest first
			opt_desc_1 = Lowest first
			opt_RL1 = Level 1:
			opt_RL1ALL = All pages on level 1
			opt_RL2 = - Level 2:
			opt_RL2ALL = - All pages on level 2
			form_legend = Search form
			form_searchFor = Suche nach:
			form_extResume = Extended resume
			form_atATime = at a time
			form_orderBy = Order by:
			form_fromSection = From section:
			form_freeIndexUid = Category:
			form_searchIn = Search in:
			form_match = Match:
			form_style = Style:
			rules_header = Rules:
			# rules_text = 
			searchFor = Suche nach
			searchFor_or = oder
			searchFor_and = und
			searchFor_butNot = but not
			noResults = Es wurden keine Treffer gefunden.
			inSection = in the section
			inNsection = in %s section:
			inNsections = in %s sections:
			word_page = page
			word_pages = pages
			unnamedSection = Other
			link_regularSearch = Regular search
			link_advancedSearch = Erweiterte Suche
			res_path = Path:
			res_modified = Modified:
			res_created = Created:
			res_size = Size:
			res_noResume = Note: This search hit may not accurately match the content on this page. The page resume cannot be shown.
			res_otherMatching = Other matching pages in the same document:
			res_otherPageAsWell = INFO: There was another page indexed as well... Probably indexed with another or no usergroup.
			res_memberGroups = Require membership of group numbers %s
			pi_list_browseresults_prev = < Previous
			pi_list_browseresults_page = Page
			pi_list_browseresults_next = Next >
			pi_list_browseresults_display = Treffer ###TAG_BEGIN###%s nach %s###TAG_END### von ###TAG_BEGIN###%s###TAG_END###
			local_operator_AND = AND
			local_operator_OR = OR
			local_operator_NOT = NOT
			makerating_addToCurrentSearch = Add to current search words
			maketitle_matches = matches
	}
}



#
##   default lang
#

lib.l10n {

	page-meta	{
		print_label = Drukuj
		contact_label = Kontakt
		foot_note = COA
		foot_note	{
			10 = TEXT
			10.value = <span class="copyright">&copy; 2009-{date:Y}&nbsp;
			10.insertData = 1
			20 = TEXT
			20.value = Yourcompany
			20.typolink.parameter = 46
			30 = TEXT
			30.value = </span>
		}
	}

	# this is used in macina_searchbox, it doesn't work other way
	search	{
		box_wrap_label = Wyszukiwarka
		macinabox_headline = szukaj >
	}

	ttnews	{
		mainpage.catmenuHeader = Pokaż wszystkie informacje
		mainpage.catmenuBoxHeader = Pokaż informacje z kategorii:
	}
}




### JavaScript

temp.page.headerData {
	2010 = TEXT
	2010.wrap (
		<script type="text/javascript">
		/* <![CDATA[ */
		|
		/* ]]> */
		</script>
	)
	2010.value (
		var ll_expand = '+ rozwiń';
		var ll_siteIndicator = '';
		var label_exp = '<p>Rozwiń</p>';
		var label_col = '<p>Zwiń</p>';
		var addFormErrorMsg = "Zła wartość";
	)
}




#
##   EN
#

[globalVar=GP:L=1]



[end]

