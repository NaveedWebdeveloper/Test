<?php

###########################
## EXTENSION: cms
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/cms/ext_tables.php
###########################

$_EXTKEY = 'cms';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


# TYPO3 SVN ID: $Id: ext_tables.php 10211 2011-01-21 14:55:53Z flyguide $
if (!defined ('TYPO3_MODE'))	die ('Access denied.');


if (TYPO3_MODE == 'BE') {
	t3lib_extMgm::addModule('web','layout','top',t3lib_extMgm::extPath($_EXTKEY).'layout/');
	t3lib_extMgm::addLLrefForTCAdescr('_MOD_web_layout','EXT:cms/locallang_csh_weblayout.xml');
	t3lib_extMgm::addLLrefForTCAdescr('_MOD_web_info','EXT:cms/locallang_csh_webinfo.xml');

	t3lib_extMgm::insertModuleFunction(
		'web_info',
		'tx_cms_webinfo_page',
		t3lib_extMgm::extPath($_EXTKEY).'web_info/class.tx_cms_webinfo.php',
		'LLL:EXT:cms/locallang_tca.xml:mod_tx_cms_webinfo_page'
	);
	t3lib_extMgm::insertModuleFunction(
		'web_info',
		'tx_cms_webinfo_lang',
		t3lib_extMgm::extPath($_EXTKEY).'web_info/class.tx_cms_webinfo_lang.php',
		'LLL:EXT:cms/locallang_tca.xml:mod_tx_cms_webinfo_lang'
	);
}


	// Add allowed records to pages:
t3lib_extMgm::allowTableOnStandardPages('pages_language_overlay,tt_content,sys_template,sys_domain,backend_layout');


// ******************************************************************
// This is the standard TypoScript content table, tt_content
// ******************************************************************
$TCA['tt_content'] = array (
	'ctrl' => array (
		'label' => 'header',
		'label_alt' => 'subheader,bodytext',
		'sortby' => 'sorting',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'title' => 'LLL:EXT:cms/locallang_tca.xml:tt_content',
		'delete' => 'deleted',
		'versioningWS' => 2,
		'versioning_followPages' => true,
		'origUid' => 't3_origuid',
		'type' => 'CType',
		'hideAtCopy' => true,
		'prependAtCopy' => 'LLL:EXT:lang/locallang_general.xml:LGL.prependAtCopy',
		'copyAfterDuplFields' => 'colPos,sys_language_uid',
		'useColumnsForDefaultValues' => 'colPos,sys_language_uid',
		'shadowColumnsForNewPlaceholders' => 'colPos',
		'transOrigPointerField' => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		'languageField' => 'sys_language_uid',
		'enablecolumns' => array (
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
		),
		'typeicon_column' => 'CType',
		'typeicon_classes' => array(
			'header' => 'mimetypes-x-content-header',
			'textpic' => 'mimetypes-x-content-text-picture',
			'image' => 'mimetypes-x-content-image',
			'bullets' => 'mimetypes-x-content-list-bullets',
			'table' => 'mimetypes-x-content-table',
			'splash' => 'mimetypes-x-content-splash',
			'uploads' => 'mimetypes-x-content-list-files',
			'multimedia' => 'mimetypes-x-content-multimedia',
			'media' => 'mimetypes-x-content-multimedia',
			'menu' => 'mimetypes-x-content-menu',
			'list' => 'mimetypes-x-content-plugin',
			'mailform' => 'mimetypes-x-content-form',
			'search' => 'mimetypes-x-content-form-search',
			'login' => 'mimetypes-x-content-login',
			'shortcut' => 'mimetypes-x-content-link',
			'script' => 'mimetypes-x-content-script',
			'div' => 'mimetypes-x-content-divider',
			'html' => 'mimetypes-x-content-html',
			'text' => 'mimetypes-x-content-text',
			'default' => 'mimetypes-x-content-text',
		),
		'typeicons' => array (
			'header' => 'tt_content_header.gif',
			'textpic' => 'tt_content_textpic.gif',
			'image' => 'tt_content_image.gif',
			'bullets' => 'tt_content_bullets.gif',
			'table' => 'tt_content_table.gif',
			'splash' => 'tt_content_news.gif',
			'uploads' => 'tt_content_uploads.gif',
			'multimedia' => 'tt_content_mm.gif',
			'media' => 'tt_content_mm.gif',
			'menu' => 'tt_content_menu.gif',
			'list' => 'tt_content_list.gif',
			'mailform' => 'tt_content_form.gif',
			'search' => 'tt_content_search.gif',
			'login' => 'tt_content_login.gif',
			'shortcut' => 'tt_content_shortcut.gif',
			'script' => 'tt_content_script.gif',
			'div' => 'tt_content_div.gif',
			'html' => 'tt_content_html.gif'
		),
		'thumbnail' => 'image',
		'requestUpdate' => 'list_type,rte_enabled',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tbl_tt_content.php',
		'dividers2tabs' => 1
	)
);

// ******************************************************************
// fe_users
// ******************************************************************
$TCA['fe_users'] = array (
	'ctrl' => array (
		'label' => 'username',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'fe_cruser_id' => 'fe_cruser_id',
		'title' => 'LLL:EXT:cms/locallang_tca.xml:fe_users',
		'delete' => 'deleted',
		'enablecolumns' => array (
			'disabled' => 'disable',
			'starttime' => 'starttime',
			'endtime' => 'endtime'
		),
		'typeicon_classes' => array(
			'default' => 'status-user-frontend',
		),
		'useColumnsForDefaultValues' => 'usergroup,lockToDomain,disable,starttime,endtime',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tbl_cms.php',
		'dividers2tabs' => 1
	),
	'feInterface' => array (
		'fe_admin_fieldList' => 'username,password,usergroup,name,address,telephone,fax,email,title,zip,city,country,www,company',
	)
);

// ******************************************************************
// fe_groups
// ******************************************************************
$TCA['fe_groups'] = array (
	'ctrl' => array (
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'delete' => 'deleted',
		'prependAtCopy' => 'LLL:EXT:lang/locallang_general.xml:LGL.prependAtCopy',
		'enablecolumns' => array (
			'disabled' => 'hidden'
		),
		'title' => 'LLL:EXT:cms/locallang_tca.xml:fe_groups',
		'typeicon_classes' => array(
			'default' => 'status-user-group-frontend',
		),
		'useColumnsForDefaultValues' => 'lockToDomain',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tbl_cms.php',
		'dividers2tabs' => 1
	)
);

// ******************************************************************
// sys_domain
// ******************************************************************
$TCA['sys_domain'] = array (
	'ctrl' => array (
		'label' => 'domainName',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'title' => 'LLL:EXT:cms/locallang_tca.xml:sys_domain',
		'iconfile' => 'domain.gif',
		'enablecolumns' => array (
			'disabled' => 'hidden'
		),
		'typeicon_classes' => array(
			'default' => 'mimetypes-x-content-domain',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tbl_cms.php'
	)
);

// ******************************************************************
// pages_language_overlay
// ******************************************************************
$TCA['pages_language_overlay'] = array (
	'ctrl' => array (
		'label'                           => 'title',
		'tstamp'                          => 'tstamp',
		'title'                           => 'LLL:EXT:cms/locallang_tca.xml:pages_language_overlay',
		'versioningWS'                    => true,
		'versioning_followPages'          => true,
		'origUid'                         => 't3_origuid',
		'crdate'                          => 'crdate',
		'cruser_id'                       => 'cruser_id',
		'delete'                          => 'deleted',
		'enablecolumns'                   => array (
			'disabled'  => 'hidden',
			'starttime' => 'starttime',
			'endtime'   => 'endtime'
		),
		'transOrigPointerField'           => 'pid',
		'transOrigPointerTable'           => 'pages',
		'transOrigDiffSourceField'        => 'l18n_diffsource',
		'shadowColumnsForNewPlaceholders' => 'title',
		'languageField'                   => 'sys_language_uid',
		'mainpalette'                     => 1,
		'dynamicConfigFile'               => t3lib_extMgm::extPath($_EXTKEY) . 'tbl_cms.php',
		'type'                            => 'doktype',
		'typeicon_classes' => array(
			'default' => 'mimetypes-x-content-page-language-overlay',
		),

		'dividers2tabs'                   => true
	)
);


// ******************************************************************
// sys_template
// ******************************************************************
$TCA['sys_template'] = array (
	'ctrl' => array (
		'label' => 'title',
		'tstamp' => 'tstamp',
		'sortby' => 'sorting',
		'prependAtCopy' => 'LLL:EXT:lang/locallang_general.xml:LGL.prependAtCopy',
		'title' => 'LLL:EXT:cms/locallang_tca.xml:sys_template',
		'versioningWS' => true,
		'origUid' => 't3_origuid',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'delete' => 'deleted',
		'adminOnly' => 1,	// Only admin, if any
		'iconfile' => 'template.gif',
		'thumbnail' => 'resources',
		'enablecolumns' => array (
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime'
		),
		'typeicon_column' => 'root',
		'typeicon_classes' => array(
			'default' => 'mimetypes-x-content-template-extension',
			'1' => 'mimetypes-x-content-template',
		),
		'typeicons' => array (
			'0' => 'template_add.gif'
		),
		'dividers2tabs' => 1,
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tbl_cms.php'
	)
);


// ******************************************************************
// layouts
// ******************************************************************
$TCA['backend_layout'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:cms/locallang_tca.xml:backend_layout',
		'label'     => 'title',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'versioningWS' => TRUE,
		'origUid' => 't3_origuid',
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'enablecolumns' => array (
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tbl_cms.php',
		'iconfile' => 'backend_layout.gif',
		'selicon_field' => 'icon',
		'selicon_field_path' => 'uploads/media',
		'thumbnail' => 'resources',
	)
);


###########################
## EXTENSION: sv
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/sv/ext_tables.php
###########################

$_EXTKEY = 'sv';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE == 'BE') {
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['sv']['services'] = array(
		'title'       => 'LLL:EXT:sv/reports/locallang.xml:report_title',
		'description' => 'LLL:EXT:sv/reports/locallang.xml:report_description',
		'icon'		  => 'EXT:sv/reports/tx_sv_report.png',
		'report'      => 'tx_sv_reports_ServicesList'
	);
}

###########################
## EXTENSION: em
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/em/ext_tables.php
###########################

$_EXTKEY = 'em';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE === 'BE') {
	t3lib_extMgm::addModule('tools', 'em', '', t3lib_extMgm::extPath($_EXTKEY) . 'classes/');
		// register Ext.Direct
	$TYPO3_CONF_VARS['SC_OPTIONS']['ExtDirect']['TYPO3.EM.ExtDirect'] = t3lib_extMgm::extPath($_EXTKEY) . 'classes/connection/class.tx_em_connection_extdirectserver.php:tx_em_Connection_ExtDirectServer';
	$TYPO3_CONF_VARS['SC_OPTIONS']['ExtDirect']['TYPO3.EMSOAP.ExtDirect'] = t3lib_extMgm::extPath($_EXTKEY) . 'classes/connection/class.tx_em_connection_extdirectsoap.php:tx_em_Connection_ExtDirectSoap';

		// register reports check
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['Extension Manager'][] = 'tx_em_reports_ExtensionStatus';

	$icons = array(
		'extension-required' => t3lib_extMgm::extRelPath('em') . 'res/icons/extension-required.png'
 	);
 	t3lib_SpriteManager::addSingleIcons($icons, 'em');
}

###########################
## EXTENSION: recordlist
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/recordlist/ext_tables.php
###########################

$_EXTKEY = 'recordlist';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE === 'BE') {
	t3lib_extMgm::addModulePath('web_list', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
	t3lib_extMgm::addModule('web', 'list', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
}

###########################
## EXTENSION: extbase
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/extbase/ext_tables.php
###########################

$_EXTKEY = 'extbase';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) die ('Access denied.');

if (TYPO3_MODE == 'BE') {

	// register the cache in BE so it will be cleared with "clear all caches"
	try {
		t3lib_cache::initializeCachingFramework();
			// Reflection cache
		$GLOBALS['typo3CacheFactory']->create(
			'tx_extbase_cache_reflection',
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_extbase_reflection']['frontend'],
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_extbase_reflection']['backend'],
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_extbase_reflection']['options']
		);
			// Object container cache
		$GLOBALS['typo3CacheFactory']->create(
			'tx_extbase_cache_object',
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_extbase_object']['frontend'],
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_extbase_object']['backend'],
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_extbase_object']['options']
		);
	} catch(t3lib_cache_exception_NoSuchCache $exception) {

	}

	$TBE_MODULES['_dispatcher'][] = 'Tx_Extbase_Core_Bootstrap';

}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['extbase'][] = 'tx_extbase_utility_extbaserequirementscheck';

t3lib_div::loadTCA('fe_users');
if (!isset($TCA['fe_groups']['ctrl']['type'])) {
	$tempColumns = array(
		'tx_extbase_type' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:extbase/Resources/Private/Language/locallang_db.xml:fe_users.tx_extbase_type',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('LLL:EXT:extbase/Resources/Private/Language/locallang_db.xml:fe_users.tx_extbase_type.0', '0'),
					array('LLL:EXT:extbase/Resources/Private/Language/locallang_db.xml:fe_users.tx_extbase_type.Tx_Extbase_Domain_Model_FrontendUser', 'Tx_Extbase_Domain_Model_FrontendUser')
				),
				'size' => 1,
				'maxitems' => 1,
				'default' => '0'
			)
		)
	);
	t3lib_extMgm::addTCAcolumns('fe_users', $tempColumns, 1);
	t3lib_extMgm::addToAllTCAtypes('fe_users', 'tx_extbase_type');
	$TCA['fe_users']['ctrl']['type'] = 'tx_extbase_type';
}
$TCA['fe_users']['types']['Tx_Extbase_Domain_Model_FrontendUser'] = $TCA['fe_users']['types']['0'];

t3lib_div::loadTCA('fe_groups');
if (!isset($TCA['fe_groups']['ctrl']['type'])) {
	$tempColumns = array(
		'tx_extbase_type' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:extbase/Resources/Private/Language/locallang_db.xml:fe_groups.tx_extbase_type',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('LLL:EXT:extbase/Resources/Private/Language/locallang_db.xml:fe_groups.tx_extbase_type.0', '0'),
					array('LLL:EXT:extbase/Resources/Private/Language/locallang_db.xml:fe_groups.tx_extbase_type.Tx_Extbase_Domain_Model_FrontendUserGroup', 'Tx_Extbase_Domain_Model_FrontendUserGroup')
				),
				'size' => 1,
				'maxitems' => 1,
				'default' => '0'
			)
		)
	);
	t3lib_extMgm::addTCAcolumns('fe_groups', $tempColumns, 1);
	t3lib_extMgm::addToAllTCAtypes('fe_groups', 'tx_extbase_type');
	$TCA['fe_groups']['ctrl']['type'] = 'tx_extbase_type';
}
$TCA['fe_groups']['types']['Tx_Extbase_Domain_Model_FrontendUserGroup'] = $TCA['fe_groups']['types']['0'];


###########################
## EXTENSION: css_styled_content
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/css_styled_content/ext_tables.php
###########################

$_EXTKEY = 'css_styled_content';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


# TYPO3 SVN ID: $Id: ext_tables.php 10449 2011-02-11 23:06:16Z lolli $
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

	// add flexform
t3lib_extMgm::addPiFlexFormValue('*', 'FILE:EXT:css_styled_content/flexform_ds.xml','table');
$TCA['tt_content']['types']['table']['showitem']='CType;;4;;1-1-1, hidden, header;;3;;2-2-2, linkToTop;;;;4-4-4,
			--div--;LLL:EXT:cms/locallang_ttc.xml:CType.I.5, layout;;10;;3-3-3, cols, bodytext;;9;nowrap:wizards[table], text_properties, pi_flexform,
			--div--;LLL:EXT:cms/locallang_tca.xml:pages.tabs.access, starttime, endtime, fe_group';

t3lib_extMgm::addStaticFile($_EXTKEY, 'static/', 'CSS Styled Content');
t3lib_extMgm::addStaticFile($_EXTKEY, 'static/v3.8/', 'CSS Styled Content TYPO3 v3.8');
t3lib_extMgm::addStaticFile($_EXTKEY, 'static/v3.9/', 'CSS Styled Content TYPO3 v3.9');
t3lib_extMgm::addStaticFile($_EXTKEY, 'static/v4.2/', 'CSS Styled Content TYPO3 v4.2');
t3lib_extMgm::addStaticFile($_EXTKEY, 'static/v4.3/', 'CSS Styled Content TYPO3 v4.3');
t3lib_extMgm::addStaticFile($_EXTKEY, 'static/v4.4/', 'CSS Styled Content TYPO3 v4.4');

$TCA['tt_content']['columns']['section_frame']['config']['items'][0] = array('LLL:EXT:css_styled_content/locallang_db.php:tt_content.tx_cssstyledcontent_section_frame.I.0', '0');
$TCA['tt_content']['columns']['section_frame']['config']['items'][9] = array('LLL:EXT:css_styled_content/locallang_db.php:tt_content.tx_cssstyledcontent_section_frame.I.9', '66');


###########################
## EXTENSION: tsconfig_help
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/tsconfig_help/ext_tables.php
###########################

$_EXTKEY = 'tsconfig_help';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE == 'BE')	{

	t3lib_extMgm::addModule('help','txtsconfighelpM1','',t3lib_extMgm::extPath($_EXTKEY).'mod1/');
}

###########################
## EXTENSION: context_help
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/context_help/ext_tables.php
###########################

$_EXTKEY = 'context_help';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::addLLrefForTCAdescr('fe_groups','EXT:context_help/locallang_csh_fe_groups.xml');
t3lib_extMgm::addLLrefForTCAdescr('fe_users','EXT:context_help/locallang_csh_fe_users.xml');
t3lib_extMgm::addLLrefForTCAdescr('pages','EXT:context_help/locallang_csh_pages.xml');
t3lib_extMgm::addLLrefForTCAdescr('pages_language_overlay','EXT:context_help/locallang_csh_pageslol.xml');
t3lib_extMgm::addLLrefForTCAdescr('static_template','EXT:context_help/locallang_csh_statictpl.xml');
t3lib_extMgm::addLLrefForTCAdescr('sys_domain','EXT:context_help/locallang_csh_sysdomain.xml');
t3lib_extMgm::addLLrefForTCAdescr('sys_template','EXT:context_help/locallang_csh_systmpl.xml');
t3lib_extMgm::addLLrefForTCAdescr('tt_content','EXT:context_help/locallang_csh_ttcontent.xml');

// Labels for TYPO3 4.5 and greater.  These labels override the ones set above, while still falling back to the original labels if no translation is available.
$GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']['EXT:context_help/locallang_csh_pages.xml'][] = 'EXT:context_help/4.5/locallang_csh_pages.xml';
$GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']['EXT:context_help/locallang_csh_ttcontent.xml'][] = 'EXT:context_help/4.5/locallang_csh_ttcontent.xml';


###########################
## EXTENSION: extra_page_cm_options
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/extra_page_cm_options/ext_tables.php
###########################

$_EXTKEY = 'extra_page_cm_options';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=='BE')	{
	$GLOBALS['TBE_MODULES_EXT']['xMOD_alt_clickmenu']['extendCMclasses'][]=array(
		'name' => 'tx_extrapagecmoptions',
		'path' => t3lib_extMgm::extPath($_EXTKEY).'class.tx_extrapagecmoptions.php'
	);
}

###########################
## EXTENSION: impexp
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/impexp/ext_tables.php
###########################

$_EXTKEY = 'impexp';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE == 'BE')	{
	$GLOBALS['TBE_MODULES_EXT']['xMOD_alt_clickmenu']['extendCMclasses'][] = array(
		'name' => 'tx_impexp_clickmenu',
		'path' => t3lib_extMgm::extPath($_EXTKEY).'class.tx_impexp_clickmenu.php'
	);


	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['taskcenter']['impexp']['tx_impexp_task'] = array(
		'title'       => 'LLL:EXT:impexp/locallang_csh.xml:.alttitle',
		'description' => 'LLL:EXT:impexp/locallang_csh.xml:.description',
		'icon'		  => 'EXT:impexp/export.gif'
	);

	t3lib_extMgm::addLLrefForTCAdescr('xMOD_tx_impexp','EXT:impexp/locallang_csh.xml');

	// CSH labels for TYPO3 4.5 and greater.  These labels override the ones set above, while still falling back to the original labels if no translation is available.
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']['EXT:impexp/locallang_csh.xml'][] = 'EXT:impexp/locallang_csh_45.xml';

		// special context menu actions for the import/export module
	$importExportActions = '
		9000 = DIVIDER

		9100 = ITEM
		9100 {
			name = exportT3d
			label = LLL:EXT:impexp/app/locallang.xml:export
			spriteIcon = actions-document-export-t3d
			callbackAction = exportT3d
		}

		9200 = ITEM
		9200 {
			name = importT3d
			label = LLL:EXT:impexp/app/locallang.xml:import
			spriteIcon = actions-document-import-t3d
			callbackAction = importT3d
		}
	';

		// context menu user default configuration
	$GLOBALS['TYPO3_CONF_VARS']['BE']['defaultUserTSconfig'] .= '
		options.contextMenu.table {
			pages_root.items {
				' . $importExportActions . '
			}

			pages.items.1000 {
				' . $importExportActions . '
			}
		}
	';
}

###########################
## EXTENSION: sys_note
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/sys_note/ext_tables.php
###########################

$_EXTKEY = 'sys_note';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=='BE')	{
	$TCA['sys_note'] = Array (
		'ctrl' => Array (
			'label' => 'subject',
			'default_sortby' => 'ORDER BY crdate',
			'tstamp' => 'tstamp',
			'crdate' => 'crdate',
			'cruser_id' => 'cruser',
			'prependAtCopy' => 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy',
			'delete' => 'deleted',
			'title' => 'LLL:EXT:sys_note/locallang_tca.php:sys_note',
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'ext_icon.gif',
		),
		'interface' => Array (
			'showRecordFieldList' => 'category,subject,message,author,email,personal'
		),
		'columns' => Array (
			'category' => Array (
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.category',
				'config' => Array (
					'type' => 'select',
					'items' => Array (
						Array('', '0'),
						Array('LLL:EXT:sys_note/locallang_tca.php:sys_note.category.I.1', '1'),
						Array('LLL:EXT:sys_note/locallang_tca.php:sys_note.category.I.2', '3'),
						Array('LLL:EXT:sys_note/locallang_tca.php:sys_note.category.I.3', '4'),
						Array('LLL:EXT:sys_note/locallang_tca.php:sys_note.category.I.4', '2')
					),
					'default' => '0'
				)
			),
			'subject' => Array (
				'label' => 'LLL:EXT:sys_note/locallang_tca.php:sys_note.subject',
				'config' => Array (
					'type' => 'input',
					'size' => '40',
					'max' => '256'
				)
			),
			'message' => Array (
				'label' => 'LLL:EXT:sys_note/locallang_tca.php:sys_note.message',
				'config' => Array (
					'type' => 'text',
					'cols' => '40',
					'rows' => '15'
				)
			),
			'author' => Array (
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.author',
				'config' => Array (
					'type' => 'input',
					'size' => '20',
					'eval' => 'trim',
					'max' => '80'
				)
			),
			'email' => Array (
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.email',
				'config' => Array (
					'type' => 'input',
					'size' => '20',
					'eval' => 'trim',
					'max' => '80'
				)
			),
			'personal' => Array (
				'label' => 'LLL:EXT:sys_note/locallang_tca.php:sys_note.personal',
				'config' => Array (
					'type' => 'check'
				)
			)
		),
		'types' => Array (
			'0' => Array('showitem' => 'category;;;;2-2-2, author, email, personal, subject;;;;3-3-3, message')
		)
	);

	t3lib_extMgm::allowTableOnStandardPages('sys_note');
}

t3lib_extMgm::addLLrefForTCAdescr('sys_note','EXT:sys_note/locallang_csh_sysnote.xml');

###########################
## EXTENSION: tstemplate
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/tstemplate/ext_tables.php
###########################

$_EXTKEY = 'tstemplate';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=='BE')	t3lib_extMgm::addModule('web','ts','',t3lib_extMgm::extPath($_EXTKEY).'ts/');

###########################
## EXTENSION: tstemplate_ceditor
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/tstemplate_ceditor/ext_tables.php
###########################

$_EXTKEY = 'tstemplate_ceditor';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=='BE')	{
	t3lib_extMgm::insertModuleFunction(
		'web_ts',
		'tx_tstemplateceditor',
		t3lib_extMgm::extPath($_EXTKEY).'class.tx_tstemplateceditor.php',
		'LLL:EXT:tstemplate/ts/locallang.xml:constantEditor'
	);
}

###########################
## EXTENSION: tstemplate_info
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/tstemplate_info/ext_tables.php
###########################

$_EXTKEY = 'tstemplate_info';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=='BE')	{
	t3lib_extMgm::insertModuleFunction(
		'web_ts',
		'tx_tstemplateinfo',
		t3lib_extMgm::extPath($_EXTKEY).'class.tx_tstemplateinfo.php',
		'LLL:EXT:tstemplate/ts/locallang.xml:infoModify'
	);
}

###########################
## EXTENSION: tstemplate_objbrowser
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/tstemplate_objbrowser/ext_tables.php
###########################

$_EXTKEY = 'tstemplate_objbrowser';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=='BE')	{
	t3lib_extMgm::insertModuleFunction(
		'web_ts',
		'tx_tstemplateobjbrowser',
		t3lib_extMgm::extPath($_EXTKEY).'class.tx_tstemplateobjbrowser.php',
		'LLL:EXT:tstemplate/ts/locallang.xml:objectBrowser'
	);
}

###########################
## EXTENSION: tstemplate_analyzer
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/tstemplate_analyzer/ext_tables.php
###########################

$_EXTKEY = 'tstemplate_analyzer';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=='BE')	{
	t3lib_extMgm::insertModuleFunction(
		'web_ts',
		'tx_tstemplateanalyzer',
		t3lib_extMgm::extPath($_EXTKEY).'class.tx_tstemplateanalyzer.php',
		'LLL:EXT:tstemplate/ts/locallang.xml:templateAnalyzer'
	);
}

###########################
## EXTENSION: func_wizards
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/func_wizards/ext_tables.php
###########################

$_EXTKEY = 'func_wizards';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=='BE')	{
	t3lib_extMgm::insertModuleFunction(
		'web_func',
		'tx_funcwizards_webfunc',
		t3lib_extMgm::extPath($_EXTKEY).'class.tx_funcwizards_webfunc.php',
		'LLL:EXT:func_wizards/locallang.php:mod_wizards'
	);
	t3lib_extMgm::addLLrefForTCAdescr('_MOD_web_func','EXT:func_wizards/locallang_csh.xml');
}

###########################
## EXTENSION: wizard_crpages
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/wizard_crpages/ext_tables.php
###########################

$_EXTKEY = 'wizard_crpages';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=='BE')	{
	t3lib_extMgm::insertModuleFunction(
		'web_func',
		'tx_wizardcrpages_webfunc_2',
		t3lib_extMgm::extPath($_EXTKEY).'class.tx_wizardcrpages_webfunc_2.php',
		'LLL:EXT:wizard_crpages/locallang.php:wiz_crMany',
		'wiz'
	);
	t3lib_extMgm::addLLrefForTCAdescr('_MOD_web_func','EXT:wizard_crpages/locallang_csh.xml');
}

###########################
## EXTENSION: wizard_sortpages
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/wizard_sortpages/ext_tables.php
###########################

$_EXTKEY = 'wizard_sortpages';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=='BE')	{
	t3lib_extMgm::insertModuleFunction(
		'web_func',
		'tx_wizardsortpages_webfunc_2',
		t3lib_extMgm::extPath($_EXTKEY).'class.tx_wizardsortpages_webfunc_2.php',
		'LLL:EXT:wizard_sortpages/locallang.php:wiz_sort',
		'wiz'
	);
	t3lib_extMgm::addLLrefForTCAdescr('_MOD_web_func','EXT:wizard_sortpages/locallang_csh.xml');
}

###########################
## EXTENSION: lowlevel
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/lowlevel/ext_tables.php
###########################

$_EXTKEY = 'lowlevel';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=='BE')	{
	t3lib_extMgm::addModule('tools','dbint','',t3lib_extMgm::extPath($_EXTKEY).'dbint/');
	t3lib_extMgm::addModule('tools','config','',t3lib_extMgm::extPath($_EXTKEY).'config/');

/*
	t3lib_extMgm::insertModuleFunction(
		'web_func',
		'tx_lowlevel_cleaner',
		t3lib_extMgm::extPath($_EXTKEY).'class.tx_lowlevel_cleaner.php',
		'Cleaner',
		'function',
		'online'
	);
*/
}

###########################
## EXTENSION: install
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/install/ext_tables.php
###########################

$_EXTKEY = 'install';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=='BE') {
	t3lib_extMgm::addModule('tools', 'install', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod/');

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['typo3'][] = 'tx_install_report_InstallStatus';
}


###########################
## EXTENSION: belog
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/belog/ext_tables.php
###########################

$_EXTKEY = 'belog';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=='BE')	{
	t3lib_extMgm::addModule('tools','log','',t3lib_extMgm::extPath($_EXTKEY).'mod/');
	t3lib_extMgm::insertModuleFunction(
		'web_info',
		'tx_belog_webinfo',
		t3lib_extMgm::extPath($_EXTKEY).'class.tx_belog_webinfo.php',
		'Log'
	);
}

###########################
## EXTENSION: beuser
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/beuser/ext_tables.php
###########################

$_EXTKEY = 'beuser';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=='BE')	{
	t3lib_extMgm::addModule('tools','beuser','top',t3lib_extMgm::extPath($_EXTKEY).'mod/');

	$GLOBALS['TBE_MODULES_EXT']['xMOD_alt_clickmenu']['extendCMclasses'][] = array(
		'name' => 'tx_beuser',
		'path' => t3lib_extMgm::extPath($_EXTKEY).'class.tx_beuser.php'
	);
}

###########################
## EXTENSION: aboutmodules
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/aboutmodules/ext_tables.php
###########################

$_EXTKEY = 'aboutmodules';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=='BE')	t3lib_extMgm::addModule('help','aboutmodules','after:about',t3lib_extMgm::extPath($_EXTKEY).'mod/');

###########################
## EXTENSION: setup
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/setup/ext_tables.php
###########################

$_EXTKEY = 'setup';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=='BE')	{
	t3lib_extMgm::addModule('user','setup','after:task',t3lib_extMgm::extPath($_EXTKEY).'mod/');
	t3lib_extMgm::addLLrefForTCAdescr('_MOD_user_setup','EXT:setup/locallang_csh_mod.xml');
}

$GLOBALS['TYPO3_USER_SETTINGS'] = array(
	'ctrl' => array (
		'dividers2tabs' => 1
	),
	'columns' => array (
		'realName' => array(
			'type' => 'text',
			'label' => 'LLL:EXT:setup/mod/locallang.xml:beUser_realName',
			'table' => 'be_users',
			'csh' => 'beUser_realName',
		),
		'email' => array(
			'type' => 'text',
			'label' => 'LLL:EXT:setup/mod/locallang.xml:beUser_email',
			'table' => 'be_users',
			'csh' => 'beUser_email',
		),
		'emailMeAtLogin' => array(
			'type' => 'check',
			'label' => 'LLL:EXT:setup/mod/locallang.xml:emailMeAtLogin',
			'csh' => 'emailMeAtLogin',
		),
		'password' => array(
			'type' => 'password',
			'label' => 'LLL:EXT:setup/mod/locallang.xml:newPassword',
			'table' => 'be_users',
			'csh' => 'newPassword',
			'eval' => 'md5',
		),
		'password2' => array(
			'type' => 'password',
			'label' => 'LLL:EXT:setup/mod/locallang.xml:newPasswordAgain',
			'table' => 'be_users',
			'csh' => 'newPasswordAgain',
			'eval' => 'md5',
		),
		'lang' => array(
			'type' => 'select',
			'itemsProcFunc' => 'SC_mod_user_setup_index->renderLanguageSelect',
			'label' => 'LLL:EXT:setup/mod/locallang.xml:language',
			'csh' => 'language',
		),
		'startModule' => array(
			'type' => 'select',
			'itemsProcFunc' => 'SC_mod_user_setup_index->renderStartModuleSelect',
			'label' => 'LLL:EXT:setup/mod/locallang.xml:startModule',
			'csh' => 'startModule',
		),
		'thumbnailsByDefault' => array(
			'type' => 'check',
			'label' => 'LLL:EXT:setup/mod/locallang.xml:showThumbs',
			'csh' => 'showThumbs',
		),
		'edit_wideDocument' => array(
			'type' => 'check',
			'label' => 'LLL:EXT:setup/mod/locallang.xml:edit_wideDocument',
			'csh' => 'edit_wideDocument',
		),
		'titleLen' => array(
			'type' => 'text',
			'label' => 'LLL:EXT:setup/mod/locallang.xml:maxTitleLen',
			'csh' => 'maxTitleLen',
		),
		'edit_RTE' => array(
			'type' => 'check',
			'label' => 'LLL:EXT:setup/mod/locallang.xml:edit_RTE',
			'csh' => 'edit_RTE',
		),
		'edit_docModuleUpload' => array(
			'type' => 'check',
			'label' => 'LLL:EXT:setup/mod/locallang.xml:edit_docModuleUpload',
			'csh' => 'edit_docModuleUpload',
		),
		'disableCMlayers' => array(
			'type' => 'check',
			'label' => 'LLL:EXT:setup/mod/locallang.xml:disableCMlayers',
			'csh' => 'disableCMlayers',
		),
		'copyLevels' => array(
			'type' => 'text',
			'label' => 'LLL:EXT:setup/mod/locallang.xml:copyLevels',
			'csh' => 'copyLevels',
		),
		'recursiveDelete' => array(
			'type' => 'check',
			'label' => 'LLL:EXT:setup/mod/locallang.xml:recursiveDelete',
			'csh' => 'recursiveDelete',
		),
		'simulate' => array(
			'type' => 'select',
			'itemsProcFunc' => 'SC_mod_user_setup_index->renderSimulateUserSelect',
			'access' => 'admin',
			'label' => 'LLL:EXT:setup/mod/locallang.xml:simulate',
			'csh' => 'simuser'
		),
		'enableFlashUploader' => array(
			'type' => 'check',
			'label' => 'LLL:EXT:setup/mod/locallang.xml:enableFlashUploader',
			'csh' => 'enableFlashUploader',
		),
		'resizeTextareas' => array(
			'type' => 'check',
			'label' => 'LLL:EXT:setup/mod/locallang.xml:resizeTextareas',
			'csh' => 'resizeTextareas',
		),
		'resizeTextareas_MaxHeight' => array(
			'type' => 'text',
			'label' => 'LLL:EXT:setup/mod/locallang.xml:resizeTextareas_MaxHeight',
			'csh' => 'resizeTextareas_MaxHeight',
		),
		'resizeTextareas_Flexible' => array(
			'type' => 'check',
			'label' => 'LLL:EXT:setup/mod/locallang.xml:resizeTextareas_Flexible',
			'csh' => 'resizeTextareas_Flexible',
		),
		'debugInWindow' => array(
			'type' => 'check',
			'label' => 'LLL:EXT:setup/mod/locallang.xml:debugInWindow',
			'access' => 'admin',
		),
		'installToolEnableButton' => array(
			'type' => 'user',
			'label' => 'LLL:EXT:setup/mod/locallang.xml:enableInstallTool.label',
			'userFunc' => 'SC_mod_user_setup_index->renderInstallToolEnableFileButton',
			'access' => 'admin',
			'csh' => 'enableInstallTool'
		),
	),
	'showitem' => '--div--;LLL:EXT:setup/mod/locallang.xml:personal_data,realName,email,emailMeAtLogin,password,password2,lang,
			--div--;LLL:EXT:setup/mod/locallang.xml:opening,startModule,thumbnailsByDefault,titleLen,
			--div--;LLL:EXT:setup/mod/locallang.xml:editFunctionsTab,edit_RTE,edit_wideDocument,edit_docModuleUpload,enableFlashUploader,resizeTextareas,resizeTextareas_MaxHeight,resizeTextareas_Flexible,disableCMlayers,copyLevels,recursiveDelete,
			--div--;LLL:EXT:setup/mod/locallang.xml:adminFunctions,simulate,debugInWindow,installToolEnableButton'

);

###########################
## EXTENSION: taskcenter
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/taskcenter/ext_tables.php
###########################

$_EXTKEY = 'taskcenter';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE == 'BE') {
	t3lib_extMgm::addModulePath('tools_txtaskcenterM1', t3lib_extMgm::extPath($_EXTKEY) . 'task/');
	t3lib_extMgm::addModule('user','task', 'top', t3lib_extMgm::extPath($_EXTKEY) . 'task/');

	$GLOBALS['TYPO3_CONF_VARS']['BE']['AJAX']['Taskcenter::saveCollapseState']	= 'EXT:taskcenter/classes/class.tx_taskcenter_status.php:tx_taskcenter_status->saveCollapseState';
	$GLOBALS['TYPO3_CONF_VARS']['BE']['AJAX']['Taskcenter::saveSortingState']	= 'EXT:taskcenter/classes/class.tx_taskcenter_status.php:tx_taskcenter_status->saveSortingState';
}

###########################
## EXTENSION: info_pagetsconfig
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/info_pagetsconfig/ext_tables.php
###########################

$_EXTKEY = 'info_pagetsconfig';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=='BE')	{
	t3lib_extMgm::insertModuleFunction(
		'web_info',
		'tx_infopagetsconfig_webinfo',
		t3lib_extMgm::extPath($_EXTKEY).'class.tx_infopagetsconfig_webinfo.php',
		'LLL:EXT:info_pagetsconfig/locallang.php:mod_pagetsconfig'
	);
}

t3lib_extMgm::addLLrefForTCAdescr('_MOD_web_info','EXT:info_pagetsconfig/locallang_csh_webinfo.xml');


###########################
## EXTENSION: viewpage
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/viewpage/ext_tables.php
###########################

$_EXTKEY = 'viewpage';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=='BE')	t3lib_extMgm::addModule('web','view','after:layout',t3lib_extMgm::extPath($_EXTKEY).'view/');

###########################
## EXTENSION: t3skin
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/t3skin/ext_tables.php
###########################

$_EXTKEY = 't3skin';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE == 'BE' || (TYPO3_MODE == 'FE' && isset($GLOBALS['BE_USER']))) {
	global $TBE_STYLES;

		// register as a skin
	$TBE_STYLES['skins'][$_EXTKEY] = array(
		'name' => 't3skin',
	);

		// Support for other extensions to add own icons...
	$presetSkinImgs = is_array($TBE_STYLES['skinImg']) ?
		$TBE_STYLES['skinImg'] :
		array();

	$TBE_STYLES['skins'][$_EXTKEY]['stylesheetDirectories']['sprites'] = 'EXT:t3skin/stylesheets/sprites/';

	/**
	 * Setting up backend styles and colors
	 */
	$TBE_STYLES['mainColors'] = array(	// Always use #xxxxxx color definitions!
		'bgColor'    => '#FFFFFF',		// Light background color
		'bgColor2'   => '#FEFEFE',		// Steel-blue
		'bgColor3'   => '#F1F3F5',		// dok.color
		'bgColor4'   => '#E6E9EB',		// light tablerow background, brownish
		'bgColor5'   => '#F8F9FB',		// light tablerow background, greenish
		'bgColor6'   => '#E6E9EB',		// light tablerow background, yellowish, for section headers. Light.
		'hoverColor' => '#FF0000',
		'navFrameHL' => '#F8F9FB'
	);

	$TBE_STYLES['colorschemes'][0] = '-|class-main1,-|class-main2,-|class-main3,-|class-main4,-|class-main5';
	$TBE_STYLES['colorschemes'][1] = '-|class-main11,-|class-main12,-|class-main13,-|class-main14,-|class-main15';
	$TBE_STYLES['colorschemes'][2] = '-|class-main21,-|class-main22,-|class-main23,-|class-main24,-|class-main25';
	$TBE_STYLES['colorschemes'][3] = '-|class-main31,-|class-main32,-|class-main33,-|class-main34,-|class-main35';
	$TBE_STYLES['colorschemes'][4] = '-|class-main41,-|class-main42,-|class-main43,-|class-main44,-|class-main45';
	$TBE_STYLES['colorschemes'][5] = '-|class-main51,-|class-main52,-|class-main53,-|class-main54,-|class-main55';

	$TBE_STYLES['styleschemes'][0]['all'] = 'CLASS: formField';
	$TBE_STYLES['styleschemes'][1]['all'] = 'CLASS: formField1';
	$TBE_STYLES['styleschemes'][2]['all'] = 'CLASS: formField2';
	$TBE_STYLES['styleschemes'][3]['all'] = 'CLASS: formField3';
	$TBE_STYLES['styleschemes'][4]['all'] = 'CLASS: formField4';
	$TBE_STYLES['styleschemes'][5]['all'] = 'CLASS: formField5';

	$TBE_STYLES['styleschemes'][0]['check'] = 'CLASS: checkbox';
	$TBE_STYLES['styleschemes'][1]['check'] = 'CLASS: checkbox';
	$TBE_STYLES['styleschemes'][2]['check'] = 'CLASS: checkbox';
	$TBE_STYLES['styleschemes'][3]['check'] = 'CLASS: checkbox';
	$TBE_STYLES['styleschemes'][4]['check'] = 'CLASS: checkbox';
	$TBE_STYLES['styleschemes'][5]['check'] = 'CLASS: checkbox';

	$TBE_STYLES['styleschemes'][0]['radio'] = 'CLASS: radio';
	$TBE_STYLES['styleschemes'][1]['radio'] = 'CLASS: radio';
	$TBE_STYLES['styleschemes'][2]['radio'] = 'CLASS: radio';
	$TBE_STYLES['styleschemes'][3]['radio'] = 'CLASS: radio';
	$TBE_STYLES['styleschemes'][4]['radio'] = 'CLASS: radio';
	$TBE_STYLES['styleschemes'][5]['radio'] = 'CLASS: radio';

	$TBE_STYLES['styleschemes'][0]['select'] = 'CLASS: select';
	$TBE_STYLES['styleschemes'][1]['select'] = 'CLASS: select';
	$TBE_STYLES['styleschemes'][2]['select'] = 'CLASS: select';
	$TBE_STYLES['styleschemes'][3]['select'] = 'CLASS: select';
	$TBE_STYLES['styleschemes'][4]['select'] = 'CLASS: select';
	$TBE_STYLES['styleschemes'][5]['select'] = 'CLASS: select';

	$TBE_STYLES['borderschemes'][0] = array('', '', '', 'wrapperTable');
	$TBE_STYLES['borderschemes'][1] = array('', '', '', 'wrapperTable1');
	$TBE_STYLES['borderschemes'][2] = array('', '', '', 'wrapperTable2');
	$TBE_STYLES['borderschemes'][3] = array('', '', '', 'wrapperTable3');
	$TBE_STYLES['borderschemes'][4] = array('', '', '', 'wrapperTable4');
	$TBE_STYLES['borderschemes'][5] = array('', '', '', 'wrapperTable5');



		// Setting the relative path to the extension in temp. variable:
	$temp_eP = t3lib_extMgm::extRelPath($_EXTKEY);

		// Alternative dimensions for frameset sizes:
	$TBE_STYLES['dims']['leftMenuFrameW'] = 190;		// Left menu frame width
	$TBE_STYLES['dims']['topFrameH']      = 42;			// Top frame height
	$TBE_STYLES['dims']['navFrameWidth']  = 280;		// Default navigation frame width

		// Setting roll-over background color for click menus:
		// Notice, this line uses the the 'scriptIDindex' feature to override another value in this array (namely $TBE_STYLES['mainColors']['bgColor5']), for a specific script "typo3/alt_clickmenu.php"
	$TBE_STYLES['scriptIDindex']['typo3/alt_clickmenu.php']['mainColors']['bgColor5'] = '#dedede';

		// Setting up auto detection of alternative icons:
	$TBE_STYLES['skinImgAutoCfg'] = array(
		'absDir'             => t3lib_extMgm::extPath($_EXTKEY).'icons/',
		'relDir'             => t3lib_extMgm::extRelPath($_EXTKEY).'icons/',
		'forceFileExtension' => 'gif',	// Force to look for PNG alternatives...
#		'scaleFactor'        => 2/3,	// Scaling factor, default is 1
		'iconSizeWidth'      => 16,
		'iconSizeHeight'     => 16,
	);

		// Changing icon for filemounts, needs to be done here as overwriting the original icon would also change the filelist tree's root icon
	$TCA['sys_filemounts']['ctrl']['iconfile'] = '_icon_ftp_2.gif';

		// Adding flags to sys_language
	t3lib_div::loadTCA('sys_language');
	$TCA['sys_language']['ctrl']['typeicon_column'] = 'flag';
	$TCA['sys_language']['ctrl']['typeicon_classes'] = array(
		'default' => 'mimetypes-x-sys_language',
		'mask'	=> 'flags-###TYPE###'
	);
	$flagNames = array(
		'multiple', 'ad', 'ae', 'af', 'ag', 'ai', 'al', 'am', 'an', 'ao', 'ar', 'as', 'at', 'au', 'aw', 'ax', 'az',
		'ba', 'bb', 'bd', 'be', 'bf', 'bg', 'bh', 'bi', 'bj', 'bm', 'bn', 'bo', 'br', 'bs', 'bt', 'bv', 'bw', 'by', 'bz',
		'ca', 'catalonia', 'cc', 'cd', 'cf', 'cg', 'ch', 'ci', 'ck', 'cl', 'cm', 'cn', 'co', 'cr', 'cs', 'cu', 'cv', 'cx', 'cy', 'cz',
		'de', 'dj', 'dk', 'dm', 'do', 'dz',
		'ec', 'ee', 'eg', 'eh', 'england', 'er', 'es', 'et', 'europeanunion',
		'fam', 'fi', 'fj', 'fk', 'fm', 'fo', 'fr',
		'ga', 'gb', 'gd', 'ge', 'gf', 'gh', 'gi', 'gl', 'gm', 'gn', 'gp', 'gq', 'gr', 'gs', 'gt', 'gu', 'gw', 'gy',
		'hk', 'hm', 'hn', 'hr', 'ht', 'hu',
		'id', 'ie', 'il', 'in', 'io', 'iq', 'ir', 'is', 'it',
		'jm', 'jo', 'jp',
		'ke', 'kg', 'kh', 'ki', 'km', 'kn', 'kp', 'kr', 'kw', 'ky', 'kz',
		'la', 'lb', 'lc', 'li', 'lk', 'lr', 'ls', 'lt', 'lu', 'lv', 'ly',
		'ma', 'mc', 'md', 'me', 'mg', 'mh', 'mk', 'ml', 'mm', 'mn', 'mo', 'mp', 'mq', 'mr', 'ms', 'mt', 'mu', 'mv', 'mw', 'mx', 'my', 'mz',
		'na', 'nc', 'ne', 'nf', 'ng', 'ni', 'nl', 'no', 'np', 'nr', 'nu', 'nz',
		'om',
		'pa', 'pe', 'pf', 'pg', 'ph', 'pk', 'pl', 'pm', 'pn', 'pr', 'ps', 'pt', 'pw', 'py',
		'qa', 'qc',
		're', 'ro', 'rs', 'ru', 'rw',
		'sa', 'sb', 'sc', 'scotland', 'sd', 'se', 'sg', 'sh', 'si', 'sj', 'sk', 'sl', 'sm', 'sn', 'so', 'sr', 'st', 'sv', 'sy', 'sz',
		'tc', 'td', 'tf', 'tg', 'th', 'tj', 'tk', 'tl', 'tm', 'tn', 'to', 'tr', 'tt', 'tv', 'tw', 'tz',
		'ua', 'ug', 'um', 'us', 'uy', 'uz',
		'va', 'vc', 've', 'vg', 'vi', 'vn', 'vu',
		'wales', 'wf', 'ws',
		'ye', 'yt',
		'za', 'zm', 'zw'
	);
	foreach ($flagNames as $flagName) {
		$TCA['sys_language']['columns']['flag']['config']['items'][] = array($flagName, $flagName, 'EXT:t3skin/images/flags/'. $flagName . '.png');
	}

		// Manual setting up of alternative icons. This is mainly for module icons which has a special prefix:
	$TBE_STYLES['skinImg'] = array_merge($presetSkinImgs, array (
		'gfx/ol/blank.gif'                         => array('clear.gif','width="18" height="16"'),
		'MOD:web/website.gif'                      => array($temp_eP.'icons/module_web.gif','width="24" height="24"'),
		'MOD:web_layout/layout.gif'                => array($temp_eP.'icons/module_web_layout.gif','width="24" height="24"'),
		'MOD:web_view/view.gif'                    => array($temp_eP.'icons/module_web_view.png','width="24" height="24"'),
		'MOD:web_list/list.gif'                    => array($temp_eP.'icons/module_web_list.gif','width="24" height="24"'),
		'MOD:web_info/info.gif'                    => array($temp_eP.'icons/module_web_info.png','width="24" height="24"'),
		'MOD:web_perm/perm.gif'                    => array($temp_eP.'icons/module_web_perms.png','width="24" height="24"'),
		'MOD:web_func/func.gif'                    => array($temp_eP.'icons/module_web_func.png','width="24" height="24"'),
		'MOD:web_ts/ts1.gif'                       => array($temp_eP.'icons/module_web_ts.gif','width="24" height="24"'),
		'MOD:web_modules/modules.gif'              => array($temp_eP.'icons/module_web_modules.gif','width="24" height="24"'),
		'MOD:web_txversionM1/cm_icon.gif'          => array($temp_eP.'icons/module_web_version.gif','width="24" height="24"'),
		'MOD:file/file.gif'                        => array($temp_eP.'icons/module_file.gif','width="22" height="24"'),
		'MOD:file_list/list.gif'                   => array($temp_eP.'icons/module_file_list.gif','width="22" height="24"'),
		'MOD:file_images/images.gif'               => array($temp_eP.'icons/module_file_images.gif','width="22" height="22"'),
		'MOD:user/user.gif'                        => array($temp_eP.'icons/module_user.gif','width="22" height="22"'),
		'MOD:user_task/task.gif'                   => array($temp_eP.'icons/module_user_taskcenter.gif','width="22" height="22"'),
		'MOD:user_setup/setup.gif'                 => array($temp_eP.'icons/module_user_setup.gif','width="22" height="22"'),
		'MOD:user_doc/document.gif'                => array($temp_eP.'icons/module_doc.gif','width="22" height="22"'),
		'MOD:user_ws/sys_workspace.gif'            => array($temp_eP.'icons/module_user_ws.gif','width="22" height="22"'),
		'MOD:tools/tool.gif'                       => array($temp_eP.'icons/module_tools.gif','width="25" height="24"'),
		'MOD:tools_beuser/beuser.gif'              => array($temp_eP.'icons/module_tools_user.gif','width="24" height="24"'),
		'MOD:tools_em/em.gif'                      => array($temp_eP.'icons/module_tools_em.png','width="24" height="24"'),
		'MOD:tools_em/install.gif'                 => array($temp_eP.'icons/module_tools_em.gif','width="24" height="24"'),
		'MOD:tools_dbint/db.gif'                   => array($temp_eP.'icons/module_tools_dbint.gif','width="25" height="24"'),
		'MOD:tools_config/config.gif'              => array($temp_eP.'icons/module_tools_config.gif','width="24" height="24"'),
		'MOD:tools_install/install.gif'            => array($temp_eP.'icons/module_tools_install.gif','width="24" height="24"'),
		'MOD:tools_log/log.gif'                    => array($temp_eP.'icons/module_tools_log.gif','width="24" height="24"'),
		'MOD:tools_txphpmyadmin/thirdparty_db.gif' => array($temp_eP.'icons/module_tools_phpmyadmin.gif','width="24" height="24"'),
		'MOD:tools_isearch/isearch.gif'            => array($temp_eP.'icons/module_tools_isearch.gif','width="24" height="24"'),
		'MOD:help/help.gif'                        => array($temp_eP.'icons/module_help.gif','width="23" height="24"'),
		'MOD:help_about/info.gif'                  => array($temp_eP.'icons/module_help_about.gif','width="25" height="24"'),
		'MOD:help_aboutmodules/aboutmodules.gif'   => array($temp_eP.'icons/module_help_aboutmodules.gif','width="24" height="24"'),
		'MOD:help_cshmanual/about.gif'         => array($temp_eP.'icons/module_help_cshmanual.gif','width="25" height="24"'),
		'MOD:help_txtsconfighelpM1/moduleicon.gif' => array($temp_eP.'icons/module_help_ts.gif','width="25" height="24"'),
	));

		// Logo at login screen
	$TBE_STYLES['logo_login'] = $temp_eP . 'images/login/typo3logo-white-greyback.gif';

		// extJS theme
	$TBE_STYLES['extJS']['theme'] =  $temp_eP . 'extjs/xtheme-t3skin.css';

	// Adding HTML template for login screen
	$TBE_STYLES['htmlTemplates']['templates/login.html'] = 'sysext/t3skin/templates/login.html';

	$GLOBALS['TYPO3_CONF_VARS']['typo3/backend.php']['additionalBackendItems'][] = t3lib_extMgm::extPath('t3skin').'registerIe6Stylesheet.php';

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/template.php']['preHeaderRenderHook'][] = t3lib_extMgm::extPath('t3skin').'pngfix/class.tx_templatehook.php:tx_templatehook->registerPngFix';

	$GLOBALS['TBE_STYLES']['stylesheets']['admPanel'] = t3lib_extMgm::siteRelPath('t3skin') . 'stylesheets/standalone/admin_panel.css';

	foreach ($flagNames as $flagName) {
		t3lib_SpriteManager::addIconSprite(
			array(
				'flags-' . $flagName,
				'flags-' . $flagName . '-overlay',
			)
		);
	}
	unset($flagNames, $flagName);

}


###########################
## EXTENSION: t3editor
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/t3editor/ext_tables.php
###########################

$_EXTKEY = 't3editor';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

if (TYPO3_MODE == 'BE') {
	// Register AJAX handlers:
	$TYPO3_CONF_VARS['BE']['AJAX']['tx_t3editor::saveCode'] = 'EXT:t3editor/classes/class.tx_t3editor.php:tx_t3editor->ajaxSaveCode';
	$TYPO3_CONF_VARS['BE']['AJAX']['tx_t3editor::getPlugins'] = 'EXT:t3editor/classes/class.tx_t3editor.php:tx_t3editor->getPlugins';
	$TYPO3_CONF_VARS['BE']['AJAX']['tx_t3editor_TSrefLoader::getTypes'] = 'EXT:t3editor/classes/ts_codecompletion/class.tx_t3editor_tsrefloader.php:tx_t3editor_TSrefLoader->processAjaxRequest';
	$TYPO3_CONF_VARS['BE']['AJAX']['tx_t3editor_TSrefLoader::getDescription'] = 'EXT:t3editor/classes/ts_codecompletion/class.tx_t3editor_tsrefloader.php:tx_t3editor_TSrefLoader->processAjaxRequest';
	$TYPO3_CONF_VARS['BE']['AJAX']['tx_t3editor_codecompletion::loadTemplates'] = 'EXT:t3editor/classes/ts_codecompletion/class.tx_t3editor_codecompletion.php:tx_t3editor_codecompletion->processAjaxRequest';
}

###########################
## EXTENSION: reports
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/reports/ext_tables.php
###########################

$_EXTKEY = 'reports';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE == 'BE') {
	t3lib_extMgm::addModulePath('tools_txreportsM1', t3lib_extMgm::extPath($_EXTKEY) . 'mod/');
	t3lib_extMgm::addModule('tools', 'txreportsM1', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod/');

	$statusReport = array(
		'title'       => 'LLL:EXT:reports/reports/locallang.xml:status_report_title',
		'description' => 'LLL:EXT:reports/reports/locallang.xml:status_report_description',
		'report'      => 'tx_reports_reports_Status'
	);

	if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status'])) {
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status'] = array();
	}

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status'] = array_merge(
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status'],
		$statusReport
	);

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['typo3'][] = 'tx_reports_reports_status_Typo3Status';
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['system'][] = 'tx_reports_reports_status_SystemStatus';
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['security'][] = 'tx_reports_reports_status_SecurityStatus';
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['configuration'][] = 'tx_reports_reports_status_ConfigurationStatus';

}


###########################
## EXTENSION: static_info_tables
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/static_info_tables/ext_tables.php
###########################

$_EXTKEY = 'static_info_tables';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::addStaticFile(STATIC_INFO_TABLES_EXTkey, 'static/static_info_tables/', 'Static Info tables');

$TCA['static_territories'] = array(
	'ctrl' => array(
		'label' => 'tr_name_en',
		'label_alt' => 'tr_name_en,tr_iso_nr',
		'readOnly' => 1,	// This should always be true, as it prevents the static data from being altered
		'adminOnly' => 1,
		'rootLevel' => 1,
		'is_static' => 1,
		'default_sortby' => 'ORDER BY tr_name_en',
		'title' => 'LLL:EXT:'.STATIC_INFO_TABLES_EXTkey.'/locallang_db.xml:static_territories.title',
		'dynamicConfigFile' => PATH_BE_staticinfotables.'tca.php',
		'iconfile' => PATH_BE_staticinfotables_rel.'icon_static_territories.gif',
	),
	'interface' => array(
		'showRecordFieldList' => 'tr_name_en,tr_iso_nr'
	)
);

// Country reference data from ISO 3166-1
$TCA['static_countries'] = array(
	'ctrl' => array(
		'label' => 'cn_short_en',
		'label_alt' => 'cn_short_en,cn_iso_2',
		'readOnly' => 1,	// This should always be true, as it prevents the static data from being altered
		'adminOnly' => 1,
		'rootLevel' => 1,
		'is_static' => 1,
		'default_sortby' => 'ORDER BY cn_short_en',
		'delete' => 'deleted',
		'title' => 'LLL:EXT:'.STATIC_INFO_TABLES_EXTkey.'/locallang_db.xml:static_countries.title',
		'dynamicConfigFile' => PATH_BE_staticinfotables.'tca.php',
		'iconfile' => PATH_BE_staticinfotables_rel.'icon_static_countries.gif',
	),
	'interface' => array(
		'showRecordFieldList' => 'cn_iso_2,cn_iso_3,cn_iso_nr,cn_official_name_local,cn_official_name_en,cn_capital,cn_tldomain,cn_currency_iso_3,cn_currency_iso_nr,cn_phone,cn_uno_member,cn_eu_member,cn_address_format,cn_short_en'
	)
);

// Country subdivision reference data from ISO 3166-2
$TCA['static_country_zones'] = array(
	'ctrl' => array(
		'label' => 'zn_name_local',
		'label_alt' => 'zn_name_local,zn_code',
		'readOnly' => 1,
		'adminOnly' => 1,
		'rootLevel' => 1,
		'is_static' => 1,
		'default_sortby' => 'ORDER BY zn_name_local',
		'title' => 'LLL:EXT:'.STATIC_INFO_TABLES_EXTkey.'/locallang_db.xml:static_country_zones.title',
		'dynamicConfigFile' => PATH_BE_staticinfotables.'tca.php',
		'iconfile' => PATH_BE_staticinfotables_rel.'icon_static_countries.gif',
	),
	'interface' => array(
		'showRecordFieldList' => 'zn_country_iso_nr,zn_country_iso_3,zn_code,zn_name_local,zn_name_en'
	)
);

// Language reference data from ISO 639-1
$TCA['static_languages'] = array(
	'ctrl' => array(
		'label' => 'lg_name_en',
		'label_alt' => 'lg_name_en,lg_iso_2',
		'readOnly' => 1,
		'adminOnly' => 1,
		'rootLevel' => 1,
		'is_static' => 1,
		'default_sortby' => 'ORDER BY lg_name_en',
		'title' => 'LLL:EXT:'.STATIC_INFO_TABLES_EXTkey.'/locallang_db.xml:static_languages.title',
		'dynamicConfigFile' => PATH_BE_staticinfotables.'tca.php',
		'iconfile' => PATH_BE_staticinfotables_rel.'icon_static_languages.gif',
	),
	'interface' => array(
		'showRecordFieldList' => 'lg_name_local,lg_name_en,lg_iso_2,lg_typo3,lg_country_iso_2,lg_collate_locale,lg_sacred,lg_constructed'
	)
);

// Currency reference data from ISO 4217
$TCA['static_currencies'] = array(
	'ctrl' => array(
		'label' => 'cu_name_en',
		'label_alt' => 'cu_name_en,cu_iso_3',
		'readOnly' => 1,
		'adminOnly' => 1,
		'rootLevel' => 1,
		'is_static' => 1,
		'default_sortby' => 'ORDER BY cu_name_en',
		'title' => 'LLL:EXT:'.STATIC_INFO_TABLES_EXTkey.'/locallang_db.xml:static_currencies.title',
		'dynamicConfigFile' => PATH_BE_staticinfotables.'tca.php',
		'iconfile' => PATH_BE_staticinfotables_rel.'icon_static_currencies.gif',
	),
	'interface' => array(
		'showRecordFieldList' => 'cu_iso_3,cu_iso_nr,cu_name_en,cu_symbol_left,cu_symbol_right,cu_thousands_point,cu_decimal_point,cu_decimal_digits,cu_sub_name_en,cu_sub_divisor,cu_sub_symbol_left,cu_sub_symbol_right'
	)
);

$TCA['static_countries']['ctrl']['readOnly'] = 0;
$TCA['static_languages']['ctrl']['readOnly'] = 0;
$TCA['static_country_zones']['ctrl']['readOnly'] = 0;
$TCA['static_currencies']['ctrl']['readOnly'] = 0;
$TCA['static_territories']['ctrl']['readOnly'] = 0;


// ******************************************************************
// sys_language
// ******************************************************************

t3lib_div::loadTCA('sys_language');
$TCA['sys_language']['columns']['static_lang_isocode']['config'] = array(
	'type' => 'select',
	'items' => array(
		array('',0),
	),
	#'foreign_table' => 'static_languages',
	#'foreign_table_where' => 'AND static_languages.pid=0 ORDER BY static_languages.lg_name_en',
	'itemsProcFunc' => 'tx_staticinfotables_div->selectItemsTCA',
	'itemsProcFunc_config' => array(
		'table' => 'static_languages',
		'indexField' => 'uid',
		// I think that will make more sense in the future
		// 'indexField' => 'lg_iso_2',
		'prependHotlist' => 1,
		//	defaults:
		//'hotlistLimit' => 8,
		//'hotlistSort' => 1,
		//'hotlistOnly' => 0,
		//'hotlistApp' => TYPO3_MODE,
	),
	'size' => 1,
	'minitems' => 0,
	'maxitems' => 1,
);

$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:'.STATIC_INFO_TABLES_EXTkey.'/class.tx_staticinfotables_syslanguage.php:&tx_staticinfotables_syslanguage';


###########################
## EXTENSION: templavoila
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/templavoila/ext_tables.php
###########################

$_EXTKEY = 'templavoila';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


# TYPO3 CVS ID: $Id: ext_tables.php 11104 2008-08-13 13:27:32Z dmitry $
if (!defined ('TYPO3_MODE'))  die ('Access denied.');

if (TYPO3_MODE=='BE') {

		// unserializing the configuration so we can use it here:
	$_EXTCONF = unserialize($_EXTCONF);

		// Adding click menu item:
	$GLOBALS['TBE_MODULES_EXT']['xMOD_alt_clickmenu']['extendCMclasses'][] = array(
		'name' => 'tx_templavoila_cm1',
		'path' => t3lib_extMgm::extPath($_EXTKEY).'class.tx_templavoila_cm1.php'
	);
	include_once(t3lib_extMgm::extPath('templavoila').'class.tx_templavoila_handlestaticdatastructures.php');

		// Adding backend modules:
	t3lib_extMgm::addModule('web','txtemplavoilaM1','top',t3lib_extMgm::extPath($_EXTKEY).'mod1/');
	t3lib_extMgm::addModule('web','txtemplavoilaM2','',t3lib_extMgm::extPath($_EXTKEY).'mod2/');

		// Remove default Page module (layout) manually if wanted:
	if (!$_EXTCONF['enable.']['oldPageModule']) {
		$tmp = $GLOBALS['TBE_MODULES']['web'];
		$GLOBALS['TBE_MODULES']['web'] = str_replace (',,',',',str_replace ('layout','',$tmp));
		unset ($GLOBALS['TBE_MODULES']['_PATHS']['web_layout']);
	}

		// Registering CSH:
	t3lib_extMgm::addLLrefForTCAdescr('be_groups','EXT:templavoila/locallang_csh_begr.xml');
	t3lib_extMgm::addLLrefForTCAdescr('pages','EXT:templavoila/locallang_csh_pages.xml');
	t3lib_extMgm::addLLrefForTCAdescr('tt_content','EXT:templavoila/locallang_csh_ttc.xml');
	t3lib_extMgm::addLLrefForTCAdescr('tx_templavoila_datastructure','EXT:templavoila/locallang_csh_ds.xml');
	t3lib_extMgm::addLLrefForTCAdescr('tx_templavoila_tmplobj','EXT:templavoila/locallang_csh_to.xml');
	t3lib_extMgm::addLLrefForTCAdescr('xMOD_tx_templavoila','EXT:templavoila/locallang_csh_module.xml');
	t3lib_extMgm::addLLrefForTCAdescr('xEXT_templavoila','EXT:templavoila/locallang_csh_intro.xml');
	t3lib_extMgm::addLLrefForTCAdescr('_MOD_web_txtemplavoilaM1','EXT:templavoila/locallang_csh_pm.xml');


	t3lib_extMgm::insertModuleFunction(
		'tools_txextdevevalM1',
		'tx_templavoila_extdeveval',
		t3lib_extMgm::extPath($_EXTKEY).'class.tx_templavoila_extdeveval.php',
		'TemplaVoila L10N Mode Conversion Tool'
	);
}

	// Adding tables:
$TCA['tx_templavoila_tmplobj'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:templavoila/locallang_db.xml:tx_templavoila_tmplobj',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'default_sortby' => 'ORDER BY title',
		'delete' => 'deleted',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_to.gif',
		'selicon_field' => 'previewicon',
		'selicon_field_path' => 'uploads/tx_templavoila',
		'type' => 'parent',
		'versioningWS' => TRUE,
		'origUid' => 't3_origuid',
		'shadowColumnsForNewPlaceholders' => 'title,datastructure,rendertype,sys_language_uid,parent,rendertype_ref',
	)
);
$TCA['tx_templavoila_datastructure'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:templavoila/locallang_db.xml:tx_templavoila_datastructure',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'default_sortby' => 'ORDER BY title',
		'delete' => 'deleted',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_ds.gif',
		'selicon_field' => 'previewicon',
		'selicon_field_path' => 'uploads/tx_templavoila',
		'versioningWS' => TRUE,
		'origUid' => 't3_origuid',
		'shadowColumnsForNewPlaceholders' => 'scope,title',
	)
);

t3lib_extMgm::allowTableOnStandardPages('tx_templavoila_datastructure');
t3lib_extMgm::allowTableOnStandardPages('tx_templavoila_tmplobj');


	// Adding access list to be_groups
t3lib_div::loadTCA('be_groups');
$tempColumns = array (
	'tx_templavoila_access' => array(
		'label' => 'LLL:EXT:templavoila/locallang_db.xml:be_groups.tx_templavoila_access',
		'config' => Array (
			'type' => 'group',
			'internal_type' => 'db',
			'allowed' => 'tx_templavoila_datastructure,tx_templavoila_tmplobj',
			'prepend_tname' => 1,
			'size' => 5,
			'autoSizeMax' => 15,
			'multiple' => 1,
			'minitems' => 0,
			'maxitems' => 1000,
			'show_thumbs'=> 1,
		),
	)
);
t3lib_extMgm::addTCAcolumns('be_groups', $tempColumns, 1);
t3lib_extMgm::addToAllTCAtypes('be_groups','tx_templavoila_access;;;;1-1-1', '1');

	// Adding the new content element, "Flexible Content":
t3lib_div::loadTCA('tt_content');
$tempColumns = Array (
	'tx_templavoila_ds' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:templavoila/locallang_db.xml:tt_content.tx_templavoila_ds',
		'config' => Array (
			'type' => 'select',
			'items' => Array (
				Array('',0),
			),
			'foreign_table' => 'tx_templavoila_datastructure',
			'foreign_table_where' => 'AND tx_templavoila_datastructure.pid=###STORAGE_PID### AND tx_templavoila_datastructure.scope IN (2) ORDER BY tx_templavoila_datastructure.sorting',
			'allowNonIdValues' => 1,
			'itemsProcFunc' => 'tx_templavoila_handleStaticdatastructures->main_scope2',
			'size' => 1,
			'minitems' => 0,
			'maxitems' => 1,
		)
	),
	'tx_templavoila_to' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:templavoila/locallang_db.xml:tt_content.tx_templavoila_to',
		'displayCond' => 'FIELD:tx_templavoila_ds:REQ:true',
		'config' => Array (
			'type' => 'select',
			'items' => Array (
				Array('',0),
			),
			'foreign_table' => 'tx_templavoila_tmplobj',
			'foreign_table_where' => 'AND tx_templavoila_tmplobj.pid=###STORAGE_PID### AND tx_templavoila_tmplobj.datastructure=\'###REC_FIELD_tx_templavoila_ds###\' AND tx_templavoila_tmplobj.parent=0 ORDER BY tx_templavoila_tmplobj.sorting',
#			'disableNoMatchingValueElement' => 1,
			'size' => 1,
			'minitems' => 0,
			'maxitems' => 1,
		)
	),
	'tx_templavoila_flex' => Array (
		'l10n_cat' => 'text',
		'exclude' => 1,
		'label' => 'LLL:EXT:templavoila/locallang_db.xml:tt_content.tx_templavoila_flex',
		'displayCond' => 'FIELD:tx_templavoila_ds:REQ:true',
		'config' => Array (
			'type' => 'flex',
			'ds_pointerField' => 'tx_templavoila_ds',
			'ds_tableField' => 'tx_templavoila_datastructure:dataprot',
		)
	),
	'tx_templavoila_pito' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:templavoila/locallang_db.xml:tt_content.tx_templavoila_pito',
		'config' => Array (
			'type' => 'select',
			'items' => Array (
				Array('',0),
			),
			'itemsProcFunc' => 'tx_templavoila_handleStaticdatastructures->pi_templates',
			'size' => 1,
			'minitems' => 0,
			'maxitems' => 1,
		)
	),
);
t3lib_extMgm::addTCAcolumns('tt_content',$tempColumns,1);

$TCA['tt_content']['types'][$_EXTKEY.'_pi1']['showitem']='CType;;4;button;1-1-1, header;;3;;2-2-2,tx_templavoila_ds,tx_templavoila_to,tx_templavoila_flex;;;;2-2-2, hidden;;1;;3-3-3';
t3lib_extMgm::addPlugin(Array('LLL:EXT:templavoila/locallang_db.xml:tt_content.CType_pi1', $_EXTKEY.'_pi1'),'CType');

if ($TCA['tt_content']['ctrl']['requestUpdate'] != '') {
	$TCA['tt_content']['ctrl']['requestUpdate'] .= ',';
}
$TCA['tt_content']['ctrl']['requestUpdate'] .= 'tx_templavoila_ds';

	// For pages:
$tempColumns = Array (
	'tx_templavoila_ds' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:templavoila/locallang_db.xml:pages.tx_templavoila_ds',
		'config' => Array (
			'type' => 'select',
			'items' => Array (
				Array('',0),
			),
			'foreign_table' => 'tx_templavoila_datastructure',
			'foreign_table_where' => 'AND tx_templavoila_datastructure.pid=###STORAGE_PID### AND tx_templavoila_datastructure.scope IN (1) ORDER BY tx_templavoila_datastructure.sorting',
			'allowNonIdValues' => 1,
			'itemsProcFunc' => 'tx_templavoila_handleStaticdatastructures->main_scope1',
			'size' => 1,
			'minitems' => 0,
			'maxitems' => 1,
			'suppress_icons' => 'IF_VALUE_FALSE',
		)
	),
	'tx_templavoila_to' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:templavoila/locallang_db.xml:pages.tx_templavoila_to',
		'displayCond' => 'FIELD:tx_templavoila_ds:REQ:true',
		'config' => Array (
			'type' => 'select',
			'items' => Array (
				Array('',0),
			),
			'foreign_table' => 'tx_templavoila_tmplobj',
			'foreign_table_where' => 'AND tx_templavoila_tmplobj.pid=###STORAGE_PID### AND tx_templavoila_tmplobj.datastructure=\'###REC_FIELD_tx_templavoila_ds###\' AND tx_templavoila_tmplobj.parent=0 ORDER BY tx_templavoila_tmplobj.sorting',
#			'disableNoMatchingValueElement' => 1,
			'size' => 1,
			'minitems' => 0,
			'maxitems' => 1,
		)
	),
	'tx_templavoila_next_ds' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:templavoila/locallang_db.xml:pages.tx_templavoila_next_ds',
		'config' => Array (
			'type' => 'select',
			'items' => Array (
				Array('',0),
			),
			'foreign_table' => 'tx_templavoila_datastructure',
			'foreign_table_where' => 'AND tx_templavoila_datastructure.pid=###STORAGE_PID### AND tx_templavoila_datastructure.scope IN (1) ORDER BY tx_templavoila_datastructure.sorting',
			'allowNonIdValues' => 1,
			'itemsProcFunc' => 'tx_templavoila_handleStaticdatastructures->main_scope1',
			'size' => 1,
			'minitems' => 0,
			'maxitems' => 1,
			'suppress_icons' => 'IF_VALUE_FALSE',
		)
	),
	'tx_templavoila_next_to' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:templavoila/locallang_db.xml:pages.tx_templavoila_next_to',
		'displayCond' => 'FIELD:tx_templavoila_next_ds:REQ:true',
		'config' => Array (
			'type' => 'select',
			'items' => Array (
				Array('',0),
			),
			'foreign_table' => 'tx_templavoila_tmplobj',
			'foreign_table_where' => 'AND tx_templavoila_tmplobj.pid=###STORAGE_PID### AND tx_templavoila_tmplobj.datastructure=\'###REC_FIELD_tx_templavoila_next_ds###\' AND tx_templavoila_tmplobj.parent=0 ORDER BY tx_templavoila_tmplobj.sorting',
#			'disableNoMatchingValueElement' => 1,
			'size' => 1,
			'minitems' => 0,
			'maxitems' => 1,
		)
	),
	'tx_templavoila_flex' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:templavoila/locallang_db.xml:pages.tx_templavoila_flex',
#		'displayCond' => 'FIELD:tx_templavoila_ds:REQ:true',
		'config' => Array (
			'type' => 'flex',
			'ds_pointerField' => 'tx_templavoila_ds',
			'ds_pointerField_searchParent' => 'pid',
			'ds_pointerField_searchParent_subField' => 'tx_templavoila_next_ds',
			'ds_tableField' => 'tx_templavoila_datastructure:dataprot',
		)
	),
);
t3lib_extMgm::addTCAcolumns('pages',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('pages','tx_templavoila_ds;;;;1-1-1,tx_templavoila_to,tx_templavoila_next_ds,tx_templavoila_next_to,tx_templavoila_flex;;;;1-1-1');
if ($TCA['pages']['ctrl']['requestUpdate'] != '') {
	$TCA['pages']['ctrl']['requestUpdate'] .= ',';
}
$TCA['pages']['ctrl']['requestUpdate'] .= 'tx_templavoila_ds,tx_templavoila_next_ds';

	// Configure the referencing wizard to be used in the web_func module:
if (TYPO3_MODE=='BE')	{
	t3lib_extMgm::insertModuleFunction(
		'web_func',
		'tx_templavoila_referenceElementsWizard',
		t3lib_extMgm::extPath($_EXTKEY).'func_wizards/class.tx_templavoila_referenceelementswizard.php',
		'LLL:EXT:templavoila/locallang.xml:wiz_refElements',
		'wiz'
	);
	t3lib_extMgm::addLLrefForTCAdescr('_MOD_web_func','EXT:wizard_crpages/locallang_csh.xml');
}


###########################
## EXTENSION: dam
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/dam/ext_tables.php
###########################

$_EXTKEY = 'dam';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');



if (!defined ('PATH_txdam')) {
	define('PATH_txdam', t3lib_extMgm::extPath('dam'));
}

if (!defined ('PATH_txdam_rel')) {
	define('PATH_txdam_rel', t3lib_extMgm::extRelPath('dam'));
}

if (!defined ('PATH_txdam_siteRel')) {
	define('PATH_txdam_siteRel', t3lib_extMgm::siteRelPath('dam'));
}




	// extend beusers for access control
$tempColumns = array(
	'tx_dam_mountpoints' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:dam/locallang_db.xml:label.tx_dam_mountpoints',
		'config' => $GLOBALS['T3_VAR']['ext']['dam']['TCA']['mountpoints_config'],
	),
);

t3lib_div::loadTCA('be_groups');
t3lib_extMgm::addTCAcolumns('be_groups',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('be_groups','tx_dam_mountpoints','','after:file_mountpoints');

t3lib_div::loadTCA('be_users');
t3lib_extMgm::addTCAcolumns('be_users',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('be_users','tx_dam_mountpoints','','after:fileoper_perms');

unset($tempColumns);



t3lib_div::loadTCA('tt_content');

	// extend tt_content with fields for general usage
$tempColumns = array(
	'tx_dam_images' => txdam_getMediaTCA('image_field', 'tx_dam_images'),
	'tx_dam_files' => txdam_getMediaTCA('media_field', 'tx_dam_files'),
);
if (!isset($TCA['tt_content']['columns']['ce_flexform'])) {
	$tempColumns['ce_flexform'] = array(
		'l10n_display' => 'hideDiff',
		'exclude' => 1,
		'label' => 'LLL:EXT:dam/lib/locallang.xml:options',
		'config' => array (
			'type' => 'flex',
			'ds_pointerField' => 'CType',
			'ds' => array(
				'default' => '
					<T3DataStructure>
					  <ROOT>
					    <type>array</type>
					    <el>
							<!-- Repeat an element like "xmlTitle" beneath for as many elements you like. Remember to name them uniquely  -->
					      <xmlTitle>
							<TCEforms>
								<label>The Title:</label>
								<config>
									<type>input</type>
									<size>48</size>
								</config>
							</TCEforms>
					      </xmlTitle>
					    </el>
					  </ROOT>
					</T3DataStructure>
				',
			)
		)
	);
}

t3lib_extMgm::addTCAcolumns('tt_content',$tempColumns,1);

unset($tempColumns);

	// Adding soft reference keys to tt_content configuration
require_once(PATH_txdam.'binding/softref/ext_tables.php');

if (TYPO3_MODE === 'BE')	{

		// this forces the DAM sysfolder to be created if not yet available
	$temp_damFolder = tx_dam_db::getPid();
	if ($TYPO3_CONF_VARS['EXTCONF']['dam']['setup']['hideMediaFolder']) {
		t3lib_extMgm::addUserTSConfig('
			options.hideRecords.pages = '.$temp_damFolder.'
		');
	}

		// add module after 'File'
	if (!isset($TBE_MODULES['txdamM1']))	{
		$temp_TBE_MODULES = array();
		foreach($TBE_MODULES as $key => $val) {
			if ($key === 'file') {
				$temp_TBE_MODULES[$key] = $val;
				$temp_TBE_MODULES['txdamM1'] = $val;
			} else {
				$temp_TBE_MODULES[$key] = $val;
			}
		}

			// remove File>Filelist module
		if(!$TYPO3_CONF_VARS['EXTCONF']['dam']['setup']['file_filelist']) {
			unset($temp_TBE_MODULES['file']);
		}
		$TBE_MODULES = $temp_TBE_MODULES;
		unset($temp_TBE_MODULES);
	}

		// add main module
	t3lib_extMgm::addModule('txdamM1','','',PATH_txdam.'mod_main/');


		// add file module
	t3lib_extMgm::addModule('txdamM1','file','',PATH_txdam.'mod_file/');

	t3lib_extMgm::insertModuleFunction(
		'txdamM1_file',
		'tx_dam_file_list',
		PATH_txdam.'modfunc_file_list/class.tx_dam_file_list.php',
		'LLL:EXT:dam/modfunc_file_list/locallang.xml:tx_dam_file_list.title'
	);

	t3lib_extMgm::insertModuleFunction(
		'txdamM1_file',
		'tx_dam_file_upload',
		PATH_txdam.'modfunc_file_upload/class.tx_dam_file_upload.php',
		'LLL:EXT:dam/modfunc_file_upload/locallang.xml:tx_dam_file_upload.title'
	);


		// add list module
	t3lib_extMgm::addModule('txdamM1','list','',PATH_txdam.'mod_list/');

		// insert module functions into list module
	t3lib_extMgm::insertModuleFunction(
		'txdamM1_list',
		'tx_dam_list_list',
		PATH_txdam.'modfunc_list_list/class.tx_dam_list_list.php',
		'LLL:EXT:dam/modfunc_list_list/locallang.xml:tx_dam_list_list.title'
	);

	t3lib_extMgm::insertModuleFunction(
		'txdamM1_list',
		'tx_dam_list_thumbs',
		PATH_txdam.'modfunc_list_thumbs/class.tx_dam_list_thumbs.php',
		'LLL:EXT:dam/modfunc_list_thumbs/locallang.xml:tx_dam_list_thumbs.title'
	);

//	t3lib_extMgm::insertModuleFunction(
//		'txdamM1_list',
//		'tx_dam_list_editsel',
//		PATH_txdam.'modfunc_list_editsel/class.tx_dam_list_editsel.php',
//		'LLL:EXT:dam/modfunc_list_editsel/locallang.xml:tx_dam_list_editsel.title'
//	);

	t3lib_extMgm::insertModuleFunction(
		'txdamM1_list',
		'tx_dam_list_batch',
		PATH_txdam.'modfunc_list_batch/class.tx_dam_list_batch.php',
		'LLL:EXT:dam/modfunc_list_batch/locallang.xml:tx_dam_list_batch.title'
	);


		// add the info module and the info->reference modfunc (previously dam_info)
	t3lib_extMgm::addModule('txdamM1', 'info', '', PATH_txdam . 'mod_info/');

	t3lib_extMgm::insertModuleFunction(
		'txdamM1_info',
		'tx_dam_info_reference',
		PATH_txdam.'modfunc_info_reference/class.tx_dam_info_reference.php',
		'LLL:EXT:dam/modfunc_info_reference/locallang.xml:tx_dam_info_reference.title'
	);

	t3lib_extMgm::addModule('txdamM1','tools','',PATH_txdam.'mod_tools/');

	if(t3lib_extMgm::isLoaded('dam_index')) {
		t3lib_extMgm::insertModuleFunction(
			'txdamM1_tools',
			'tx_dam_tools_indexsetup',
			PATH_txdam.'modfunc_tools_indexsetup/class.tx_dam_tools_indexsetup.php',
			'LLL:EXT:dam/modfunc_tools_indexsetup/locallang.xml:tx_dam_tools_indexsetup.title'
		);
	}

	t3lib_extMgm::insertModuleFunction(
		'txdamM1_tools',
		'tx_dam_tools_indexupdate',
		PATH_txdam.'modfunc_tools_indexupdate/class.tx_dam_tools_indexupdate.php',
		'LLL:EXT:dam/modfunc_tools_indexupdate/locallang.xml:tx_dam_tools_indexupdate.title'
	);

	t3lib_extMgm::insertModuleFunction(
		'txdamM1_tools',
		'tx_dam_tools_config',
		PATH_txdam.'modfunc_tools_config/class.tx_dam_tools_config.php',
		'LLL:EXT:dam/modfunc_tools_config/locallang.xml:tx_dam_tools_config.title'
	);

	t3lib_extMgm::insertModuleFunction(
		'txdamM1_tools',
		'tx_dam_tools_serviceinfo',
		PATH_txdam.'modfunc_tools_serviceinfo/class.tx_dam_tools_serviceinfo.php',
		'LLL:EXT:dam/lib/locallang.xml:serviceinfo'
	);

	t3lib_extMgm::insertModuleFunction(
		'txdamM1_tools',
		'tx_dam_tools_mimetypes',
		PATH_txdam.'modfunc_tools_mimetypes/class.tx_dam_tools_mimetypes.php',
		'LLL:EXT:dam/lib/locallang.xml:mimetypes'
	);


		// command modules (invisible)
	t3lib_extMgm::addModule('txdamM1','cmd','',PATH_txdam.'mod_cmd/');

	t3lib_extMgm::insertModuleFunction(
		'txdamM1_cmd',
		'tx_dam_cmd_nothing',
		PATH_txdam.'mod_cmd/class.tx_dam_cmd_nothing.php',
		'LLL:EXT:dam/mod_cmd/locallang.xml:tx_dam_cmd_nothing.title'
	);

		// file command modules (invisible)
	t3lib_extMgm::insertModuleFunction(
		'txdamM1_cmd',
		'tx_dam_cmd_filerename',
		PATH_txdam.'mod_cmd/class.tx_dam_cmd_filerename.php',
		'LLL:EXT:dam/mod_cmd/locallang.xml:tx_dam_cmd_filerename.title'
	);
	t3lib_extMgm::insertModuleFunction(
		'txdamM1_cmd',
		'tx_dam_cmd_filereplace',
		PATH_txdam.'mod_cmd/class.tx_dam_cmd_filereplace.php',
		'LLL:EXT:dam/mod_cmd/locallang.xml:tx_dam_cmd_filereplace.title'
	);
	t3lib_extMgm::insertModuleFunction(
		'txdamM1_cmd',
		'tx_dam_cmd_filedelete',
		PATH_txdam.'mod_cmd/class.tx_dam_cmd_filedelete.php',
		'LLL:EXT:dam/mod_cmd/locallang.xml:tx_dam_cmd_filedelete.title'
	);
	t3lib_extMgm::insertModuleFunction(
		'txdamM1_cmd',
		'tx_dam_cmd_filecopy',
		PATH_txdam.'mod_cmd/class.tx_dam_cmd_filecopymove.php',
		'LLL:EXT:dam/mod_cmd/locallang.xml:tx_dam_cmd_filecopy.title'
	);
	t3lib_extMgm::insertModuleFunction(
		'txdamM1_cmd',
		'tx_dam_cmd_filemove',
		PATH_txdam.'mod_cmd/class.tx_dam_cmd_filecopymove.php',
		'LLL:EXT:dam/mod_cmd/locallang.xml:tx_dam_cmd_filemove.title'
	);
	t3lib_extMgm::insertModuleFunction(
		'txdamM1_cmd',
		'tx_dam_cmd_filenew',
		PATH_txdam.'mod_cmd/class.tx_dam_cmd_filenew.php',
		'LLL:EXT:dam/mod_cmd/locallang.xml:tx_dam_cmd_filenew.title'
	);

		// folder command modules (invisible)
	t3lib_extMgm::insertModuleFunction(
		'txdamM1_cmd',
		'tx_dam_cmd_foldernew',
		PATH_txdam.'mod_cmd/class.tx_dam_cmd_foldernew.php',
		'LLL:EXT:dam/mod_cmd/locallang.xml:tx_dam_cmd_foldernew.title'
	);
	t3lib_extMgm::insertModuleFunction(
		'txdamM1_cmd',
		'tx_dam_cmd_folderdelete',
		PATH_txdam.'mod_cmd/class.tx_dam_cmd_folderdelete.php',
		'LLL:EXT:dam/mod_cmd/locallang.xml:tx_dam_cmd_filerename.title'
	);
	t3lib_extMgm::insertModuleFunction(
		'txdamM1_cmd',
		'tx_dam_cmd_folderrename',
		PATH_txdam.'mod_cmd/class.tx_dam_cmd_folderrename.php',
		'LLL:EXT:dam/mod_cmd/locallang.xml:tx_dam_cmd_filerename.title'
	);


		// add context menu
	$GLOBALS['TBE_MODULES_EXT']['xMOD_alt_clickmenu']['extendCMclasses'][]=array(
		'name' => 'tx_dam_cm_record',
		'path' => PATH_txdam.'binding/be/class.tx_dam_cm_record.php'
	);
	$GLOBALS['TBE_MODULES_EXT']['xMOD_alt_clickmenu']['extendCMclasses'][]=array(
		'name' => 'tx_dam_cm_file',
		'path' => PATH_txdam.'binding/be/class.tx_dam_cm_file.php'
	);


		// media folder type and icon
	if(t3lib_div::int_from_ver(TYPO3_version) < 4004000) {
		$ICON_TYPES['dam'] = array('icon' => PATH_txdam_rel.'modules_dam.gif');
	} else {
		t3lib_SpriteManager::addTcaTypeIcon('pages', 'contains-dam', PATH_txdam_rel.'modules_dam.gif');
	}
	$TCA['pages']['columns']['module']['config']['items'][] = array('Media', 'dam', PATH_txdam_rel.'modules_dam.gif');




		// language hotlist
	$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:dam/binding/tce/class.tx_dam_tce_languagehotlist.php:&tx_dam_tce_languagehotlist';



	tx_dam::register_action ('tx_dam_action_renameFolder',    'EXT:dam/components/class.tx_dam_actionsFolder.php:&tx_dam_action_renameFolder');
	tx_dam::register_action ('tx_dam_action_deleteFolder',    'EXT:dam/components/class.tx_dam_actionsFolder.php:&tx_dam_action_deleteFolder');
	tx_dam::register_action ('tx_dam_action_newFolder',       'EXT:dam/components/class.tx_dam_actionsFolder.php:&tx_dam_action_newFolder');

	tx_dam::register_action ('tx_dam_action_newTextfile',     'EXT:dam/components/class.tx_dam_actionsFile.php:&tx_dam_action_newTextfile');
	tx_dam::register_action ('tx_dam_action_editFileRecord',  'EXT:dam/components/class.tx_dam_actionsFile.php:&tx_dam_action_editFileRecord');
	tx_dam::register_action ('tx_dam_action_viewFile',        'EXT:dam/components/class.tx_dam_actionsFile.php:&tx_dam_action_viewFile');
	tx_dam::register_action ('tx_dam_action_copyFile',      'EXT:dam/components/class.tx_dam_actionsFile.php:&tx_dam_action_copyFile');
	tx_dam::register_action ('tx_dam_action_editFile',        'EXT:dam/components/class.tx_dam_actionsFile.php:&tx_dam_action_editFile');
	tx_dam::register_action ('tx_dam_action_infoFile',        'EXT:dam/components/class.tx_dam_actionsFile.php:&tx_dam_action_infoFile');
	tx_dam::register_action ('tx_dam_action_moveFile',      'EXT:dam/components/class.tx_dam_actionsFile.php:&tx_dam_action_moveFile');
	tx_dam::register_action ('tx_dam_action_renameFile',      'EXT:dam/components/class.tx_dam_actionsFile.php:&tx_dam_action_renameFile');
	tx_dam::register_action ('tx_dam_action_replaceFile',     'EXT:dam/components/class.tx_dam_actionsFile.php:&tx_dam_action_replaceFile');
	tx_dam::register_action ('tx_dam_action_deleteFile',      'EXT:dam/components/class.tx_dam_actionsFile.php:&tx_dam_action_deleteFile');
#	tx_dam::register_action ('tx_dam_action_deleteFileQuick', 'EXT:dam/components/class.tx_dam_actionsFile.php:&tx_dam_action_deleteFileQuick');

	tx_dam::register_action ('tx_dam_action_localizeRec',     'EXT:dam/components/class.tx_dam_actionsRecord.php:&tx_dam_action_localizeRec');
	tx_dam::register_action ('tx_dam_action_editRec',         'EXT:dam/components/class.tx_dam_actionsRecord.php:&tx_dam_action_editRec');
	tx_dam::register_action ('tx_dam_action_editRecPopup',    'EXT:dam/components/class.tx_dam_actionsRecord.php:&tx_dam_action_editRecPopup');
	tx_dam::register_action ('tx_dam_action_viewFileRec',     'EXT:dam/components/class.tx_dam_actionsRecord.php:&tx_dam_action_viewFileRec');
	tx_dam::register_action ('tx_dam_action_editFileRec',     'EXT:dam/components/class.tx_dam_actionsRecord.php:&tx_dam_action_editFileRec');
	tx_dam::register_action ('tx_dam_action_infoRec',         'EXT:dam/components/class.tx_dam_actionsRecord.php:&tx_dam_action_infoRec');
	tx_dam::register_action ('tx_dam_action_cmSubFile',       'EXT:dam/components/class.tx_dam_actionsRecord.php:&tx_dam_action_cmSubFile');
	tx_dam::register_action ('tx_dam_action_revertRec',       'EXT:dam/components/class.tx_dam_actionsRecord.php:&tx_dam_action_revertRec');
	tx_dam::register_action ('tx_dam_action_hideRec',         'EXT:dam/components/class.tx_dam_actionsRecord.php:&tx_dam_action_hideRec');
	tx_dam::register_action ('tx_dam_action_renameFileRec',   'EXT:dam/components/class.tx_dam_actionsRecord.php:&tx_dam_action_renameFileRec');
	tx_dam::register_action ('tx_dam_action_replaceFileRec',  'EXT:dam/components/class.tx_dam_actionsRecord.php:&tx_dam_action_replaceFileRec');
	tx_dam::register_action ('tx_dam_action_deleteRec',       'EXT:dam/components/class.tx_dam_actionsRecord.php:&tx_dam_action_deleteRec');
#	tx_dam::register_action ('tx_dam_action_deleteQuickRec',  'EXT:dam/components/class.tx_dam_actionsRecord.php:&tx_dam_action_deleteQuickRec');
	tx_dam::register_action ('tx_dam_action_lockWarningRec',  'EXT:dam/components/class.tx_dam_actionsRecord.php:&tx_dam_action_lockWarningRec');

	tx_dam::register_action ('tx_dam_multiaction_hideRec',    'EXT:dam/components/class.tx_dam_multiActionsRecord.php:&tx_dam_multiaction_hideRec');
	tx_dam::register_action ('tx_dam_multiaction_unHideRec',  'EXT:dam/components/class.tx_dam_multiActionsRecord.php:&tx_dam_multiaction_unHideRec');
	tx_dam::register_action ('tx_dam_multiaction_copyRec',    'EXT:dam/components/class.tx_dam_multiActionsRecord.php:&tx_dam_multiaction_copyRec');
	tx_dam::register_action ('tx_dam_multiaction_moveRec',    'EXT:dam/components/class.tx_dam_multiActionsRecord.php:&tx_dam_multiaction_moveRec');
	tx_dam::register_action ('tx_dam_multiaction_deleteRec',  'EXT:dam/components/class.tx_dam_multiActionsRecord.php:&tx_dam_multiaction_deleteRec');

	tx_dam::register_action ('tx_dam_multiaction_copyFile',   'EXT:dam/components/class.tx_dam_multiActionsFile.php:&tx_dam_multiaction_copyFile');
	tx_dam::register_action ('tx_dam_multiaction_moveFile',   'EXT:dam/components/class.tx_dam_multiActionsFile.php:&tx_dam_multiaction_moveFile');
	tx_dam::register_action ('tx_dam_multiaction_deleteFile', 'EXT:dam/components/class.tx_dam_multiActionsFile.php:&tx_dam_multiaction_deleteFile');



	tx_dam::register_previewer ('tx_dam_previewerImage', 'EXT:dam/components/class.tx_dam_previewerImage.php:&tx_dam_previewerImage');
	tx_dam::register_previewer ('tx_dam_previewerMP3',   'EXT:dam/components/class.tx_dam_previewerMP3.php:&tx_dam_previewerMP3');



	tx_dam::register_editor ('tx_dam_edit_text',   'EXT:dam/mod_edit/class.tx_dam_edit_text.php:&tx_dam_edit_text');

}




tx_dam::register_mediaTable ('tx_dam');
#t3lib_extMgm::addToInsertRecords('tx_dam');

t3lib_extMgm::addLLrefForTCAdescr('tx_dam','EXT:dam/locallang_csh_dam.xml');

$TCA['tx_dam'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'type' => 'media_type',
#		'sortby' => 'sorting',
		'default_sortby' => 'ORDER BY title',
		'delete' => 'deleted',

		'versioningWS' => true,
		'shadowColumnsForNewPlaceholders' => 'sys_language_uid,l18n_parent,media_type,file_type,file_name,file_path,file_mime_type,file_mime_subtype',

		'transOrigPointerField' => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		'languageField' => 'sys_language_uid',

		'useColumnsForDefaultValues' => '',

		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
		),
		'dividers2tabs' => '1',
		'typeicon_column' => 'media_type',
		'typeicons' => array(
			'0' => PATH_txdam_rel.'i/18/mtype_undefined.gif',
			'1' => PATH_txdam_rel.'i/18/mtype_text.gif',
			'2' => PATH_txdam_rel.'i/18/mtype_image.gif',
			'3' => PATH_txdam_rel.'i/18/mtype_audio.gif',
			'4' => PATH_txdam_rel.'i/18/mtype_video.gif',
			'5' => PATH_txdam_rel.'i/18/mtype_interactive.gif',
			'6' => PATH_txdam_rel.'i/18/mtype_service.gif',
			'7' => PATH_txdam_rel.'i/18/mtype_font.gif',
			'8' => PATH_txdam_rel.'i/18/mtype_model.gif',
			'9' => PATH_txdam_rel.'i/18/mtype_dataset.gif',
			'10' => PATH_txdam_rel.'i/18/mtype_collection.gif',
			'11' => PATH_txdam_rel.'i/18/mtype_software.gif',
			'12' => PATH_txdam_rel.'i/18/mtype_application.gif',
		),

		'dynamicConfigFile' => PATH_txdam.'tca.php',
		'iconfile' => PATH_txdam_rel.'icon_tx_dam.gif',
	),
	'feInterface' => array(
		'fe_admin_fieldList' => 'hidden, starttime, endtime, fe_group, media_type, title, file_type',
	),
	'txdamInterface' => array(
		'index_fieldList' => 'title,keywords,description,caption,alt_text,file_orig_location,file_orig_loc_desc,ident,creator,publisher,copyright,instructions,date_cr,date_mod,loc_desc,loc_country,loc_city,language,category',
		'info_fieldList_add' => '',
// currently unused		'info_displayFields_exclude' => 'category',
		'info_displayFields_isNonEditable' => 'media_type,thumb,file_usage',
	),
);


tx_dam::register_mediaTable ('tx_dam_cat');

$TCA['tx_dam_cat'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_cat_item',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'default_sortby' => 'ORDER BY sorting,title',
		'delete' => 'deleted',

		'treeParentField' => 'parent_id',

		'versioningWS' => true,

		'transOrigPointerField' => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		'languageField' => 'sys_language_uid',

		'enablecolumns' => array(
			'disabled' => 'hidden',
			'fe_group' => 'fe_group',
		),
		'dividers2tabs' => '1',
		'dynamicConfigFile' => PATH_txdam.'tca.php',
		'iconfile' => PATH_txdam_rel.'icon_tx_dam_cat.gif',
	),
	'feInterface' => array(
		'fe_admin_fieldList' => 'hidden, fe_group, title',
	)
);



tx_dam::register_mediaTable ('tx_dam_metypes_avail');

$TCA['tx_dam_metypes_avail'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:dam/lib/locallang.xml:mediaTypes',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'sortby' => 'sorting',
		'default_sortby' => 'ORDER BY sorting,title',

		'treeParentField' => 'parent_id',

		'dynamicConfigFile' => PATH_txdam.'tca.php',
		'iconfile' => PATH_txdam_rel.'i/mediafolder.gif',
	),
);



tx_dam::register_mediaTable ('tx_dam_selection');

$TCA['tx_dam_selection'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_selection',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'type' => 'type',
		'versioning' => '0',
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
		),
		'dynamicConfigFile' => PATH_txdam.'tca.php',
		'iconfile' => PATH_txdam_rel.'icon_tx_dam_selection.gif',
	),
	'feInterface' => array(
		'fe_admin_fieldList' => 'hidden, starttime, endtime, fe_group, type, title, definition',
	)
);

$TCA['tx_dam_media_types'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_media_types',
		'label' => 'ext',
		'versioning' => '0',
		'rootLevel'	=> '1',
		'dynamicConfigFile' => PATH_txdam.'tca.php',
		'iconfile' => PATH_txdam_rel.'i/mimetype.gif',
	),
	'feInterface' => array(
		'fe_admin_fieldList' => 'ext, mime, type, icon',
	)
);




###########################
## EXTENSION: rsaauth
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/rsaauth/ext_tables.php
###########################

$_EXTKEY = 'rsaauth';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) {
	die('Access denied.');
}

// Define the table for keys. Make sure that it cannot be edited or seen by
// any user in any way.
$TCA['tx_rsaauth_keys'] = array (
	'ctrl' => array (
		'adminOnly' => true,
		'hideTable' => true,
		'is_static' => true,
		'label' => 'uid',
		'readOnly' => true,
		'rootLevel' => 1,
		'title' => 'Oops! You should not see this!'
	),
	'columns' => array(
	),
	'types' => array(
		'0' => array(
			'showitem' => ''
		)
	)
);


###########################
## EXTENSION: saltedpasswords
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/saltedpasswords/ext_tables.php
###########################

$_EXTKEY = 'saltedpasswords';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_div::loadTCA('fe_users');
$TCA['fe_users']['columns']['password']['config']['max'] = 60;

if (tx_saltedpasswords_div::isUsageEnabled('FE')) {
	$TCA['fe_users']['columns']['password']['config']['eval'] = 'trim,required,tx_saltedpasswords_eval_fe,password';
}

t3lib_div::loadTCA('be_users');
$TCA['be_users']['columns']['password']['config']['max'] = 60;

if (tx_saltedpasswords_div::isUsageEnabled('BE')) {
	$TCA['be_users']['columns']['password']['config']['eval'] = 'trim,required,tx_saltedpasswords_eval_be,password';

		// Prevent md5 hashing on client side via JS
	$GLOBALS['TYPO3_USER_SETTINGS']['columns']['password']['eval'] = '';
	$GLOBALS['TYPO3_USER_SETTINGS']['columns']['password2']['eval'] = '';
}



###########################
## EXTENSION: go_tsconfig
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3conf/ext/go_tsconfig/ext_tables.php
###########################

$_EXTKEY = 'go_tsconfig';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
$tempColumns = array (
	// #
	// ### Mansoor Ahmad @ Gosign media. GmbH - SEO Robots
	// #
	'robots' => array (
		"exclude" => 0,
		"label" => "LLL:EXT:go_tsconfig/locallang_db.xml:pages.tx_gotsconfig_robots",
		"config" => Array (
			"type" => "radio",
			"items" => Array (
				Array("LLL:EXT:go_tsconfig/locallang_db.xml:pages.tx_gotsconfig_robots.0", ""),
				Array("LLL:EXT:go_tsconfig/locallang_db.xml:pages.tx_gotsconfig_robots.1", "index, follow"),
				Array("LLL:EXT:go_tsconfig/locallang_db.xml:pages.tx_gotsconfig_robots.2", "noindex, follow"),
				Array("LLL:EXT:go_tsconfig/locallang_db.xml:pages.tx_gotsconfig_robots.3", "index, nofollow"),
				Array("LLL:EXT:go_tsconfig/locallang_db.xml:pages.tx_gotsconfig_robots.4", "noindex, nofollow"),
			),
			"default" => "",
		)
	),
);

t3lib_div::loadTCA('pages');
t3lib_extMgm::addTCAcolumns('pages',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('pages','robots', '', 'before:abstract,after:shortcut_mode,after:mount_pid_ol,after:urltype');


###########################
## EXTENSION: static_info_tables_de
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/static_info_tables_de/ext_tables.php
###########################

$_EXTKEY = 'static_info_tables_de';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE'))     die ('Access denied.');

$tempTablesDef = array (
	'static_countries' => array (
		'cn_short_en' => 'cn_short_de',
	),
	'static_country_zones' => array (
		'zn_name_en' => 'zn_name_de',
	),
	'static_currencies' => array (
		'cu_name_en' => 'cu_name_de',
		'cu_sub_name_en' => 'cu_sub_name_de',
	),
	'static_languages' => array (
		'lg_name_en' => 'lg_name_de',
	),
	'static_territories' => array (
		'tr_name_en' => 'tr_name_de',
	)
);

foreach ($tempTablesDef as $tempTable => $tempFieldDef) {
	t3lib_div::loadTCA($tempTable);
	foreach ($tempFieldDef as $tempSourceField => $tempDestField) {
		$tempColumns = array();
		$tempColumns[$tempDestField] = $TCA[$tempTable]['columns'][$tempSourceField];
		$tempColumns[$tempDestField]['label'] = 'LLL:EXT:'.STATIC_INFO_TABLES_DE_EXTkey.'/locallang_db.xml:'.$tempTable.'_item.'.$tempDestField;
		t3lib_extMgm::addTCAcolumns($tempTable, $tempColumns, 1);
		t3lib_extMgm::addToAllTCAtypes($tempTable, $tempDestField, '', 'after:'.$tempSourceField);
	}
}


###########################
## EXTENSION: recycler
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/recycler/ext_tables.php
###########################

$_EXTKEY = 'recycler';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE == 'BE') {

		// add module

	t3lib_extMgm::addModulePath('web_txrecyclerM1',t3lib_extMgm::extPath ($_EXTKEY).'mod1/');
	t3lib_extMgm::addModule('web','txrecyclerM1','',t3lib_extMgm::extPath($_EXTKEY).'mod1/');

}

###########################
## EXTENSION: sys_action
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/sys_action/ext_tables.php
###########################

$_EXTKEY = 'sys_action';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE == 'BE')	{
	$TCA['sys_action'] = array(
		'ctrl' => array(
			'label' => 'title',
			'tstamp' => 'tstamp',
			'default_sortby' => 'ORDER BY title',
			'sortby' => 'sorting',
			'prependAtCopy' => 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy',
			'title' => 'LLL:EXT:sys_action/locallang_tca.php:sys_action',
			'crdate' => 'crdate',
			'cruser_id' => 'cruser_id',
			'adminOnly' => 1,
			'rootLevel' => -1,
			'setToDefaultOnCopy' => 'assign_to_groups',
			'enablecolumns' => array(
				'disabled' => 'hidden'
			),
			'typeicon_classes' => array(
				'default' => 'mimetypes-x-sys_action',
			),
			'type' => 'type',
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'x-sys_action.png',
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php'
		)
	);

	$GLOBALS['TYPO3_CONF_VARS']['typo3/backend.php']['additionalBackendItems'][] = t3lib_extMgm::extPath('sys_action') . 'toolbarmenu/registerToolbarItem.php';

	t3lib_extMgm::addLLrefForTCAdescr('sys_action','EXT:sys_action/locallang_csh_sysaction.xml');

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['taskcenter']['sys_action']['tx_sysaction_task'] = array(
		'title'       => 'LLL:EXT:sys_action/locallang_tca.xml:sys_action',
		'description' => 'LLL:EXT:sys_action/locallang_csh_sysaction.xml:.description',
		'icon'		  => 'EXT:sys_action/x-sys_action.png',
	);
}

###########################
## EXTENSION: version
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/version/ext_tables.php
###########################

$_EXTKEY = 'version';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE=='BE')	{
	$GLOBALS['TBE_MODULES_EXT']['xMOD_alt_clickmenu']['extendCMclasses'][]=array(
		'name' => 'tx_version_cm1',
		'path' => t3lib_extMgm::extPath($_EXTKEY).'class.tx_version_cm1.php'
	);

	if (!t3lib_extMgm::isLoaded('workspaces')) {
		t3lib_extMgm::addModule('web', 'txversionM1', '', t3lib_extMgm::extPath($_EXTKEY) . 'cm1/');
	}
}

###########################
## EXTENSION: dam_catedit
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3conf/ext/dam_catedit/ext_tables.php
###########################

$_EXTKEY = 'dam_catedit';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=='BE')	{

	t3lib_extMgm::addModule('txdamM1','txdamcateditM1','before:tools',t3lib_extMgm::extPath($_EXTKEY).'mod1/');


	// Command module

	t3lib_extMgm::addModule('web','txdamcateditCmd','',t3lib_extMgm::extPath($_EXTKEY).'mod_cmd/');

	t3lib_extMgm::insertModuleFunction(
		'web_txdamcateditCmd',
		'tx_damcatedit_cmd_nothing',
		t3lib_extMgm::extPath($_EXTKEY).'mod_cmd/class.tx_damcatedit_cmd_nothing.php',
		'LLL:EXT:dam_catedit/mod_cmd/locallang.xml:tx_damcatedit_cmd_nothing.title'
	);

	t3lib_extMgm::insertModuleFunction(
		'web_txdamcateditCmd',
		'tx_damcatedit_cmd_new',
		t3lib_extMgm::extPath($_EXTKEY).'mod_cmd/class.tx_damcatedit_cmd_new.php',
		'LLL:EXT:dam_catedit/mod_cmd/locallang.xml:tx_damcatedit_cmd_new.title'
	);

		// adds the clickMenu output that is specific for tx_dam_cat
	$GLOBALS['TBE_MODULES_EXT']['xMOD_alt_clickmenu']['extendCMclasses'][] = array(
		'name' => 'tx_damcatedit_cm',
		'path' => t3lib_extMgm::extPath($_EXTKEY).'lib/class.tx_damcatedit_cm.php'
    );
	
	
}

###########################
## EXTENSION: dam_index
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3conf/ext/dam_index/ext_tables.php
###########################

$_EXTKEY = 'dam_index';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=='BE')	{


	$tempSetup =  unserialize($_EXTCONF);

	if ($tempSetup['add_media_file_indexing']) {

		t3lib_extMgm::insertModuleFunction(
			'txdamM1_file',
			'tx_damindex_index',
			t3lib_extMgm::extPath($_EXTKEY).'modfunc_index/class.tx_damindex_index.php',
			'LLL:EXT:dam_index/modfunc_index/locallang.xml:tx_damindex_index.title'
		);
	}

	if ($tempSetup['add_media_indexing']) {
		t3lib_extMgm::addModule('txdamM1','index','before:tools',t3lib_extMgm::extPath($_EXTKEY).'mod1/');

		t3lib_extMgm::insertModuleFunction(
			'txdamM1_index',
			'tx_damindex_index',
			t3lib_extMgm::extPath($_EXTKEY).'modfunc_index/class.tx_damindex_index.php',
			'LLL:EXT:dam_index/modfunc_index/locallang.xml:tx_damindex_index.title'
		);
	}

}


###########################
## EXTENSION: dam_ttcontent
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3conf/ext/dam_ttcontent/ext_tables.php
###########################

$_EXTKEY = 'dam_ttcontent';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


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


###########################
## EXTENSION: lfeditor
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3conf/ext/lfeditor/ext_tables.php
###########################

$_EXTKEY = 'lfeditor';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if(!defined('TYPO3_MODE')) {
	die('Access denied!!!');
}

if(TYPO3_MODE == 'BE') {
	t3lib_extMgm::addModule('user', 'txlfeditorM1', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
}

###########################
## EXTENSION: kickstarter
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/kickstarter/ext_tables.php
###########################

$_EXTKEY = 'kickstarter';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

if (TYPO3_MODE=="BE")	{
	t3lib_extMgm::insertModuleFunction(
		"tools_em",
		"tx_kickstarter_modfunc1",
		t3lib_extMgm::extPath($_EXTKEY)."modfunc1/class.tx_kickstarter_modfunc1.php",
		"LLL:EXT:kickstarter/locallang_db.xml:moduleFunction.tx_kickstarter_modfunc1"
	);
	t3lib_extMgm::insertModuleFunction(
		"tools_em",
		"tx_kickstarter_modfunc2",
		t3lib_extMgm::extPath($_EXTKEY)."modfunc1/class.tx_kickstarter_modfunc1.php",
		"LLL:EXT:kickstarter/locallang_db.xml:moduleFunction.tx_kickstarter_modfunc2",
		'singleDetails'
	);
}

###########################
## EXTENSION: go_imageedit_be
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3conf/ext/go_imageedit_be/ext_tables.php
###########################

$_EXTKEY = 'go_imageedit_be';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_div::loadTCA('tt_content');

$TCA['tt_content']['columns']['tx_goimageeditbe_croped_image'] = Array(
			'label' => 'LLL:EXT:go_imageedit_be/locallang_db.xml:elementTypeTitle',
			'config' => Array (
				'type' => 'user',
				'userFunc' => 'tx_imageedit->init'
			)
);
$TCA['tt_content']['imageedit']['default']= Array
											(
											"debug" => 0,						//gibt einige Debugwerte aus
											"imgPath" => '../uploads/pics/', 	// vom Backend aus gesehen
											"rootImgPath" => 'uploads/pics/', 	// vom Frontend aus
											
											//Backend
											"selector" => Array(
												"allowCustomRatio" => 1,		//dieses Flag lässt den benutzer 
																				//das Format des Selectors frei bestimmen
																			
												"lockWH" => 0,					//sperrt die Aktuelle Höhe und Breite
												"formatW" => '',				//Aus den Werten <FormatW>, <FormatH> wird beim erstmaligen angucken
												"formatH" => '',				// das Selector-Format berechnet
												
												"minHeight" => 500,
												"minWidth" => 500
											),
											
											"menu" => Array(					
												"displayType" => 0,					// 	1 : HTML-SELECT-BOX;  	
																					//	0 : BUTTONS (nachfolgende Einstellungen)
												"showImageName" => 0,				//Zeigt den Namen des Bildes an
												"showThumbnail" => 1,				//Zeigt ein Thumbnail 
												"showThumbnail_size" => "150x120",	//diesen Ausmaßes
												"showResolution" => 1,				//Zeigt die Auflösung der Bilder im Selector an
												
												"maxImages" =>1000,
											),
											
											"adjustResolution" => Array(
												"enabled" => 1,					//Bild runterrechnen ( 1 ) wenn > maxDisplayedWidth & maxDisplayedHeight
												"maxDisplayedWidth" => "700",		//hoechste unangetastete im Backend Angezeigte Auflösung
												"maxDisplayedHeight" => "400",
											),
											);

$goImageEditShowitem = $TCA['tt_content']['types']['textpic']['showitem'];
$goImageEditShowitem = substr_replace($goImageEditShowitem, '--div--;LLL:EXT:go_imageedit_be/locallang_db.xml:tabLabel,tx_goimageeditbe_croped_image,', strrpos($goImageEditShowitem, '--div--'), 0);
$TCA['tt_content']['types']['textpic']['showitem']= $goImageEditShowitem;

$goImageEditShowitem = $TCA['tt_content']['types']['image']['showitem'];
$goImageEditShowitem = substr_replace($goImageEditShowitem, '--div--;LLL:EXT:go_imageedit_be/locallang_db.xml:tabLabel,tx_goimageeditbe_croped_image,', strrpos($goImageEditShowitem, '--div--'), 0);
$TCA['tt_content']['types']['image']['showitem']= $goImageEditShowitem;


###########################
## EXTENSION: go_pibase
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3conf/ext/go_pibase/ext_tables.php
###########################

$_EXTKEY = 'go_pibase';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_div::loadTCA('pages');
$TCA['pages']['columns']['content_from_pid']['exclude'] = 1;

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['columns']['imagecols']['exclude'] = 1;

$TCA['tt_content']['columns']['tx_dam_files']['exclude'] = 1;
$TCA['tt_content']['columns']['tx_dam_files']['config']['show_thumbs'] = 0;
$TCA['tt_content']['columns']['tx_dam_files']['config']['size'] = 1;
$TCA['tt_content']['columns']['tx_dam_files']['config']['maxitems'] = 1;
$TCA['tt_content']['columns']['tx_dam_files']['config']['minitems'] = 0;
$TCA['tt_content']['columns']['tx_dam_files']['config']['autoSizeMax'] = 1;

$TCA['tt_content']['columns']['tx_dam_images']['exclude'] = 1;
$TCA['tt_content']['columns']['tx_dam_images']['config']['show_thumbs'] = 0;
$TCA['tt_content']['columns']['tx_dam_images']['config']['size'] = 1;
$TCA['tt_content']['columns']['tx_dam_images']['config']['maxitems'] = 1;
$TCA['tt_content']['columns']['tx_dam_images']['config']['minitems'] = 0;
$TCA['tt_content']['columns']['tx_dam_images']['config']['autoSizeMax'] = 1;


###########################
## EXTENSION: queo_speedup
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/queo_speedup/ext_tables.php
###########################

$_EXTKEY = 'queo_speedup';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:queo_speedup/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');

###########################
## EXTENSION: info
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/info/ext_tables.php
###########################

$_EXTKEY = 'info';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE === 'BE') {
	t3lib_extMgm::addModule('web', 'info', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
}

###########################
## EXTENSION: perm
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/perm/ext_tables.php
###########################

$_EXTKEY = 'perm';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE === 'BE') {
	t3lib_extMgm::addModule('web', 'perm', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
	$TYPO3_CONF_VARS['BE']['AJAX']['SC_mod_web_perm_ajax::dispatch'] = t3lib_extMgm::extPath($_EXTKEY) . 'mod1/class.sc_mod_web_perm_ajax.php:SC_mod_web_perm_ajax->dispatch';
}

###########################
## EXTENSION: func
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/func/ext_tables.php
###########################

$_EXTKEY = 'func';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE === 'BE') {
	t3lib_extMgm::addModule('web', 'func', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
}

###########################
## EXTENSION: cshmanual
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/cshmanual/ext_tables.php
###########################

$_EXTKEY = 'cshmanual';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=='BE') {
	t3lib_extMgm::addModule('help','cshmanual','top',t3lib_extMgm::extPath($_EXTKEY).'mod/');
}

###########################
## EXTENSION: opendocs
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/opendocs/ext_tables.php
###########################

$_EXTKEY = 'opendocs';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];



if (!defined('TYPO3_MODE')) 	die('Access denied.');


if(TYPO3_MODE == 'BE') {

	$opendocsPath = t3lib_extMgm::extPath('opendocs');

		// register toolbar item
	$GLOBALS['TYPO3_CONF_VARS']['typo3/backend.php']['additionalBackendItems'][] = $opendocsPath.'registerToolbarItem.php';


		// register AJAX calls
	$GLOBALS['TYPO3_CONF_VARS']['BE']['AJAX']['tx_opendocs::renderMenu']   = $opendocsPath.'class.tx_opendocs.php:tx_opendocs->renderAjax';
	$GLOBALS['TYPO3_CONF_VARS']['BE']['AJAX']['tx_opendocs::closeDocument'] = $opendocsPath.'class.tx_opendocs.php:tx_opendocs->closeDocument';

		// register update signal to update the number of open documents
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_befunc.php']['updateSignalHook']['tx_opendocs::updateNumber'] = $opendocsPath.'class.tx_opendocs.php:tx_opendocs->updateNumberOfOpenDocsHook';


		// register menu module if option is wanted
	$_EXTCONF = unserialize($_EXTCONF);
	if($_EXTCONF['enableModule']) {
		t3lib_extMgm::addModule('user', 'doc', 'after:ws', $opendocsPath.'mod/');
	}
}


###########################
## EXTENSION: scheduler
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/scheduler/ext_tables.php
###########################

$_EXTKEY = 'scheduler';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


/* $Id: ext_tables.php 6536 2009-11-25 14:07:18Z stucki $ */

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE == 'BE') {
		// Add module
	t3lib_extMgm::addModule('tools', 'txschedulerM1', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');

		// Add context sensitive help (csh) to the backend module
	t3lib_extMgm::addLLrefForTCAdescr('_MOD_tools_txschedulerM1', 'EXT:' . $_EXTKEY . '/mod1/locallang_csh_scheduler.xml');
}

###########################
## EXTENSION: fluid
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/fluid/ext_tables.php
###########################

$_EXTKEY = 'fluid';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) die ('Access denied.');

t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Fluid: Default Ajax Configuration');

###########################
## EXTENSION: workspaces
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/workspaces/ext_tables.php
###########################

$_EXTKEY = 'workspaces';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
	// avoid that this block is loaded in the frontend or within the upgrade-wizards
if (TYPO3_MODE == 'BE' && !(TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_INSTALL)) {
	/**
	* Registers a Backend Module
	*/
	Tx_Extbase_Utility_Extension::registerModule(
		$_EXTKEY,
		'web',	// Make module a submodule of 'web'
		'workspaces',	// Submodule key
		'before:info', // Position
		array(
				// An array holding the controller-action-combinations that are accessible
			'Review'		=> 'index,fullIndex,singleIndex',
			'Preview'		=> 'index,newPage'
		),
		array(
			'access' => 'user,group',
			'icon'   => 'EXT:workspaces/Resources/Public/Images/moduleicon.gif',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_mod.xml',
			'navigationComponentId' => 'typo3-pagetree',
		)
	);

		// register ExtDirect
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ExtDirect']['TYPO3.Workspaces.ExtDirect'] = t3lib_extMgm::extPath($_EXTKEY) . 'Classes/ExtDirect/Server.php:tx_Workspaces_ExtDirect_Server';
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ExtDirect']['TYPO3.Workspaces.ExtDirectActions'] = t3lib_extMgm::extPath($_EXTKEY) . 'Classes/ExtDirect/ActionHandler.php:tx_Workspaces_ExtDirect_ActionHandler';
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ExtDirect']['TYPO3.Workspaces.ExtDirectMassActions'] = t3lib_extMgm::extPath($_EXTKEY) . 'Classes/ExtDirect/MassActionHandler.php:tx_Workspaces_ExtDirect_MassActionHandler';
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ExtDirect']['TYPO3.Ajax.ExtDirect.ToolbarMenu'] = t3lib_extMgm::extPath($_EXTKEY) . 'Classes/ExtDirect/ToolbarMenu.php:tx_Workspaces_ExtDirect_ToolbarMenu';

		// register the reports statusprovider
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['configuration'][] = 'Tx_Workspaces_Reports_StatusProvider';


}

/**
 * Table "sys_workspace":
 */
$TCA['sys_workspace'] = array(
	'ctrl' => array(
		'label' => 'title',
		'tstamp' => 'tstamp',
		'title' => 'LLL:EXT:lang/locallang_tca.xml:sys_workspace',
		'adminOnly' => 1,
		'rootLevel' => 1,
		'delete' => 'deleted',
		'iconfile' => 'sys_workspace.png',
		'typeicon_classes' => array(
			'default' => 'mimetypes-x-sys_workspace'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'tca.php',
		'versioningWS_alwaysAllowLiveEdit' => true,
		'dividers2tabs' => true
	)
);

/**
 * Table "sys_workspace_stage":
 * Defines single custom stages which are related to sys_workspace table to create complex working processes
 * This is only the 'header' part (ctrl). The full configuration is found in t3lib/stddb/tbl_be.php
 */
$TCA['sys_workspace_stage'] = array(
	'ctrl' => array(
		'label' => 'title',
		'tstamp' => 'tstamp',
		'sortby' => 'sorting',
		'title' => 'LLL:EXT:workspaces/Resources/Private/Language/locallang_db.xml:sys_workspace_stage',
		'adminOnly' => 1,
		'rootLevel' => 1,
		'hideTable' => TRUE,
		'delete' => 'deleted',
		'iconfile' => 'sys_workspace.png',
		'typeicon_classes' => array(
			'default' => 'mimetypes-x-sys_workspace'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'tca.php',
		'versioningWS_alwaysAllowLiveEdit' => true,
	)
);
	// todo move icons to Core sprite or keep them here and remove the todo note ;)
$icons = array(
	'sendtonextstage' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Images/version-workspace-sendtonextstage.png',
	'sendtoprevstage' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Images/version-workspace-sendtoprevstage.png',
	'generatepreviewlink' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Images/generate-ws-preview-link.png',
);
t3lib_SpriteManager::addSingleIcons($icons, $_EXTKEY);
t3lib_extMgm::addLLrefForTCAdescr('sys_workspace_stage','EXT:workspaces/Resources/Private/Language/locallang_csh_sysws_stage.xml');



###########################
## EXTENSION: realurl
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3conf/ext/realurl/ext_tables.php
###########################

$_EXTKEY = 'realurl';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=='BE')	{
//	t3lib_extMgm::addModule('tools','txrealurlM1','',t3lib_extMgm::extPath($_EXTKEY).'mod1/');

	// Add Web>Info module:
	t3lib_extMgm::insertModuleFunction(
		'web_info',
		'tx_realurl_modfunc1',
		t3lib_extMgm::extPath($_EXTKEY) . 'modfunc1/class.tx_realurl_modfunc1.php',
		'LLL:EXT:realurl/locallang_db.xml:moduleFunction.tx_realurl_modfunc1',
		'function',
		'online'
	);
}

$TCA['pages']['columns'] += array(
	'tx_realurl_pathsegment' => array(
		'label' => 'LLL:EXT:realurl/locallang_db.xml:pages.tx_realurl_pathsegment',
		'displayCond' => 'FIELD:tx_realurl_exclude:!=:1',
		'exclude' => 1,
		'config' => array (
			'type' => 'input',
			'max' => 255,
			'eval' => 'trim,nospace,lower'
		),
	),
	'tx_realurl_pathoverride' => array(
		'label' => 'LLL:EXT:realurl/locallang_db.xml:pages.tx_realurl_path_override',
		'exclude' => 1,
		'config' => array (
			'type' => 'check',
			'items' => array(
				array('', '')
			)
		)
	),
	'tx_realurl_exclude' => array(
		'label' => 'LLL:EXT:realurl/locallang_db.xml:pages.tx_realurl_exclude',
		'exclude' => 1,
		'config' => array (
			'type' => 'check',
			'items' => array(
				array('', '')
			)
		)
	),
	'tx_realurl_nocache' => array(
		'label' => 'LLL:EXT:realurl/locallang_db.xml:pages.tx_realurl_nocache',
		'exclude' => 1,
		'config' => array (
			'type' => 'check',
			'items' => array(
				array('', ''),
			),
		),
	)
);

$TCA['pages']['ctrl']['requestUpdate'] .= ',tx_realurl_exclude';

$TCA['pages']['palettes']['137'] = array(
	'showitem' => 'tx_realurl_pathoverride'
);

if (t3lib_div::compat_version('4.3')) {
	t3lib_extMgm::addFieldsToPalette('pages', '3', 'tx_realurl_nocache', 'after:cache_timeout');
}
if (t3lib_div::compat_version('4.2')) {
	// For 4.2 or new add fields to advanced page only
	t3lib_extMgm::addToAllTCAtypes('pages', 'tx_realurl_pathsegment;;137;;,tx_realurl_exclude', '1', 'after:nav_title');
	t3lib_extMgm::addToAllTCAtypes('pages', 'tx_realurl_pathsegment;;137;;,tx_realurl_exclude', '4,254', 'after:title');
}
else {
	// Put it for standard page
	t3lib_extMgm::addToAllTCAtypes('pages', 'tx_realurl_pathsegment;;137;;,tx_realurl_exclude', '2', 'after:nav_title');
	t3lib_extMgm::addToAllTCAtypes('pages', 'tx_realurl_pathsegment;;137;;,tx_realurl_exclude', '1,5,4,254', 'after:title');
}

t3lib_extMgm::addLLrefForTCAdescr('pages','EXT:realurl/locallang_csh.xml');

$TCA['pages_language_overlay']['columns'] += array(
	'tx_realurl_pathsegment' => array(
		'label' => 'LLL:EXT:realurl/locallang_db.xml:pages.tx_realurl_pathsegment',
		'exclude' => 1,
		'config' => array (
			'type' => 'input',
			'max' => 255,
			'eval' => 'trim,nospace,lower'
		),
	),
);

t3lib_extMgm::addToAllTCAtypes('pages_language_overlay', 'tx_realurl_pathsegment', '', 'after:nav_title');


###########################
## EXTENSION: naw_securedl
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/naw_securedl/ext_tables.php
###########################

$_EXTKEY = 'naw_securedl';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE'))     die ('Access denied.');

$_EXTCONF = unserialize($_EXTCONF);

if (TYPO3_MODE == 'BE' AND $_EXTCONF['log'])	{
	t3lib_extMgm::addModule('tools','txnawsecuredlM1','',t3lib_extMgm::extPath($_EXTKEY).'modLog/');
}

unset ($_EXTCONF);


###########################
## EXTENSION: go_stopcslide
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3/sysext/go_stopcslide/ext_tables.php
###########################

$_EXTKEY = 'go_stopcslide';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types'][$_EXTKEY . '_pi1']['showitem'] = 'CType;;4;button;1-1-1';


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:go_stopcslide/locallang_db.xml:tt_content.go_stopcslide.CType_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'CType');

###########################
## EXTENSION: go_language
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3conf/ext/go_language/ext_tables.php
###########################

$_EXTKEY = 'go_language';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['sys_language']['columns']['disabled_in_menu'] = array (
			'exclude' => 1,
			'label'   => 'LLL:EXT:go_language/locallang_db.xml:disabled_in_menu',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		);


$TCA['sys_language']['types'][1]['showitem'] .= ',disabled_in_menu';

if (TYPO3_MODE == 'BE') {
	$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','sys_language', ''); 
	if ($GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
		t3lib_extMgm::addModulePath('web_txgolanguageM1', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
		
		t3lib_extMgm::addModule('web', 'txgolanguageM1', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
	}
}

###########################
## EXTENSION: tinymce_rte
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3conf/ext/tinymce_rte/ext_tables.php
###########################

$_EXTKEY = 'tinymce_rte';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


	if (!defined ('TYPO3_MODE')) die ('Access denied.');

	// remove of default RTE Fullsize as TinyMCE's fullsize is much better [brought to you by Peter Klein]
	t3lib_div::loadTCA('tt_content');
	unset($TCA['tt_content']['columns']['bodytext']['config']['wizards']['RTE']);

###########################
## EXTENSION: rlmp_tvnotes
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3conf/ext/rlmp_tvnotes/ext_tables.php
###########################

$_EXTKEY = 'rlmp_tvnotes';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::allowTableOnStandardPages('tx_rlmptvnotes_notes');

$TCA['tx_rlmptvnotes_notes'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:rlmp_tvnotes/locallang_db.php:tx_rlmptvnotes_notes',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_rlmptvnotes_notes.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'title, note, beusersread',
	)
);

###########################
## EXTENSION: sys_notepad
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3conf/ext/sys_notepad/ext_tables.php
###########################

$_EXTKEY = 'sys_notepad';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['taskcenter']['sys_notepad']['tx_sysnotepad_task'] = array(
	'title'       => 'LLL:EXT:sys_notepad/task/locallang.xml:mod_note',
	'description' => 'LLL:EXT:sys_notepad/task/locallang.xml:note_helpText',
	'icon'		  => 'EXT:sys_notepad/ext_icon.gif'
);


###########################
## EXTENSION: go_backend_layout
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3conf/ext/go_backend_layout/ext_tables.php
###########################

$_EXTKEY = 'go_backend_layout';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=='BE')	{
	
	// Setting the relative path to the extension in temp. variable:
	$temp_eP = t3lib_extMgm::extRelPath($_EXTKEY);

	$TBE_STYLES['stylesheet2'] = $temp_eP.'go_backend_layout.css';	

	t3lib_div::loadTCA('be_users');
	$TCA['be_users']['columns']['lang']['config']['default'] = 'de';
	
	//t3lib_extMgm::addModule('user', 'gobeconfig', '', t3lib_extMgm::extPath($_EXTKEY) . 'moduls/config/');
}

###########################
## EXTENSION: go_teaser
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3conf/ext/go_teaser/ext_tables.php
###########################

$_EXTKEY = 'go_teaser';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


t3lib_div::loadTCA("tt_content");
$tempColumns = array( 
					'header_rte' => Array (
						'l10n_mode' => 'prefixLangTitle',
						'l10n_cat' => 'text',
						'label' => 'LLL:EXT:go_teaser/locallang_db.xml:tt_content.header_rte',
						'config' => Array (
							'type' => 'text',
							'cols' => '48',
							'rows' => '3',
							'wizards' => Array(
								'_PADDING' => 4,
								'_VALIGN' => 'middle',
								'RTE' => Array(
									'notNewRecords' => 1,
									'RTEonly' => 1,
									'type' => 'script',
									'title' => 'LLL:EXT:cms/locallang_ttc.php:bodytext.W.RTE',
									'icon' => 'wizard_rte2.gif',
									'script' => 'wizard_rte.php',
								),
								'table' => Array(
									'notNewRecords' => 1,
									'enableByTypeConfig' => 1,
									'type' => 'script',
									'title' => 'Table wizard',
									'icon' => 'wizard_table.gif',
									'script' => 'wizard_table.php',
									'params' => array('xmlOutput' => 0)
								),
								'forms' => Array(
									'notNewRecords' => 1,
									'enableByTypeConfig' => 1,
									'type' => 'script',
			#						'hideParent' => array('rows' => 4),
									'title' => 'Forms wizard',
									'icon' => 'wizard_forms.gif',
									'script' => 'wizard_forms.php?special=formtype_mail',
									'params' => array('xmlOutput' => 0)
								)
							),
							'softref' => 'typolink_tag,images,email[subst],url'
						)
					),
					'header_rte2' => Array (
						'l10n_mode' => 'prefixLangTitle',
						'l10n_cat' => 'text',
						'label' => 'LLL:EXT:go_teaser/locallang_db.xml:tt_content.header_rte2',
						'config' => Array (
							'type' => 'text',
							'cols' => '48',
							'rows' => '3',
							'wizards' => Array(
								'_PADDING' => 4,
								'_VALIGN' => 'middle',
								'RTE' => Array(
									'notNewRecords' => 1,
									'RTEonly' => 1,
									'type' => 'script',
									'title' => 'LLL:EXT:cms/locallang_ttc.php:bodytext.W.RTE',
									'icon' => 'wizard_rte2.gif',
									'script' => 'wizard_rte.php',
								),
								'table' => Array(
									'notNewRecords' => 1,
									'enableByTypeConfig' => 1,
									'type' => 'script',
									'title' => 'Table wizard',
									'icon' => 'wizard_table.gif',
									'script' => 'wizard_table.php',
									'params' => array('xmlOutput' => 0)
								),
								'forms' => Array(
									'notNewRecords' => 1,
									'enableByTypeConfig' => 1,
									'type' => 'script',
			#						'hideParent' => array('rows' => 4),
									'title' => 'Forms wizard',
									'icon' => 'wizard_forms.gif',
									'script' => 'wizard_forms.php?special=formtype_mail',
									'params' => array('xmlOutput' => 0)
								)
							),
							'softref' => 'typolink_tag,images,email[subst],url'
						)
					),
					'go_content_image' => txdam_getMediaTCA('image_field', 'go_content_image'),
					'go_teaser_layout' => array (
						'exclude' => 0,
						'label' => 'LLL:EXT:go_teaser/locallang_db.xml:tt_content.tx_goteaser.piBoxTeaser_layout',
						'config' => array (
							'type' => 'select',
							'items' => array (
								array('LLL:EXT:go_teaser/locallang_db.xml:tt_content.tx_goteaser.piThemenTeaser_layout.I.0', '0', t3lib_extMgm::extRelPath('go_teaser').'res/selicon_tt_content_tx_goteaser.piThemenTeaser_layout_0.gif'),
								array('LLL:EXT:go_teaser/locallang_db.xml:tt_content.tx_goteaser.piThemenTeaser_layout.I.1', '1', t3lib_extMgm::extRelPath('go_teaser').'res/selicon_tt_content_tx_goteaser.piThemenTeaser_layout_1.gif')
							),
							'size' => 1,
							'maxitems' => 1,
						)
					),
					'go_content_linktext' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:go_teaser/locallang_db.xml:tt_content.go_content_linktext',
						'config' => array (
							'type' => 'input',
							'size' => '20',
							'max' => '64',
						)
					),
);

t3lib_extMgm::addTCAcolumns('tt_content',$tempColumns,1);


$TCA['tt_content']['columns']['go_content_image']['label'] = 'LLL:EXT:go_teaser/locallang_db.xml:tt_content.go_content_image';
$TCA['tt_content']['columns']['go_content_image']['exclude'] = 1;
$TCA['tt_content']['columns']['go_content_image']['config']['show_thumbs'] = 1;
$TCA['tt_content']['columns']['go_content_image']['config']['size'] = 1;
$TCA['tt_content']['columns']['go_content_image']['config']['maxitems'] = 1;
$TCA['tt_content']['columns']['go_content_image']['config']['minitems'] = 0;
$TCA['tt_content']['columns']['go_content_image']['config']['autoSizeMax'] = 1;



// #
// ### piTeaser
// #
$TCA['tt_content']['types'][$_EXTKEY . '_piTeaser']['showitem'] = 'CType;;;button;1-1-1, tx_damttcontent_files, header_rte, header_rte2;;;richtext:rte_transform[flag=rte_enabled|mode=ts];2-2-2, image_link, 
																			--div--;LLL:EXT:go_imageedit_be/locallang_db.xml:tabLabel, tx_goimageeditbe_croped_image,
																			--div--;LLL:EXT:cms/locallang_tca.xml:pages.tabs.access, starttime, endtime, fe_group';

																			
$TCA['tt_content']['imageedit'][$_EXTKEY.'_piTeaser']= Array
											(
											"debug" => 0,						//gibt einige Debugwerte aus
											"imgPath" => '../uploads/pics/', 	// vom Backend aus gesehen
											"rootImgPath" => 'uploads/pics/', 	// vom Frontend aus
											
											//Backend
											"selector" => Array(
												"allowCustomRatio" => 1,		//dieses Flag lässt den benutzer 
																				//das Format des Selectors frei bestimmen
																			
												"lockWH" => 1,					//sperrt die Aktuelle Höhe und Breite
												"formatW" => '211',				//Aus den Werten <FormatW>, <FormatH> wird beim erstmaligen angucken
												"formatH" => '158',				// das Selector-Format berechnet
												
												"minHeight" => 211,
												"minWidth" => 158
											),
											
											"menu" => Array(					
												"displayType" => 0,					// 	1 : HTML-SELECT-BOX;  	
																					//	0 : BUTTONS (nachfolgende Einstellungen)
												"showImageName" => 0,				//Zeigt den Namen des Bildes an
												"showThumbnail" => 1,				//Zeigt ein Thumbnail 
												"showThumbnail_size" => "211x158",	//diesen Ausmaßes
												"showResolution" => 1,				//Zeigt die Auflösung der Bilder im Selector an
												
												"maxImages" =>1,
											),
											
											"adjustResolution" => Array(
												"enabled" => 1,					//Bild runterrechnen ( 1 ) wenn > maxDisplayedWidth & maxDisplayedHeight
												"maxDisplayedWidth" => "700",		//hoechste unangetastete im Backend Angezeigte Auflösung
												"maxDisplayedHeight" => "400",
											),
	
											);																				
																			
t3lib_extMgm::addPlugin(array(
	'LLL:EXT:go_teaser/locallang_db.xml:tt_content.piTeaser.CType', 
	$_EXTKEY . '_piTeaser',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'piTeaser/icon.gif')
,'CType');


// #
// ### piMyElement
// #
$TCA['tt_content']['types'][$_EXTKEY . '_piMyElement']['showitem'] = 'CType, header, myheader, header_link';

t3lib_div::loadTCA("tt_content");
$tempColumns = array( 
	'myheader' => array(
			'label' => 'LLL:EXT:go_teaser/locallang_db.xml:tt_content.myheader',
			'config' => array(
				'type' => 'input',
				'size' => '50',
				'max' => '256',
			),
	),
);
t3lib_extMgm::addTCAcolumns('tt_content',$tempColumns,1);

t3lib_extMgm::addPlugin(array(
	'LLL:EXT:go_teaser/locallang_db.xml:tt_content.piMyElement.CType', 
	$_EXTKEY . '_piMyElement',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'piMyElement/icon.gif')
,'CType');

###########################
## EXTENSION: formhandler
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3conf/ext/formhandler/ext_tables.php
###########################

$_EXTKEY = 'formhandler';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


/**
 * ext tables config file for ext: "formhandler"
 *
 * @author Reinhard FÃ¼hricht <rf@typoheads.at>

 * @package	Tx_Formhandler
 */
 
 /**
	\mainpage 	
	
	 @version V1.0.0 Beta

	Released under the terms of the GNU General Public License version 2 as published by
	the Free Software Foundation.
	
	The swiss army knife for all kinds of mailforms, completely new written using the MVC concept. 
	Result: Flexibility, Flexibility, Flexibility. Formhandler is a total redesign of the getting-old
	MailformPlus (aka th_mailformplus). Formhandler has now a new core, new architecture, new features.

	Beside the reach set of features provided by Formhandler, you may like the flexibility in the sense
	of possible different configuration. Projects have all their own specificities. One customer want this 
	component while the other one want to have this other one. I think it is very challenging to come up 
	with an extension that is features reach without overloading the code basis.
	
	Formhandler solves the problem by having a very modular approach. The extension is piloted 
	mainly by some nice TypoScript where is is possible to define exactly what to implement. You may
	want to play with some interceptor, finisher, logger, validators etc... For more information,
	you should have a look into the folder "Examples" of the extension which refers many interesting samples.
		
	Latest development version on
	http://forge.typo3.org/repositories/show/extension-formhandler
	  
 */

if (!defined ('TYPO3_MODE')) die ('Access denied.');

if (TYPO3_MODE == 'BE')   {

	# dynamic flexform
	include_once(t3lib_extMgm::extPath($_EXTKEY) . '/Resources/PHP/class.tx_dynaflex.php');
	
	t3lib_div::loadTCA('tt_content');
	
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY . '_pi1'] = 'layout,select_key,pages';
	
	// Add flexform field to plugin options
	$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_pi1'] = 'pi_flexform';
	
	$file = 'FILE:EXT:' . $_EXTKEY . '/Resources/XML/flexform_ds.xml';
	
	// Add flexform DataStructure
	t3lib_extMgm::addPiFlexFormValue($_EXTKEY . '_pi1', $file);

	t3lib_extMgm::addModule('web', 'txformhandlermoduleM1', '', t3lib_extMgm::extPath($_EXTKEY) . 'Classes/Controller/Module/');
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_formhandler_wizicon'] = t3lib_extMgm::extPath($_EXTKEY) . 'Resources/PHP/class.tx_formhandler_wizicon.php';
}

t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/Settings/', 'Example Configuration');
t3lib_extMgm::addPlugin(array('Formhandler', $_EXTKEY . '_pi1'), 'list_type');

$TCA['tx_formhandler_log'] = array (
    'ctrl' => array (
		'title' => 'LLL:EXT:formhandler/Resources/Language/locallang_db.xml:tx_formhandler_log',
		'label' => 'uid',
		'default_sortby' => 'ORDER BY crdate DESC',
		'crdate' => 'crdate',
		'tstamp' => 'tstamp',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'tca.php',
		'readOnly' => 1
	)
);
t3lib_extMgm::allowTableOnStandardPages('tx_formhandler_log');


###########################
## EXTENSION: be_acl
## FILE:      C:/Dokumente und Einstellungen/Gosign/Desktop/TYPO3Winstaller/htdocs/Dummy/typo3conf/ext/be_acl/ext_tables.php
###########################

$_EXTKEY = 'be_acl';
$_EXTCONF = $TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY];


if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

t3lib_extMgm::allowTableOnStandardPages("tx_beacl_acl");

$TCA["tx_beacl_acl"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:be_acl/locallang_db.php:tx_beacl_acl",
		"label" => "uid",
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"type" => "type",
		"default_sortby" => "ORDER BY type",
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_beacl_acl.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "type, object_id, permissions, recursive",
	)
);

?>
