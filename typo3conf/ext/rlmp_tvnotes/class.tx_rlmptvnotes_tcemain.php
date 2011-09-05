<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004 Robert Lemke (rl@robertlemke.de)
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
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
 * Class 'tx_rlmptvnotes_tcemain' for the rlmp_tvnotes extension.
 *
 * $Id: class.tx_rlmptvnotes_tcemain.php 1132 2004-11-05 01:12:06Z andreas $
 *
 * @author     Robert Lemke <rl@robertlemke.de>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   54: class tx_rlmptvnotes_tcemain
 *
 *              SECTION: Public API (called by hook handler)
 *   74:     function processDatamap_postProcessFieldArray ($status, $table, $id, &$fieldArray, &$reference)
 *
 * TOTAL FUNCTIONS: 1
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

/**
 * Class being included by TCEmain using a hook
 *
 * @author	Robert Lemke <rl@robertlemke.de>
 * @package TYPO3
 * @subpackage rlmp_tvnotes
 */
class tx_rlmptvnotes_tcemain {

	/********************************************
	 *
	 * Public API (called by hook handler)
	 *
	 ********************************************/

	/**
	 * This method is called by a hook in the TYPO3 Core Engine (TCEmain). We use it to reset the "read" status for all backend
	 * users, whenever the content of a note is modified.
	 *
	 * @param	string		$status: The TCEmain operation status, fx. 'update'
	 * @param	string		$table: The table TCEmain is currently processing
	 * @param	string		$id: The records id (if any)
	 * @param	array		$fieldArray: The field names and their values to be processed
	 * @param	object		$reference: Reference to the parent object (TCEmain)
	 * @return	void
	 * @access public
	 */
	function processDatamap_postProcessFieldArray ($status, $table, $id, &$fieldArray, &$reference) {
		global $BE_USER;

			// If the content or title has changed, reset "read" status for all backend users (except for the current one):
		if ($status == 'update' && $table == 'tx_rlmptvnotes_notes' && (isset ($fieldArray['note']) || isset ($fieldArray['title']))) {
			$fieldArray['beusersread'] = $BE_USER->user[$BE_USER->userid_column];
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rlmp_tvnotes/class.tx_rlmptvnotes_tcemain.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rlmp_tvnotes/class.tx_rlmptvnotes_tcemain.php']);
}

?>