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
 * xll workspace class (php)
 *
 * $Id: class.tx_lfeditor_mod1_file_xllPHP.php 97 2007-05-05 18:09:04Z fire $
 *
 * @author Stefan Galinski <stefan.galinski@frm2.tum.de>
 */

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 */

/** general filefunctions */
require_once(t3lib_extMgm::extPath('lfeditor') . 'mod1/class.tx_lfeditor_mod1_file_xll.php');

/**
 * xll workspace class (php)
 *
 * @author Stefan Galinski <stefan.galinski@frm2.tum.de>
 * @package Typo3
 * @subpackage tx_lfeditor
 */
class tx_lfeditor_mod1_file_xllPHP extends tx_lfeditor_mod1_file_xll {
	/**
	 * extended init
	 *
	 * @param string name of the file (can be a path, if you need this (no check))
	 * @param string path to the file
	 * @param string relative typo3 path to an language file (i.e. EXT:lfeditor/mod1/locallang.xml)
	 * @return void
	 */
	public function init($file, $path, $typo3RelFile)
	{
		$this->setVar(array('fileType' => 'php'));
		parent::init($file, $path, $typo3RelFile);
	}

	/**
	 * reads a language file
	 *
	 * @throws LFException raised if the file dont contain a locallang array
	 * @return array language content
	 */
	protected function readLLFile()
	{
		if(is_file($this->absFile))
			include($this->absFile);

		if(!is_array($LOCAL_LANG))
			throw new LFException('failure.search.noFileContent', 0, '(' . $file . ')');

		// set meta informations
		$this->meta = $LFMETA;

		return $LOCAL_LANG;
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
			$value = preg_replace('/[^\\\]\'/', '\\\'', $value);
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
		$relWithoutExt = str_replace('EXT:', '', $this->typo3RelFile);
		$extKey = substr($relWithoutExt, 0, strpos($relWithoutExt, '/'));
		$langFile = substr($relWithoutExt, strpos($relWithoutExt, '/')+1);

		$header = '<?php' . "\n";
		$header .= "/**\n * local language labels of module \"$extKey\"\n";
		$header .= " *\n * file: \"$langFile\"\n";
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
	 * prepares the final content
	 *
	 * @return array absolute xll file as key and content as value
	 */
	protected function prepareFileContents()
	{
		unset($mainFileContent);
		$languages = explode('|', TYPO3_languages);
		foreach($languages as $langKey)
			$mainFileContent .= $this->getLangContent($this->localLang[$langKey], $langKey);

		// prepare Content for the main file
		$languageFiles[$this->absFile] = $this->getHeader();
		$languageFiles[$this->absFile] .= '$LFMETA = array (' . "\n";
		$languageFiles[$this->absFile] .= $this->prepareMeta();
		$languageFiles[$this->absFile] .= ');' . "\n\n";
		$languageFiles[$this->absFile] .= '$LOCAL_LANG = array (' . "\n";
		$languageFiles[$this->absFile] .= $mainFileContent;
		$languageFiles[$this->absFile] .= ");\n";
		$languageFiles[$this->absFile] .= $this->getFooter();

		return $languageFiles;
	}
}

// Default-Code for using XCLASS (dont touch)
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lfeditor/mod1/class.tx_lfeditor_mod1_file_xllPHP.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lfeditor/mod1/class.tx_lfeditor_mod1_file_xllPHP.php']);
}

?>
