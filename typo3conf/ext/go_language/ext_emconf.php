<?php

########################################################################
# Extension Manager/Repository config file for ext: "go_language"
#
# Auto generated 31-07-2009 10:32
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Gosign Language Menu',
	'description' => 'This extension creates an language menu based on the table sys_language.
It also checks the default browser-langauges and switch the page language to the browser language',
	'category' => 'fe',
	'author' => 'Caspar Stuebs',
	'author_email' => 'caspar@gosign.de',
	'shy' => '',
	'dependencies' => 'static_info_tables',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.3.0',
	'constraints' => array(
		'depends' => array(
			'php' => '5.0.0-0.0.0',
			'typo3' => '4.2.0-0.0.0',
			'static_info_tables' => '2.0.10-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);

?>