<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010  <>
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

##require_once(PATH_tslib.'class.tslib_pibase.php');

require_once(PATH_typo3conf.'ext/go_pibase/class.tx_gopibase.php');
/**
 * Plugin 'Search Box' for the 'go_searchbox' extension.
 *
 * @author	 <>
 * @package	TYPO3
 * @subpackage	tx_gosearchbox
 */
class tx_gosearchbox_pi1 extends tx_gopibase {
	var $prefixId      = 'tx_gosearchbox_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_gosearchbox_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'go_searchbox';	// The extension key.
	var $pi_checkCHash = true;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		
	
		return $this->doTemplateParsing();
	}
	
	
		/**
	 * do the template parsing action
	 *
	 * @access	protected
	 * @return	string	parsed template
	 */
	protected function doTemplateParsing() {
		
		$markerArray['###SEARCHWORD###'] = $this->pi_getLL('searchword');
		$markerArray['###SEARCHBOX###'] = nl2br($this->cObj->data['header']);
		$markerArray['###TEXT###'] = $this->pi_RTEcssText($this->cObj->data['bodytext']);
		
		return $this->parseTemplate('SEARCHBOX', $markerArray);	
	}
	
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/go_searchbox/pi1/class.tx_gosearchbox_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/go_searchbox/pi1/class.tx_gosearchbox_pi1.php']);
}

?>