<?php

$GLOBALS['T3_VAR']['ext'][$_EXTKEY]['setup'] = unserialize($_EXTCONF);


if ($GLOBALS['T3_VAR']['ext'][$_EXTKEY]['setup']['ctypes_textpic_image_add_ref']) {

	t3lib_extMgm::addTypoScript(
		$_EXTKEY,
		'setup','
		includeLibs.tx_damttcontent = EXT:dam/lib/class.tx_dam_tsfe.php

		temp.tx_dam.fileList < tt_content.image.20.imgList

		tt_content.image.20.imgList >
		tt_content.image.20.imgList.cObject = USER
		tt_content.image.20.imgList.cObject {
			userFunc = tx_dam_tsfe->fetchFileList

			refField = tx_damttcontent_files
			refTable = tt_content

			additional.fileList < temp.tx_dam.fileList
			additional.filePath < tt_content.image.20.imgPath
			'.($GLOBALS['T3_VAR']['ext'][$_EXTKEY]['setup']['ctype_image_add_orig_field']?'':'additional >').'
		}
		tt_content.image.20.imgPath >
		tt_content.image.20.imgPath =


		temp.tx_dam.fileList < tt_content.textpic.20.imgList

		tt_content.textpic.20.imgList >
		tt_content.textpic.20.imgList.cObject = USER
		tt_content.textpic.20.imgList.cObject {
			userFunc = tx_dam_tsfe->fetchFileList

			refField = tx_damttcontent_files
			refTable = tt_content

			additional.fileList < temp.tx_dam.fileList
			additional.filePath < tt_content.textpic.20.imgPath
			'.($GLOBALS['T3_VAR']['ext'][$_EXTKEY]['setup']['ctype_textpic_add_orig_field']?'':'additional >').'
		}
		tt_content.textpic.20.imgPath >
		tt_content.textpic.20.imgPath =

		',
		43
	);
}


$PATH_damttcontent = t3lib_extMgm::extPath('dam_ttcontent');

if ($GLOBALS['T3_VAR']['ext'][$_EXTKEY]['setup']['add_css_styled_hook']) {
	$TYPO3_CONF_VARS['EXTCONF']['css_styled_content']['pi1_hooks']['render_textpic'] = 'EXT:dam_ttcontent/pi_cssstyledcontent/class.tx_damttcontent_pi1.php:&tx_damttcontent_pi1';
}


$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem'][$_EXTKEY] = $PATH_damttcontent . 'hooks/class.tx_damttcontent_tt_content_drawItem.php:tx_damttcontent_tt_content_drawItem';

if ($GLOBALS['T3_VAR']['ext'][$_EXTKEY]['setup']['add_ws_mod_xclass']) {

		// yes, there's double code in the core!
	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['typo3/mod/user/ws/class.wslib_gui.php'] = $PATH_damttcontent . 'class.ux_wslib_gui.php';
	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/version/cm1/index.php']             = $PATH_damttcontent . 'class.ux_tx_version_cm1.php';

		// TODO when hook is added to TYPO3 core, add a version check
	$TYPO3_CONF_VARS['SC_OPTIONS']['typo3/mod/user/ws/class.wslib_gui.php']['postProcessDiffView'][] = 'EXT:dam_ttcontent/class.tx_damttcontent_workspacediffview.php:&tx_damttcontent_workspaceDiffView';
}

?>