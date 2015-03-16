<?php
namespace Extension\Templavoila\Domain\Model;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * File model
 */
class File {

	/**
	 * @return boolean
	 */
	static public function includesFal() {
		return class_exists('TYPO3\\CMS\\Core\\Resource\\ResourceFactory');
	}

	/**
	 * Build a File/Folder object from an resource pointer. This might raise exceptions.
	 *
	 * @param $filename
	 *
	 * @return \TYPO3\CMS\Core\Resource\FileInterface|\TYPO3\CMS\Core\Resource\Folder
	 */
	static protected function file($filename) {
		/** @var $resourceFactory \TYPO3\CMS\Core\Resource\ResourceFactory */
		$resourceFactory = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Resource\ResourceFactory::class);
		$file = $resourceFactory->getObjectFromCombinedIdentifier($filename);

		return $file;
	}

	/**
	 * Retrieve filename from the FAL resource or pass the
	 * given string along as this is a filename already.
	 *
	 * @param $filename
	 *
	 * @return string
	 */
	static public function filename($filename) {

		if (!self::includesFal()) {
			return $filename;
		}

		try {
			$file = self::file($filename);
			$filename = $file->getForLocalProcessing(FALSE);
		} catch (\Exception $e) {
		}

		return $filename;
	}

	/**
	 * Check whether the given input points to an (existing) file.
	 *
	 * @param string $filename
	 *
	 * @return boolean
	 */
	static public function is_file($filename) {
		if (!self::includesFal()) {
			return @is_file($filename);
		}
		$is_file = TRUE;
		try {
			self::file($filename);
		} catch (\Exception $e) {
			$is_file = FALSE;
		}

		return $is_file;
	}

	/**
	 * Check whether the given file can be used for mapping
	 * purposes (is an XML file).
	 *
	 *
	 * @param string $filename
	 *
	 * @return boolean
	 */
	static public function is_xmlFile($filename) {

		if (!self::includesFal()) {
			return self::is_xmlFile_finfo($filename);
		}
		$isXmlFile = FALSE;
		try {
			$file = self::file($filename);
			if (!$file instanceof \TYPO3\CMS\Core\Resource\FolderInterface) {
				$isXmlFile = in_array($file->getMimeType(), array('text/html', 'application/xml'));
			}
		} catch (\Exception $e) {
		}

		return $isXmlFile;
	}

	/**
	 * Check whether the given file can be used for mapping
	 * purposes (is an XML file) based on the finfo toolset.
	 *
	 * @param $filename
	 *
	 * @return boolean
	 */
	static protected function is_xmlFile_finfo($filename) {
		$isXml = FALSE;
		if (function_exists('finfo_open')) {
			$finfoMode = defined('FILEINFO_MIME_TYPE') ? FILEINFO_MIME_TYPE : FILEINFO_MIME;
			$fi = finfo_open($finfoMode);
			$mimeInformation = @finfo_file($fi, $filename);
			if (\TYPO3\CMS\Core\Utility\GeneralUtility::isFirstPartOfStr($mimeInformation, 'text/html') ||
				\TYPO3\CMS\Core\Utility\GeneralUtility::isFirstPartOfStr($mimeInformation, 'application/xml')
			) {
				$isXml = TRUE;
			}
			finfo_close($fi);
		} else {
			$pi = @pathinfo($filename);
			$isXml = preg_match('/(html?|tmpl|xml)/', $pi['extension']);
		}

		return $isXml;
	}
}
