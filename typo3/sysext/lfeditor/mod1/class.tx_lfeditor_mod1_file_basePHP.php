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
 * base workspace class (php)
 *
 * $Id: class.tx_lfeditor_mod1_file_basePHP.php 97 2007-05-05 18:09:04Z fire $
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
 * base workspace class (php)
 *
 * @author Stefan Galinski <stefan.galinski@frm2.tum.de>
 * @package Typo3
 * @subpackage tx_lfeditor
 */
class tx_lfeditor_mod1_file_basePHP extends tx_lfeditor_mod1_file_base {
	/**
	 * extended init
	 *
	 * @param string name of the file (can be a path, if you need this (no check))
	 * @param string path to the file
	 * @return void
	 */
	public function init($file, $path)
	{
		$this->setVar(array('fileType' => 'php'));
		parent::init($file, $path);
	}

	/**
	 * calls the parent function and convert all values from the original charset to utf-8
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

		// convert all language values from the original charset to utf-8
		if($GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] == 'utf-8')
			$this->localLang = typo3Lib::utf8($this->localLang, true, array('default'));
	}

	/**
	 * reads a language file
	 *
	 * @throws LFException raised if the file dont contain a locallang array
	 * @param string language file
	 * @param string language shortcut (not used)
	 * @return array language content
	 */
	protected function readLLFile($file, $langKey)
	{
		if(!is_file($file))
			throw new LFException('failure.select.noLangfile');

		include($file);

		if(!is_array($LOCAL_LANG))
			throw new LFException('failure.search.noFileContent', 0, '(' . $file . ')');

		if($langKey == 'default')
			$this->meta = $LFMETA;

		return $LOCAL_LANG;
	}

	/**
	 * checks the given content, if its a localized language file reference
	 *
	 * @param mixed language content (only one language)
	 * @param string language shortcut
	 * @return string localized file (absolute) or a boolean false
	 */
	protected function getLocalizedFile($content, $langKey)
	{
		if((string)$content != 'EXT')
			return '';

		return typo3Lib::fixFilePath(dirname($this->absFile) .
			'/' . $this->nameLocalizedFile($langKey));
	}

	/**
	 * checks a filename, if its a localized file
	 *
	 * @param string filename
	 * @param string language shortcut
	 * @return boolean true(localized) or false
	 */
	public function checkLocalizedFile($filename, $langKey)
	{
		if(!preg_match('/^.*\.(' . $langKey . ')\.php$/', $filename))
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
		return sgLib::setFileExtension($langKey . '.php', basename($this->relFile));
	}

	/**
	 * prepares the meta data for writing into a file
	 *
	 * @return string meta data for writing purposes
	 */
	private function prepareMeta()
	{
		if(!is_array($this->meta))
			return '';

		unset($metaData);
		foreach($this->meta as $metaIndex=>$value)
		{
			$value = preg_replace('/[^\\\]\'/', '\\\'', str_replace("\n", '<br />', $value));
			$metaData .= "\t" . '\'' . $metaIndex . '\' => \'' . $value . '\',' . "\n";
		}

		return $metaData;
	}

	/**
	 * generates the header data of a language file
	 *
	 * @return string header data
	 */
	private function getHeader()
	{
		$extKey = basename($this->absPath);

		$header = '<?php' . "\n";
		$header .= "/**\n * local language labels of module \"$extKey\"\n";
		$header .= " *\n * This file is detected by the translation tool\n";
		$header .= " *\n * Modified/Created by extension 'lfeditor'\n */\n\n";

		return $header;
	}

	/**
	 * generates the footer data of a language file
	 *
	 * @return string footer data
	 */
	private function getFooter()
	{
		return '?>' . "\n";
	}

	/**
	 * prepares the content of a language file
	 *
	 * @param array content of the given language
	 * @param string language shortcut
	 * @return string language part of the main file
	 */
	private function getLangContent($localLang, $lang)
	{
		$content .= "\t'$lang' => array (\n";
		if(is_array($localLang))
		{
			ksort($localLang);
			foreach($localLang as $const=>$value)
			{
				$value = preg_replace("/([^\\\])'/", "$1\'", $value);
				$value = str_replace("\r", '', str_replace("\n", '<br />', $value));
				$content .= "\t\t'$const' => '$value',\n";
			}
		}
		$content .= "\t),\n";

		return $content;
	}

	/**
	 * prepares the content of a localized language file
	 *
	 * @param array content of the given language
	 * @param string language shortcut
	 * @return string language content
	 */
	private function getLangContentLoc($localLang, $lang)
	{
		$content .= '$LOCAL_LANG[\'' . $lang . '\'] = array (' . "\n";
		if(is_array($localLang))
		{
			ksort($localLang);
			foreach($localLang as $const=>$value)
			{
				$value = preg_replace("/([^\\\])'/", "$1\'", $value);
				$value = str_replace("\r", '', str_replace("\n", '<br />', $value));
				$content .= "\t'$const' => '$value',\n";
			}
		}
		$content .= ");\n";

		return $content;
	}

	/**
	 * prepares the final content
	 *
	 * @return array language files as key and content as value
	 */
	protected function prepareFileContents()
	{
		// convert all language values from utf-8 to the original charset
		if($GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] == 'utf-8')
			$this->localLang = typo3Lib::utf8($this->localLang, false, array('default'));

		// prepare Content
		unset($mainFileContent);
		$languages = explode('|', TYPO3_languages);
		foreach($languages as $lang)
		{
			// get content of localized and main file
			if($this->checkLocalizedFile(basename($this->originLang[$lang]), $lang)) {
				if(is_array($this->localLang[$lang]) && count($this->localLang[$lang])) {
					$languageFiles[$this->originLang[$lang]] = $this->getHeader();
					$languageFiles[$this->originLang[$lang]] .=
						$this->getLangContentLoc($this->localLang[$lang], $lang);
					$languageFiles[$this->originLang[$lang]] .= $this->getFooter();
				}
				$mainFileContent .= "\t'$lang' => 'EXT',\n";
			}
			else
				$mainFileContent .= $this->getLangContent($this->localLang[$lang], $lang);
		}

		// only a localized file?
		if($this->checkLocalizedFile(basename($this->absFile), TYPO3_languages))
			return $languageFiles;

		// prepare Content for the main file
		$languageFiles[$this->absFile] = $this->getHeader();
		$languageFiles[$this->absFile] .= '$LFMETA = array (' . "\n";
		$languageFiles[$this->absFile] .= $this->prepareMeta();
		$languageFiles[$this->absFile] .= ');' . "\n\n";
		$languageFiles[$this->absFile] .= '$LOCAL_LANG = array (' . "\n";
		$languageFiles[$this->absFile] .= $mainFileContent;
		$languageFiles[$this->absFile] .= ');' . "\n";
		$languageFiles[$this->absFile] .= $this->getFooter();

		return $languageFiles;
	}
}

// Default-Code for using XCLASS (dont touch)
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lfeditor/mod1/class.tx_lfeditor_mod1_file_basePHP.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lfeditor/mod1/class.tx_lfeditor_mod1_file_basePHP.php']);
}

?>
