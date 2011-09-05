<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

/**
  *  Enable hook after saving/altering a DAM category
  */

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:dam_catedit/class.tx_damcatedit_hooks.php:&tx_damcatedit_hooks';

?>
