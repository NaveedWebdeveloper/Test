<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

require_once(PATH_site.'typo3conf/ext/go_language/lib/user_languageRedirect.php');

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_golanguage_pi1.php', '_pi1', '', 0);

#$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tsparser.php']['preParseFunc']['getSysLanguageUid'] = 'EXT:go_language/pi1/class.tx_golanguage_pi1.php:&tx_golanguage_pi1->getSysLanguageUid';
#$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tsparser.php']['preParseFunc']['getLanguageT3'] = 'EXT:go_language/pi1/class.tx_golanguage_pi1.php:&tx_golanguage_pi1->getLanguageT3';
#$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tsparser.php']['preParseFunc']['getLanguageIso'] = 'EXT:go_language/pi1/class.tx_golanguage_pi1.php:&tx_golanguage_pi1->getLanguageIso';
#$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tsparser.php']['preParseFunc']['getLocaleAll'] = 'EXT:go_language/pi1/class.tx_golanguage_pi1.php:&tx_golanguage_pi1->getLocaleAll';
#$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tsparser.php']['preParseFunc']['getLanguageTitle'] = 'EXT:go_language/pi1/class.tx_golanguage_pi1.php:&tx_golanguage_pi1->getLanguageTitle';

?>