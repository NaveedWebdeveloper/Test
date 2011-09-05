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
 * Plugin 'Gosign Key-Visual' for the 'go_keyvisual' extension.
 *
 * @author	Caspar Stuebs <caspar@gosign.de>
 * @package	TYPO3
 * @subpackage	tx_gokeyvisual
 */
class tx_gokeyvisual_pi1 extends tx_gopibase {
	var $prefixId      = 'tx_gokeyvisual_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_gokeyvisual_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'go_keyvisual';	// The extension key.
	var $pi_checkCHash = true;
	
	var $swfFile = '';
	var $imgFile = '';
	var $imgLink = '';
	var $noFlashText = '';
	var $swfWidth = 950;
	var $swfHeight = 150;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf)	{
		if(!empty($conf['swfWidth'])) $this->swfWidth = $conf['swfWidth'];
		if(!empty($conf['swfHeight'])) $this->swfHeight = $conf['swfHeight'];
		
		$this->getKeyVisualValues();
		
		if($conf['srcOnly']) {
			return $this->imgFile;
		}
		
		if(!empty($this->swfFile)) {
			$this->addHeaderData($this->getHeaderJavaScript());
			$this->addJSfile(t3lib_extMgm::siteRelPath($this->extKey).'res/swfobject.js');
		}
		
		$img = !empty($this->imgFile) ? $this->cObj->IMAGE(array('file' => $this->imgFile, 'file.' => array('maxW' => $this->swfWidth, 'maxH' => $this->swfHeight),
																'stdWrap.' => array('typolink.' => array('parameter' => $this->imgLink)))) : '';
		
		$cnt =	'<div id="flashmovie">
					<div id="noflash">'.$img.'</div>
				</div>';
		
		return $cnt;
	}
	
	/**
	 * This method get the Keyvisual Fields from the DB and puts it to the class variables
	 *
	 * @return	none
	 */
	function getKeyVisualValues($takeDefaultLang = false) {
		$pageTable = ($GLOBALS['TSFE']->sys_language_uid == 0 || $takeDefaultLang) ? array('pages', 'uid') : array('pages_language_overlay', 'pid');
		$rootline = array();
		foreach($GLOBALS['TSFE']->rootLine as $tmpPage) {
			$rootline[] = $tmpPage['uid'];
		}
		$tmpUid = array_shift($rootline);
		
		if($damLoaded = t3lib_extMgm::isLoaded('dam')) {
			while((empty($this->swfFile) && empty($this->imgFile)) && $tmpUid != 0) {
				$myImages = $this->getDamImages($pageTable[0], '', '', 'AND '.$pageTable[0].'.'.$pageTable[1].'='.$tmpUid, 'tx_gokeyvisual_imagelink');
				
				foreach($myImages['rows'] as $row) {
					if($row['ident'] == 'tx_gokeyvisual_flash') $this->swfFile = $row['file_path'].$row['file_name'];
					if($row['ident'] == 'tx_gokeyvisual_image') $this->imgFile = $row['file_path'].$row['file_name'];
					if(isset($row['tx_gokeyvisual_imagelink']) && empty($this->imgLink)) $this->imgLink = $row['tx_gokeyvisual_imagelink'];
				}
				$tmpUid = array_shift($rootline);
			}
		}
		else {
			$this->swfFile = $GLOBALS['TSFE']->page['tx_gokeyvisual_flash'];
			$this->imgFile = $GLOBALS['TSFE']->page['tx_gokeyvisual_image'];
			$this->imgLink = $GLOBALS['TSFE']->page['tx_gokeyvisual_imagelink'];
			
			while((empty($this->swfFile) && empty($this->imgFile)) && $tmpUid != 0) {
				$res = $GLOBALS['TYPO3_DB']->exec_SELECT_query('pid, tx_gokeyvisual_flash, tx_gokeyvisual_image, tx_gokeyvisual_imagelink', $pageTable[0], $pageTable[1].'='.$tmpUid);
				
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					$this->swfFile = $row['tx_gokeyvisual_flash'];
					$this->imgFile = $row['tx_gokeyvisual_image'];
					$this->imgLink = empty($this->imgLink) ? $row['tx_gokeyvisual_imagelink'] : '';
				}
				$tmpUid = array_shift($rootline);
			}
			if(!empty($this->swfFile) || !empty($this->imgFile)) {
				$GLOBALS['TSFE']->includeTCA();
				t3lib_div::loadTCA($pageTable[0]);
				if(!empty($this->swfFile)) $this->swfFile = $GLOBALS['TCA'][$pageTable[0]]['columns']['tx_gokeyvisual_flash']['config']['uploadfolder'] .'/'. $this->swfFile;
				if(!empty($this->imgFile)) $this->imgFile = $GLOBALS['TCA'][$pageTable[0]]['columns']['tx_gokeyvisual_image']['config']['uploadfolder'] .'/'. $this->imgFile;
			}
		}
		if (empty($this->swfFile) && empty($this->imgFile) && !$takeDefaultLang) {
			$this->getKeyVisualValues(true);
		}
	}
	
	/**
	 * This method creates the JavaScript to include the Flash-Film
	 *
	 * @return	The JavaScript that is included on the website
	 */
	function getHeaderJavaScript() {
		$result =  '
		<script type="text/javascript">
		// <![CDATA[
			$(document).ready(function() {
				var flashvars = {};
				var params = {};
				params.bgcolor = "#ffffff";
				params.allowScriptAccess = "always";
				params.wmode = "opaque"
				var attributes = {};
				attributes.id = "flashmovie";
				attributes.name = "flashmovie";
				swfobject.embedSWF("'.$this->swfFile.'", "flashmovie", "'.$this->swfWidth.'", "'.$this->swfHeight.'", "9.0.0", "'.t3lib_extMgm::siteRelPath($this->extKey).'res/expressInstall.swf", flashvars, params, attributes);
			});
		// ]]>
		</script>
';
		return $result;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/go_keyvisual/pi1/class.tx_gokeyvisual_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/go_keyvisual/pi1/class.tx_gokeyvisual_pi1.php']);
}

?>