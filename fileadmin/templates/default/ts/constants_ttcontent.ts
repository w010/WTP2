# links target
#PAGE_TARGET =
#styles.content.links.target =
#styles.content.links.extTarget = _blank

content {
	#pageFrameObj =
	defaultHeaderType = 2
}

styles.content.links {
   allowTags = a,b,i,u,img,br,div,pre,hr,small,sub,sup,p,strong,em,li,ul,ol,blockquote,strike,span,h1,h2,h3,h4,h5,h6
}


styles.content.imgtext {
  #titleInLink = 1
  #titleInLinkAndImg = 1
  #colSpace = 12
  #colspace = 12
  #rowSpace = 12
  #textMargin = 12
  #colSpaceHalf = 6
  #captionSplit =

  #borderColor = #C7C7C7
  #borderThick = 1
  #borderSpace = 1

  #separateRows = 1
  
  #maxW = 740
  
  # linkWrap.lightboxEnabled = 1
}

[globalVar= GP:tx_ttnews|tt_news > 0 ]
#styles.content.imgtext.maxW = 530
[end]

# very interesting condition
#[globalVar = TSFE:register|tx_himisc_div.templavoila.to = 2]
#styles.content.imgtext.maxW = 960
#[end]