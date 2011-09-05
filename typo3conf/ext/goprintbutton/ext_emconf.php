<?php

########################################################################
# Extension Manager/Repository config file for ext: "goprintbutton"
#
# Auto generated 03-04-2008 17:13
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'goPrintButton',
	'description' => 'Druckansicht und Buttons',
	'category' => 'fe',
	'author' => 'Marius Stuebs',
	'author_email' => 'marius@gosign.de',
	'shy' => '',
	'dependencies' => 'tx_pdfgenerator, tv_pdfgen',
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
	'version' => '1.0.0',
	'constraints' => array(
		'depends' => array("tx_pdfgenerator", "tv_pdfgen"
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:9:{s:9:"ChangeLog";s:4:"82bc";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"1dd5";s:14:"ext_tables.php";s:4:"b718";s:16:"print_button.png";s:4:"58f3";s:19:"doc/wizard_form.dat";s:4:"4c21";s:20:"doc/wizard_form.html";s:4:"15c4";s:39:"static/_ext_goPrintButton/constants.txt";s:4:"d41d";s:35:"static/_ext_goPrintButton/setup.txt";s:4:"19a3";}',
	'suggests' => array("fileadmin/templates/images/pdf_button.png", "fileadmin/templates/images/print_button.png"
	),
);

?>