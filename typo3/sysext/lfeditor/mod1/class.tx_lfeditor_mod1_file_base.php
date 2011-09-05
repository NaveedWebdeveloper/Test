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
 * base workspace class
 *
 * $Id: class.tx_lfeditor_mod1_file_base.php 97 2007-05-05 18:09:04Z fire $
 *
 * @author Stefan Galinski <stefan.galinski@frm2.tum.de>
 */

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 */

/** general filefunctions */
require_once(t3lib_extMgm::extPath('lfeditor') . 'mod1/class.tx_lfeditor_mod1_file.php');

/**
 * base workspace class
 *
 * @author Stefan Galinski <stefan.galinski@frm2.tum.de>
 * @package Typo3
 * @subpackage tx_lfeditor
 */
abstract class tx_lfeditor_mod1_file_base extends tx_lfeditor_mod1_file {
	/**#@+
	/** abstract methods */
	abstract protected function getLocalizedFile($content, $lang);
	abstract protected function checkLocalizedFile($filename, $langKey);
	abstract protected function nameLocalizedFile($langKey);
	abstract protected function readLLFile($file, $langKey);
	/**#@-*/

	/**
	 * extended init
	 *
	 * @param string name of the file (can be a path, if you need this (no check))
	 * @param string path to the file
	 * @return void
	 */
	public function init($file, $path)
	{
		// localization files shouldnt be edited
		if($this->checkLocalizedFile(basename($file), TYPO3_languages))
			throw new LFException('failure.langfile.notSupported');

		$this->setVar(array('workspace' => 'base'));
		parent::init($file, $path);
	}

	/**
	 * reads the absolute language file with all localized sub files
	 *
	 * @return void
	 */
	public function readFile()
	{
		// read absolute file
		try {
			$localLang = $this->readLLFile($this->absFile, 'default');
		} catch(LFException $e) {
			throw $e;
		}

		// loop all languages
		$languages = explode('|', TYPO3_languages);
		foreach($languages as $lang)
		{
			$originLang[$lang] = $this->absFile;
			if(is_array($localLang[$lang]) || $lang == 'default') {
				if (count($localLang[$lang]))
					ksort($localLang[$lang]);
				continue;
			}

			// get localized file
			$lFile = $this->getLocalizedFile($localLang[$lang], $lang);
			if($this->checkLocalizedFile(basename($lFile), $lang))
			{
				$originLang[$lang] = $lFile;
				$localLang[$lang] = array();

				if(!is_file($lFile))
					continue;

				// read the content
				try {
					$llang = $this->readLLFile($lFile, $lang);
				} catch(LFException $e) {
					throw $e;
				}

				// merge arrays and save origin of current language
				$localLang = t3lib_div::array_merge_recursive_overrule($localLang, $llang);
			}
		}

		// check
		if(!is_array($localLang))
			throw new LFException('failure.search.noFileContent');

		// copy all to object variables, if everything was ok
		$this->localLang = $localLang;
		$this->originLang = $originLang;
	}
}

// Default-Code for using XCLASS (dont touch)
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lfeditor/mod1/class.tx_lfeditor_mod1_file_base.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lfeditor/mod1/class.tx_lfeditor_mod1_file_base.php']);
}

?>
