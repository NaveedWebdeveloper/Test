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
 * backup class
 *
 * $Id: class.tx_lfeditor_mod1_file_backup.php 97 2007-05-05 18:09:04Z fire $
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
 * backup class
 *
 * @author Stefan Galinski <stefan.galinski@frm2.tum.de>
 * @package Typo3
 * @subpackage tx_lfeditor
 */
class tx_lfeditor_mod1_file_backup extends tx_lfeditor_mod1_file {
	/** @var string meta information file */
	private $metaFile;

	/** @var array meta informations */
	private $metaArray = array();

	/**#@+
	/** @var string some variables needed to get informations from the metaArray */
	private $extName;
	private $extPath;
	private $langFile;
	/**#@-*/

	/**
	 * extended init
	 *
	 * @throws LFException raised if the meta file cant be correctly readed
	 * @param string name of the file (can be a path, if you need this (no check))
	 * @param string path to the file
	 * @param string absolute path to the meta file (includes filename)
	 * @return void
	 */
	public function init($file, $path, $metaFile)
	{
		// init
		$this->setVar(array('metaFile' => $metaFile));
		parent::init($file, $path);

		// read meta file
		try {
			if(is_file($this->metaFile))
				$this->readMetaFile();
		} catch(LFException $e) {
			throw $e;
		}
	}

	#####################
	###### Set/Get ######
	#####################

	/**
	 * sets informations
	 *
	 * structure:
	 * $infos["metaFile"] = absolute path to the meta file (includes filename)
	 * $infos["extPath"] = extension path
	 * $infos["langFile"] = language file
	 *
	 * @param array informations (see above)
	 * @return void
	 */
	public function setVar($informations)
	{
		if(!empty($informations['metaFile']))
			$this->metaFile = typo3Lib::fixFilePath($informations['metaFile']);

		if(!empty($informations['extPath'])) {
			$this->extPath = typo3Lib::fixFilePath($informations['extPath']);
			$this->extName = basename($informations['extPath']);
		}

		if(!empty($informations['langFile']))
			$this->langFile = typo3Lib::fixFilePath($informations['langFile']);

		parent::setVar($informations);
	}

	/**
	 * returns requested information
	 *
	 * @param string
	 * @return void
	 */
	public function getVar($info)
	{
		if($info == 'metaFile')
			return $this->metaFile;
		elseif($info == 'extName')
			return $this->extName;
		elseif($info == 'extPath')
			return $this->extPath;
		elseif($info == 'langFile')
			return $this->langFile;
		else
			return parent::getVar($info);
	}

	/**
	 * returns meta informations about backup files
	 *
	 * Modes:
	 * - 0 => full meta informations (default)
	 * - 1 => only meta informations of given extension key
	 * - 2 => only meta informations of given extension key and workspace
	 * - 3 => only meta informations of given extension key, workspace and language file
	 *
	 * @param integer mode (see above)
	 * @param string extension Name (default = $this->extName)
	 * @param string workspace (default = $this->workspace)
	 * @param string language file (default = $this->langFile)
	 * @return array meta data
	 */
	public function getMetaInfos($mode=0, $extName='', $workspace='', $langFile='')
	{
		$extName = empty($extName) ? $this->extName : $extName;
		$langFile = empty($langFile) ? $this->langFile : $langFile;
		$workspace = empty($workspace) ? $this->workspace : $workspace;

		// build return value
		if(!$mode)
			return $this->metaArray;
		elseif($mode == 1)
			return $this->metaArray[$extName];
		elseif($mode == 2)
			return $this->metaArray[$extName][$workspace];
		elseif($mode == 3)
			return $this->metaArray[$extName][$workspace][$langFile];
		else
			return array();
	}

	/**
	 * rewrites current meta information array with the given equivalent
	 *
	 * Modes:
	 * - 0 => full meta informations (default)
	 * - 1 => only meta informations of given extension key
	 * - 2 => only meta informations of given extension key and workspace
	 * - 3 => only meta informations of given extension key, workspace and language file
	 *
	 * @param array meta informations
	 * @param integer mode (see above)
	 * @param string extension Name (default = $this->extName)
	 * @param string workspace (default = $this->workspace)
	 * @param string language file (default = $this->langFile)
	 * @return void
	 */
	private function setMetaInfos($metaArray, $mode=0, $extName='', $workspace='', $langFile='')
	{
		$extName = empty($extName) ? $this->extName : $extName;
		$langFile = empty($langFile) ? $this->langFile : $langFile;
		$workspace = empty($workspace) ? $this->workspace : $workspace;

		// build new meta information array
		if(is_array($metaArray))
		{
			if(!$mode)
				$this->metaArray = $metaArray;
			elseif($mode == 1)
				$this->metaArray[$extName] = $metaArray;
			elseif($mode == 2)
				$this->metaArray[$extName][$workspace] = $metaArray;
			elseif($mode == 3)
				$this->metaArray[$extName][$workspace][$langFile] = $metaArray;
		}
		else
		{
			if(!$mode)
				unset($this->metaArray);
			elseif($mode == 1)
				unset($this->metaArray[$extName]);
			elseif($mode == 2)
				unset($this->metaArray[$extName][$workspace]);
			elseif($mode == 3)
				unset($this->metaArray[$extName][$workspace][$langFile]);
		}
	}

	###############################
	###### Meta File Methods ######
	###############################

	/**
	 * reads the meta information file and parses the content into $this->metaArray
	 *
	 * @throws LFException raised if no meta content was generated
	 * @return void
	 */
	private function readMetaFile()
	{
		// read file and parse xml to array
		$metaArray = t3lib_div::xml2array(file_get_contents($this->metaFile));
		if(!is_array($metaArray))
			throw new LFException('failure.backup.metaFile.notRead');

		$this->metaArray = $metaArray;
	}

	/**
	 * generate meta XML
	 *
	 * @return string meta information (xml)
	 */
	private function genMetaXML()
	{
		// define assocTagNames
		$options['parentTagMap'] = array(
			'' => 'extKey',
			'extKey' => 'workspace',
			'workspace' => 'langFile',
			'langFile' => 'file',
		);
		return t3lib_div::array2xml($this->getMetaInfos(), '', 0, 'LFBackupMeta', 0, $options);
	}

	/**
	 * writes the meta information file
	 *
	 * @throws LFException raised if the meta file cant be written
	 * @return void
	 */
	private function writeMetaFile()
	{
		$metaXML = $this->genMetaXML();
		if(empty($metaXML))
			throw new LFException('failure.backup.metaFile.notWritten');

		if(!t3lib_div::writeFile($this->metaFile, $this->getXMLHeader() . $metaXML))
			throw new LFException('failure.backup.metaFile.notWritten');
	}

	#################################
	###### Backup File Methods ######
	#################################

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
	 * removes the meta information entry and the backup file
	 *
	 * @throws LFException raised if the backup or meta file cant be written
	 * @param string file name
	 * @param string extension Name (default = $this->extName)
	 * @param string language file (default = $this->langFile)
	 * @return void
	 */
	public function deleteSpecFile($filename, $extName='', $langFile='')
	{
		// get needed meta informations
		$extName = empty($extName) ? $this->extName : $extName;
		$langFile = empty($langFile) ? $this->langFile : $langFile;
		$metaArray = $this->getMetaInfos(3, $extName, '', $langFile);

		// check backup file
		if(!isset($metaArray[$filename]))
			throw new LFException('failure.backup.notDeleted');

		// get file
		$backupPath = $metaArray[$filename]['pathBackup'];
		$file = t3lib_div::fixWindowsFilePath(PATH_site . '/' . $backupPath . '/' . $filename);

		// build new meta information file
		unset($metaArray[$filename]);
		if(!count($metaArray))
			unset($metaArray);
		$this->setMetaInfos($metaArray, 3, $extName, '', $langFile);

		$extMetaArray = $this->getMetaInfos(2, $extName);
		if(!count($extMetaArray))
			unset($extMetaArray);
		$this->setMetaInfos($extMetaArray, 2, $extName);

		// write meta information
		try {
			$this->writeMetaFile();
		} catch(LFException $e) {
			throw $e;
		}

		// delete backup file
		try {
			sgLib::deleteFiles(array($file));
		} catch(Exception $e) {
			throw new LFException('failure.backup.notDeleted', 0,
				'(' . $e->getMessage() , ')');
		}
	}

	/**
	 * wrapper for deleteSpecFile()
	 *
	 * @throws LFException raised if the backup or meta file cant be written
	 * @return void
	 */
	public function deleteFile()
	{
		try {
			$this->deleteSpecFile($this->relFile);
		} catch(LFException $e) {
			throw $e;
		}
	}

	/**
	 * reads a backup file
	 *
	 * @throws LFException raised if the backup file cant be readed
	 * @return void
	 */
	public function readFile()
	{
		if(!is_file($this->absFile))
			throw new LFException('failure.backup.notRead');

		// read file and transform from xml to array
		$phpArray = t3lib_div::xml2array(file_get_contents($this->absFile));
		if(!is_array($phpArray))
			throw new LFException('failure.backup.notRead');

		// read array
		foreach($phpArray['data'] as $langKey=>$informations)
		{
			// read origin
			try {
				$originLang[$langKey] = typo3Lib::transTypo3File($informations['meta']['origin'], true);
			} catch(Exception $e) {
				$originLang[$langKey] = PATH_site . $informations['meta']['origin'];
			}

			// read data
			if(is_array($informations['langData']))
				foreach($informations['langData'] as $const=>$value)
					$localLang[$langKey][$const] = $value;
		}

		// check
		if(!is_array($localLang) || !is_array($originLang))
			throw new LFException('failure.backup.notRead');

		// convert all values back to their original charsets
		if($GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] != 'utf-8')
			$this->localLang = typo3Lib::utf8($localLang, false, array('default'));
		else
			$this->localLang = $localLang;

		$this->originLang = $originLang;
		$this->meta = $phpArray['meta'];
	}

	/**
	 * prepares the final Content
	 *
	 * @return string prepared content (xml)
	 */
	private function prepareBackupContent()
	{
		// convert all values to utf-8
		if($GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] != 'utf-8')
			$localLang = typo3Lib::utf8($this->localLang, true, array('default'));
		else
			$localLang = $this->localLang;

		// set meta
		$phpArray['meta'] = $this->meta;

		// set array
		foreach($this->originLang as $lang=>$origin)
		{
			// set origin
			try {
				$phpArray['data'][$lang]['meta']['origin'] = typo3Lib::transTypo3File($origin, false);
			} catch(Exception $e) {
				$phpArray['data'][$lang]['meta']['origin'] = substr($origin, strlen(PATH_site));
			}

			// set data
			if(is_array($localLang[$lang]))
				foreach($localLang[$lang] as $labelKey=>$labelVal)
					$phpArray['data'][$lang]['langData'][$labelKey] = $labelVal;
		}

		// define assocTagNames
		$options['parentTagMap'] = array(
			'data' => 'languageKey',
			'langData' => 'label'
		);

		// get xml
		return t3lib_div::array2xml($phpArray, '', 0, 'LFBackup', 0, $options);
	}

	/**
	 * prepares the backup file and writes the new meta informations
	 *
	 * @throws LFException raised if meta file cant be written
	 * @return array backup file as key and content as value
	 */
	protected function prepareFileContents()
	{
		// get content
		$xml = $this->prepareBackupContent();

		// get and set name of backup
		$backupName = t3lib_div::shortMD5(md5($xml)) . '.bak';
		$this->setVar(array('relFile' => $backupName));

		// get new meta information
		$metaArray = $this->getMetaInfos(3);
		$metaArray[$this->relFile]['createdAt'] = time();
		$metaArray[$this->relFile]['pathBackup'] = str_replace(PATH_site, '', $this->absPath);
		$this->setMetaInfos($metaArray, 3);

		// write meta information file
		try {
			$this->writeMetaFile($metaXML);
		} catch(LFException $e) {
			throw $e;
		}

		$backupFiles[$this->absPath . $this->relFile] = $this->getXMLHeader() . $xml;
		return $backupFiles;
	}
}

// Default-Code for using XCLASS (dont touch)
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lfeditor/mod1/class.tx_lfeditor_mod1_file_backup.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lfeditor/mod1/class.tx_lfeditor_mod1_file_backup.php']);
}

?>
