<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Mansoor Ahmad
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

require_once(PATH_typo3conf.'ext/go_pibase/class.tx_gopibase.php');

/**
 * Plugin 'piMyElement' for the 'go_teaser' extension.
 *
 * @author	Mansoor Ahmad
 * @package	TYPO3
 * @subpackage	piMyElement
 */
class tx_goteaser_piMyElement extends tx_gopibase {
	var $prefixId      = 'tx_goteaser_piMyElement';		// Same as class name
	var $scriptRelPath = 'piMyElement/class.tx_goteaser_piMyElement.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'go_teaser';	// The extension key.
	var $pi_checkCHash = true;

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content,$conf){
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->loadTemplate();
		$this->substituteLanguageMarkers();		

		// return it
		return $this->cObj->data['uid'];
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/go_teaser/piMyElement/class.tx_goteaser_piMyElement.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/go_teaser/piMyElement/class.tx_goteaser_piMyElement.php']);
}

?>