<?php
$TYPO3_CONF_VARS['EXTCONF']['realurl'] = array(
	'_DEFAULT' => array(
		'init' => array(
			'enableCHashCache' => 1,
			'appendMissingSlash' => 'ifNotFile,redirect',
			'adminJumpToBackend' => 1,
			'enableUrlDecodeCache' => 1,
			'enableUrlEncodeCache' => 1,
			'emptyUrlReturnValue' => '/',
			'disableErrorLog' => 1,
		),
		'pagePath' => array(
			'rootpage_id' => 3,
			'type' => 'user',
			'userFunc' => 'EXT:realurl/class.tx_realurl_advanced.php:&tx_realurl_advanced->main',
			'spaceCharacter' => '-',
			'languageGetVar' => 'L',
		),
		'fileName' => array(
			'defaultToHTMLsuffixOnPrev' => 1,
			'acceptHTMLsuffix' => 1,
		),
		'preVars' => array (
			0 => array (
				'GETvar' => 'L',
				'valueMap' => array (
					'de' => '0',
					'en' => '1',
					'es' => '2',
					'fr' => '3',
				),
				'valueDefault' => 'de',
				//'noMatch' => 'bypass',
			),
		),
	),
);
?>