<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
$tempColumns = Array (
	'tx_browserpagetitle_browser_title' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:browser_page_title/locallang_db.php:pages.tx_browserpagetitle_browser_title',
		'config' => Array (
			'type' => 'input',
			'size' => '48',
			'max' => '90',
			'eval' => 'trim',
		)
	),
);

t3lib_div::loadTCA('pages');
t3lib_extMgm::addTCAcolumns('pages',$tempColumns,0);
t3lib_extMgm::addToAllTCAtypes('pages','tx_browserpagetitle_browser_title;;;;1-1-1', '', 'before:abstract,after:shortcut_mode,after:mount_pid_ol,after:urltype');


t3lib_div::loadTCA('pages_language_overlay');
t3lib_extMgm::addTCAcolumns('pages_language_overlay',$tempColumns,0);
t3lib_extMgm::addToAllTCAtypes('pages_language_overlay','tx_browserpagetitle_browser_title;;;;1-1-1', '', 'before:abstract,after:shortcut_mode,after:mount_pid_ol,after:urltype');

?>