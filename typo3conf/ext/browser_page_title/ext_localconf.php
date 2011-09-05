<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::addPItoST43($_EXTKEY,'class.tx_browserpagetitle.php','','includeLib',1);

	// $TYPO3_CONF_VARS['FE']['pageOverlayFields'] is created ONLY if it doesn't already exists (eg defined in typo3conf/localconf.php)
if (!$TYPO3_CONF_VARS['FE']['pageOverlayFields'])
	$TYPO3_CONF_VARS['FE']['pageOverlayFields'] = 'uid,title,subtitle,nav_title,media,keywords,description,abstract,author,author_email,tx_browserpagetitle_browser_title';
else	// Already exists => just adding the new field
	$TYPO3_CONF_VARS['FE']['pageOverlayFields'] .= ',tx_browserpagetitle_browser_title';

?>