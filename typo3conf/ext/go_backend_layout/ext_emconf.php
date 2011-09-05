<?php

########################################################################
# Extension Manager/Repository config file for ext: "go_backend_layout"
#
# Auto generated 08-09-2008 11:01
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Go Backend Layout',
	'description' => 'Styling Backend Contentelements + Flexforms automatic and some more Feature like Drag & Drop',
	'category' => 'be',
	'author' => 'Mansoor Ahmad',
	'author_email' => 'mansoor@gosign.de',
	'shy' => '',
	'dependencies' => 'templavoila',
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
	'author_company' => 'Gosign media. GmbH',
	'version' => '1.6.6',
	'constraints' => array(
		'depends' => array(
			'templavoila'			=>	'templavoila'
		),
		'conflicts' => array(
			't3skin_improved' 		=> 	't3skin_improved',
			'me_templavoilalayout'	=>	'me_templavoilalayout',
			'me_templavoilalayout2'	=>	'me_templavoilalayout2',
			'modern_skin'			=> 	'modern_skin',
			'tm_tvpagemodule' 		=> 	'tm_tvpagemodule'
		),
		'suggests' => array(
			'dam'					=>	'dam'
		),
	),
	'_md5_values_when_last_written' => 'a:5:{s:9:"ChangeLog";s:4:"89c1";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"1bdc";s:19:"doc/wizard_form.dat";s:4:"827b";s:20:"doc/wizard_form.html";s:4:"bbd4";}',
);

?>