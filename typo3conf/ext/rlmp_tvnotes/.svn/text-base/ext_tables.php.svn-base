<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::allowTableOnStandardPages('tx_rlmptvnotes_notes');

$TCA['tx_rlmptvnotes_notes'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:rlmp_tvnotes/locallang_db.php:tx_rlmptvnotes_notes',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_rlmptvnotes_notes.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'title, note, beusersread',
	)
);
?>