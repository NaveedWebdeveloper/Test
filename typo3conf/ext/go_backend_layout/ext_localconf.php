<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

//# Hook in "typo3/class.db_list_extra.inc"
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/class.db_list_extra.inc']['getTable'][] = 'EXT:go_backend_layout/moduls/tt_news/hook.tt_news.php:user_go_backend_layout_modify';

/*
*	@modul		templavoila
*	@version	1.3.7
*/

// #
// ### Backend Listview Styling
// #
$TYPO3_CONF_VARS['BE']['XCLASS']['ext/templavoila/mod1/index.php'] = t3lib_extMgm::extPath('go_backend_layout').'moduls/templavoila/class.ux_tx_templavoila_module1.php';

// #
// ### Wizardlist of Pages
// #
$TYPO3_CONF_VARS['BE']['XCLASS']['ext/templavoila/mod1/class.tx_templavoila_mod1_wizards.php'] = t3lib_extMgm::extPath('go_backend_layout').'moduls/templavoila/class.ux_tx_templavoila_mod1_wizards.php';

// #
// ### Wizardlist of Contentelements
// #
$TYPO3_CONF_VARS['BE']['XCLASS']['ext/templavoila/mod1/db_new_content_el.php'] = t3lib_extMgm::extPath('go_backend_layout').'moduls/templavoila/ux_db_new_content_el.php';





?>