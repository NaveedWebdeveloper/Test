<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_queospeedup_pi1.php', '_pi1', 'list_type', 1);

//$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] = 'EXT:queo_speedup/user_clearcache_hook.php:user_clearcache_hook->clearCache';
?>