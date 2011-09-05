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
 * include some general functions only usable for the 'lfeditor' module
 *
 * $Id: class.tx_lfeditor_mod1_file.php 97 2007-05-05 18:09:04Z fire $
 *
 * @author Stefan Galinski <stefan.galinski@frm2.tum.de>
 */

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 */

/** t3lib_install */
require_once(PATH_t3lib . 'class.t3lib_install.php');

/**
 * include some general functions only usable for the 'lfeditor' module
 *
 * @author Stefan Galinski <stefan.galinski@frm2.tum.de>
 * @package Typo3
 * @subpackage tx_lfeditor
 */
abstract class tx_lfeditor_mod1_file {
	/**#@+
	/** @var array content of parsed files and origin of every language */
	protected $localLang = array();
	protected $originLang = array();
	/**#@-*/

	/**#@+
	/** @var string informations about some paths, current file type and workspace */
	protected $absPath;
	protected $relFile;
	protected $absFile;
	protected $fileType;
	protected $workspace;
	/**#@-*/

	/** @var array header informations */
	protected $meta;

	/**#@+
	/** abstract methods */
	abstract protected function prepareFileContents();
	abstract protected function readFile();
	/**#@-*/

	/**
	 * sets some variables
	 *
	 * @param string filename or relative path from second param to the language file
	 * @param string absolute path to the extension or language file
	 * @return void
	 */
	public function init($file, $path) {
		$this->setVar(array('absPath' => $path, 'relFile' => $file));
	}

	/**
	 * sets informations
	 *
	 * structure:
	 * $infos["absPath"] = absolute path to an extension or file
	 * $infos["relFile"] = relative path with filename from "absPath"
	 * $infos["workspace"] = workspace (base or xll)
	 * $infos["fileType"] = file type (php or xml)
	 * $infos["localLang"] = language data
	 * $infos["originLang"] = origin language array
	 * $infos["meta"] = meta data
	 *
	 * @param array informations (see above)
	 * @return void
	 */
	public function setVar($informations)
	{
		// path and file informations
		if(!empty($informations['absPath']))
			$this->absPath = typo3Lib::fixFilePath($informations['absPath'] . '/');
		if(!empty($informations['relFile']))
			$this->relFile = typo3Lib::fixFilePath($informations['relFile']);
		$this->absFile = $this->absPath . $this->relFile;

		// filetype and workspace
		if(!empty($informations['workspace']))
			$this->workspace = $informations['workspace'];
		if(!empty($informations['fileType']))
			$this->fileType = $informations['fileType'];

		// data arrays
		if(!count($this->localLang) && is_array($informations['localLang']))
			$this->localLang = $informations['localLang'];
		if(!count($this->originLang) && is_array($informations['originLang']))
			$this->originLang = $informations['originLang'];
		if(!count($this->meta) && is_array($informations['meta']))
			$this->meta = $informations['meta'];
	}

	/**
	 * returns requested information
	 *
	 * @param string
	 * @return void
	 */
	public function getVar($info)
	{
		if($info == 'relFile')
			return $this->relFile;
		elseif($info == 'absPath')
			return $this->absPath;
		elseif($info == 'absFile')
			return $this->absFile;
		elseif($info == 'fileType')
			return $this->fileType;
		elseif($info == 'workspace')
			return $this->workspace;
	}

	/**
	 * returns language data
	 *
	 * @param string valid language key
	 * @return array language data
	 */
	public function getLocalLangData($langKey='')
	{
		if(empty($langKey))
			return $this->localLang;
		elseif(is_array($this->localLang[$langKey]))
			return $this->localLang[$langKey];
		else
			return array();
	}

	/**
	 * deletes or sets constants in local language data
	 *
	 * @param string constant name (if empty an index number will be used)
	 * @param string new value (if empty the constant will be deleted)
	 * @param string language shortcut
	 * @param boolean set to true, if you want delete default values too
	 * @return void
	 */
	public function setLocalLangData($constant, $value, $langKey, $forceDel=false)
	{
		if(!empty($value) || (($langKey == 'default' && !$forceDel)))
			$this->localLang[$langKey][$constant] = $value;
		elseif(isset($this->localLang[$langKey][$constant]))
			unset($this->localLang[$langKey][$constant]);
	}

	/**
	 * returns origin
	 *
	 * @param string valid language key
	 * @return mixed an origin or full array
	 */
	public function getOriginLangData($langKey='')
	{
		if(empty($langKey))
			return $this->originLang;
		else
			return $this->originLang[$langKey];
	}

	/**
	 * sets new origin of a given language
	 *
	 * @param string new origin
	 * @param string language shortcut
	 * @return void
	 */
	public function setOriginLangData($origin, $langKey)
	{
		if(!empty($origin))
			$this->originLang[$langKey] = $origin;
	}

		/**
	 * returns meta data
	 *
	 * @param string special meta index
	 * @return mixed meta data
	 */
	public function getMetaData($metaIndex='')
	{
		if(!empty($metaIndex))
			return $this->meta[$metaIndex];
		else
			return $this->meta;
	}

	/**
	 * deletes or sets constants in meta data
	 *
	 * @param string special meta index
	 * @param string new value (if empty the meta index will be deleted)
	 * @return void
	 */
	public function setMetaData($metaIndex, $value)
	{
		if(!empty($value))
			$this->meta[$metaIndex] = $value;
		elseif(isset($this->meta[$metaIndex]))
			unset($this->meta[$metaIndex]);
	}

	/**
	 * writes language files
	 *
	 * @throws LFException raised if a file cant be written
	 * @return void
	 */
	public function writeFile()
	{
		// get prepared language files
		$languageFiles = $this->prepareFileContents();

		// check write permissions of all files
		foreach($languageFiles as $file=>$content)
			if(!sgLib::checkWritePerms($file))
				throw new LFException('failure.file.badPermissions');

		// write files
		foreach($languageFiles as $file=>$content)
			if(!t3lib_div::writeFile($file, $content))
				 throw new LFException('failure.file.notWritten');
	}
}

// Default-Code for using XCLASS (dont touch)
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lfeditor/mod1/class.tx_lfeditor_mod1_file.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lfeditor/mod1/class.tx_lfeditor_mod1_file.php']);
}

?>
