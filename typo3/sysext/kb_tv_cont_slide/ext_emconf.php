<?php

########################################################################
# Extension Manager/Repository config file for ext "kb_tv_cont_slide".
#
# Auto generated 16-02-2011 09:26
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'KB TV Content Slide',
	'description' => 'This extension lets you insert the content of a TV Column field into a TV TS-Object path field. But if no content is found in the column on the actual page the content of the parent page is taken. Other description: The content of the root-page is inherited to its child pages',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '0.4.3',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Bernhard Kraft',
	'author_email' => 'kraftb@kraftb.at',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '4.0.0-0.0.0',
			'typo3' => '4.0.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:5:{s:9:"ChangeLog";s:4:"3b6b";s:12:"ext_icon.gif";s:4:"5fe8";s:17:"ext_localconf.php";s:4:"acc3";s:14:"doc/manual.sxw";s:4:"8a69";s:34:"pi1/class.tx_kbtvcontslide_pi1.php";s:4:"0113";}',
);

?>