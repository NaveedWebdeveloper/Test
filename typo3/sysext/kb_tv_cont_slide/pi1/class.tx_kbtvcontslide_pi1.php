<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004 Bernhard Kraft (kraftb@kraftb.at)
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
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Plugin 'TV Content Slide' for the 'kb_tv_cont_slide' extension.
 *
 * $Id$
 *
 * @author	Bernhard Kraft <kraftb@kraftb.at>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   49: class tx_kbtvcontslide_pi1 extends tslib_pibase
 *   64:     function main($content,$conf)
 *   93:     function getPageFlexValue($page, $field)
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension 'extdeveval')
 *
 */

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(PATH_t3lib.'class.t3lib_div.php');
require_once(PATH_t3lib.'class.t3lib_befunc.php');


class tx_kbtvcontslide_pi1 extends tslib_pibase {
	var $prefixId = 'tx_kbtvcontslide_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_kbtvcontslide_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey = 'kb_tv_cont_slide';	// The extension key.
	var $pi_checkCHash = TRUE;

	/**
	 * The main method getting called as pre/postUserFunc from the 'source' property of the RECORDS TS cObject
	 * rendering the Content Elements for a TV Column. Should return the tt_content entries of the first page
	 * which has this value set.
	 *
	 * @param	string		The already set content
	 * @param	array		The configuration of the plugin
	 * @return	string		The content elements
	 */
	function main($content,$conf) {
		if ($conf['overridePage'] || $conf['overridePage.']) {
			$overridePage = $this->cObj->stdWrap($conf['overridePage'], $conf['overridePage.']);
			$rootLine = $GLOBALS['TSFE']->sys_page->getRootLine($overridePage, $GLOBALS['TSFE']->MP);
		} else {
			$rootLine = $GLOBALS['TSFE']->rootLine;
		}
		$recordsFromTable = trim($this->cObj->stdWrap($conf['table'], $conf['table.']));
		$reverse = intval($this->cObj->stdWrap($conf['reverse'], $conf['reverse.']));
		$innerReverse = intval($this->cObj->stdWrap($conf['innerReverse'], $conf['innerReverse.']));
		$field = $this->cObj->stdWrap($conf['field'], $conf['field.']);
		$collect = intval($this->cObj->stdWrap($conf['collect'], $conf['collect.']));
		$slide = intval($this->cObj->stdWrap($conf['slide'], $conf['slide.']));
		if (!$slide) {
			$slide = -1;
		}
		$languageFallback = $this->cObj->stdWrap($conf['languageFallback'], $conf['languageFallback.']);
		if (strlen($languageFallback)) {
			$this->languageFallback = t3lib_div::intExplode(',', $languageFallback);
		} else {
			$this->languageFallback = array();
		}
		while ($page = array_shift($rootLine)) {
			$page = $GLOBALS['TSFE']->sys_page->getPage($page['uid']);
			$value = $this->getPageFlexValue($page, $field);
			if ($value && $recordsFromTable) {
				$value = $this->removeHiddenRecords($value, $recordsFromTable);
			}
			if ($innerReverse) {
				$parts = t3lib_div::trimExplode(',', $value, 1);
				$parts = array_reverse($parts);
				$value = implode(',', $parts);
			}
			if ($reverse) {
				$content = $value.(strlen($content)&&strlen($value)?',':'').$content;
			} else {
				$content .= (strlen($content)&&strlen($value)?',':'').$value;
			}
			if ($collect) {
				$collect--;
			}
			if ($slide) {
				$slide--;
			}
			if (strlen($content)&&!$collect) {
				break;
			}
			if (!$slide) {
				break;
			}
		}
		return $content;
	}

	/**
	 * This method removes hidden or disabled content elements from the list.
	 *
	 * @param	string		A list of content element uids to check
	 * @return	string		A list of valid content elements
	 */
	function removeHiddenRecords($value, $recordTable) {
		$uids = t3lib_div::intExplode(',', $value);
		$uidList = implode(',', $uids);
		$result = '';

		$loadDB = t3lib_div::makeInstance('FE_loadDBGroup');
		$loadDB->start($uidList, $recordTable);
		foreach ($loadDB->tableArray as $table => $tableData) {
			if (is_array($GLOBALS['TCA'][$table])) {
				$loadDB->additionalWhere[$table] = $this->cObj->enableFields($table);
			}
		}
		$loadDB->getFromDB();

		if (is_array($loadDB->results[$recordTable])) {
			$result = array_keys($loadDB->results[$recordTable]);
			$result = implode(',', array_intersect($uids, $result));
		}
		return $result;
	}

	/**
	 * This method returns the contents of the flex-field given.
	 *
	 * @param	array		The page row
	 * @param	string		The field name
	 * @return	string		The contents of the field
	 */
	function getPageFlexValue($page, $field) {
		$xml = t3lib_div::xml2array($page['tx_templavoila_flex']);
		$GLOBALS['TSFE']->includeTCA();
		t3lib_div::loadTCA('pages');
		$ds = t3lib_BEfunc::getFlexFormDS($GLOBALS['TCA']['pages']['columns']['tx_templavoila_flex']['config'], $page, 'pages', 'tx_templavoila_flex');
		if (is_array($ds)&&is_array($ds['meta'])) {
			$langChildren = intval($ds['meta']['langChildren']);
			$langDisable = intval($ds['meta']['langDisable']);
		} else {
			$langChildren = 0;
			$langDisable = 0;
		}
		$translatedLanguagesArr = $this->getAvailableLanguages($pageUid);
		$tryLang = $GLOBALS['TSFE']->sys_language_content;
		$tryLangArr = $this->languageFallback;
		do {
			if ($langArr = $translatedLanguagesArr[$tryLang]) {
				$lKey = $langDisable ? 'lDEF' : ($langChildren ? 'lDEF' : 'l'.$langArr['ISOcode']);
				$vKey = $langDisable ? 'vDEF' : ($langChildren ? 'v'.$langArr['ISOcode']: 'vDEF');
			} else {
				$lKey = 'lDEF';
				$vKey = 'vDEF';
			}
			$value = '';
			if (is_array($xml)&&is_array($xml['data'])&&is_array($xml['data']['sDEF'])&&is_array($xml['data']['sDEF'][$lKey])) {
				$value = $this->getSubKey($xml['data']['sDEF'][$lKey], t3lib_div::trimExplode(',', $field, 1), $vKey);
			}
		} while ((!strlen($value))&&strlen($tryLang = array_shift($tryLangArr)));
		return $value;
	}

	function getSubKey($arr, $keys, $vKey) {
		if (!is_array($arr)) {
			return '';
		}
		if (!count($keys)) {
			return $arr[$vKey];
		} else {
			$sKey = array_shift($keys);
			return $this->getSubKey($arr[$sKey], $keys, $vKey);
		}
	}

	function getAvailableLanguages($id=0, $onlyIsoCoded=true, $setDefault=true, $setMulti=false) {
		global $LANG, $TYPO3_DB, $BE_USER, $TCA, $BACK_PATH;

		t3lib_div::loadTCA ('sys_language');
		$flagAbsPath = t3lib_div::getFileAbsFileName($TCA['sys_language']['columns']['flag']['config']['fileFolder']);
		$flagIconPath = $BACK_PATH.'../'.substr($flagAbsPath, strlen(PATH_site));

		$output = array();
		//$excludeHidden = $BE_USER->isAdmin() ? '1' : 'sys_language.hidden=0';

		if ($id) {
			$excludeHidden .= ' AND pages_language_overlay.deleted=0';
			$res = $TYPO3_DB->exec_SELECTquery(
				'DISTINCT sys_language.*',
				'pages_language_overlay,sys_language',
				'pages_language_overlay.sys_language_uid=sys_language.uid AND pages_language_overlay.pid='.intval($id).' AND '.$excludeHidden,
				'',
				'sys_language.title'
			);
		} else {
			$res = $TYPO3_DB->exec_SELECTquery(
				'sys_language.*',
				'sys_language',
				$excludeHidden,
				'',
				'sys_language.title'
			);
		}

		if ($setDefault) {
			$output[0]=array(
				'uid' => 0,
				'ISOcode' => 'DEF',
			);
		}

		if ($setMulti) {
			$output[-1]=array(
				'uid' => -1,
				'ISOcode' => 'DEF',
			);
		}

		while(TRUE == ($row = $TYPO3_DB->sql_fetch_assoc($res))) {
			t3lib_BEfunc::workspaceOL('sys_language', $row);
			$output[$row['uid']]=$row;

			if ($row['static_lang_isocode']) {
				$staticLangRow = t3lib_BEfunc::getRecord('static_languages',$row['static_lang_isocode'],'lg_iso_2');
				if ($staticLangRow['lg_iso_2']) {
					$output[$row['uid']]['ISOcode'] = $staticLangRow['lg_iso_2'];
				}
			}
			if (strlen ($row['flag'])) {
				$output[$row['uid']]['flagIcon'] = @is_file($flagAbsPath.$row['flag']) ? $flagIconPath.$row['flag'] : '';
			}

			if ($onlyIsoCoded && !$output[$row['uid']]['ISOcode']) unset($output[$row['uid']]);
		}

		return $output;
	}




}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kb_tv_cont_slide/pi1/class.tx_kbtvcontslide_pi1.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kb_tv_cont_slide/pi1/class.tx_kbtvcontslide_pi1.php']);
}

?>
