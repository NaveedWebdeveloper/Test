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
 * Plugin 'piTeaser' for the 'go_teaser' extension.
 *
 * @author	Mansoor Ahmad
 * @package	TYPO3
 * @subpackage	piTeaser
 */
class tx_goteaser_piTeaser extends tx_gopibase {
	var $prefixId      = 'tx_goteaser_piTeaser';		// Same as class name
	var $scriptRelPath = 'piTeaser/class.tx_goteaser_piTeaser.php';	// Path to this script relative to the extension dir.
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

		$markerArrayTeaser['###IMAGE###'] = $this->getImage('tx_damttcontent_files','132','','altText', 'image_link');

		$markerArrayTeaser['###HEADLINE###'] = $this->cObj->data['header_rte'];

		$tsArrayMore = array();
		$tsArrayMore['value'] = $this->pi_getLL('go_teaser.piTeaser.more');
		$tsArrayMore['typolink.']['parameter'] = $this->cObj->getTypoLink_URL($this->cObj->data['image_link']);
		$markerArrayTeaser['###TEXT###'] = $this->pi_RTEcssText($this->cObj->data['header_rte2'].$this->cObj->TEXT($tsArrayMore));



		// return it
		return $this->parseTemplate('TEMPLATE_TEASER', $markerArrayTeaser);
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/go_teaser/piTeaser/class.tx_goteaser_piTeaser.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/go_teaser/piTeaser/class.tx_goteaser_piTeaser.php']);
}

?>