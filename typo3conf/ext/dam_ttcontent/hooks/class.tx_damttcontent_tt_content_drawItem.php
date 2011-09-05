<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 snowflake gmbh <info@snowflake.ch>
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

require_once(PATH_typo3 . 'sysext/cms/layout/interfaces/interface.tx_cms_layout_tt_content_drawitemhook.php');
require_once(PATH_txdam . 'lib/class.tx_dam_image.php');
require_once(PATH_txdam . 'lib/class.tx_dam_tcefunc.php');
require_once(PATH_txdam . 'lib/class.tx_dam_guifunc.php');

class tx_damttcontent_tt_content_drawItem implements tx_cms_layout_tt_content_drawItemHook  {

	/**
	 * Preprocesses the preview rendering of a content element.
	 *
	 * @param	tx_cms_layout		$parentObject: Calling parent object
	 * @param	boolean				$drawItem: Whether to draw the item using the default functionalities
	 * @param	string				$headerContent: Header content
	 * @param	string				$itemContent: Item content
	 * @param	array				$row: Record row of tt_content
	 * @return	void
	 */
	public function preProcess(tx_cms_layout &$parentObject, &$drawItem, &$headerContent, &$itemContent, array &$row) {
		if ($row['tx_damttcontent_files'])	{
			$config = &$GLOBALS['TCA']['tt_content']['columns']['tx_damttcontent_files']['config'];
			$record = $this->determineRecord($row);

			$damItems = tx_dam_db::getReferencedFiles('tt_content', $record['uid'], $config['MM_match_fields'], $config['MM'], 'tx_dam.*');
			if($damItems['rows']) {
				$itemContent = $this->renderDamItems($damItems['rows']);
			}
		}
	}

	/**
	 * Determine record to use. Current or workspace record?
	 *
	 * @param	array			$row current tt_content record
	 * @return	array			$record to use
	 */
	protected function determineRecord(array $row) {
		if ($GLOBALS['BE_USER']->workspace !== 0) {
			$workspaceRecord = t3lib_BEfunc::getWorkspaceVersionOfRecord(
				$GLOBALS['BE_USER']->workspace,
				'tt_content',
				intval($row['uid'])
			);

			if ($workspaceRecord) {
				$row = $workspaceRecord;
			}
		}

		return $row;
	}

	/**
	 * Creates thumbnail of dam items
	 *
	 * @param	array			$row current tt_content record
	 * @return	string			html of thumbnails
	 */
	protected function renderDamItems(array $damItems) {
		$itemContent = '';

		foreach ($damItems as $damItem) {
			$caption = tx_dam_guiFunc::meta_compileInfoData($damItem, '_caption:truncate:100', 'value-string');

			$thumb = tx_dam_guiFunc::thumbnail($damItem);

			$thumb = '<div style="float:left;width:56px;overflow:auto;margin:2px 5px 2px 0" title="' .
				htmlspecialchars($caption) .
				'">'.$thumb.'</div>';

			$itemContent.= $thumb;
		}

		if ($itemContent != '') {
			$itemContent = '<div style="clear:left;overflow:hidden">' . $itemContent . '</div>';
		}

		return $itemContent;
	}
}
?>
