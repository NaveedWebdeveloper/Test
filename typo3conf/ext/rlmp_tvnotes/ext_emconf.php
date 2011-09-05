<?php

########################################################################
# Extension Manager/Repository config file for ext "rlmp_tvnotes".
#
# Auto generated 19-05-2011 17:08
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Notes for TemplaVoila',
	'description' => 'This extension adds PostIt(tm)-like notes to your TemplaVoila page module.',
	'category' => 'be',
	'shy' => 0,
	'dependencies' => 'cms,templavoila',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => 0,
	'uploadfolder' => 0,
	'createDirs' => 'uploads/tx_rlmptvnotes/rte/',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author' => 'Robert Lemke',
	'author_email' => 'rl@robertlemke.de',
	'author_company' => 'robert lemke medienprojekte',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'version' => '1.1.0',
	'_md5_values_when_last_written' => 'a:20:{s:32:"class.tx_rlmptvnotes_tcemain.php";s:4:"1ffa";s:40:"class.tx_rlmptvnotes_templavoilamod1.php";s:4:"5bba";s:16:"de.locallang.xml";s:4:"ac71";s:12:"ext_icon.gif";s:4:"1dcc";s:17:"ext_localconf.php";s:4:"7730";s:14:"ext_tables.php";s:4:"4588";s:14:"ext_tables.sql";s:4:"104d";s:29:"icon_tx_rlmptvnotes_notes.gif";s:4:"401f";s:13:"locallang.xml";s:4:"f60b";s:16:"locallang_db.php";s:4:"b99b";s:7:"tca.php";s:4:"a93b";s:12:"doc/ToDo.txt";s:4:"4c44";s:14:"doc/manual.sxw";s:4:"c2ac";s:13:"res/close.gif";s:4:"8204";s:14:"res/delete.gif";s:4:"1f6a";s:12:"res/edit.gif";s:4:"66e4";s:25:"res/notes_small_empty.gif";s:4:"2ba3";s:24:"res/notes_small_read.gif";s:4:"1dcc";s:26:"res/notes_small_unread.gif";s:4:"260a";s:12:"res/save.gif";s:4:"d7b2";}',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'templavoila' => '',
			'php' => '3.0.0-0.0.0',
			'typo3' => '3.5.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
);

?>