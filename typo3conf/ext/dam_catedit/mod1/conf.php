<?php

	// DO NOT REMOVE OR CHANGE THESE 3 LINES:
define('TYPO3_MOD_PATH', '../typo3conf/ext/dam_catedit/mod1/');
$BACK_PATH='../../../../typo3/';


$MCONF['name']='txdamM1_txdamcateditM1';
$MCONF['access']='user,group';

$MCONF['script']='index.php';
#$MCONF['navFrameScriptParam']='&folderOnly=1';
$MCONF['navFrameScript']='tx_dam_catedit_navframe.php';

$MLANG['default']['tabs_images']['tab'] = 'moduleicon.gif';
$MLANG['default']['ll_ref']='LLL:EXT:dam_catedit/mod1/locallang_mod.xml';
?>