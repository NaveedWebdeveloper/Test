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
 * includes special typo3 methods
 *
 * $Id: class.typo3Lib.php 92 2006-08-15 17:01:59Z fire $
 *
 * @author Stefan Galinski <stefan.galinski@frm2.tum.de>
 */

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 */

/**
 * includes special typo3 methods
 *
 * @author Stefan Galinski <stefan.galinski@frm2.tum.de>
 * @package Typo3
 * @subpackage tx_lfeditor
 */
class typo3Lib {
	/**#@+
	/** @var string typo3 path informations */
	const pathLocalExt = 'typo3conf/ext/';
	const pathGlobalExt = 'typo3/ext/';
	const pathSysExt = 'typo3/sysext/';
	const pathL10n = 'typo3conf/l10n/';
	/**#@-*/

	/**
	 * checks the file location type
	 *
	 * @param string file
	 * @param string location type: local, global, system or l10n (nothing if unknown)
	 */
	public static function checkFileLocation($file)
	{
		if(strpos($file, typo3Lib::pathLocalExt) !== false)
			return 'local';
		elseif(strpos($file, typo3Lib::pathGlobalExt) !== false)
			return 'global';
		elseif(strpos($file, typo3Lib::pathSysExt) !== false)
			return 'system';
		elseif(strpos($file, typo3Lib::pathL10n) !== false)
			return 'l10n';
		else
			return '';
	}

	/**
	 * converts an absolute or relative typo3 style (EXT:) file path
	 *
	 * @throws Exception raised, if the conversion fails
	 * @param string absolute file or an typo3 relative file (EXT:)
	 * @param boolean generate to relative(false) or absolute file
	 * @return string converted file path
	 */
	public static function transTypo3File($file, $mode)
	{
		$extType['local'] = typo3Lib::pathLocalExt;
		$extType['global'] = typo3Lib::pathGlobalExt;
		$extType['system'] = typo3Lib::pathSysExt;

		// relative to absolute
		if($mode)
		{
			if(strpos($file, 'EXT:') === false)
				throw new Exception('no typo3 relative path "' . $file . '"');

			$cleanFile = sgLib::trimPath('EXT:', $file);
			foreach($extType as $type)
			{
				$path = typo3Lib::fixFilePath(PATH_site . '/' . $type . '/' . $cleanFile);
				if(is_dir(dirname($path)))
					return $path;
			}

			throw new Exception('cant convert typo3 relative file "' . $file . '"');
		}
		else // absolute to relative
		{
			foreach($extType as $type)
			{
				if(strpos($file, $type) === false)
					continue;

				return 'EXT:' . sgLib::trimPath($type, sgLib::trimPath(PATH_site, $file));
			}

			throw new Exception('cant convert absolute file "' . $file . '"');
		}
	}

	/**
	 * generates portable file paths
	 *
	 * @param string file
	 * @return string fixed file
	 */
	public static function fixFilePath($file) {
		return t3lib_div::fixWindowsFilePath(str_replace('//', '/', $file));
	}

	/**
	 * writes the localconf file
	 *
	 * @throws Exception raised if localconf is empty or cant be backuped
	 * @param string line which should be added
	 * @param string value of line
	 * @return void
	 */
	public static function writeLocalconf($addLine, $value)
	{
		$localconf = PATH_typo3conf . 'localconf.php';

		// get current content
		$lines = file_get_contents($localconf);
		if(empty($lines))
			throw new Exception('localconf is empty...');
		$lines = explode("\n", str_replace('?>', '', $lines));
		$localConfObj = t3lib_div::makeInstance('t3lib_install');
		$localConfObj->updateIdentity = 'LFEditor';

		// add informations
		$localConfObj->setValueInLocalconfFile($lines, $addLine, $value);

		// backup localconf
		if(!copy($localconf, $localconf . '.bak'))
			throw new Exception('localconf couldnt be backuped...');

		// write localconf
		$localConfObj->allowUpdateLocalConf = 1;
		$localConfObj->writeToLocalconf_control($lines);
	}

	/**
	 * decodes or encodes all values in the given language array to utf-8
	 *
	 * @param array language content array
	 * @param boolean to utf-8 (true) or to original charset (false)
	 * @param array language keys to ignore
	 * @return array decoded or encoded language content array
	 */
	public static function utf8($localLang, $mode, $ignoreKeys)
	{
		// check
		if(!is_array($localLang) || !count($localLang))
			return $localLang;

		// get charset object
		$csConvObj = &$GLOBALS['LANG']->csConvObj;

		// loop all possible languages
		foreach($localLang as $langKey => $convContent)
		{
			if(!is_array($convContent) || in_array($langKey, $ignoreKeys))
				continue;

			$origCharset = $csConvObj->parse_charset($csConvObj->charSetArray[$langKey] ?
				$csConvObj->charSetArray[$langKey] : 'iso-8859-1');

			if($csConvObj->charSetArray[$langKey] == 'utf-8')
				continue;

			foreach($convContent as $labelKey => $value)
				if($mode)
					$localLang[$langKey][$labelKey] = $csConvObj->utf8_encode($value, $origCharset);
				else
					$localLang[$langKey][$labelKey] = $csConvObj->utf8_decode($value, $origCharset);
		}

		return $localLang;
	}
}

// Default-Code for using XCLASS (dont touch)
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lfeditor/mod1/class.typo3Lib.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lfeditor/mod1/class.typo3Lib.php']);
}

?>
