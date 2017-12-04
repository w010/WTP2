<?php
/**
 * wolo.pl '.' studio 2016
 *
 * w_tools MVC base 0.5
 *
 * Viewhelper - links
 */

namespace WTP\WTools\Mvc\Viewhelper;


//class Tx_WTools_Mvc_Viewhelper_Links {

class Links extends AbstractViewhelper	{



// GENERAL SYSTEM LINKS
    public function makeLink($label, $piVars = [], $clear = 0, $cache = 1, $pid = null)  {      //['view'=>'single', 'mode'=>'rooms', 'uid'=>$row['uid']]
        if (!$pid)      $pid = null;
        // not sure about these params! check
        return $this->pObj->pi_linkTP_keepPIvars($label, $piVars, $clear, $cache, $pid);
    }

    /**
     * @param array $piVars
     * @param int $clear - is this used? linkTP doesn't use it
     * @param int $cache
     * @param mixed $pid - int 0 means homepage not current page?, OR null
     * @param array $params - last param to leave compatibility with pibase calls, where this is not provided
     * @return string
     */
    public function makeLink_url($piVars = [], $clear = 0, $cache = 1, $pid = null, $params = [])  {      //['view'=>'single', 'mode'=>'rooms', 'uid'=>$row['uid']]
        if (!$pid)      $pid = null;
        $pid = intval($pid);
        // not sure about these params! check links with no_debug or something
        //return $this->pObj->pi_linkTP_keepPIvars_url($params, $clear, $cache, $pid);
        $this->pObj->pi_linkTP('|', [$this->pObj->prefixId=>$piVars]+$params, intval($cache), $pid);
        return $this->pObj->cObj->lastTypoLinkUrl;
    }



//
// AJAX
//




// THESE BELOW SHOULD BE CHANGED TO CONFIGURABLE
// NOW ARE FOR EXAMPLE, TO REWRITE





    /**
     * Make general ajax base url for non-cached interactions
     * For cached like getting resources, build proper urls in makeLink_ methods and pass it to ajax call
     * @param array $config
     * @return string
     */
    public function makeLink_ajax_baseurl($config = []) {
        return $this->makeLink_url( [], intval($config['clear']), !$config['no_cache'], $config['pid'], ['type'=>$GLOBALS['TSFE']->tmpl->setup[ $this->pObj->extKey . '_ajax.' ]['typeNum']] );
    }


	/**
	 * General ajax button
	 *
	 * @param string $label  - link/button label
	 * @param string $onclick
	 * @param array  $conf - can pass successFunc, wraps or own onclick
	 * @return string link
	 */
	public function makeAjaxLink($label, $onclick, $conf = [])  {
		// remove > in case when by mistake pass full tag
		list ($conf['wrapA'], $conf['wrapB']) = explode('|', $conf['wrap']);
		return ($conf['wrapA']?str_replace('>','',$conf['wrapA']):'<a href="#"')
			. ' onclick="' . $onclick .'"'
			. ($conf['class']?' class="'.$conf['class'].'"':'').'>'
			. $label. ($conf['wrapB']?$conf['wrapB']:'</a>');
	}


	/**
	 * General ajax action call
	 *
	 * @param string $label  - link/button label
	 * @param string $action
	 * @param string $params - JSON!
	 * @param array  $conf - can pass successFunc, wraps or own onclick
	 * @return string link
	 */
    public function makeAjaxLink_action($label, $action, $params, $conf = [])  {

    	return $this->makeAjaxLink(
    		$label,
		    $this->makeAjaxCall_action($action, $params, $conf['containerAnimate'], true, $conf['successFunc'], $conf),
		    $conf
	    );

		    /*    // remove > in case when by mistake pass full tag
		    list ($conf['wrapA'], $conf['wrapB']) = explode('|', $conf['wrap']);
	        return ($conf['wrapA']?str_replace('>','',$conf['wrapA']):'<a href="#"')
	            . ' onclick="' . $this->makeAjaxCall_action($action, $params, $conf['containerAnimate'], true, $conf['successFunc'], $conf) .'"'
	            . ($conf['class']?' class="'.$conf['class'].'"':'').'>'
	            . $label. ($conf['wrapB']?$conf['wrapB']:'</a>');*/
    }

	/**
	 * General ajax action call - only onclick js
	 * WARNING - note, that fourth param is not the same as in js callAction() method - here it's additional return false, in js it's a dom caller (this should be normalized in future)
	 *
	 * @param string $action
	 * @param string $params - JSON!
	 * @param string $containerAnimate - jquery selector
	 * @param bool $addReturnFalse
	 * @param string $successFunc
	 * @param array $conf
	 * @return string onclick
	 */
	public function makeAjaxCall_action($action, $params, $containerAnimate = null, $addReturnFalse = true, $successFunc = '', $conf = [])  {
		//if (!$params)      $params = "''";  // because you can pass json with js here
		if (!$params)      $params = "{}";  // because you can pass json with js here, it may be empty string also, which is not compatible with js / json
		$str = 'Social.callAction(\''.$action.'\', '.$params.', '.($conf['containerAnimateRaw']?$containerAnimate: '\''.$containerAnimate.'\'').', this'.($successFunc?', '.$successFunc:'').');'.($addReturnFalse?'  return false;':'');
		return str_replace('"', '&quot;', $str);
	}


	/**
	 * General ajax load items
	 *
	 * @param string $label              - link/button label
	 * @param string $controller         - controller name to build its view
	 * @param string $displayMode        - controller display mode
	 * @param int    $offset             - query limit offset
	 * @param array  $additionalPiVars   - piVars to pass in link. here you can specify ie. userUid.
	 * @param string $specifiedContainer - load into this html container, not loadbutton parent
	 * @param array  $conf
	 * @return string link
	 */
    public function makeAjaxLink_load($label, $controller, $displayMode, $offset, $additionalPiVars = [], $specifiedContainer = '', $conf = []) {
        $onClick = $this->makeAjaxCall_load($controller, $displayMode, $offset, $additionalPiVars, $specifiedContainer, false, $conf);
        return '<a href="#" onclick="'.$onClick.'">'.$label.'</a>';
    }

	/**
	 * General ajax load items
	 *
	 * @param string $controller         - controller name to build its view
	 * @param string $displayMode        - controller display mode
	 * @param int    $offset             - query limit offset
	 * @param array  $additionalPiVars   - piVars to pass in link. here you can specify ie. userUid.
	 * @param string $specifiedContainer - load into this html container, not loadbutton parent
	 * @param bool   $onlyUrl
	 * @param array  $conf               - additional options for call build
	 * @return string link
	 */
    public function makeAjaxCall_load($controller, $displayMode, $offset, $additionalPiVars = [], $specifiedContainer = '', $onlyUrl = false, $conf = []) {
        $piVars = ['ajaxType'=>'getResults', 'controller'=>$controller, 'displayMode'=>$displayMode?$displayMode:$controller, 'offset'=>$offset] + $additionalPiVars;
        $url = $this->makeLink_url($piVars, 0, 1, '', ['type'=>$GLOBALS['TSFE']->tmpl->setup[ $this->pObj->extKey . '_ajax.' ]['typeNum']]);
        return $onlyUrl ? $url : str_replace('"', '&quot;', 'Social.getResults(\''.$url.'\', this, \'\', null, false, '.json_encode($conf).'); return false;' );
    }




	// forms, ajax triggers
	public function makeInputCheck($userData, $formFieldname, $action, $userfieldName, $animationSelector)  {
		$onclick = $this->makeAjaxCall_action($action, '{"value":$(this).attr(\'checked\')?1:0}', $animationSelector, false);
		// $onclick = 'console.log($(this).attr(\'checked\'));');
		$checked = $userData[$userfieldName]?' checked':'';
		return '<input type="checkbox" name="'.$formFieldname.'" value="1" onclick="'.$onclick.'"'.$checked.'>';
	}
	


}


?>