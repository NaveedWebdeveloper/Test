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
 * Class 'tx_rlmptvnotes_templavoilamod1' for the rlmp_tvnotes extension.
 *
 * $Id: class.tx_rlmptvnotes_templavoilamod1.php 1132 2004-11-05 01:12:06Z andreas $
 *
 * @author     Robert Lemke <rl@robertlemke.de>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   67: class tx_rlmptvnotes_templavoilamod1
 *
 *              SECTION: Public API (called by hook handler)
 *   92:     function renderFrameWork_preProcessOutput ($dsInfo, $sheet, $content, $isLocal, &$linkCustom, &$reference)
 *  149:     function menuConfig_preProcessModMenu (&$modMenu, &$reference)
 *
 *              SECTION: Internal processing functions
 *  171:     function internal_doProcessing ($cmdArr, &$reference)
 *  271:     function internal_checkWriteAccess ($uid, &$reference)
 *
 *              SECTION: Internal render functions
 *  296:     function internal_renderInlineStylesheet ()
 *  320:     function internal_renderDivLayerView ($uid, $title, $content, &$reference)
 *  372:     function internal_renderDivLayerEdit ($uid, $title, $content, &$reference)
 *
 * TOTAL FUNCTIONS: 7
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once (PATH_t3lib.'class.t3lib_div.php');
require_once (PATH_t3lib.'class.t3lib_tcemain.php');

/**
 * Class being included by templavoila's page module using a hook
 *
 * @author	Robert Lemke <rl@robertlemke.de>
 * @package TYPO3
 * @subpackage rlmp_tvnotes
 */
class tx_rlmptvnotes_templavoilamod1 {

	var $noteBackgroundColor = '#FBFBC5';					// The default background color of the note layer
	var $titleBackgroundColor = '#FBFB8B';					// The note's title bar default color

	/********************************************
	 *
	 * Public API (called by hook handler)
	 *
	 ********************************************/

	/**
	 * This method is called by a hook in the templavoila page module. We use it to add another icon and our layer HTML output to the
	 * title bar of the page record.
	 *
	 * @param	array		$dsInfo: The current data structure
	 * @param	string		$sheet: The current sheet
	 * @param	string		$content: The HTML output rendered so far (preview of CE)
	 * @param	boolean		$isLocal: True if current CE is local, false if it is referenced from a different page
	 * @param	string		&$linkCustom: HTML output for a custom link in the titlebar of a CE. That's what we're going to use in this function
	 * @param	object		&$reference: Reference to parent object
	 * @return	void
	 * @access public
	 */
	function renderFrameWork_preProcessOutput ($dsInfo, $sheet, $content, $isLocal, &$linkCustom, &$reference) {
		global $BE_USER, $LANG;

		$LANG->includeLLFile('EXT:rlmp_tvnotes/locallang.xml');

		if ($dsInfo['el']['table'] == 'pages')	{	// Notes may only be attached to a page

				// First do the processing if some note has to be deleted or saved
			$cmdArr = array ();
			foreach (array ('deleteNote', 'newNote', 'saveNote', 'setRead') as $cmd) {
				$cmdArr[$cmd] = t3lib_div::GPvar('tx_rlmptvnotes_'.$cmd);
			}
			$this->internal_doProcessing ($cmdArr, $reference);

				// Finally render the note icon and the note layer itself
			$divLayer = '';
			$noteRows = t3lib_beFunc::getRecordsByField ('tx_rlmptvnotes_notes', 'pid', $reference->id);

			if (is_array ($noteRows)) {
				$editNote = t3lib_div::GPvar('tx_rlmptvnotes_editNote');
				$postCode  = $this->internal_renderInlineStylesheet();

				if (intval ($editNote)) {
					$postCode .= $this->internal_renderDivLayerEdit ($noteRows[0]['uid'], $noteRows[0]['title'], $noteRows[0]['note'], $reference);
				} else {
					$postCode .= $reference->MOD_SETTINGS['tx_rlmptvnotes_show'] ? $this->internal_renderDivLayerView ($noteRows[0]['uid'], $noteRows[0]['title'], $noteRows[0]['note'], $reference) : '';
				}
				$reference->doc->postCode .= $postCode;

					// Display a small note icon if the note is note opened:
				if (!$reference->MOD_SETTINGS['tx_rlmptvnotes_show']) {
					$beUserId = $BE_USER->user[$BE_USER->userid_column];

					if (t3lib_div::inList ($noteRows[0]['beusersread'], $beUserId)) {
						$smallIcon = '<img'.t3lib_iconWorks::skinImg($reference->doc->backPath.t3lib_extMgm::extRelPath('rlmp_tvnotes'),'res/notes_small_read.gif','').' style="text-align: center; vertical-align: middle;" title="'.$LANG->getLL('note_status_read',1).'" alt="" />';
						$linkCustom .= $showIcon ? '' : ('<a href="index.php?id='.$reference->id.'&amp;SET[tx_rlmptvnotes_show]=1">'.$smallIcon.'</a>');
					} else {
						$smallIcon = '<img'.t3lib_iconWorks::skinImg($reference->doc->backPath.t3lib_extMgm::extRelPath('rlmp_tvnotes'),'res/notes_small_unread.gif','').' style="text-align: center; vertical-align: middle;" title="'.$LANG->getLL('note_status_unread',1).'" alt="" />';
						$linkCustom .= $showIcon ? '' : ('<a href="index.php?id='.$reference->id.'&amp;SET[tx_rlmptvnotes_show]=1&amp;tx_rlmptvnotes_setRead='.$beUserId.'">'.$smallIcon.'</a>');
					}
				}
			} else {
				$smallIcon = '<img'.t3lib_iconWorks::skinImg($reference->doc->backPath.t3lib_extMgm::extRelPath('rlmp_tvnotes'),'res/notes_small_empty.gif','').' style="text-align: center; vertical-align: middle;" title="'.$LANG->getLL('note_status_empty',1).'" alt="" />';
				$linkCustom .= $showIcon ? '' : ('<a href="index.php?id='.$reference->id.'&amp;SET[tx_rlmptvnotes_show]=1&amp;tx_rlmptvnotes_newNote=1">'.$smallIcon.'</a>');
			}

		}
	}

	/**
	 * This method is also called by a hook in the templavoila page module. It registers some variables to be stored as session data.
	 *
	 * @param	array		&$modMenu: The $this->MOD_MENU variable
	 * @param	object		&$reference: Reference to parent object
	 * @return	void
	 * @access public
	 */
	function menuConfig_preProcessModMenu (&$modMenu, &$reference) {
		$modMenu['tx_rlmptvnotes_show'] = false;		// Show or hide the note (DIV-) layer
	}





	/********************************************
	 *
	 * Internal processing functions
	 *
	 ********************************************/

	/**
	 * Takes care of deletion and storage of the notes
	 *
	 * @param	array		$cmdArr: An array of submitted commands (through GET/POST) and their values
	 * @param	object		&$reference: Reference to the page module
	 * @return	void
	 * @access private
	 */
	function internal_doProcessing ($cmdArr, &$reference) {
		global $LANG;

			// Delete a note?
		if (intval ($cmdArr['deleteNote'])) {
			if ($this->internal_checkWriteAccess (intval ($cmdArr['deleteNote']), $reference)) {
				$cmdArray = array();
				$cmdArray['tx_rlmptvnotes_notes'][intval ($cmdArr['deleteNote'])]['delete'] = 1;

					// Store:
				$TCEmain = t3lib_div::makeInstance('t3lib_TCEmain');
				$TCEmain->stripslashes_values = 0;
				$TCEmain->start(array(),$cmdArray);
				$TCEmain->process_cmdmap();
			}
		}

			// Create a new note?
		if (intval ($cmdArr['newNote'])) {
			$pageInfoArr = t3lib_BEfunc::readPageAccess($reference->id, $reference->perms_clause);
			if ($pageInfoArr['uid'] > 0) {

					// Make sure that no other note exists for this page:
				$noteRecords = t3lib_beFunc::getRecordsByField ('tx_rlmptvnotes_notes', 'pid', $reference->id);
				if (count ($noteRecords) == 0) {

						// Configure the TCEmain command array:
					$dataArr = array();
					$dataArr['tx_rlmptvnotes_notes']['NEW'] = array (
						'pid' => $reference->id,
						'title' => $LANG->getLL('untitled',0),
						'note' => '',
					);

						// Create the new note:
					$TCEmain = t3lib_div::makeInstance('t3lib_TCEmain');
					$TCEmain->stripslashes_values = 0;
					$TCEmain->start($dataArr, array());
					$TCEmain->process_datamap();
				}
			}
		}

			// Save a modified note?
		if (intval ($cmdArr['saveNote'])) {

			if ($this->internal_checkWriteAccess (intval ($cmdArr['saveNote']), $reference)) {
				$postDataArr = t3lib_div::GPvar('tx_rlmptvnotes_data');

					// Configure the TCEmain command array:
				$dataArr = array ();
				$dataArr['tx_rlmptvnotes_notes'][intval ($cmdArr['saveNote'])] = array (
					'title' => $postDataArr['title'],
					'note' => $postDataArr['note']
				);

					// Update the note:
				$TCEmain = t3lib_div::makeInstance('t3lib_TCEmain');
				$TCEmain->stripslashes_values = 0;
				$TCEmain->start($dataArr, array());
				$TCEmain->process_datamap();
			}
		}

			// Set status to 'read' for specified backend user?
		if (intval ($cmdArr['setRead'])) {

			$pageInfoArr = t3lib_BEfunc::readPageAccess($reference->id, $reference->perms_clause);
			if ($pageInfoArr['uid'] > 0) {

					// Read note record and add be user
				$noteRecords = t3lib_beFunc::getRecordsByField ('tx_rlmptvnotes_notes', 'pid', $reference->id);
				$beUsersRead = $noteRecords[0]['beusersread'];
				if (is_array ($noteRecords) && !t3lib_div::inList ($beUsersRead, intval ($cmdArr['setRead']))) {

						// Configure the TCEmain command array:
					$dataArr = array ();
					$dataArr['tx_rlmptvnotes_notes'][$noteRecords[0]['uid']] = array (
						'beusersread' => (strlen ($beUsersRead) ? $beUsersRead.',' : '').intval ($cmdArr['setRead']),
					);

						// Create the new note:
					$TCEmain = t3lib_div::makeInstance('t3lib_TCEmain');
					$TCEmain->stripslashes_values = 0;
					$TCEmain->start($dataArr, array());
					$TCEmain->process_datamap();
				}
			}
		}

	}

	/**
	 * Returns true if the current backend user has write access to the note specified by $uid
	 *
	 * @param	integer		$uid: The note UID
	 * @param	object		&$reference: Reference to the page module
	 * @return	boolean		true if the current BE user may write / delete the specified note record
	 * @access private
	 */
	function internal_checkWriteAccess ($uid, &$reference) {
		$noteRecord = t3lib_beFunc::getRecord ('tx_rlmptvnotes_notes', $uid);
		if (is_array ($noteRecord)) {
			$pageInfoArr = t3lib_BEfunc::readPageAccess($noteRecord['pid'], $reference->perms_clause);
			return ($pageInfoArr['uid'] > 0);
		}
		return false;
	}





	/********************************************
	 *
	 * Internal render functions
	 *
	 ********************************************/

	/**
	 * renders the inline stylesheet
	 *
	 * @return	string		HTML output
	 * @access private
	 */
	function internal_renderInlineStylesheet () {
		return '
			<style type="text/css" id="tx_rlmptvnotes_style">
				/*<![CDATA[*/
					DIV#tx_rlmptvnotes_noteslayer { padding-right:1px; position:absolute; width:80%; left:10%; top:15%; }
					DIV#tx_rlmptvnotes_noteslayer TABLE {  padding: 0px; width:100%; height: 350px; }
					TD#tx_rlmptvnotes_noteslayer_dragbar { padding: 4px; background-color: '.$this->titleBackgroundColor.'; width:100%; height:16px; vertical-align: top; filter:alpha(opacity=90); opacity:0.9; -moz-opacity:0.9;}
					TD#tx_rlmptvnotes_noteslayer_iconbar { padding: 2px; background-color: '.$this->titleBackgroundColor.'; height:16px; white-space: nowrap; vertical-align: top; filter:alpha(opacity=90); opacity:0.9; -moz-opacity:0.9;}
					TD#tx_rlmptvnotes_noteslayer_displaycontent { white-space: normal; width: 100%; height: 100%; font-size: 12px; font-family: Arial, Helvetica; background-color: '.$this->noteBackgroundColor.'; padding:4px; vertical-align: top; filter:alpha(opacity=90); opacity:0.9; -moz-opacity:0.9;}
				/*]]>*/
			</style>
		';
	}

	/**
	 * renders the div layer containing a "post-it" like note in "view" mode
	 *
	 * @param	integer		$uid: The UID of the note record
	 * @param	string		$title: The note's title
	 * @param	string		$content: The note's content
	 * @param	object		&$reference: Reference to parent object
	 * @return	string		HTML output
	 * @access private
	 */
	function internal_renderDivLayerView ($uid, $title, $content, &$reference) {
		global $LANG, $BE_USER;

		$this->internal_doProcessing (array ('setRead'=>$BE_USER->user[$BE_USER->userid_column]), $reference);

		return '
			<!-- rlmp_tvnotes layer output begin -->

			<div id="tx_rlmptvnotes_noteslayer">
				<table cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td>
							<table cellspacing="0" cellpadding="0">
								<tr>
									<td id="tx_rlmptvnotes_noteslayer_dragbar">
										<ilayer width="100%">
											<layer style="width: 100%;">
												<strong>'.htmlspecialchars ($title).'</strong>
											</layer>
										</ilayer>
									</td>
									<td id="tx_rlmptvnotes_noteslayer_iconbar">
										<a href="index.php?'.$reference->linkParams().'&amp;tx_rlmptvnotes_editNote='.$uid.'"><img '.t3lib_iconWorks::skinImg($reference->doc->backPath.t3lib_extMgm::extRelPath('rlmp_tvnotes'),'res/edit.gif','').' width="18" height="16" border=0 title="'.$LANG->getLL('action_edit',1).'" alt=""></a>
										<a href="index.php?'.$reference->linkParams().'&amp;tx_rlmptvnotes_deleteNote='.$uid.'" onclick="'.htmlspecialchars('return confirm('.$LANG->JScharCode($LANG->getLL('action_confirm_delete')).');').'"><img '.t3lib_iconWorks::skinImg($reference->doc->backPath.t3lib_extMgm::extRelPath('rlmp_tvnotes'),'res/delete.gif','').' width="18" height="16" border=0  title="'.$LANG->getLL('action_delete',1).'" alt=""></a>
										<a href="index.php?'.$reference->linkParams().'&amp;SET[tx_rlmptvnotes_show]=0"><img '.t3lib_iconWorks::skinImg($reference->doc->backPath.t3lib_extMgm::extRelPath('rlmp_tvnotes'),'res/close.gif','').' width="18" height="16" border=0  title="'.$LANG->getLL('action_close',1).'" alt=""></a>
									</td>
								</tr>
								<tr>
									<td id="tx_rlmptvnotes_noteslayer_displaycontent" colspan="2">
										'.nl2br (htmlspecialchars ($content)).'
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>

			<!-- rlmp_tvnotes layer output end -->
		';
	}

	/**
	 * renders the div layer containing a "post-it" like note in "edit" mode
	 *
	 * @param	integer		$uid: The UID of the note record
	 * @param	string		$title: The note's title
	 * @param	string		$content: The note's content
	 * @param	object		&$reference: Reference to parent object
	 * @return	string		HTML output
	 * @access private
	 */
	function internal_renderDivLayerEdit ($uid, $title, $content, &$reference) {
		global $LANG, $BE_USER;

		$this->internal_doProcessing (array ('setRead'=>$BE_USER->user[$BE_USER->userid_column]), $reference);

		return '
			<!-- rlmp_tvnotes layer output begin -->

			<div id="tx_rlmptvnotes_noteslayer">
				<table cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td>
							<form name="tx_rlmptvnotes_notedata" action="index.php?'.$reference->linkParams().'" method="post">
								<table cellspacing="0" cellpadding="0">
									<tr>
										<td id="tx_rlmptvnotes_noteslayer_dragbar" onMousedown="initializedrag(event)">
											<ilayer width="100%" onSelectStart="return false;">
												<layer style="width: 100%;">
													<input name="tx_rlmptvnotes_data[title]" type="text" value="'.htmlspecialchars($title).'" style="margin: 2px 6px 2px 6px; width:95%; font-weight:bold;" />
												</layer>
											</ilayer>
										</td>
										<td id="tx_rlmptvnotes_noteslayer_iconbar">
											<input type="image" name="tx_rlmptvnotes_saveNote" value="'.$uid.'" '.t3lib_iconWorks::skinImg($reference->doc->backPath.t3lib_extMgm::extRelPath('rlmp_tvnotes'),'res/save.gif','').' title="'.$LANG->getLL('action_save',1).'" alt="" />
											<a href="index.php?'.$reference->linkParams().'&amp;tx_rlmptvnotes_deleteNote='.$uid.'" onclick="'.htmlspecialchars('return confirm('.$LANG->JScharCode($LANG->getLL('action_confirm_delete')).');').'"><img '.t3lib_iconWorks::skinImg($reference->doc->backPath.t3lib_extMgm::extRelPath('rlmp_tvnotes'),'res/delete.gif','').' width="18" height="16" border=0 title="'.$LANG->getLL('action_delete',1).'" alt=""></a>
											<a href="index.php?'.$reference->linkParams().'&amp;SET[tx_rlmptvnotes_show]=0"><img '.t3lib_iconWorks::skinImg($reference->doc->backPath.t3lib_extMgm::extRelPath('rlmp_tvnotes'),'res/close.gif','').' width="18" height="16" border=0 title="'.$LANG->getLL('action_close',1).'" alt=""></a>
										</td>
									</tr>
									<tr>
										<td id="tx_rlmptvnotes_noteslayer_displaycontent" colspan="2">
											<textarea name="tx_rlmptvnotes_data[note]" style="margin: 2px 6px 2px 6px; width:95%; height:90%;" rows="20">'.htmlspecialchars ($content).'</textarea>
										</td>
									</tr>
								</table>
							</form>
						</td>
					</tr>
				</table>
			</div>

			<!-- rlmp_tvnotes layer output end -->
		';
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rlmp_tvnotes/class.tx_rlmptvnotes_templavoilamod1.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rlmp_tvnotes/class.tx_rlmptvnotes_templavoilamod1.php']);
}

?>