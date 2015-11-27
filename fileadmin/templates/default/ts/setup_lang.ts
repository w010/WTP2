# todo: implement hreflang tags (urls to other languages)
# https://support.google.com/webmasters/answer/6059209


# How to handle localization, possible values:
# content_fallback: the system will always operate with the selected language even if the page is not translated with a page overlay record.
# strict: the system will report an error if the requested translation does not exist
# ignore: the system will stay with the selected language even if the page is not translated
# @todo fix me! it looks the setting is not taken into account with latest version of fedext
# sys_language_mode = content_fallback

# Records that are not localized till be hidden
# Possible value hideNonTranslated | int (the sys_language)
# @todo fix me! it looks the setting is not taken into account with latest version of fedext
# sys_language_overlay = hideNonTranslated

config.sys_language_mode = content_fallback ; 0
config.sys_language_overlay = hideNonTranslated
config.sys_language_softMergeIfNotBlank = tt_content:imagecols,tt_content:image,tt_content:image_noRows,tt_content:imageborder,tt_content:imagewidth,tt_content:imageheight

config.locale_all = pl_PL.UTF8
config.language = pl
config.htmlTag_setParams = lang="pl" class="no-js"
#config.htmlTag_langKey = pl
config.sys_language_uid = 0
[globalVar=GP:L=1]
config.sys_language_uid = 1
config.locale_all = en_GB.UTF8
config.language = en
config.htmlTag_setParams = lang="en" class="no-js"
[globalVar=GP:L=2]
config.sys_language_uid = 2
config.locale_all = de_DE.UTF8
config.language = de
config.htmlTag_setParams = lang="de" class="no-js"
[end]

# if default is other than polish, don't set sys_language_uid, just leave it 0, set locale, language etc. and remove db record of that language


### TEXT-MENU
lib.lang-menu = HMENU
lib.lang-menu {
  wrap = <div id="menu-lang"> <ul class="menu lang"> | </ul> </div>

  special = language
  special.value = 0,1,2

  # if no lang overlay for current page, link language like normal, mark selected language as ACT
  special.normalWhenNoLanguage = 0

  1 = TMENU
  1 {
    noBlur = 1
    NO {
		stdWrap.current = 1
		stdWrap.setCurrent = PL || EN || DE
		linkWrap = <li id="lang-pl">|</li> || <li id="lang-en">|</li> || <li id="lang-de">|</li>

		ATagTitle = PL || EN || DE
    }
    ACT < .NO
    ACT = 1
    ACT {
      linkWrap = <li id="lang-pl" class="act">|</li> || <li id="lang-en" class="act">|</li> || <li id="lang-de" class="act">|</li>

	  noLink = 0
      10 >
    }
  }
}


#[globalVar= ENV:PRODUCTION=0]
#	lib.lang-menu.special.value = 0,1,3
#[end]

