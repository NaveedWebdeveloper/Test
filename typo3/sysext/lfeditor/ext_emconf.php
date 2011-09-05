<?php

########################################################################
# Extension Manager/Repository config file for ext "lfeditor".
#
# Auto generated 16-02-2011 09:15
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Language File Editor',
	'description' => 'This module serves several functions for editing of language files.
					  Translators, extension authors and simple users are supported with
					  special functions for each of them.
					  Following functions are implemented in this module:
					  * Formats: PHP and XML
					  * enhanced insert types
					  * conversion from PHP to XML and vice versa
					  * splitting and merging of language files
					  * workspaces (local (only for backend modules) and global)
					  * simple editing of constants and languages
					  * flexible search and view of constants and values
					  * meta information handling
					  * backups, recovering and diff view',
	'category' => 'module',
	'shy' => 0,
	'version' => '2.6.2',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod1',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Stefan Galinski',
	'author_email' => 'Stefan.Galinski@gmail.com',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.0.0-0.0.0',
			'typo3' => '3.8.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:48:{s:9:"ChangeLog";s:4:"8e74";s:8:"TODO.txt";s:4:"85d3";s:21:"ext_conf_template.txt";s:4:"9360";s:12:"ext_icon.gif";s:4:"9905";s:14:"ext_tables.php";s:4:"2c2d";s:14:"doc/manual.sxw";s:4:"d1a4";s:26:"mod1/class.LFException.php";s:4:"85ea";s:20:"mod1/class.sgLib.php";s:4:"28d3";s:36:"mod1/class.tx_lfeditor_mod1_file.php";s:4:"8736";s:43:"mod1/class.tx_lfeditor_mod1_file_backup.php";s:4:"6339";s:41:"mod1/class.tx_lfeditor_mod1_file_base.php";s:4:"a9b3";s:44:"mod1/class.tx_lfeditor_mod1_file_basePHP.php";s:4:"1101";s:44:"mod1/class.tx_lfeditor_mod1_file_baseXML.php";s:4:"08c2";s:40:"mod1/class.tx_lfeditor_mod1_file_xll.php";s:4:"a63e";s:43:"mod1/class.tx_lfeditor_mod1_file_xllPHP.php";s:4:"87be";s:43:"mod1/class.tx_lfeditor_mod1_file_xllXML.php";s:4:"18c1";s:41:"mod1/class.tx_lfeditor_mod1_functions.php";s:4:"fda8";s:40:"mod1/class.tx_lfeditor_mod1_template.php";s:4:"a317";s:23:"mod1/class.typo3Lib.php";s:4:"2c35";s:13:"mod1/conf.php";s:4:"13c1";s:21:"mod1/de.locallang.xml";s:4:"c512";s:25:"mod1/de.locallang_mod.xml";s:4:"1ee6";s:21:"mod1/dk.locallang.xml";s:4:"c64c";s:25:"mod1/dk.locallang_mod.xml";s:4:"cb42";s:21:"mod1/fi.locallang.xml";s:4:"644f";s:25:"mod1/fi.locallang_mod.xml";s:4:"b778";s:14:"mod1/index.php";s:4:"f84c";s:18:"mod1/locallang.xml";s:4:"85de";s:22:"mod1/locallang_mod.xml";s:4:"d60a";s:19:"mod1/moduleicon.png";s:4:"f2a8";s:22:"mod1/textareaResize.js";s:4:"8100";s:21:"mod1/tinyMCEConfig.js";s:4:"9d06";s:24:"mod1/tx_lfeditor_mod1.js";s:4:"2d0b";s:29:"mod1/tx_lfeditor_mod1_css.css";s:4:"104a";s:19:"res/images/diff.gif";s:4:"3ba9";s:22:"res/images/garbage.gif";s:4:"5d02";s:19:"res/images/join.gif";s:4:"86ea";s:25:"res/images/joinBottom.gif";s:4:"3822";s:19:"res/images/line.gif";s:4:"d3d7";s:19:"res/images/mail.gif";s:4:"aa1c";s:22:"res/images/recover.gif";s:4:"ee1a";s:24:"res/images/treeMinus.gif";s:4:"dd7a";s:30:"res/images/treeMinusBottom.gif";s:4:"a1b6";s:23:"res/images/treePlus.gif";s:4:"86da";s:29:"res/images/treePlusBottom.gif";s:4:"6ac4";s:32:"res/phpCompat/array_diff_key.php";s:4:"acbb";s:37:"res/phpCompat/array_intersect_key.php";s:4:"8090";s:19:"res/zip/zip.lib.php";s:4:"46cd";}',
);

?>