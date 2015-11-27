<?php
/**
 * wolo.pl '.' studio 2015
 *
 * w_tools MVC base 0.2
 *
 * Viewhelper - general
 */




class Tx_WTools_Mvc_Viewhelper_General	{

    /**
     * @var Tx_WTools_Mvc_Pibase
     */
    protected $pObj;

    public function __construct(tx_wtools_pibase &$pObj)   {
        $this->pObj = $pObj;
    }




	// why not cobj/stdwrap?
	public function wrap($string, $wrap)    {
		$wrapParts = explode('|', $wrap);
		return $wrapParts[0] . $string . $wrapParts[1];
	}

    public function pi_getLL($label, $default = '')  {
        return $this->pObj->pi_getLL($label, $default);
    }





	/**
	 * Renders img tag
	 *
	 * @param $fileName
	 * @param bool $hasOwnImage - if original image is present or blank is used
	 * @param string $viewName - to get proper ts setup for image
	 * @internal param $userData
	 * @return string
	 */
	public function makeImageTag($fileName, $hasOwnImage, $viewName)  {
		if ($fileName)  {
			$conf['file'] = $fileName;
			$conf['file.'] = $this->conf['view.'][$viewName.'.']['image.'];
			if (!$hasOwnImage)
				$conf['titleText'] = $this->pi_getLL('label_noimage', 'brak obrazka');
			$image = $GLOBALS['TSFE']->cObj->IMG_RESOURCE($conf);
		}

		if ($image)
			// todo: alt and class configurable
			return '<img src="'.$image.'" alt="avatar" class="img-responsive img-thumbnail">';
		return DEV?'image error. view:'.$viewName:'';
	}

	/**
	 * Generates user name string to display in various places as link to profile
	 * @param array $row user row
	 * @return string
	 */
	public function displayUsername($row){
		$parts = [
			$row['username']
		];
		if ($row['first_name'] || $row['last_name'])
			$parts[] = ' <span class="user_username">('.$row['first_name'] . ($row['last_name']?' '.$row['last_name']:'') .')</span>';
		return  implode(' ', $parts);
	}

	
}


?>