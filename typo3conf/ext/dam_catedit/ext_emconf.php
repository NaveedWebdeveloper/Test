<?php

########################################################################
# Extension Manager/Repository config file for ext "dam_catedit".
#
# Auto generated 16-02-2011 09:05
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Media>Categories',
	'description' => 'Module for editing the DAM categories.',
	'category' => 'module',
	'shy' => 0,
	'version' => '1.2.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod1,mod_cmd,mod_clickmenu',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'The DAM development team',
	'author_email' => 'typo3-project-dam@lists.netfielders.de',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'dam' => '',
			'php' => '4.0.0-0.0.0',
			'typo3' => '4.5.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:27:{s:9:"ChangeLog";s:4:"c8cb";s:25:"class.tx_dam_db_list2.php";s:4:"fd1e";s:29:"class.tx_damcatedit_hooks.php";s:4:"e2b8";s:35:"class.tx_damcatedit_positionmap.php";s:4:"040d";s:12:"ext_icon.gif";s:4:"eb28";s:17:"ext_localconf.php";s:4:"42e8";s:14:"ext_tables.php";s:4:"8ced";s:16:"locallang_cm.xml";s:4:"6be2";s:14:"doc/manual.sxw";s:4:"9b57";s:30:"lib/class.tx_damcatedit_cm.php";s:4:"8f66";s:30:"lib/class.tx_damcatedit_db.php";s:4:"fc99";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"39bb";s:14:"mod1/index.php";s:4:"cbba";s:18:"mod1/locallang.xml";s:4:"7fea";s:22:"mod1/locallang_mod.xml";s:4:"7790";s:22:"mod1/mod_template.html";s:4:"6374";s:27:"mod1/mod_template_tree.html";s:4:"7ccb";s:19:"mod1/moduleicon.gif";s:4:"b27b";s:32:"mod1/tx_dam_catedit_navframe.php";s:4:"703c";s:39:"mod_cmd/class.tx_damcatedit_cmd_new.php";s:4:"fdcf";s:43:"mod_cmd/class.tx_damcatedit_cmd_nothing.php";s:4:"edc1";s:16:"mod_cmd/conf.php";s:4:"2a10";s:17:"mod_cmd/index.php";s:4:"5c77";s:21:"mod_cmd/locallang.xml";s:4:"537f";s:25:"mod_cmd/locallang_mod.xml";s:4:"0136";s:22:"mod_cmd/moduleicon.gif";s:4:"adc5";}',
);

?>