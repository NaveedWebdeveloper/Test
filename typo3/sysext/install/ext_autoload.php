<?php
/*
 * Register necessary class names with autoloader
 *
 * $Id: ext_autoload.php 9906 2010-12-27 11:54:35Z stephenking $
 */

$extPath = t3lib_extMgm::extPath('install');
return array(
	'tx_install_report_installstatus' => $extPath . 'report/class.tx_install_report_installstatus.php',
	'tx_install_updates_base' => $extPath . 'Classes/Updates/Base.php'
);

?>