<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$tempColumns = array (
	'tx_gokeyvisual_image' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:go_keyvisual/locallang_db.xml:pages.tx_gokeyvisual_image',
		'config' => array (
			'type' => 'group',
			'internal_type' => 'file',
			'allowed' => 'gif,png,jpeg,jpg',
			'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
			'uploadfolder' => 'uploads/tx_gokeyvisual',
			'show_thumbs' => 1,
			'size' => 1,
			'minitems' => 0,
			'maxitems' => 1,
		)
	),
	'tx_gokeyvisual_imagelink' => Array (
		'label' => 'LLL:EXT:go_keyvisual/locallang_db.xml:pages.tx_gokeyvisual_imagelink',
		'exclude' => 1,
		'config' => Array (
			'type' => 'input',
			'size' => '15',
			'max' => '256',
			'checkbox' => '',
			'eval' => 'trim',
			'wizards' => Array(
				'_PADDING' => 2,
				'link' => Array(
					'type' => 'popup',
					'title' => 'Link',
					'icon' => 'link_popup.gif',
					'script' => 'browse_links.php?mode=wizard',
					'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
				)
			),
			'softref' => 'typolink'
		)
	),
	'tx_gokeyvisual_flash' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:go_keyvisual/locallang_db.xml:pages.tx_gokeyvisual_flash',
		'config' => array (
			'type' => 'group',
			'internal_type' => 'file',
			'allowed' => 'swf',
			'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
			'uploadfolder' => 'uploads/tx_gokeyvisual',
			'size' => 1,
			'minitems' => 0,
			'maxitems' => 1,
		)
	),
);

// If DAM is installed, use DAM fields...
if (t3lib_extMgm::isLoaded('dam')) {
	$tempColumns['tx_gokeyvisual_image'] = txdam_getMediaTCA('image_field', 'tx_gokeyvisual_image');
	$tempColumns['tx_gokeyvisual_image']['exclude'] = 1;
	$tempColumns['tx_gokeyvisual_image']['label'] = 'LLL:EXT:go_keyvisual/locallang_db.xml:pages.tx_gokeyvisual_image';
	
	$tempColumns['tx_gokeyvisual_image']['config']['allowed_types'] = 'gif,png,jpeg,jpg';
	$tempColumns['tx_gokeyvisual_image']['config']['max_size'] = $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'];
	$tempColumns['tx_gokeyvisual_image']['config']['size'] = 1;
	$tempColumns['tx_gokeyvisual_image']['config']['maxitems'] = 1;
	$tempColumns['tx_gokeyvisual_image']['config']['minitems'] = 0;
	$tempColumns['tx_gokeyvisual_image']['config']['autoSizeMax'] = 1;
	
	$tempColumns['tx_gokeyvisual_flash'] = txdam_getMediaTCA('image_field', 'tx_gokeyvisual_flash');
	$tempColumns['tx_gokeyvisual_flash']['exclude'] = 1;
	$tempColumns['tx_gokeyvisual_flash']['label'] = 'LLL:EXT:go_keyvisual/locallang_db.xml:pages.tx_gokeyvisual_flash';
	
	$tempColumns['tx_gokeyvisual_flash']['config']['allowed_types'] = 'swf';
	$tempColumns['tx_gokeyvisual_flash']['config']['max_size'] = $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'];
	$tempColumns['tx_gokeyvisual_flash']['config']['show_thumbs'] = 0;
	$tempColumns['tx_gokeyvisual_flash']['config']['size'] = 1;
	$tempColumns['tx_gokeyvisual_flash']['config']['maxitems'] = 1;
	$tempColumns['tx_gokeyvisual_flash']['config']['minitems'] = 0;
	$tempColumns['tx_gokeyvisual_flash']['config']['autoSizeMax'] = 1;
}

t3lib_div::loadTCA('pages');
t3lib_extMgm::addTCAcolumns('pages',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('pages','tx_gokeyvisual_flash;;;;1-1-1, tx_gokeyvisual_image, tx_gokeyvisual_imagelink');

$tempColumns = array (
	'tx_gokeyvisual_image' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:go_keyvisual/locallang_db.xml:pages_language_overlay.tx_gokeyvisual_image',
		'config' => array (
			'type' => 'group',
			'internal_type' => 'file',
			'allowed' => 'gif,png,jpeg,jpg',
			'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
			'uploadfolder' => 'uploads/tx_gokeyvisual',
			'show_thumbs' => 1,
			'size' => 1,
			'minitems' => 0,
			'maxitems' => 1,
		)
	),
	'tx_gokeyvisual_imagelink' => Array (
		'label' => 'LLL:EXT:go_keyvisual/locallang_db.xml:pages_language_overlay.tx_gokeyvisual_imagelink',
		'exclude' => 1,
		'config' => Array (
			'type' => 'input',
			'size' => '15',
			'max' => '256',
			'checkbox' => '',
			'eval' => 'trim',
			'wizards' => Array(
				'_PADDING' => 2,
				'link' => Array(
					'type' => 'popup',
					'title' => 'Link',
					'icon' => 'link_popup.gif',
					'script' => 'browse_links.php?mode=wizard',
					'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
				)
			),
			'softref' => 'typolink'
		)
	),
	'tx_gokeyvisual_flash' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:go_keyvisual/locallang_db.xml:pages_language_overlay.tx_gokeyvisual_flash',
		'config' => array (
			'type' => 'group',
			'internal_type' => 'file',
			'allowed' => 'swf',
			'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
			'uploadfolder' => 'uploads/tx_gokeyvisual',
			'size' => 1,
			'minitems' => 0,
			'maxitems' => 1,
		)
	),
);

// If DAM is installed, use DAM fields...
if (t3lib_extMgm::isLoaded('dam')) {
	$tempColumns['tx_gokeyvisual_image'] = txdam_getMediaTCA('image_field', 'tx_gokeyvisual_image');
	$tempColumns['tx_gokeyvisual_image']['exclude'] = 1;
	$tempColumns['tx_gokeyvisual_image']['label'] = 'LLL:EXT:go_keyvisual/locallang_db.xml:pages_language_overlay.tx_gokeyvisual_image';
	
	$tempColumns['tx_gokeyvisual_image']['config']['allowed_types'] = 'gif,png,jpeg,jpg';
	$tempColumns['tx_gokeyvisual_image']['config']['max_size'] = $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'];
	$tempColumns['tx_gokeyvisual_image']['config']['size'] = 1;
	$tempColumns['tx_gokeyvisual_image']['config']['maxitems'] = 1;
	$tempColumns['tx_gokeyvisual_image']['config']['minitems'] = 0;
	$tempColumns['tx_gokeyvisual_image']['config']['autoSizeMax'] = 1;
	
	$tempColumns['tx_gokeyvisual_flash'] = txdam_getMediaTCA('image_field', 'tx_gokeyvisual_flash');
	$tempColumns['tx_gokeyvisual_flash']['exclude'] = 1;
	$tempColumns['tx_gokeyvisual_flash']['label'] = 'LLL:EXT:go_keyvisual/locallang_db.xml:pages_language_overlay.tx_gokeyvisual_flash';
	
	$tempColumns['tx_gokeyvisual_flash']['config']['allowed_types'] = 'swf';
	$tempColumns['tx_gokeyvisual_flash']['config']['max_size'] = $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'];
	$tempColumns['tx_gokeyvisual_flash']['config']['show_thumbs'] = 0;
	$tempColumns['tx_gokeyvisual_flash']['config']['size'] = 1;
	$tempColumns['tx_gokeyvisual_flash']['config']['maxitems'] = 1;
	$tempColumns['tx_gokeyvisual_flash']['config']['minitems'] = 0;
	$tempColumns['tx_gokeyvisual_flash']['config']['autoSizeMax'] = 1;
}

t3lib_div::loadTCA('pages_language_overlay');
t3lib_extMgm::addTCAcolumns('pages_language_overlay',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('pages_language_overlay','tx_gokeyvisual_flash;;;;1-1-1, tx_gokeyvisual_image, tx_gokeyvisual_imagelink');

?>