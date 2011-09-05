<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005-2006 Stefan Galinski (stefan.galinski@frm2.tum.de)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * base workspace class (xml)
 *
 * $Id: class.tx_lfeditor_mod1_file_baseXML.php 97 2007-05-05 18:09:04Z fire $
 *
 * @author Stefan Galinski <stefan.galinski@frm2.tum.de>
 */

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 */

/** general filefunctions */
require_once(t3lib_extMgm::extPath('lfeditor') . 'mod1/class.tx_lfeditor_mod1_file_base.php');

/**
 * base workspace class (xml)
 *
 * @author Stefan Galinski <stefan.galinski@frm2.tum.de>
 * @package Typo3
 * @subpackage tx_lfeditor
 */
class tx_lfeditor_mod1_file_baseXML extends tx_lfeditor_mod1_file_base {
	/**
	 * extended init
	 *
	 * @param string name of the file (can be a path, if you need this (no check))
	 * @param string path to the file
	 * @return void
	 */
	public function init($file, $path)
	{
		$this->setVar(array('fileType' => 'xml'));
		parent::init($file, $path);
	}

	/**
	 * calls the parent function and convert all values from utf-8 to the original charset
	 *
	 * @throws LFException raised if the parent read file method fails
	 * @return void
	 */
	public function readFile()
	{
		try {
			parent::readFile();
		} catch(LFException $e) {
			throw $e;
		}

		// convert all language values from utf-8 to the original charset
		if($GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] != 'utf-8')
			$this->localLang = typo3Lib::utf8($this->localLang, false, array('default'));
	}

	/**
	 * reads a language file
	 *
	 * @throws LFException raised if the file havent contain valid llxml or xml isnt valid
	 * @param string language file
	 * @param string language shortcut
	 * @return array language content
	 */
	protected function readLLFile($file, $langKey)
	{
		if(!is_file($file))
			throw new LFException('failure.select.noLangfile');

		// read xml into array
		$xmlContent = t3lib_div::xml2array(file_get_contents($file));

		// check data
		if(!is_array($xmlContent['data']) || !count($xmlContent['data']))
			throw new LFException('failure.search.noFileContent', 0, '(' . $file . ')');

		// set header data
		if($langKey == 'default')
			$this->meta = $xmlContent['meta'];

		return $xmlContent['data'];
	}

	/**
	 * checks the localLang array to find localized version of the language
	 * (checks l10n directory too)
	 *
	 * @param string language content (only one language)
	 * @param string language shortcut
	 * @return string localized file (absolute)
	 */
	protected function getLocalizedFile($content, $langKey)
	{
		try {
			$file = typo3Lib::transTypo3File($content, true);
		} catch(Exception $e) {
			if(!$file = t3lib_div::llXmlAutoFileName($this->absFile, $langKey))
				return $content;
			$file = PATH_site . $file;
			if(!is_file($file))
				return $content;
		}

		return typo3Lib::fixFilePath($file);
	}

	/**
	 * checks a filename if its a localized file reference
	 *
	 * @param string filename
	 * @param string language shortcut
	 * @return boolean true(localized) or false
	 */
	public function checkLocalizedFile($filename, $langKey)
	{
		if(!preg_match('/^(' . $langKey . ')\..*\.xml$/', $filename))
			return false;

		return true;
	}

	/**
	 * get the name of a localized file
	 *
	 * @param string language shortcut
	 * @return string localized file (only filename)
	 */
	public function nameLocalizedFile($langKey)
	{
		return $langKey . '.' . basename($this->relFile);
	}

	/**
	 * generates the xml header
	 *
	 * @return string xml header
	 */
	private function getXMLHeader()
	{
		return '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>' . "\n";
	}

	/**
	 * converts the array to a xml string
	 *
	 * @param array php structure with data
	 * @param string name of first tag
	 * @return string xml content
	 */
	private function array2xml($phpArray, $firstTag)
	{
		// define assocTagNames
		$options['parentTagMap'] = array(
			'data' => 'languageKey',
			'languageKey' => 'label',
		);

		// get xml
		$xmlString = t3lib_div::array2xml($phpArray, '', 0, $firstTag, 0, $options);
		return $this->getXMLHeader() . $xmlString;
	}

	/**
	 * prepares the content of a language file
	 *
	 * @param array content of the given language
	 * @param string language shortcut
	 * @return array new xml array
	 */
	private function getLangContent($localLang, $lang)
	{
		$content['data'][$lang] = '';
		if(!is_array($localLang))
			return $content;

		ksort($localLang);
		foreach($localLang as $const=>$value)
			$content['data'][$lang][$const] =
				$value = str_replace("\r", '', str_replace("\n", '<br />', $value));

		return $content;
	}

	/**
	 * prepares the meta array for nicer saving
	 *
	 * @return array meta content
	 */
	private function prepareMeta()
	{
		if(is_array($this->meta))
			foreach($this->meta as $label=>$value)
				$this->meta[$label] = str_replace("\r", '', str_replace("\n", '<br />', $value));

		// add generator string
		$this->meta['generator'] = 'LFEditor';

		return $this->meta;
	}

	/**
	 * prepares the final content
	 *
	 * @return array language files as key and content as value
	 */
	protected function prepareFileContents()
	{
		// convert all language values to utf-8
		if($GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] != 'utf-8')
			$this->localLang = typo3Lib::utf8($this->localLang, true, array('default'));

		// prepare Content
		$mainFileContent = array('meta' => $this->prepareMeta());
		$languages = explode('|', TYPO3_languages);
		foreach($languages as $lang)
		{
			// get content of localized and main file
			if($this->checkLocalizedFile(basename($this->originLang[$lang]), $lang))
			{
				if(is_array($this->localLang[$lang]) && count($this->localLang[$lang]))
					$languageFiles[$this->originLang[$lang]] .=
						$this->array2xml($this->getLangContent($this->localLang[$lang], $lang),
							'T3locallangExt');

				try {
					$mainFileContent['data'][$lang] =
						typo3Lib::transTypo3File($this->originLang[$lang], false);
				} catch(Exception $e) {
					if(!typo3Lib::checkFileLocation($this->originLang[$lang]) == 'l10n')
						$mainFileContent['data'][$lang] = $this->originLang[$lang];
				}
			}
 			else
				$mainFileContent = array_merge_recursive($mainFileContent,
					$this->getLangContent($this->localLang[$lang], $lang));
		}

		// only a localized file?
		if($this->checkLocalizedFile(basename($this->absFile), TYPO3_languages))
			return $languageFiles;

		// prepare Content for the main file
		$languageFiles[$this->absFile] = $this->array2xml($mainFileContent, 'T3locallang');

		return $languageFiles;
	}
}

// Default-Code for using XCLASS (dont touch)
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lfeditor/mod1/class.tx_lfeditor_mod1_file_baseXML.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lfeditor/mod1/class.tx_lfeditor_mod1_file_baseXML.php']);
}

?>
