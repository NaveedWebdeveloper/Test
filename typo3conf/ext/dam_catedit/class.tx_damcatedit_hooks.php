<?php
/**
 * ************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Lorenz Ulrich (lorenz.ulrich@visol.ch)
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
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * **************************************************************/

class tx_damcatedit_hooks {

	/**
	 * Main function. Hook from t3lib/class.t3lib_tcemain.php
	 *
	 * @param	string		$status: Status of the current operation, 'new' or 'update
	 * @param	string		$table: The table currently processing data for
	 * @param	string		$id: The record uid currently processing data for, [integer] or [string] (like 'NEW...')
	 * @param	array		$fieldArray: The field array of a record
	 * @param	object		$reference: reference to parent object
	 * @return	void
	 */
	function processDatamap_afterDatabaseOperations($status, $table, $id, &$fieldArray, &$reference) {

		// Return if not the tx_dam_cat table or if status is not "new" or "update".
		if ($table != 'tx_dam_cat' || !($status == 'new' || $status == 'update')) return;

		// If not returned, update the dam_catedit tree.
		t3lib_BEfunc::setUpdateSignal('updateFolderTree');

	}


}


?>
