<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


$tempColumns = array(
	'tx_damttcontent_files' => txdam_getMediaTCA('image_field', 'tx_damttcontent_files')
);

t3lib_div::loadTCA('tt_content');
t3lib_extMgm::addTCAcolumns('tt_content',$tempColumns,1);


$tempSetup = $GLOBALS['T3_VAR']['ext']['dam_ttcontent']['setup'];

// CTypes "text w/image" and "image"

// for TYPO3 < 4.5
if (($tempSetup['ctypes_textpic_image_add_ref']) && (t3lib_div::int_from_ver(TYPO3_version) < 4005000)) {

	if ($tempSetup['ctypes_textpic_image_add_orig_field']) {
		t3lib_extMgm::addToAllTCAtypes('tt_content','tx_damttcontent_files','image','after:image');
		t3lib_extMgm::addToAllTCAtypes('tt_content','tx_damttcontent_files','textpic','after:image');
	} else {
		$TCA['tt_content']['types']['image']['showitem'] = str_replace(', image;', ', tx_damttcontent_files;', $TCA['tt_content']['types']['image']['showitem']);
		$TCA['tt_content']['types']['textpic']['showitem'] = str_replace(', image;', ', tx_damttcontent_files;', $TCA['tt_content']['types']['textpic']['showitem']);
	}

}

// for TYPO3 >= 4.5
if (($tempSetup['ctypes_textpic_image_add_ref']) && (t3lib_div::int_from_ver(TYPO3_version) >= 4005000)) {

	if ($tempSetup['ctypes_textpic_image_add_orig_field']) {
		t3lib_extMgm::addToAllTCAtypes('tt_content','tx_damttcontent_files','image','after:image');
		t3lib_extMgm::addToAllTCAtypes('tt_content','tx_damttcontent_files','textpic','after:image');
	} else {
		t3lib_extMgm::addToAllTCAtypes('tt_content','tx_damttcontent_files','textpic','replace:image');
	}

}

if ($GLOBALS['T3_VAR']['ext']['dam_ttcontent']['setup']['add_css_styled_hook']) {
	
	t3lib_extMgm::addStaticFile($_EXTKEY,'pi_cssstyledcontent/static/','DAM: CSS Styled Content');
	
	$TCA['tt_content']['columns']['imagecaption_position']['config']['items'] = array (
				array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', ''),
				array('LLL:EXT:cms/locallang_ttc.xml:imagecaption_position.I.1', 'center'),
				array('LLL:EXT:cms/locallang_ttc.xml:imagecaption_position.I.2', 'right'),
				array('LLL:EXT:cms/locallang_ttc.xml:imagecaption_position.I.3', 'left'),
				array('LLL:EXT:lang/locallang_core.xml:labels.hidden', 'hidden'),
			);	
			
	$TCA['tt_content']['palettes']['5'] = array('showitem' => 'imagecaption_position', 'canNotCollapse' => '1');

}

?>