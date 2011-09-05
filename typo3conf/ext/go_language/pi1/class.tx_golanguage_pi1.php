<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Caspar Stuebs <caspar@gosign.de>
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

//require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(PATH_typo3conf.'ext/go_pibase/class.tx_gopibase.php');

/**
 * Plugin 'Langauge Menu' for the 'go_language' extension.
 *
 * @author	Caspar Stuebs <caspar@gosign.de>
 * @package	TYPO3
 * @subpackage	tx_golanguage
 */
class tx_golanguage_pi1 extends tx_gopibase {
	var $prefixId      = 'tx_golanguage_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_golanguage_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'go_language';	// The extension key.
	#var $pi_checkCHash = true;
	
	var $defaultLangIsoCode = 'DE';
	var $myLanguageUid = 0;
	var $pageID = 0;
	var $sysLanguages = array();
	
	/*
	 * Class constructor.
	 */
	function __construct() {
		parent::__construct();
		
		$this->pageID = $GLOBALS['TSFE']->id;
		
		$this->getSysLanguages();
		
		if(($tmp = t3lib_div::_GET('L')) && isset($this->sysLanguages[$tmp])) {
			$this->myLanguageUid = $tmp;
		}
		else {
			$browserLanguage = t3lib_div::getIndpEnv('HTTP_ACCEPT_LANGUAGE');
			$tmpLanguageList = array();
			foreach ($this->sysLanguages as $lang) {
				$tmpLanguageList[$lang['uid']] = ($pos = stripos($browserLanguage, $lang['lg_iso_2'])) !== false ? $pos : strlen($browserLanguage)+1;
			}
			asort($tmpLanguageList);
			reset($tmpLanguageList);
			$this->myLanguageUid = key($tmpLanguageList);
			t3lib_div::_GETset($this->myLanguageUid, 'L');
		}
		
		#print_r($this->sysLanguages);
		#echo $this->myLanguageUid."<br />\n";
	}
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf)	{
		$content .= $GLOBALS['TSFE']->config['config']['sys_language_uid'].'<br />';
		$content .= $GLOBALS['TSFE']->config['config']['language'].'<br />';
		$content .= $GLOBALS['TSFE']->config['config']['htmlTag_langKey'].'<br />';
		$content .= $GLOBALS['TSFE']->config['config']['locale_all'].'<br />';
		
		return $content;
	}
	
	function getMenu($content, $conf) {
		$languageLink = array();
		
		// elio@gosign, 13.12.2010 : get the info about "language hidden in menu"
		$lang_from_table = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*','sys_language',"hidden = '0'",'','','','uid');

		foreach($this->sysLanguages as $id => $werte) {
			if ( $lang_from_table[$werte['uid']]['disabled_in_menu'] ) {
				continue; // jump to next language if this has been hidden
			}
			if ($id != $this->myLanguageUid) {
				$languageLink[] = $this->cObj->getTypoLink($werte['lg_name_local'], $this->pageID, array('L' => $id), '_top');
			}
		}
		
		return '<div class="language-menu">' . join(' | ', $languageLink) . '</div>';
	}
	
	function getLinkListe($content = '', $conf = '') {
		$languageLink = array();
		
		foreach($this->sysLanguages as $id => $werte) {
			if ($id != $this->myLanguageUid) {
				$languageLink[htmlspecialchars($werte['lg_name_local'])] = $this->cObj->getTypoLink_URL($this->pageID, array('L' => $id), '_top');
			}
		}
		
		return $languageLink;
	}
	
	function getSysLanguageUid($params, $ref) {
		return $this->sysLanguages[$this->myLanguageUid]['uid'];
	}
	
	function getLanguageT3($params, $ref) {
		return (!empty($this->sysLanguages[$this->myLanguageUid]['lg_typo3']) ? $this->sysLanguages[$this->myLanguageUid]['lg_typo3'] : strtolower($this->sysLanguages[$this->myLanguageUid]['lg_iso_2']));
	}
	
	function getLanguageIso($params, $ref) {
		return strtolower($this->sysLanguages[$this->myLanguageUid]['lg_iso_2']);
	}
	
	function getLocaleAll($params, $ref) {
		return $this->sysLanguages[$this->myLanguageUid]['lg_collate_locale'];
	}
	
	function getLanguageTitle($params, $ref) {
		return $this->sysLanguages[$this->myLanguageUid]['lg_name_local'];
	}
	
	/**
	 * This function gets the langauge informations from the database and puts it into the class variable
	 */
	private function getSysLanguages() {
		global $TYPO3_DB;
		
		$this->sysLanguages = $TYPO3_DB->exec_SELECTgetRows("'0' as uid, '' as title, '' as flag, lg_iso_2, lg_typo3, lg_collate_locale, lg_name_en, lg_name_local", // select fields
															"static_languages", // from table
															"lg_iso_2 = '".$this->defaultLangIsoCode."'", // where
															"", // group by
															"uid", // order by
															"", // limit
															"uid"); // index field
		
		$this->sysLanguages = array_merge($this->sysLanguages,
								$TYPO3_DB->exec_SELECTgetRows("sy.uid, sy.title, sy.flag, st.lg_iso_2, st.lg_typo3, st.lg_collate_locale, st.lg_name_en, st.lg_name_local", // select fields
															"sys_language as sy INNER JOIN static_languages as st on sy.static_lang_isocode = st.uid", // from table
															"hidden = 0", // where
															"", // group by
															"uid", // order by
															"", // limit
															"uid")); // index field
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/go_language/pi1/class.tx_golanguage_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/go_language/pi1/class.tx_golanguage_pi1.php']);
}

?>