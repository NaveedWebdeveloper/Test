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



/**
 * Module 'LFEditor' for the 'lfeditor' extension
 *
 * @author Stefan Galinski <stefan.galinski@frm2.tum.de>
 * @package Typo3
 * @subpackage tx_lfeditor
 */
class go_backend_module2 extends t3lib_SCbase {

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
	 * Returns nothing. Initializes the class.
	 *
	 * @return	void		nothing
	 */
	function init()	{
		global $AB,$BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$HTTP_GET_VARS,$HTTP_POST_VARS,$CLIENT,$TYPO3_CONF_VARS;
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

	
}

// Default-Code for using XCLASS (dont touch)
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/go_backend_layout/moduls/config/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/go_backend_layout/moduls/config/index.php']);
}

?>
