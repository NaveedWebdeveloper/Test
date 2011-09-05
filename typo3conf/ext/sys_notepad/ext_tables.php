<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['taskcenter']['sys_notepad']['tx_sysnotepad_task'] = array(
	'title'       => 'LLL:EXT:sys_notepad/task/locallang.xml:mod_note',
	'description' => 'LLL:EXT:sys_notepad/task/locallang.xml:note_helpText',
	'icon'		  => 'EXT:sys_notepad/ext_icon.gif'
);

?>