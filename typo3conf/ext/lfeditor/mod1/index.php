<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005-2006 Stefan Galinski (stefan.galinski@frm2.tum.de)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Module 'LFEditor' for the 'lfeditor' extension.
 *
 * $Id: index.php 103 2007-09-05 19:46:07Z fire $
 *
 * @author Stefan Galinski <stefan.galinski@frm2.tum.de>
 */

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 */

// default initialization
unset($MCONF);
require('conf.php');
require($GLOBALS['BACK_PATH'] . 'init.php');
require($GLOBALS['BACK_PATH'] . 'template.php');
require_once(PATH_t3lib . 'class.t3lib_scbase.php');
// This checks permissions and exits if the users has no permission for const.
$GLOBALS['BE_USER']->modAccess($MCONF, 1);

// include language file
if(is_file(t3lib_extMgm::extPath('lfeditor') . 'mod1/locallang.xml'))
	$GLOBALS['LANG']->includeLLFile('EXT:lfeditor/mod1/locallang.xml');
else
	$GLOBALS['LANG']->includeLLFile('EXT:lfeditor/mod1/locallang.php');

/**#@+
/** some needed classes and libraries */
require_once(t3lib_extMgm::extPath('lfeditor') . 'mod1/class.tx_lfeditor_mod1_functions.php');
require_once(t3lib_extMgm::extPath('lfeditor') . 'mod1/class.tx_lfeditor_mod1_template.php');
require_once(t3lib_extMgm::extPath('lfeditor') . 'mod1/class.LFException.php');
require_once(t3lib_extMgm::extPath('lfeditor') . 'mod1/class.sgLib.php');
require_once(t3lib_extMgm::extPath('lfeditor') . 'mod1/class.typo3Lib.php');
require_once(t3lib_extMgm::extPath('lfeditor') . 'res/phpCompat/array_diff_key.php');
require_once(t3lib_extMgm::extPath('lfeditor') . 'res/phpCompat/array_intersect_key.php');
require_once(t3lib_extMgm::extPath('lfeditor') . 'res/zip/zip.lib.php');

// search and include filetype classes
$searchPath = t3lib_extMgm::extPath('lfeditor') . 'mod1/';
if($fhd = opendir($searchPath))
	while($file = readdir($fhd))
		if(preg_match('/^class.tx_lfeditor_mod1_file_.+\.php$/', $file))
			require_once($searchPath . $file);
/**#@-*/

// global variable
/** @var boolean pmktextarea indicator */
$PMKTEXTAREA = false;

/**
 * Module 'LFEditor' for the 'lfeditor' extension
 *
 * @author Stefan Galinski <stefan.galinski@frm2.tum.de>
 * @package Typo3
 * @subpackage tx_lfeditor
 */
class tx_lfeditor_module1 extends t3lib_SCbase {

	#######################################
	############## variables ##############
	#######################################

	/**
	 * @var array page access
	 * @see main()
	 */
	public $pageinfo;

	/**
	 * @var array extension configuration
	 * @see prepareConfig()
	 */
	private $extConfig;

	/**#@+
	/** @var object containers for file, converter and backup object */
	private $fileObj;
	private $convObj;
	private $backupObj;
	/**#@-*/

	#######################################
	############ main functions ###########
	#######################################

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		// prepare configuration
		$this->prepareConfig();

		// set error wrap
		$errorWrap = '<p class="tx-lfeditor-error">|</p>';
		$noticeWrap = '<p class="tx-lfeditor-notice">|</p>';
		LFException::setWrap($errorWrap, $noticeWrap);

		parent::init();
	}

	/**
	 * Main function of the module. Writes the content to $this->content
	 *
	 * @throws LFException raised if access denied
	 * @return void
	 */
	public function main()
	{
		// generate doc object
		$this->doc = t3lib_div::makeInstance('bigDoc');
		$this->doc->backPath = $GLOBALS['BACK_PATH'];
		$this->doc->form = '<form action="" method="post" name="mainForm">';

		// generate main menus
		// this stuff must be done before we set the header code, because the switchInsertType
		// variable will be set at the init process (DONT MOVE THE CODE AWAY!)
		$funcMenu = '<p style="margin-bottom: 5px;">' . $this->getFuncMenu('function') . '</p>';
		$this->menuInsertMode();

		// include WYSIWIG, pmktextarea or normal textareas (with resize bar)
		$this->doc->JScode = '<script type="text/javascript" src="textareaResize.js"></script>';
		if($this->MOD_SETTINGS['insertMode'] == 'tinyMCE') {
			require($GLOBALS['BACK_PATH'] . t3lib_extMgm::extRelPath('tinymce') . 'class.tinymce.php');
			$tinyMCE = new tinyMCE($this->extConfig['pathTinyMCEConfig']);
			if($tinyMCE->checkBrowser())
				$this->doc->JScode = $tinyMCE->getJS();
		}
		elseif($this->MOD_SETTINGS['insertMode'] == 'pmktextarea') {
			$GLOBALS['PMKTEXTAREA'] = true;
			$this->doc->JScode = '
				<script type="text/javascript">
					var ta_init = {
						linenumState: "0",
						lockW: "1",
					};
				</script>';
		}

		// JavaScript
		$this->doc->JScode .= '
			<script type="text/javascript">
				var script_ended = 0;
				function jumpToUrl(URL) {
					document.location = URL;
				}
				var treeHide = ' . $this->extConfig['treeHide'] . ';
				' . file_get_contents('tx_lfeditor_mod1.js') . '
			</script>';

		$this->doc->postCode='
			<script language="javascript" type="text/javascript">
				script_ended = 1;
				if (top.theMenu) top.theMenu.recentuid = ' . intval($this->id) . ';
			</script>';

		// add CSS
		$this->doc->JScode .=
			'<link rel="stylesheet" type="text/css" href="' . $this->extConfig['pathCSS'] . '">';

		// draw the header
		$this->content = $this->doc->startPage($GLOBALS['LANG']->getLL('title'));
		$this->content .= $this->doc->header($GLOBALS['LANG']->getLL('title'));
		$this->content .= $this->doc->spacer(5);

		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id, $this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;
		if((!$this->id || !$access) && (!$GLOBALS['BE_USER']->user['uid'] || $this->id))
			throw new LFException('failure.access.denied');

		// generate the path-information
		$headerSection = '<p>' . $this->doc->getHeader('pages', $this->pageinfo,
			$this->pageinfo['_thePath']) . '</p>';
		$label = $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:labels.path');
		$headerSection .= '<p>' . $label . ': ' .
			t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'], 50) . '</p>';

		// draw the header
		$funcMenu .= $this->getFuncMenu('insertMode');
		$this->content .= $this->doc->funcMenu($headerSection, $funcMenu);

		// render content
		$this->moduleContent();

		// generate shortcut-icon
		if($GLOBALS['BE_USER']->mayMakeShortcut()) {
			$selKeys = implode(',', array_keys($this->MOD_MENU));
			$icon =  $this->doc->makeShortcutIcon('id', $selKeys, $this->MCONF['name']);
			$this->content .= $this->doc->section('', $this->doc->spacer(20) . $icon);
		}
		$this->content .= $this->doc->spacer(10);
	}

	/**
	 * adds some possible stuff to the content and print it out
	 *
	 * @param string extra content (appended at the string)
	 * @return void
	 */
	public function printContent($content='')
	{
		$this->content .= $content;
		$this->content .= $this->doc->endPage();
		echo $this->content;
	}

	#######################################
	########## config functions ###########
	#######################################

	/**
	 * preparation and check of the configuration
	 *
	 * Note that the default value will be set, if a option check fails.
	 *
	 * @return void
	 */
	private function prepareConfig()
	{
		// unserialize the Configuration
		$this->extConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['lfeditor']);

		// regular Expressions
		$this->extConfig['searchRegex'] = '/^[a-z0-9_]*locallang[a-z0-9_-]*\.(php|xml)$/i';
		if(!preg_match('/^\/.*\/.*$/', $this->extConfig['extIgnore']))
			$this->extConfig['extIgnore'] = '/^csh_.*$/';

		// some only integer values
		$this->extConfig['searchPathDepth'] = intval($this->extConfig['searchPathDepth']);
		$this->extConfig['viewStateExt'] = intval($this->extConfig['viewStateExt']);
		$this->extConfig['numTextAreaRows'] = intval($this->extConfig['numTextAreaRows']);
		$this->extConfig['numSiteConsts'] = intval($this->extConfig['numSiteConsts']);
		$this->extConfig['anzBackup'] = intval($this->extConfig['anzBackup']);
		$this->extConfig['viewStateExt'] = intval($this->extConfig['viewStateExt']);

		// paths and files (dont need to exist)
		$this->extConfig['pathBackup'] = typo3Lib::fixFilePath(PATH_site . '/' .
			$this->extConfig['pathBackup']) . '/';
		$this->extConfig['metaFile'] = typo3Lib::fixFilePath(PATH_site . '/' .
			$this->extConfig['metaFile']);
		$this->extConfig['pathXLLFiles'] = typo3Lib::fixFilePath(PATH_site . '/' .
			$this->extConfig['pathXLLFiles']) . '/';

		// files
		$this->extConfig['pathCSS'] = 'tx_lfeditor_mod1_css.css';
		$this->extConfig['pathTinyMCEConfig'] = PATH_site .
			t3lib_extMgm::siteRelPath('lfeditor') . 'mod1/tinyMCEConfig.js';

		// languages (default is forbidden)
		if(!empty($this->extConfig['viewLanguages']))
		{
			$langs = explode(',', $this->extConfig['viewLanguages']);
			unset($this->extConfig['viewLanguages']);
			foreach($langs as $lang)
				if($lang != 'default')
					$this->extConfig['viewLanguages'][] = $lang;
		}
	}

	#######################################
	####### object initializations ########
	#######################################

	/**
	 * creates and instantiates a file object
	 *
	 * Naming Convention:
	 * tx_lfeditor_mod1_file_<workspace><filetype>
	 *
	 * @throws LFException raised if the the object cant be generated or language file not read
	 * @param string relative path to language file from second param (includes filename)
	 * @param string absolute path to extension
	 * @param string mode (xll or base)
	 * @param boolean set to false, if you dont want read the given file (probably wont exist?)
	 * @return void
	 */
	private function initFileObject($langFile, $extPath, $mode, $flagReadFile=true)
	{
		// xll specific
		try {
			if($mode == 'xll')
			{
				try {
					$typo3RelFile = typo3Lib::transTypo3File($extPath . '/' . $langFile, false);
				} catch(Exception $e) {
					$typo3RelFile = '';
				}
				$xllFile = typo3Lib::fixFilePath(PATH_site . '/' .
					$GLOBALS['TYPO3_CONF_VARS']['BE']['XLLfile'][$typo3RelFile]);
				if(is_file($xllFile))
				{
					$langFile = basename($xllFile);
					$extPath = dirname($xllFile);
				}
				else
				{
					$langFile = t3lib_div::shortMD5(md5(microtime())) . '.' .
						sgLib::getFileExtension($langFile);
					$extPath = $this->extConfig['pathXLLFiles'];
					$flagReadFile = false;
				}
			}
			$fileType = sgLib::getFileExtension($langFile);
		} catch(Exception $e) {
			throw new LFException('failure.failure', 0, '(' . $e->getMessage() . ')');
		}

		// create file object
		$className = 'tx_lfeditor_mod1_file_' . $mode . strtoupper($fileType);
		if(!class_exists($className))
			throw new LFException('failure.langfile.unknownType');
		$this->fileObj = t3lib_div::makeInstance($className);

		// initialize class
		try {
			if($mode == 'xll')
				$this->fileObj->init($langFile, $extPath, $typo3RelFile);
			else
				$this->fileObj->init($langFile, $extPath);

			if($flagReadFile)
				$this->fileObj->readFile();
		} catch(LFException $e) {
			throw $e;
		}
	}

	/**
	 * init backup object
	 *
	 * @throws LFException raised if directories cant be created or backup class instantiated
	 * @param string workspace
	 * @param boolean set to true if you want use informations from the file object
	 * @return void
	 */
	private function initBackupObject($mode, $infos=null)
	{
		// create backup and meta directory
		$backupPath = $this->extConfig['pathBackup'];
		$metaFile = $this->extConfig['metaFile'];
		try {
			sgLib::createDir($backupPath, PATH_site);
			sgLib::createDir(dirname($metaFile), PATH_site);
		} catch(Exception $e) {
			throw new LFException('failure.failure', 0, '(' . $e->getMessage() . ')');
		}

		// get informations
		if(!is_array($infos))
		{
			// build language file and extension path
			if($mode == 'xll')
			{
				try {
					$typo3RelFile = $this->fileObj->getVar('typo3RelFile');
					$typo3AbsFile = typo3Lib::transTypo3File($typo3RelFile, true);
				} catch(Exception $e) {
					throw new LFException('failure.failure', 0, '(' . $e->getMessage() . ')');
				}

				$langFile = sgLib::trimPath('EXT:', $typo3RelFile);
				$langFile = substr($langFile, strpos($langFile, '/')+1);

				$extPath = sgLib::trimPath($langFile, sgLib::trimPath(PATH_site,
					$typo3AbsFile), '/');
			}
			else
			{
				$extPath = sgLib::trimPath(PATH_site, $this->fileObj->getVar('absPath'), '/');
				$langFile = $this->fileObj->getVar('relFile');
			}

			// set data informations
			$informations['localLang'] = $this->fileObj->getLocalLangData();
			$informations['originLang'] = $this->fileObj->getOriginLangData();
			$informations['meta'] = $this->fileObj->getMetaData();
		}

		// set informations
		$informations['workspace'] = $mode;
		$informations['extPath'] = is_array($infos) ? $infos['extPath'] : $extPath;
		$informations['langFile'] = is_array($infos) ? $infos['langFile'] : $langFile;

		// create and initialize the backup object
		try {
			$this->backupObj = t3lib_div::makeInstance('tx_lfeditor_mod1_file_backup');
			$this->backupObj->init('', $backupPath, $metaFile);
			$this->backupObj->setVar($informations);
		} catch(LFException $e) {
			throw $e;
		}
	}

	########################################
	######## menu generation methods #######
	########################################

	/**
	 * returns a generated Menu
	 *
	 * @param string contains the array key of the menu
	 * @return string generated Menu (HTML-Code)
	 */
	private function getFuncMenu($key)
	{
		$retVal = t3lib_BEfunc::getFuncMenu($this->id, 'SET[' . $key . ']',
			$this->MOD_SETTINGS[$key], $this->MOD_MENU[$key]);

		// problem with # char in uris ... :-(
		$this->MOD_SETTINGS[$key] = str_replace('$*-*$', '#', $this->MOD_SETTINGS[$key]);

		return $retVal;
	}

	/**
	 * adds items to the MOD_MENU array. Used for the function menu selector.
	 *
	 * @return void
	 */
	public function menuConfig()
	{
		$this->MOD_MENU = array (
			'function' => array (
				'general' => $GLOBALS['LANG']->getLL('function.general.general'),
				'langfile.edit' => $GLOBALS['LANG']->getLL('function.langfile.edit'),
				'const.edit' => $GLOBALS['LANG']->getLL('function.const.edit.edit'),
				'const.add' => $GLOBALS['LANG']->getLL('function.const.add.add'),
				'const.delete' => $GLOBALS['LANG']->getLL('function.const.delete.delete'),
				'const.rename' => $GLOBALS['LANG']->getLL('function.const.rename.rename'),
				'const.search' => $GLOBALS['LANG']->getLL('function.const.search.search'),
				'const.treeview' => $GLOBALS['LANG']->getLL('function.const.treeview.treeview'),
				'backupMgr' => $GLOBALS['LANG']->getLL('function.backupMgr.backupMgr')
			)
		);
		parent::menuConfig();
	}

	/**
	 * adds items to the MOD_MENU array. Used for the extension menu selector.
	 *
	 * @throws LFException raised if no extensions are found
	 * @return void
	 */
	private function menuExtList()
	{
		// search extensions
		try {
			// local extensions
			if($this->extConfig['viewLocalExt'])
				if(is_array($content = tx_lfeditor_mod1_functions::searchExtensions(
					PATH_site . typo3Lib::pathLocalExt, $this->extConfig['viewStateExt'],
					$this->extConfig['extIgnore'])))
					$tmpExtList[$GLOBALS['LANG']->getLL('ext.local')] = $content;

			// global extensions
			if($this->extConfig['viewGlobalExt'])
				if(is_array($content = tx_lfeditor_mod1_functions::searchExtensions(
					PATH_site . typo3Lib::pathGlobalExt, $this->extConfig['viewStateExt'],
					$this->extConfig['extIgnore'])))
					$tmpExtList[$GLOBALS['LANG']->getLL('ext.global')] = $content;

			// system extensions
			if($this->extConfig['viewSysExt'])
				if(is_array($content = tx_lfeditor_mod1_functions::searchExtensions(
					PATH_site . typo3Lib::pathSysExt, $this->extConfig['viewStateExt'],
					$this->extConfig['extIgnore'])))
					$tmpExtList[$GLOBALS['LANG']->getLL('ext.system')] = $content;
		} catch(Exception $e) {
			throw new LFException('failure.failure', 0, '(' . $e->getMessage() . ')');
		}

		// check extension array
		if(!is_array($tmpExtList))
			throw new LFException('failure.search.noExtension');

		// create list
		$this->MOD_MENU = array(
			'extList' => tx_lfeditor_mod1_functions::prepareExtList($tmpExtList)
		);
		parent::menuConfig();
	}

	/**
	 * adds items to the MOD_MENU array. Used for the language file menu selector.
	 *
	 * @throws LFException raised if no language files are found
	 * @return void
	 */
	private function menuLangFileList()
	{
		// check
		if(empty($this->MOD_SETTINGS['extList']))
			throw new LFException('failure.search.noLangFile');

		// search and prepare files
		try {
			$files = sgLib::searchFiles($this->MOD_SETTINGS['extList'],
                $this->extConfig['searchRegex'], $this->extConfig['searchPathDepth']);
		} catch(Exception $e) {
			throw new LFException('failure.search.noLangFile', 0,
				'(' . $e->getMessage() . ')');
		}

		if(is_array($files) && count($files)) {
			foreach($files as $file) {
				$filename = substr($file, strlen($this->MOD_SETTINGS['extList']) + 1);
			$fileArray[$filename] = $filename;
			}
		} else
			throw new LFException('failure.search.noLangFile');

		// create list
		$this->MOD_MENU = array('langFileList' => $fileArray);
		parent::menuConfig();
	}

	/**
	 * adds items to the MOD_MENU array. Used for the workspace selector
	 *
	 * @return void
	 */
	private function menuWorkspaceList()
	{
		$wsList['base'] = $GLOBALS['LANG']->getLL('workspace.base');
		$wsList['xll'] = $GLOBALS['LANG']->getLL('workspace.xll');

		$this->MOD_MENU = array('wsList' => $wsList);
		parent::menuConfig();
	}

	/**
	 * adds items to the MOD_MENU array. Used for the insert mode selector
	 *
	 * @return void
	 */
	private function menuInsertMode()
	{
		if(t3lib_extMgm::isLoaded('tinymce'))
			$switch['tinyMCE'] = $GLOBALS['LANG']->getLL('select.insertMode.tinyMCE');
		if(t3lib_extMgm::isLoaded('pmktextarea'))
			$switch['pmktextarea'] = $GLOBALS['LANG']->getLL('select.insertMode.pmktextarea');
		$switch['normal'] = $GLOBALS['LANG']->getLL('select.insertMode.normal');

		$this->MOD_MENU = array('insertMode' => $switch);
		parent::menuConfig();
	}

	/**
	 * adds items to the MOD_MENU array. Used for the constant type selector
	 *
	 * @return void
	 */
	private function menuConstantType()
	{
		$constTypeList['all'] = $GLOBALS['LANG']->getLL('const.type.all');
		$constTypeList['translated'] = $GLOBALS['LANG']->getLL('const.type.translated');
		$constTypeList['unknown'] = $GLOBALS['LANG']->getLL('const.type.unknown');
		$constTypeList['untranslated'] = $GLOBALS['LANG']->getLL('const.type.untranslated');

		$this->MOD_MENU = array('constTypeList' => $constTypeList);
		parent::menuConfig();
	}

	/**
	 * adds items to the MOD_MENU array. Used for the language menu selector
	 *
	 * @param array language data
	 * @param string keyword of the menuBox
	 * @param string optional default value (if you dont want a default let it empty)
	 * @return void
	 */
	private function menuLangList($langData, $funcKey, $default='')
	{
		// build languages
		$languages = tx_lfeditor_mod1_functions::buildLangArray($this->extConfig['viewLanguages']);
		$langArray = array_merge(array('default'), $languages);
		foreach($langArray as $lang)
		{
			$anzConsts = 0;
			if(is_array($langData[$lang]))
				$anzConsts = count($langData[$lang]);

			$langList[$lang] = $lang . ' (' . $anzConsts . ' ' .
				$GLOBALS['LANG']->getLL('const.consts') . ')';
		}
		asort($langList);

		// add default value
		if(!empty($default))
			$langList = array_merge(array('###default###'=>$default), $langList);

		$this->MOD_MENU = array($funcKey => $langList);
		parent::menuConfig();
	}

	/**
	 * adds items to the MOD_MENU array. Used for the editConst-List
	 *
	 * @param array language data
	 * @param string name of default entry
	 * @return void
	 */
	private function menuConstList($langData, $default)
	{
		// generate constant list
		$constList = array();
		$languages = tx_lfeditor_mod1_functions::buildLangArray();
		foreach($languages as $language)
		{
			if(!is_array($langData[$language]))
				continue;

			$constants = array_keys($langData[$language]);
			foreach($constants as $constant)
				$constList[str_replace('#', '$*-*$', $constant)] = $constant;
		}

		// sorting and default entry
		asort($constList);
		$constList = array_merge(array('###default###'=>$default), $constList);

		$this->MOD_MENU = array('constList' => $constList);
		parent::menuConfig();
	}

	#######################################
	############ exec functions ###########
	#######################################

	/**
	 * splits (with typo3 V4 l10n support) or merges a language file (inclusive backup)
	 *
	 * @throws LFException raised if file couldnt be splitted or merged (i.e. empty langModes)
	 * @param array language shortcuts and their mode (1 = splitNormal, 2 = splitL10n, 3 = merge)
	 * @return void
	 */
	private function execSplitFile($langModes)
	{
		// check
		if(!is_array($langModes))
			throw new LFException('failure.langfile.notSplittedOrMerged');

		// rewrite originLang array
		$delLangFiles = array();
		foreach($langModes as $langKey=>$mode)
		{
			if($langKey == 'default')
				continue;

			// get origin of this language
			$origin = $this->fileObj->getOriginLangData($langKey);

			// split or merge
			if($mode == 1)
			{
				// nothing to do if the file is already a normal splitted file
				if(typo3lib::checkFileLocation($origin) != 'l10n')
					if($this->fileObj->checkLocalizedFile(basename($origin), $langKey))
						continue;

				// delete file if was it a l10n file
				if($this->fileObj->checkLocalizedFile(basename($origin), $langKey))
					$delLangFiles[] = $origin;

				$origin = typo3Lib::fixFilePath(dirname($this->fileObj->getVar('absFile')) .
					 '/' . $this->fileObj->nameLocalizedFile($langKey));
			}
			elseif($mode == 2)
			{
				// nothing to do if the file is already a l10n file
				if(typo3lib::checkFileLocation($origin) == 'l10n')
					continue;

				// delete file if was it a normal splitted file
				if($this->fileObj->checkLocalizedFile(basename($origin), $langKey))
					$delLangFiles[] = $origin;

				if(is_dir(PATH_site . typo3lib::pathL10n . $langKey))
				{
					// generate middle of the path between extension start and file
					try {
						$midPath = typo3Lib::transTypo3File($origin, false);
						$midPath = substr($midPath, 4);
						$midPath = substr($midPath, 0, strrpos($midPath, '/')+1);

						$origin = PATH_site . typo3lib::pathL10n . $langKey .
							'/' . $midPath . $this->fileObj->nameLocalizedFile($langKey);
					} catch(Exception $e) {
						throw new LFException('failure.langfile.notSplittedOrMerged', 0,
							'(' . $e->getMessage() . ')');
					}
				}
			}
			elseif($mode == 3)
			{
				if($this->fileObj->checkLocalizedFile(basename($origin), $langKey))
					$delLangFiles[] = $origin;
				$origin = $this->fileObj->getVar('absFile');
			}
			else
				continue;
			$this->fileObj->setOriginLangData($origin, $langKey);
		}

		// write new language file
		try {
			$this->execWrite(array());
		} catch(LFException $e) {
			throw $e;
		}

		// delete old localized files, if single mode was selected
		try {
			if(is_array($delLangFiles))
				sgLib::deleteFiles($delLangFiles);
		} catch(Exception $e) {
			throw new LFException('failure.langfile.notDeleted', 0,
				'(' . $e->getMessage(). ')');
		}
	}

	/**
	 * converts language files between different formats
	 *
	 * @throws LFException raised if transforming or deletion of old files failed
	 * @param string new file format
	 * @param string new relative file
	 * @return void
	 */
	private function execTransform($type, $newFile)
	{
		// copy current object to convObj
		$this->convObj = clone $this->fileObj;
		unset($this->fileObj);

		// init new language file object (dont try to read file)
		try {
			$this->initFileObject($newFile, $this->convObj->getVar('absPath'),
				$this->MOD_SETTINGS['wsList'], false);
		} catch(LFException $e) {
			throw $e;
		}

		// recreate originLang
		$dirNameOfAbsFile = dirname($this->fileObj->getVar('absFile'));
		$origins = $this->convObj->getOriginLangData();
		foreach($origins as $langKey=>$file)
		{
			// localized or merged language origin
			$newFile = sgLib::setFileExtension($type, $file);
			if($this->convObj->getVar('workspace') == 'base')
				if($this->convObj->checkLocalizedFile(basename($file), $langKey))
					$newFile = $dirNameOfAbsFile . '/' . $this->fileObj->nameLocalizedFile($langKey);
			$this->fileObj->setOriginLangData(typo3Lib::fixFilePath($newFile), $langKey);
		}

		// recreate meta data
		$meta = $this->convObj->getMetaData();
		foreach($meta as $metaIndex=>$metaValue)
			$this->fileObj->setMetaData($metaIndex, $metaValue);

		// copy typo3RelFile if xll workspace is selected
		if($this->MOD_SETTINGS['wsList'] == 'xll')
			$this->fileObj->setVar(array('typo3RelFile' => $this->convObj->getVar('typo3RelFile')));

		// write new language file
		try {
			$this->extConfig['execBackup'] = 0;
			$this->execWrite($this->convObj->getLocalLangData());
		} catch(LFException $e) {
			throw $e;
		}

		// delete all old files
		try {
			$delFiles = $this->convObj->getOriginLangData();
			if(is_array($delFiles))
				sgLib::deleteFiles($delFiles);
		} catch(Exception $e) {
			throw new LFException('failure.langfile.notDeleted', 0,
				'(' . $e->getMessage() . ')');
		}
	}

	/**
	 * executes the deletion of backup files
	 *
	 * @throws LFException raised if a backup file couldnt be deleted
	 * @param array files as key and the language file as value
	 * @return void
	 */
	public function execBackupDelete($delFiles)
	{
		// delete files
		try {
			foreach($delFiles as $filename=>$langFile)
				$this->backupObj->deleteSpecFile($filename, '', $langFile);
		} catch(LFException $e) {
			throw $e;
		}
	}

	/**
	 * restores a backup file
	 *
	 * @throws LFException raised if some unneeded files couldnt be deleted
	 * @return void
	 */
	private function execBackupRestore()
	{
		// get vars
		$localLang = array();
		$meta = array();
		$origLang = $this->fileObj->getLocalLangData();
		$origMeta = $this->fileObj->getMetaData();
		$backupMeta = $this->backupObj->getMetaData();
		$backupLocalLang = $this->backupObj->getLocalLangData();
		$backupOriginLang = $this->backupObj->getOriginLangData();

		// get differences between original and backup file
		$origDiff = tx_lfeditor_mod1_functions::getBackupDiff(1, $origLang, $backupLocalLang);
		$backupDiff = tx_lfeditor_mod1_functions::getBackupDiff(2, $origLang, $backupLocalLang);

		if(is_array($origDiff))
			foreach($origDiff as $langKey=>$data)
				foreach($data as $label=>$value)
					if(isset($backupLocalLang[$langKey][$label]))
						$localLang[$langKey][$label] = $value;
					else
						$localLang[$langKey][$label] = '';

		if(is_array($backupDiff))
			foreach($backupDiff as $langKey=>$data)
				foreach($data as $label=>$value)
					$localLang[$langKey][$label] = $value;

		// get differences between original and backup meta
		$origDiff = tx_lfeditor_mod1_functions::getMetaDiff(1, $origMeta, $backupMeta);
		$backupDiff = tx_lfeditor_mod1_functions::getMetaDiff(2, $origMeta, $backupMeta);

		if(is_array($origDiff))
			foreach($origDiff as $label=>$value)
				if(isset($backupMeta[$label]))
					$meta[$label] = $value;
				else
					$meta[$label] = '';

		if(is_array($backupDiff))
			foreach($backupDiff as $label=>$value)
				$meta[$label] = $value;

		// restore origins of languages
		foreach($backupOriginLang as $langKey=>$file)
		{
			$curFile = $this->fileObj->getOriginLangData($langKey);
			if($curFile != $file && $curFile != $this->fileObj->getVar('absFile'))
				$deleteFiles[] = $curFile;
			$this->fileObj->setOriginLangData($file, $langKey);
		}

		// write modified language array
		try {
			$this->extConfig['execBackup'] = 0;
			$this->execWrite($localLang, $meta, true);
		} catch(LFException $e) {
			throw $e;
		}

		// delete all old files
		try {
			if(is_array($deleteFiles))
				sgLib::deleteFiles($deleteFiles);
		} catch(Exception $e) {
			throw new LFException('failure.langfile.notDeleted', 0,
				'(' . $e->getMessage() . ')');
		}
	}

	/**
	 * exec the backup of files and deletes automatic old files
	 *
	 * @throws LFException raised if backup file cant written or unneeded files cant deleted
	 * @return void
	 */
	private function execBackup()
	{
		// create backup object
		try {
			$this->initBackupObject($this->MOD_SETTINGS['wsList'], true);
		} catch(LFException $e) {
			throw $e;
		}

		// write backup file
		try {
			$this->backupObj->writeFile();
		} catch(LFException $e) {
			throw $e;
		}

		// exec automatic deletion of backup files, if anzBackup greater zero
		if($this->extConfig['anzBackup'] <= 0)
			return true;

		// get difference information
		$metaArray = $this->backupObj->getMetaInfos(3);
		$rows = count($metaArray);
		$dif = $rows - $this->extConfig['anzBackup'];

		if($dif <= 0)
			return true;

		// sort metaArray
		foreach($metaArray as $key=>$row)
			$createdAt[$key] = $row['createdAt'];
		array_multisort($createdAt, SORT_DESC, $metaArray);

		// get filenames
		$files = array_keys($metaArray);
		$numberFiles = count($files);

		// delete files
		try {
			for(; $dif > 0; --$dif, --$numberFiles)
				$this->backupObj->deleteSpecFile($files[$numberFiles-1]);
		} catch(LFException $e) {
			try { // delete current written file
				$this->backupObj->deleteFile();
			} catch(LFException $e) {
				throw $e;
			}
			throw $e;
		}
	}

	/**
	 * executes writing of language files
	 *
	 * @throws LFException raised if file couldnt be written or some param criterias arent correct
	 * @param array changes (constants with empty values will be deleted)
	 * @param array meta changes (indexes with empty values will be deleted)
	 * @param boolean set to true if you want delete default constants
	 * @return void
	 */
	private function execWrite($modArray, $modMetaArray=array(), $forceDel=false)
	{
		// checks
		if(!is_array($modArray))
			throw new LFException('failure.file.notWritten');

		// execute backup
		try {
			if($this->extConfig['execBackup'])
				$this->execBackup();
		} catch(LFException $e) {
			throw $e;
		}

		// set new language data
		foreach($modArray as $langKey=>$data)
			if(is_array($data))
				foreach($data as $const=>$value)
					$this->fileObj->setLocalLangData($const, $value, $langKey, $forceDel);

		// set changed meta data
		foreach($modMetaArray as $metaIndex=>$metaValue)
			$this->fileObj->setMetaData($metaIndex, $metaValue);

		// write new language data
		try {
			$this->fileObj->writeFile();
		} catch(LFException $e) {
			throw $e;
		}

		// delete possible language files
		$absFile = $this->fileObj->getVar('absFile');
		$originLang = $this->fileObj->getOriginLangData();
		unset($emptyFiles);
		foreach($originLang as $lang=>$origin)
		{
			if($origin == $absFile || !is_file($origin))
				continue;

			$langData = $this->fileObj->getLocalLangData($lang);
			if(is_array($langData) && !count($langData))
				$emptyFiles[] = $origin;
		}

		// delete all empty language files
		try {
			if(is_array($emptyFiles))
				sgLib::deleteFiles($emptyFiles);
		} catch(Exception $e) {
			throw new LFException('failure.langfile.notDeleted', 0,
				'(' . $e->getMessage() . ')');
		}

		// reinitialize fileobject
		try {
			$this->initFileObject($this->MOD_SETTINGS['langFileList'],
				$this->MOD_SETTINGS['extList'], $this->MOD_SETTINGS['wsList']);
		} catch(LFException $e) {
			throw $e;
		}
	}

	#######################################
	####### main content function #########
	#######################################

	/**
	 * code for output generation of function "general"
	 *
	 * @return string generated html content
	 */
	private function outputFuncGeneral()
	{
		// get vars
		$patternList = $this->MOD_SETTINGS['patternList'];
		$numTextAreaRows = $this->extConfig['numTextAreaRows'];
		$mailIt = t3lib_div::_POST('mailIt');
		$sendMail = t3lib_div::_POST('sendMail');

		// get information array
		$languages = tx_lfeditor_mod1_functions::buildLangArray($this->extConfig['viewLanguages']);
		$languages = array_merge(array('default'), $languages);
		$infoArray = tx_lfeditor_mod1_functions::genGeneralInfoArray($patternList,
			$languages, $this->fileObj);

		// get output
		if(is_array($mailIt) && !$sendMail) {
			// add mailIt pre selection
			foreach($infoArray as $langKey=>$info)
				$infoArray[$langKey]['email'] = isset($mailIt[$langKey]) ? true : false;

			$email = tx_lfeditor_mod1_template::outputGeneralEmail($infoArray['default']['meta'],
				$numTextAreaRows);
		}

		$content = $email . tx_lfeditor_mod1_template::outputGeneral($infoArray, $patternList,
			$numTextAreaRows, ($this->fileObj->getVar('workspace') == 'base') ? true: false);

		return $content;
	}

	/**
	 * code for all actions of function "general"
	 *
	 * @throws LFException raised if something failes
	 * @return boolean true or false (only false if some files should be mailed)
	 */
	private function actionFuncGeneral()
	{
		// get vars
		$splitFile = t3lib_div::_POST('splitFile');
		$transFile = t3lib_div::_POST('transFile');
		$langModes = t3lib_div::_POST('langModes');
		$language = t3lib_div::_POST('language');
		$metaArray = t3lib_div::_POST('meta');
		$mailIt = t3lib_div::_POST('mailIt');
		$sendMail = t3lib_div::_POST('sendMail');
		$emailToAddress = t3lib_div::_POST('mailItEmailToAddress');
		$emailFromAddress = t3lib_div::_POST('mailItEmailFromAddress');
		$emailSubject = t3lib_div::_POST('mailItEmailSubject');
		$emailText = t3lib_div::_POST('mailItEmailText');

		// redirect
		if(!empty($language))
			header("Location: http://" . $_SERVER['HTTP_HOST'] .
				rtrim($_SERVER['PHP_SELF'], '/\\') .
				'?SET[langList]=' . $language . '&SET[function]=const.treeview');

		// zip and mail selected languages
		if(is_array($mailIt)) {
			if(!$sendMail)
				return false;

			$zipFile = new zipfile();
			foreach($mailIt as $langKey=>$in) {
				$origin = $this->fileObj->getOriginLangData($langKey);
				try {
					$saveOrigin = typo3Lib::transTypo3File($origin, false);
					$saveOrigin = str_replace('EXT:', '', $saveOrigin);
				} catch(Exception $e) {
					$saveOrigin = substr($origin, strlen(PATH_site));
				}
				$zipFile->addFile(file_get_contents($origin), $saveOrigin);
			}
			$dumpBuffer = $zipFile->file();

			// try to send mail
			try {
				sgLib::sendMail($emailSubject, $emailText, $emailFromAddress, $emailToAddress,
					$dumpBuffer, 'files.zip');
			} catch(Exception $e) {
				throw new LFException('failure.failure', 0, '(' . $e->getMessage() . ')');
			}
		}

		// write meta informations
		try {
			$this->execWrite(array(), $metaArray);
		} catch(LFException $e) {
			throw $e;
		}

		// split or merge
		if(($splitFile == 1 || $splitFile == 2 || $splitFile == 3) || is_array($langModes))
		{
			// set vars
			if($splitFile != 1 && $splitFile != 2 && $splitFile != 3)
				$splitFile = 0;
			$langKeys = tx_lfeditor_mod1_functions::buildLangArray();

			// generate langModes
			foreach($langKeys as $langKey)
				if(!isset($langModes[$langKey]))
					$langModes[$langKey] = $splitFile;

			// exec split or merge
			try {
				$this->execSplitFile($langModes);
			} catch(LFException $e) {
				throw $e;
			}

			// reinitialize file object
			try {
				$this->initFileObject($this->MOD_SETTINGS['langFileList'],
					$this->MOD_SETTINGS['extList'], $this->MOD_SETTINGS['wsList']);
			} catch(LFException $e) {
				throw $e;
			}
		}

		// transform file
		try {
			if(!empty($transFile) && $this->fileObj->getVar('fileType') != $transFile) {
				$newFile = sgLib::setFileExtension($transFile, $this->fileObj->getVar('relFile'));
				$this->execTransform($transFile, $newFile);
				if($this->MOD_SETTINGS['wsList'] != 'xll')
					header("Location: http://" . $_SERVER['HTTP_HOST'] .
						rtrim($_SERVER['PHP_SELF'], '/\\') . '?SET[langFileList]=' . $newFile);
			}
		} catch(LFException $e) {
			throw $e;
		}

		return true;
	}

	/**
	 * code for output generation of function "langfile.edit"
	 *
	 * @param array language array
	 * @return string generated html content
	 */
	private function outputFuncLangfileEdit($langData)
	{
		// user selection
		$langList = $this->MOD_SETTINGS['langList'];
		$patternList = $this->MOD_SETTINGS['langfileEditPatternList'];
		$constTypeList = $this->MOD_SETTINGS['constTypeList'];

		// get language data of user selection
		$langEdit = is_array($langData[$langList]) ? $langData[$langList] : array() ;
		$langPattern = is_array($langData[$patternList]) ? $langData[$patternList] : array() ;
		$langDefault = is_array($langData['default']) ? $langData['default'] : array() ;

		// session related stuff
		$session = t3lib_div::_POST('session'); // used for staying at current page after saving
		$numSessionConsts = intval(t3lib_div::_POST('numSessionConsts'));
		$numLastPageConsts = intval(t3lib_div::_POST('numLastPageConsts'));
		$buttonType = intval(t3lib_div::_POST('buttonType'));
		$sessID = $GLOBALS['BE_USER']->user['username']; // typo3 user session id

		// user configuration
		$numTextAreaRows = $this->extConfig['numTextAreaRows'];
		$maxSiteConsts = $this->extConfig['numSiteConsts'];

		// new translation
		if(!$session || $buttonType <= 0)
		{
			// adjust number of session constants
			if($constTypeList == 'untranslated' || $constTypeList == 'translated' ||
			   $constTypeList == 'unknown' || $buttonType <= 0)
				$numSessionConsts = 0;
			elseif(!$session) // session written to file
				$numSessionConsts -= $numLastPageConsts;

			// delete old data in session
			unset($_SESSION[$sessID]['langfileEditNewLangData']);
			unset($_SESSION[$sessID]['langfileEditConstantsList']);

			// get language data
			if($constTypeList == 'untranslated')
				$myLangData = array_diff_key($langDefault, $langEdit);
			elseif($constTypeList == 'unknown')
				$myLangData = array_diff_key($langEdit, $langDefault);
			elseif($constTypeList == 'translated')
				$myLangData = array_intersect_key($langDefault, $langEdit);
			else
				$myLangData = $langDefault;
			$_SESSION[$sessID]['langfileEditConstantsList'] = array_keys($myLangData);
		}
		elseif($buttonType == 1) // back button
			$numSessionConsts -= ($maxSiteConsts + $numLastPageConsts);

		// get language constants
		$langData = $_SESSION[$sessID]['langfileEditConstantsList'];
		$numConsts = count($langData);
		if(!count($langData))
			throw new LFException('failure.select.emptyLangDataArray', 1);

		// prepare constant list for this page
		$numLastPageConsts = 0;
		do
		{
			// check number of session constants
			if($numSessionConsts >= $numConsts)
				break;
			++$numLastPageConsts;

			// set constant value (maybe already changed in this session)
			$constant = $langData[$numSessionConsts];
			$editLangVal = $langEdit[$constant];
			if(!isset($_SESSION[$sessID]['langfileEditNewLangData'][$langList][$constant]))
				$_SESSION[$sessID]['langfileEditNewLangData'][$langList][$constant] = $editVal;
			else
				$editLangVal = $_SESSION[$sessID]['langfileEditNewLangData'][$langList][$constant];

			// set constant value (maybe already changed in this session)
			$editPatternVal = $langPattern[$constant];
			if(!isset($_SESSION[$sessID]['langfileEditNewLangData'][$patternList][$constant]))
				$_SESSION[$sessID]['langfileEditNewLangData'][$patternList][$constant] = $editVal;
			else
				$editPatternVal =
					$_SESSION[$sessID]['langfileEditNewLangData'][$patternList][$constant];

			// save informations about the constant
			$constValues[$constant]['edit'] = $editLangVal;
			$constValues[$constant]['pattern'] = $editPatternVal;
			$constValues[$constant]['default'] = $langDefault[$constant];
		} while(++$numSessionConsts % $maxSiteConsts);

		// get output
		$content .= tx_lfeditor_mod1_template::outputEditLangfile($constValues, $numSessionConsts,
			$numLastPageConsts, $numConsts, $langList, $patternList,
			// parallel edit mode
			(($patternList != '###default###' && $patternList != $langList) ? true: false),
			($numSessionConsts > $maxSiteConsts ? true : false), // display back button?
			($numSessionConsts < $numConsts ? true : false), // display next button?
			$numTextAreaRows);

		return $content;
	}

	/**
	 * code for all actions of function "langfile.edit"
	 *
	 * @throws LFException raised if file couldnt be written
	 * @return void
	 */
	private function actionFuncLangfileEdit()
	{
		// get session id
		$sessID = $GLOBALS['BE_USER']->user['username'];

		// get vars
		$newLang = t3lib_div::_POST('newLang');
		$session = t3lib_div::_POST('session');
		$langList = $this->MOD_SETTINGS['langList'];
		$patternList = $this->MOD_SETTINGS['langfileEditPatternList'];

		// write new language file or save informations into session
		try {
			$_SESSION[$sessID]['langfileEditNewLangData'][$langList] =
				array_merge($_SESSION[$sessID]['langfileEditNewLangData'][$langList],
				$newLang[$langList]);

			// parallel edit mode?
			if($patternList != '###default###' && $patternList != $langList)
				$_SESSION[$sessID]['langfileEditNewLangData'][$patternList] =
					array_merge($_SESSION[$sessID]['langfileEditNewLangData'][$patternList],
					$newLang[$patternList]);

			// write if no session continued
			if(!$session)
				$this->execWrite($_SESSION[$sessID]['langfileEditNewLangData']);
		} catch(LFException $e) {
			throw $e;
		}
	}

	/**
	 * code for output generation of function "const.edit"
	 *
	 * @throws LFException raised if no constant was selected
	 * @param array language array
	 * @return string generated html content
	 */
	private function outputFuncConstEdit($langData)
	{
		// get vars
		$constant = $this->MOD_SETTINGS['constList'];
		$numTextAreaRows = $this->extConfig['numTextAreaRows'];

		// checks
		if(empty($constant) || $constant == '###default###')
			throw new LFException('failure.select.noConst', 1);

		// get output
		$languages = tx_lfeditor_mod1_functions::buildLangArray($this->extConfig['viewLanguages']);
		$langArray = array_merge(array('default'), $languages);
		$content = tx_lfeditor_mod1_template::outputEditConst($langArray, $constant,
			$langData, $numTextAreaRows);

		return $content;
	}

	/**
	 * code for all actions of function "const.edit"
	 *
	 * @throws LFException raised if language file couldnt be written
	 * @return void
	 */
	private function actionFuncConstEdit()
	{
		// get vars
		$newLang = t3lib_div::_POST('newLang');

		// write new language file
		try {
			$this->execWrite($newLang);
		} catch(LFException $e) {
			$content = $e->getMessage();
		}
	}

	/**
	 * code for output generation of function "const.add"
	 *
	 * @param string name of adding constant
	 * @param array default Values
	 * @return string generated html content
	 */
	private function outputFuncConstAdd($constant, $defValues)
	{
		// get vars
		$numTextAreaRows = $this->extConfig['numTextAreaRows'];

		// get output
		$languages = tx_lfeditor_mod1_functions::buildLangArray($this->extConfig['viewLanguages']);
		$langArray = array_merge(array('default'), $languages);
		$content = tx_lfeditor_mod1_template::outputAddConst($langArray, $constant,
			$defValues, $numTextAreaRows);

		return $content;
	}

	/**
	 * code for all actions of function "const.add"
	 *
	 * @throws LFException raised if constant is empty or already exists or writing of file failed
	 * @param array language array
	 * @param array new values of each language for the constant
	 * @param string name of constant which should be added
	 * @return void
	 */
	private function actionFuncConstAdd($langData, &$newLang, &$constant)
	{
		// checks
		if(empty($constant))
			throw new LFException('failure.select.noConstDefined');

		if(!empty($langData['default'][$constant]))
			throw new LFException('failure.langfile.constExists');

		// writing
		try {
			foreach($newLang as $lang=>$value)
				$add[$lang][$constant] = $value;

			$this->execWrite($add);
			$constant = '';
			$newLang = array();
		} catch(LFException $e) {
			throw $e;
		}
	}

	/**
	 * code for output generation of function "const.delete"
	 *
	 * @throws LFException raised if no constant was selected
	 * @return string generated html content
	 */
	private function outputFuncConstDelete()
	{
		// get vars
		$constant = $this->MOD_SETTINGS['constList'];

		// checks
		if(empty($constant) || $constant == '###default###')
			throw new LFException('failure.select.noConst', 1);

		// get output
		$content = tx_lfeditor_mod1_template::outputDeleteConst($constant);

		return $content;
	}

	/**
	 * code for all actions of function "const.delete"
	 *
	 * @throws LFException raised if the language file couldnt be written
	 * @return void
	 */
	private function actionFuncConstDelete()
	{
		// get vars
		$constant = $this->MOD_SETTINGS['constList'];
		$delAllLang = t3lib_div::_POST('delAllLang');

		// write new language file
		try {
			// get languages
			if($delAllLang) {
				$languages = tx_lfeditor_mod1_functions::buildLangArray();
				$langArray = array_merge(array('default'), $languages);
			}
			else
				$langArray =
					tx_lfeditor_mod1_functions::buildLangArray($this->extConfig['viewLanguages']);

			// build modArray
			unset($newLang);
			foreach($langArray as $lang)
				$newLang[$lang][$constant] = '';

			$this->execWrite($newLang, array(), true);
		} catch(LFException $e) {
			throw $e;
		}
	}

	/**
	 * code for output generation of function "const.rename"
	 *
	 * @throws LFException raised if no constant was selected
	 * @return string generated html content
	 */
	private function outputFuncConstRename()
	{
		// get vars
		$constant = $this->MOD_SETTINGS['constList'];

		// checks
		if(empty($constant) || $constant == '###default###')
			throw new LFException('failure.select.noConst', 1);

		// get output
		$content = tx_lfeditor_mod1_template::outputRenameConst($constant);

		return $content;
	}

	/**
	 * code for all actions of function "const.rename"
	 *
	 * @throws LFException raised if the language file couldnt be written
	 * @param array language array
	 * @return void
	 */
	private function actionFuncConstRename($langData)
	{
		// get vars
		$oldConst = $this->MOD_SETTINGS['constList'];
		$newConst = t3lib_div::_POST('newConst');

		// write new language file
		try {
			// get languages
			$langArray = array_merge(array('default'), tx_lfeditor_mod1_functions::buildLangArray());

			// build modArray
			unset($newLang);
			foreach($langArray as $lang)
			{
				$newLang[$lang][$newConst] = $langData[$lang][$oldConst];
				$newLang[$lang][$oldConst] = '';
			}

			$this->execWrite($newLang, array(), true);
		} catch(LFException $e) {
			throw $e;
		}
	}

	/**
	 * code for output generation of function "const.search"
	 *
	 * @throws LFException raised if nothing found or a empty search string is given
	 * @param array language array
	 * @return string generated html content
	 */
	private function outputFuncConstSearch($langData)
	{
		// get vars
		$searchStr = t3lib_div::_POST('searchStr');
		$caseSensitive = t3lib_div::_POST('caseSensitive');
		$searchOptions = $caseSensitive ? '': 'i';

		// search
		$resultArray = array();
		if(!preg_match('/^\/.*\/.*$/', $searchStr) && !empty($searchStr)) {
			foreach($langData as $langKey=>$data)
				if(is_array($data))
					foreach($data as $labelKey=>$labelValue)
						if(preg_match('/' . $searchStr . '/' . $searchOptions, $labelValue))
							$resultArray[$langKey][$labelKey] = $labelValue;
			if(!count($resultArray))
				$preMsg = new LFException('failure.search.noConstants', 1);
		}
		else
			$preMsg = new LFException('function.const.search.enterSearchStr', 1);

		// get output
		$content = tx_lfeditor_mod1_template::outputSearchConst($searchStr, $resultArray,
			(is_object($preMsg) ? $preMsg->getMessage() : ''), $caseSensitive);

		return $content;
	}

	/**
	 * code for all actions of function "const.search"
	 *
	 * @return void
	 */
	private function actionFuncConstSearch()
	{
		// get vars
		$constant = t3lib_div::_POST('constant');

		// redirect
		if(!empty($constant))
			header("Location: http://" . $_SERVER['HTTP_HOST'] .
				rtrim($_SERVER['PHP_SELF'], '/\\') .
				'?SET[constList]=' . $constant . '&SET[function]=const.edit');
	}

	/**
	 * code for output generation of function "const.treeview"
	 *
	 * @throws LFException raised if no language data was found in the selected language
	 * @param array language array
	 * @param string current explode Token
	 * @return string generated html content
	 */
	private function outputFuncConstTreeview($langData, $curToken)
	{
		// get vars
		$usedLangData = $langData[$this->MOD_SETTINGS['langList']];
		$refLangData = $langData[$this->MOD_SETTINGS['patternList']];
		$treeHide = $this->extConfig['treeHide'];

		// checks
		if(!is_array($usedLangData) || !count($usedLangData))
			throw new LFException('failure.select.emptyLanguage', 1);

		// get output
		$tree = tx_lfeditor_mod1_functions::genTreeInfoArray($usedLangData, $refLangData, $curToken);
		$content = tx_lfeditor_mod1_template::outputTreeView($tree, $treeHide);

		return $content;
	}

	/**
	 * code for all actions of function "const.treeview"
	 *
	 * @return void
	 */
	private function actionFuncConstTreeview()
	{
		// get vars
		$constant = t3lib_div::_POST('constant');

		// redirect
		if(!empty($constant))
			header("Location: http://" . $_SERVER['HTTP_HOST'] .
				rtrim($_SERVER['PHP_SELF'], '/\\') .
				'?SET[constList]=' . $constant . '&SET[function]=const.edit');
	}

	/**
	 * code for output generation of function "backupMgr"
	 *
	 * @throws LFException raised if meta array is empty (no backup files)
	 * @return string generated html content
	 */
	private function outputFuncBackupMgr()
	{
		// get vars
		$filename = t3lib_div::_POST('file');
		$origDiff = t3lib_div::_POST('origDiff');
		$extPath = $this->MOD_SETTINGS['extList'];

		// get output
		$metaArray = $this->backupObj->getMetaInfos(2);
		if(!count($metaArray))
			throw new LFException('failure.backup.noFiles', 1);
		$content = tx_lfeditor_mod1_template::outputManageBackups($metaArray, $extPath);

		if($origDiff)
		{
			// set backup file
			$metaArray = $this->backupObj->getMetaInfos(3);
			$informations = array(
				'absPath' => typo3Lib::fixFilePath(PATH_site . '/' .
					$metaArray[$filename]['pathBackup']),
				'relFile' => $filename,
			);
			$this->backupObj->setVar($informations);

			// exec diff
			try {
				// read original file
				$this->initFileObject($this->backupObj->getVar('langFile'),
					PATH_site . '/' . $this->backupObj->getVar('extPath'),
					$this->MOD_SETTINGS['wsList']);

				// read backup file
				$this->backupObj->readFile();

				// get language data
				$origLang = $this->fileObj->getLocalLangData();
				$backupLocalLang = $this->backupObj->getLocalLangData();

				// get meta data
				$origMeta = $this->fileObj->getMetaData();
				$backupMeta = $this->backupObj->getMetaData();

				$diff = tx_lfeditor_mod1_functions::getBackupDiff(0, $origLang, $backupLocalLang);
				$metaDiff = tx_lfeditor_mod1_functions::getMetaDiff(0, $origMeta, $backupMeta);
			} catch(LFException $e) {
				return $e->getMessage() . $content;
			}
		}

		// generate diff
		if(is_array($diff))
			$content .= tx_lfeditor_mod1_template::outputManageBackupsDiff($diff, $metaDiff,
				$this->fileObj->getLocalLangData(), $this->backupObj->getLocalLangData(),
				$this->fileObj->getOriginLangData(), $this->backupObj->getOriginLangData(),
				$this->fileObj->getMetaData(), $this->backupObj->getMetaData());

		return $content;
	}

	/**
	 * code for all actions of function "backupMgr"
	 *
	 * @throws LFException raised if a backup file couldnt be deleted or recovered
	 * @return void
	 */
	private function actionFuncBackupMgr()
	{
		// get vars
		$filename = t3lib_div::_POST('file');
		$restore = t3lib_div::_POST('restore');
		$deleteAll = t3lib_div::_POST('deleteAll');
		$delete = t3lib_div::_POST('delete');

		// exec changes
		try {
			// restore or delete backup files
			if($restore)
			{
				// set backup file
				$metaArray = $this->backupObj->getMetaInfos(3);
				$informations = array(
					'absPath' => PATH_site . $metaArray[$filename]['pathBackup'],
					'relFile' => $filename,
				);
				$this->backupObj->setVar($informations);
				$this->backupObj->readFile();

				// read original file
				$this->initFileObject($this->backupObj->getVar('langFile'),
					PATH_site . '/' . $this->backupObj->getVar('extPath'),
					$this->MOD_SETTINGS['wsList']);

				// restore
				$this->execBackupRestore();
			}
			elseif($deleteAll || $delete)
			{
				if($deleteAll)
				{
					$metaArray = $this->backupObj->getMetaInfos(2);
					foreach($metaArray as $langFile=>$metaPiece)
					{
						$files = array_keys($metaPiece);
						foreach($files as $filename)
							$delFiles[$filename] = $langFile;
					}
				}
				else
					$delFiles[$filename] = '';

				$this->execBackupDelete($delFiles);
			}
		} catch(LFException $e) {
			throw $e;
		}
	}

	/**
	 * generates the module content
	 *
	 * @throws LFException raised if any output failure occured
	 * @return void at failure
	 */
	private function moduleContent()
	{
		// generate menus
		try {
			// generate extension and workspace list
			$name = 'select.extensionAndWorkspace';
			$name = tx_lfeditor_mod1_functions::prepareSectionName($name);
			$this->content .= $this->doc->section($name, '', 0, 1);
			$this->menuExtList();
			$extList = $this->getFuncMenu('extList');
			$this->menuWorkspaceList();
			$this->content .= $this->doc->funcMenu($extList,
				$this->getFuncMenu('wsList'));

			// generate language file list
			if($this->MOD_SETTINGS['function'] != 'backupMgr')
			{
				$name = tx_lfeditor_mod1_functions::prepareSectionName('select.langfile');
				$this->content .= $this->doc->section($name, '', 0, 1);
				$this->menuLangFileList();
				$this->content .= $this->doc->funcMenu($this->getFuncMenu('langFileList'), '');
			}
		} catch(LFException $e) {
			throw $e;
		}

		// init language file object
		try {
			if($this->MOD_SETTINGS['function'] != 'backupMgr')
				$this->initFileObject($this->MOD_SETTINGS['langFileList'],
					$this->MOD_SETTINGS['extList'], $this->MOD_SETTINGS['wsList']);
		} catch(LFException $e) {
			throw $e;
		}

		// init backup object
		try {
			if($this->MOD_SETTINGS['function'] == 'backupMgr')
			{
				$informations = array(
					'extPath' => sgLib::trimPath(PATH_site, $this->MOD_SETTINGS['extList']),
					'langFile' => t3lib_div::_POST('langFile'),
				);
				$this->initBackupObject($this->MOD_SETTINGS['wsList'], $informations);
			}
		} catch(LFException $e) {
			throw $e;
		}

		// generate general output
		switch($this->MOD_SETTINGS['function'])
		{
			case 'general':
				// exec action specific part of function
				try {
					$submit = t3lib_div::_POST('submitted');
					$sendMail = t3lib_div::_POST('sendMail');
					if($submit) {
						if($this->actionFuncGeneral()) {
							if(!$sendMail)
								$preContent = '<p class="tx-lfeditor-success">' .
									$GLOBALS['LANG']->getLL('lang.file.write.success') . '</p>';
							else
								$preContent = '<p class="tx-lfeditor-success">' .
									$GLOBALS['LANG']->getLL('function.general.mail.success') .
									'</p>';
						}
					}
				} catch(LFException $e) {
					$preContent = $e->getMessage();
				}

				// get language data
				$langData = $this->fileObj->getLocalLangData();

				// draw the language reference list
				$this->menuLangList($langData, 'patternList');
				$refMenu = $this->doc->funcMenu($this->getFuncMenu('patternList'), '');
				$sectName = 'select.referenceLanguage';
				$name = tx_lfeditor_mod1_functions::prepareSectionName($sectName);
				$this->content .= $this->doc->section($name, $refMenu, 0, 1);

				// get main content
				$content = $this->outputFuncGeneral();
				$sectName = 'function.general.general';
				$sectName = tx_lfeditor_mod1_functions::prepareSectionName($sectName);
				break;

			case 'langfile.edit':
				// start session
				session_start();

				// exec action specific part of function
				try {
					$submit = t3lib_div::_POST('submitted');
					$session = t3lib_div::_POST('session');
					if($submit) {
						$this->actionFuncLangfileEdit();
						if(!$session)
							$preContent = '<p class="tx-lfeditor-success">' .
								$GLOBALS['LANG']->getLL('lang.file.write.success') . '</p>';
					}
				} catch(LFException $e) {
					$preContent = $e->getMessage();
				}

				// get language data
				$langData = $this->fileObj->getLocalLangData();

				// draw the language- and patternlist
				$this->menuLangList($langData, 'langList');
				$langList = $this->getFuncMenu('langList');

				$this->menuLangList($langData, 'langfileEditPatternList',
					$GLOBALS['LANG']->getLL('select.nothing'));
				$patternList = $this->getFuncMenu('langfileEditPatternList');

				$languageMenu = $this->doc->funcMenu($langList, $patternList);
				$name = 'select.languageAndPattern';
				$name = tx_lfeditor_mod1_functions::prepareSectionName($name);
				$this->content .= $this->doc->section($name, $languageMenu, 0, 1);

				// draw type selector
				$this->menuConstantType();
				$typeList = $this->getFuncMenu('constTypeList');

				$typeMenu = $this->doc->funcMenu($typeList, '');
				$name = 'select.constantType';
				$name = tx_lfeditor_mod1_functions::prepareSectionName($name);
				$this->content .= $this->doc->section($name, $typeMenu, 0, 1);

				// get main content
				try {
					$content = $this->outputFuncLangfileEdit($langData);
				} catch(LFException $e) {
					$content = $e->getMessage();
				}
				$sectName = 'function.langfile.edit';
				$sectName = tx_lfeditor_mod1_functions::prepareSectionName($sectName);
				break;

			case 'const.edit':
				// exec action specific part of function
				try {
					$submit = t3lib_div::_POST('submit');
					if($submit) {
						$this->actionFuncConstEdit();
						$preContent = '<p class="tx-lfeditor-success">' .
							$GLOBALS['LANG']->getLL('lang.file.write.success') . '</p>';
					}
				} catch(LFException $e) {
					$preContent = $e->getMessage();
				}

				// get language data
				$langData = $this->fileObj->getLocalLangData();

				// draw the constant list menu
				$this->menuConstList($langData, $GLOBALS['LANG']->getLL('select.nothing'));
				$constList = $this->doc->funcMenu($this->getFuncMenu('constList'), '');
				$name = tx_lfeditor_mod1_functions::prepareSectionName('select.constant');
				$this->content .= $this->doc->section($name, $constList, 0, 1);

				// get main content
				try {
					$content = $this->outputFuncConstEdit($langData);
				} catch(LFException $e) {
					$content = $e->getMessage();
				}
				$sectName = 'function.const.edit.edit';
				$sectName = tx_lfeditor_mod1_functions::prepareSectionName($sectName);
				break;

			case 'const.add':
				$constant = t3lib_div::_POST('nameOfConst');
				$newLang = t3lib_div::_POST('newLang');
				$langData = $this->fileObj->getLocalLangData();

				// exec action specific part of function
				$submit = t3lib_div::_POST('submit');
				try {
					if($submit) {
						$this->actionFuncConstAdd($langData, $newLang, $constant);
						$preContent = '<p class="tx-lfeditor-success">' .
							$GLOBALS['LANG']->getLL('lang.file.write.success') . '</p>';
					}
				} catch(LFException $e) {
					$preContent = $e->getMessage();
				}

				// get main content
				try {
					$content = $this->outputFuncConstAdd($constant, $newLang);
				} catch(LFException $e) {
					$content = $e->getMessage();
				}
				$sectName = 'function.const.add.add';
				$sectName = tx_lfeditor_mod1_functions::prepareSectionName($sectName);
				break;

			case 'const.delete':
				// exec action specific part of function
				try {
					$submit = t3lib_div::_POST('submit');
					if($submit) {
						$this->actionFuncConstDelete();
						$preContent = '<p class="tx-lfeditor-success">' .
								$GLOBALS['LANG']->getLL('lang.file.write.success') . '</p>';
					}
				} catch(LFException $e) {
					$preContent = $e->getMessage();
				}

				// get language data
				$langData = $this->fileObj->getLocalLangData();

				// draw the constant list menu
				$this->menuConstList($langData, $GLOBALS['LANG']->getLL('select.nothing'));
				$constList = $this->doc->funcMenu($this->getFuncMenu('constList'), '')	;
				$name = tx_lfeditor_mod1_functions::prepareSectionName('select.constant');
				$this->content .= $this->doc->section($name, $constList, 0, 1);

				// get main content
				try {
					$content = $this->outputFuncConstDelete();
				} catch(LFException $e) {
					$content = $e->getMessage();
				}
				$sectName = 'function.const.delete.delete';
				$sectName = tx_lfeditor_mod1_functions::prepareSectionName($sectName);
				break;

			case 'const.rename':
				// exec action specific part of function
				try {
					$submit = t3lib_div::_POST('submit');
					if($submit) {
						$langData = $this->fileObj->getLocalLangData();
						$this->actionFuncConstRename($langData);
						$preContent = '<p class="tx-lfeditor-success">' .
							$GLOBALS['LANG']->getLL('lang.file.write.success') . '</p>';
					}
				} catch(LFException $e) {
					$preContent = $e->getMessage();
				}

				// get language data
				$langData = $this->fileObj->getLocalLangData();

				// draw the constant list menu
				$this->menuConstList($langData, $GLOBALS['LANG']->getLL('select.nothing'));
				$constList = $this->doc->funcMenu($this->getFuncMenu('constList'), '')	;
				$name = tx_lfeditor_mod1_functions::prepareSectionName('select.constant');
				$this->content .= $this->doc->section($name, $constList, 0, 1);

				// get main content
				try {
					$content = $this->outputFuncConstRename();
				} catch(LFException $e) {
					$content = $e->getMessage();
				}
				$sectName = 'function.const.rename.rename';
				$sectName = tx_lfeditor_mod1_functions::prepareSectionName($sectName);
				break;

			case 'const.search':
				// exec action specific part of function
				$submit = t3lib_div::_POST('submitted');
				if($submit)
					$this->actionFuncConstSearch();

				// get language data
				$langData = $this->fileObj->getLocalLangData();

				// get main content
				try {
					$content = $this->outputFuncConstSearch($langData);
				} catch(LFException $e) {
					$content = $e->getMessage();
				}
				$sectName = 'function.const.search.search';
				$sectName = tx_lfeditor_mod1_functions::prepareSectionName($sectName);
				break;

			case 'const.treeview':
				$curToken = t3lib_div::_POST('usedToken');

				// exec action specific part of function
				$submit = t3lib_div::_POST('submitted');
				if($submit)
					$this->actionFuncConstTreeview();

				// get language data
				$langData = $this->fileObj->getLocalLangData();

				// draw the language and reference list
				$this->menuLangList($langData, 'langList');
				$langList = $this->getFuncMenu('langList');

				$this->menuLangList($langData, 'patternList');
				$refList = $this->getFuncMenu('patternList');

				$name = 'select.languageAndPattern';
				$name = tx_lfeditor_mod1_functions::prepareSectionName($name);
				$langMenu = $this->doc->funcMenu($langList, $refList);
				$this->content .= $this->doc->section($name, $langMenu, 0, 1);

				// draw explode token menu
				$curToken = tx_lfeditor_mod1_functions::getExplodeToken($curToken,
					$langData[$this->MOD_SETTINGS['patternList']]);
				$selToken = tx_lfeditor_mod1_template::fieldSetToken($curToken);
				$treeMenu = $this->doc->funcMenu($selToken, '');
				$name = 'select.explodeToken';
				$name = tx_lfeditor_mod1_functions::prepareSectionName($name);
				$this->content .= $this->doc->section($name, $treeMenu, 0, 1);

				// get main content
				try {
					$content = $this->outputFuncConstTreeview($langData, $curToken);
				} catch(LFException $e) {
					$content = $e->getMessage();
				}
				$sectName = 'function.const.treeview.treeview';
				$sectName = tx_lfeditor_mod1_functions::prepareSectionName($sectName);
				break;

			case 'backupMgr':
				// exec action specific part of function
				try {
					$origDiff = t3lib_div::_POST('origDiff');
					$submit = t3lib_div::_POST('submitted');
					if($submit) {
						$this->actionFuncBackupMgr();
						if(!$origDiff)
							$preContent = '<p class="tx-lfeditor-success">' .
								$GLOBALS['LANG']->getLL('function.backupMgr.success') . '</p>';
					}
				} catch(LFException $e) {
					$preContent = $e->getMessage();
				}

				// get main content
				try {
					$content = $this->outputFuncBackupMgr();
				} catch(LFException $e) {
					$content = $e->getMessage();
				}
				$sectName = 'function.backupMgr.backupMgr';
				$sectName = tx_lfeditor_mod1_functions::prepareSectionName($sectName);
				break;
		}

		// save generated content
		$this->content .= $this->doc->section($sectName, $preContent . $content, 0, 1);
	}
}

// Default-Code for using XCLASS (dont touch)
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lfeditor/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lfeditor/mod1/index.php']);
}

// make and call instance
try {
	$SOBE = t3lib_div::makeInstance('tx_lfeditor_module1');
	$SOBE->main();
	$SOBE->printContent();
} catch(LFException $e) {
	$SOBE->printContent($e->getMessage());
}

?>
