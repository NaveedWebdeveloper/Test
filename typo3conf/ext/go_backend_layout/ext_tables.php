<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=='BE')	{
	
	// Setting the relative path to the extension in temp. variable:
	$temp_eP = t3lib_extMgm::extRelPath($_EXTKEY);

	$TBE_STYLES['stylesheet2'] = $temp_eP.'go_backend_layout.css';	

	t3lib_div::loadTCA('be_users');
	$TCA['be_users']['columns']['lang']['config']['default'] = 'de';
	
	//t3lib_extMgm::addModule('user', 'gobeconfig', '', t3lib_extMgm::extPath($_EXTKEY) . 'moduls/config/');
}
?>