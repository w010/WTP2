
# wtp todo: pid
plugin.tx_linkhandler.tt_news.singlePid = 34


plugin.feadmin.dmailsubscription {
    # cat=plugin.feadmin.dmailsubscription/file; type=file[html,htm,tmpl,txt]; label= Template File: HTML-template file
  #file.templateFile = EXT:direct_mail_subscription/pi/fe_admin_dmailsubscrip.tmpl

    # cat=plugin.feadmin.dmailsubscription//; type=string; label= Administration email: Enter the administration email address here. This email address will be the sender email and also recieve administration notes.
  email = test@example.com
    # cat=plugin.feadmin.dmailsubscription//; type=string; label= Administration name: Enter the administration name here. If set, this will be used as the email address name in the mails sent.
  emailName = WTP site

    # cat=plugin.feadmin.dmailsubscription//; type=int+; label= Record PID: If the records edited/created is located in another page than the current, enter the PID of that page here.
  pid = 46


    # cat=plugin.feadmin.dmailsubscription/typo; type=wrap; label= Wrap 1: This wrap is used in the template-file.
  wrap1 = <h3 class="newsletter-info"> | </h3>
    # cat=plugin.feadmin.dmailsubscription/typo; type=wrap; label= Wrap 2: This wrap is used in the template-file.
  wrap2 = <span class="form"> | </span>

    # cat=plugin.feadmin.dmailsubscription/color; type=color; label= Color 1: This bgcolor is used in the template-file.
  #color1 = #cccccc
    # cat=plugin.feadmin.dmailsubscription/color; type=color; label= Color 2: This bgcolor is used in the template-file.
  #color2 = #999999
    # cat=plugin.feadmin.dmailsubscription/color; type=color; label= Color 3: This bgcolor is used in the template-file.
  #color3 = #333333
}



plugin.sk_fancybox {

    #overlayOpacity = 0.7
    #overlayColor = #260009
    #hideOnContentClick = 0
}


# don't include it's jquery, if t3jquery is used
plugin.tx_jfmulticontent_pi1.file.jQueryLibrary =
#plugin.tx_jfmulticontent_pi1.file.jQueryUI


#[globalVar = TSFE:id = 80]
#plugin.myextpi {
	
#}
#[end]
