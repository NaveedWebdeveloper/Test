<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::addPItoST43($_EXTKEY,'class.go_pibase.php','','',1);

t3lib_extMgm::addPItoST43($_EXTKEY,'class.tx_go404handling.php','','',1);
?>