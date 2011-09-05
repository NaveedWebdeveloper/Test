<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Ingo Renner (ingo@typo3.org)
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
***************************************************************/


require_once(PATH_txdam.'lib/class.tx_dam_image.php');
require_once(PATH_txdam.'lib/class.tx_dam_tcefunc.php');
require_once(PATH_txdam.'lib/class.tx_dam_guifunc.php');


/**
 * class to render a more human friendly view of DAM enabled content element
 * records in the workspace list views
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @version	$Id: class.tx_damttcontent_workspacediffview.php 16119 2009-01-27 20:03:55Z tuurlijk $
 * @package	TYPO3
 * @subpackage	DAM
 */
class tx_damttcontent_workspaceDiffView {

	protected $fileFieldConfiguration;

	/**
	 * constructor
	 *
	 */
	public function __construct() {
		$this->fileFieldConfiguration = $GLOBALS['TCA']['tt_content']['columns']['tx_damttcontent_files']['config'];
	}


	public function postProcessDiffView($table, $fieldName, $recordOld, $recordNew, $currentDiff, $parentObject) {
		$diffView = $currentDiff;

		if($table == 'tt_content'
		&& $fieldName == 'tx_damttcontent_files'
		&& $this->needsPostProcessing($recordOld, $recordNew)) {

			$filesOld = tx_dam_db::getReferencedFiles(
				'tt_content',
				$recordOld['uid'],
				$this->fileFieldConfiguration['MM_match_fields'],
				$this->fileFieldConfiguration['MM'],
				'tx_dam.*'
			);

			$filesNew = tx_dam_db::getReferencedFiles(
				'tt_content',
				$recordNew['uid'],
				$this->fileFieldConfiguration['MM_match_fields'],
				$this->fileFieldConfiguration['MM'],
				'tx_dam.*'
			);

				// building a string represemtation of the fields that can then
				// be sent to t3lib_diff, also collect the files to replace later
			$fieldContentOld = array();
			$placeholders    = array();
			foreach ($filesOld['rows'] as $damFile) {
				$fieldContentOld[] = 'tx_dam:' . $damFile['uid'];
				$placeholders['tx_dam:' . $damFile['uid']] = $damFile;
			}
			$fieldContentOld = implode(' ', $fieldContentOld);

			$fieldContentNew = array();
			foreach ($filesNew['rows'] as $damFile) {
				$fieldContentNew[] = 'tx_dam:' . $damFile['uid'];
				$placeholders['tx_dam:' . $damFile['uid']] = $damFile;
			}
			$fieldContentNew = implode(' ', $fieldContentNew);

				// create the diff
			$diff      = t3lib_div::makeInstance('t3lib_diff');
			$fieldDiff = $diff->makeDiffDisplay(
				$fieldContentOld,
				$fieldContentNew,
				'div'
			);

				// replace placeholders with thumbnail and title
			foreach ($placeholders as $placeholder => $damFile) {
				$thumbnail = tx_dam_guiFunc::thumbnail($damFile);

				$replacement = $thumbnail . ' ' . $damFile['title'];

				$fieldDiff = str_replace($placeholder, $replacement, $fieldDiff);
			}

			$diffView = $fieldDiff;
		}

		return $diffView;
	}

	/**
	 * checks whether the current record diff need post processing - that is
	 * when containing different values for the DAM image fields
	 *
	 * @param	array		the old record
	 * @param	array		the new record
	 * @return	boolean		true if post processing is neede, false otherwise
	 */
	protected function needsPostProcessing($recordOld, $recordNew) {
		$needsProcessing = false;

		$mediaContentElements = array(
			'textpic',
			'image'
		);

		if ((in_array($recordOld['CType'], $mediaContentElements)
			|| in_array($recordNew['CType'], $mediaContentElements))
		&& ($recordOld['tx_damttcontent_files'] != $recordNew['tx_damttcontent_files'])) {
			$needsProcessing = true;
		}

		return $needsProcessing;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam_ttcontent/class.tx_damttcontent_workspacediffview.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam_ttcontent/class.tx_damttcontent_workspacediffview.php']);
}

?>