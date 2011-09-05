<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=='BE')	{

	t3lib_extMgm::addModule('txdamM1','txdamcateditM1','before:tools',t3lib_extMgm::extPath($_EXTKEY).'mod1/');


	// Command module

	t3lib_extMgm::addModule('web','txdamcateditCmd','',t3lib_extMgm::extPath($_EXTKEY).'mod_cmd/');

	t3lib_extMgm::insertModuleFunction(
		'web_txdamcateditCmd',
		'tx_damcatedit_cmd_nothing',
		t3lib_extMgm::extPath($_EXTKEY).'mod_cmd/class.tx_damcatedit_cmd_nothing.php',
		'LLL:EXT:dam_catedit/mod_cmd/locallang.xml:tx_damcatedit_cmd_nothing.title'
	);

	t3lib_extMgm::insertModuleFunction(
		'web_txdamcateditCmd',
		'tx_damcatedit_cmd_new',
		t3lib_extMgm::extPath($_EXTKEY).'mod_cmd/class.tx_damcatedit_cmd_new.php',
		'LLL:EXT:dam_catedit/mod_cmd/locallang.xml:tx_damcatedit_cmd_new.title'
	);

		// adds the clickMenu output that is specific for tx_dam_cat
	$GLOBALS['TBE_MODULES_EXT']['xMOD_alt_clickmenu']['extendCMclasses'][] = array(
		'name' => 'tx_damcatedit_cm',
		'path' => t3lib_extMgm::extPath($_EXTKEY).'lib/class.tx_damcatedit_cm.php'
    );
	
	
}
?>