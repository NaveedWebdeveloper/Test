<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
t3lib_extMgm::addPItoST43($_EXTKEY,'piTeaser/class.tx_goteaser_piTeaser.php','_piTeaser','CType',1);
t3lib_extMgm::addPItoST43($_EXTKEY,'piMyElement/class.tx_goteaser_piMyElement.php','_piMyElement','CType',1);
?>