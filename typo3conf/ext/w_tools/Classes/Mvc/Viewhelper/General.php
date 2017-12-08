<?php
/**
 * wolo.pl '.' studio 2016
 *
 * w_tools MVC base 0.5
 *
 * Viewhelper - general
 */

namespace WTP\WTools\Mvc\Viewhelper;


class General extends AbstractViewhelper	{



	/**
	 * Renders img tag
	 *
	 * @param $fileName
	 * @param bool $hasOwnImage - if original image is present or blank is used
	 * @param string $viewName - to get proper ts setup for image
	 * @return string
	 */
	public function makeImageTag($fileName, $hasOwnImage, $viewName)  {
		$isFALReference = $fileName === intval($fileName);

		if ($fileName)  {
			$conf['image.']['file'] = $fileName;    // sys_file_reference.uid that links a sys_file to e.g. a tt_content element (not sys_file.uid!)
			$conf['image.']['file.'] = $this->pObj->conf['view.'][$viewName.'.']['image.'];
			$conf['image.']['file.']['treatIdAsReference'] = $isFALReference;
			// set in ts view.VIEWNAME.image.
			//$conf['image.']['file.']['height'] = '150c';
			//$conf['image.']['file.']['width'] = '150c';

			$conf['image.']['params'] = $this->pObj->conf['view.'][$viewName.'.']['image.']['params'];
			$conf['image.']['altText'] = $this->pObj->conf['view.'][$viewName.'.']['image.']['altText'];

			if (!$hasOwnImage)
				$conf['image.']['titleText'] = $this->pi_getLL('label_noimage', 'no image');

			$theImgCode = $this->pObj->cObj->IMAGE($conf['image.']);
			$image = $this->pObj->cObj->stdWrap($theImgCode, $this->pObj->conf['view.'][$viewName.'.']['image_stdWrap.']);

			//$GLOBALS['TSFE']->lastImageInfo;	- if we need only image path
			//$image = $GLOBALS['TSFE']->cObj->IMG_RESOURCE($conf);

			/*debugster($conf);
			debugster($theImgCode);
			debugster($image);*/

			if ($image)
				return $image;
			else
				return (defined('DEV') && DEV)
					? 'image generating error - check ts. view: ' . $viewName
					: '';
		}


		return (defined('DEV') && DEV)
			? 'image error - no image. view: ' . $viewName
			: '';
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