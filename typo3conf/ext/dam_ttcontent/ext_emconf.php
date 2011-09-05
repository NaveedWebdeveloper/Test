<?php

########################################################################
# Extension Manager/Repository config file for ext "dam_ttcontent".
#
# Auto generated 16-02-2011 09:05
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'DAM for Content Elements',
	'description' => 'Enhance some of the default content elements to make use of DAM functionality. Eg. modify the content types "Image" and "Text/Image" for usage of the DAM.',
	'category' => 'fe',
	'shy' => 0,
	'version' => '1.2.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => 'tt_content',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'The DAM development team',
	'author_email' => 'typo3-project-dam@lists.netfielders.de',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'dam' => '1.2.0',
			'php' => '4.0.0-0.0.0',
			'typo3' => '4.5.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:16:{s:9:"ChangeLog";s:4:"b1ab";s:20:"class.ext_update.php";s:4:"738e";s:43:"class.tx_damttcontent_workspacediffview.php";s:4:"570f";s:26:"class.ux_tx_cms_layout.php";s:4:"bf2a";s:27:"class.ux_tx_version_cm1.php";s:4:"31f2";s:22:"class.ux_wslib_gui.php";s:4:"89b4";s:21:"ext_conf_template.txt";s:4:"308d";s:12:"ext_icon.gif";s:4:"999b";s:17:"ext_localconf.php";s:4:"ca09";s:14:"ext_tables.php";s:4:"296e";s:14:"ext_tables.sql";s:4:"aa4c";s:14:"doc/manual.sxw";s:4:"1ac6";s:51:"hooks/class.tx_damttcontent_tt_content_drawItem.php";s:4:"ce00";s:49:"pi_cssstyledcontent/class.tx_damttcontent_pi1.php";s:4:"8f54";s:40:"pi_cssstyledcontent/static/constants.txt";s:4:"6418";s:36:"pi_cssstyledcontent/static/setup.txt";s:4:"0298";}',
);

?>