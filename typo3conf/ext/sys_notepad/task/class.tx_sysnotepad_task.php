<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2004 Kasper Skaarhoj (kasper@typo3.com)
*  (c) 2010 Georg Ringer (typo3@ringerge.org)
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
 * This class provides a textarea to save personal notes
 *
 * @author		Kasper Skaarhoj <kasper@typo3.com>
 * @author		Georg Ringer <typo3ext@ringerge.org>
 * @package		TYPO3
 * @subpackage	sys_notepad
 *
 */
class tx_sysnotepad_task implements tx_taskcenter_Task {
	/**
	 * Back-reference to the calling taskcenter module
	 *
	 * @var	SC_mod_user_task_index	$taskObject
	 */
	protected $taskObject;

	/**
	 * Constructor
	 */
	public function __construct(SC_mod_user_task_index $taskObject) {
		$this->taskObject = $taskObject;
		$GLOBALS['LANG']->includeLLFile('EXT:sys_notepad/task/locallang.xml');

			// configuration from extension manager
		$this->confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['sys_notepad']);

			// if encryption is needed, load it
		if ($this->confArr['encrypt'] == 1) {
			require_once t3lib_extMgm::extPath('sys_notepad', 'classes/class.t3lib_symencryption.php');
			$this->secure = t3lib_div::makeInstance('t3lib_symencryption');
		}

	}

	/**
	 * This method renders the task
	 *
	 * @return string	The task as HTML
	 */
	public function getTask() {
		$content = $this->taskObject->description(
			$GLOBALS['LANG']->getLL('mod_note'),
			$GLOBALS['LANG']->getLL('note_helpText')
		);

		$content .= $this->renderNote();

		return $content;
	}

	/**
	 * Render an optional additional information for the 1st view in taskcenter.
	 * Empty for this extension
	 *
	 * @return string		Overview as HTML
	 */
	public function getOverview() {

		return '';
	}


	/**
	 * Render the personal note including the form to save it again
	 *
	 * @return	string		The note
	 */
	public function renderNote() {
		$content = '';

		$incoming = t3lib_div::_GP('data');

			// Saving / creating note:
		if (isset($incoming['note']))	{
			$flashMessage = t3lib_div::makeInstance(
				't3lib_FlashMessage',
				$GLOBALS['LANG']->getLL('success.message')
			);
			$content .= $flashMessage->render();

			$this->setQuickNote($incoming);
		}

			// get the note
		$note = $this->getQuickNote();

			// if encrypten is used, a password is required
		if ($this->confArr['encrypt'] == 1 && empty($incoming['password'])) {
			$content.= '<form method="post">' .
							$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_tca.xml:be_users.password') .
							'<input type="text" name="data[password]" /><br /><br />
							<input type="submit" value="' . $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_common.xml:save', 1) . '" />
						</form>';

		} else {
			$passwordField = $passwordPrompt = '';

				// if encrypten is used, decrypt the note
			if ($this->confArr['encrypt'] == 1) {

				// decrypt only if there is a note available
				if ($note['note'] != '') {
					$this->secure->setIV(base64_decode($note['securecode']));
					$note['note'] = $this->secure->decrypt($incoming['password'], base64_decode($note['note']));
				}

					// additional password field if encryption is used
				$passwordField = $GLOBALS['LANG']->sL('LLL:EXT:setup/mod/locallang.xml:newPassword') . ': ' .
									'<input type="text" name="data[password]" /><br /><br />';
				$passwordPrompt = ' onclick="return confirm(\'' . $GLOBALS['LANG']->getLL('remember_password') . '\')" ';
			}

				// Render textarea
			$styles = (is_array($this->confArr)) ? ' style="' . htmlspecialchars($this->confArr['styles']) . '" ' : '';
				$content .= '<form method="post">
					<textarea rows="30" cols="48"  name="data[note]"' . $styles . '>' . t3lib_div::formatForTextarea($note['note']) . '</textarea>
					<br /><br />
					' . $passwordField . '
					<input type="submit" ' . $passwordPrompt . 'value="' . $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_common.xml:save', 1) . '" />
					<input type="reset" value="' . $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_common.xml:cancel', 1) . '" />
				</form>';
		}

		return $content;
	}

	/**
	 * Save the note of the user to the DB
	 *
	 * @return void
	 */
	protected function setQuickNote($data) {

			// if encrypten is used, the note needs to be encrpyted
		if ($this->confArr['encrypt'] == 1) {
			if ($data['note'] == '') {
				$data['securecode'] = '';
			} else {
				$data['note'] = base64_encode($this->secure->encrypt($data['password'], $data['note']));
				$data['securecode'] = base64_encode($this->secure->getCurrentIV());
			}
			
		} else {
				// quoting only if no encrypten is used
			$data['note'] = $GLOBALS['TYPO3_DB']->quoteStr($data['note'], 'sys_notepad');
		}

			// extend data
		$data['tstamp']	= $GLOBALS['ACCESS_TIME'];
			// unset password field
		unset($data['password']);


			// check if record should be inserted or updated
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*',
			'sys_notepad',
			'uid=' . $GLOBALS['BE_USER']->user['uid']
		);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$GLOBALS['TYPO3_DB']->sql_free_result($res);

		if (is_array($row))	{
				// update
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('sys_notepad', 'uid='.$GLOBALS['BE_USER']->user['uid'], $data);
		} else {
				// insert
			$data['cruser_id']	= $GLOBALS['BE_USER']->user['uid'];
			$data['crdate']		= $GLOBALS['ACCESS_TIME'];

			$GLOBALS['TYPO3_DB']->exec_INSERTquery("sys_notepad", $data);
		}
	}


	/**
	 * Get the note of the BE user
	 *
	 * @return resource		resource of the query
	 */
	protected function getQuickNote()	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*',
			'sys_notepad',
			'cruser_id=' . $GLOBALS['BE_USER']->user['uid']
		);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$GLOBALS['TYPO3_DB']->sql_free_result($res);

		return $row;
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sys_notepad/task/class.tx_sysnotepad_task.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sys_notepad/task/class.tx_sysnotepad_task.php']);
}

?>