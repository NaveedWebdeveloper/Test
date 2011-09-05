<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2006 Rene Fritz (r.fritz@colorcube.de)
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
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
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * damcatedit command module
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_damcatedit
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   76: class tx_damcatedit_mod_cmd extends t3lib_SCbase
 *  100:     function init()
 *  139:     function handleExternalFunctionValue($MM_key='function', $MS_value=NULL)
 *  170:     function main()
 *  270:     function jumpToUrl(URL)
 *  274:     function jumpBack()
 *  318:     function printContent()
 *  330:     function wrongCommandMessageBox()
 *  345:     function redirect()
 *  364:     function btn_back($params=array(), $absUrl='')
 *
 * TOTAL FUNCTIONS: 9
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



unset($MCONF);
require ('conf.php');
require ($BACK_PATH.'init.php');
require ($BACK_PATH.'template.php');

require_once (PATH_t3lib.'class.t3lib_scbase.php');

$LANG->includeLLFile('EXT:dam_catedit/mod_cmd/locallang.xml');


// Module is available to everybody
// $BE_USER->modAccess($MCONF,1);



/**
 * Script class for the damcatedit command script
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
class tx_damcatedit_mod_cmd extends t3lib_SCbase {

	/**
	 * the action for the form tag
	 */
	var $actionTarget = '';

	/**
	 * the page title
	 */
	var $pageTitle = '[no title]';

	/**
	 * t3lib_basicFileFunctions object
	 */
	var $basicFF;



	/**
	 * Initializes the backend module
	 *
	 * @return	void
	 */
	function init()	{
		global $BE_USER, $TYPO3_CONF_VARS, $FILEMOUNTS;

			// Checking referer / executing
		$refInfo=parse_url(t3lib_div::getIndpEnv('HTTP_REFERER'));
		$vC = t3lib_div::_GP('vC');
		$httpHost = t3lib_div::getIndpEnv('TYPO3_HOST_ONLY');
		if ($httpHost!=$refInfo['host'] && $vC!=$BE_USER->veriCode() && !$TYPO3_CONF_VARS['SYS']['doNotCheckReferer'])	{
			t3lib_BEfunc::typo3PrintError ('Access Error','Referer did not match and veriCode was not valid either!','');
			exit;
		}



		parent::init();


			// Initialize GPvars:
		$this->data = t3lib_div::_GP('data');
		$this->returnUrl = t3lib_div::_GP('returnUrl');
		$this->returnUrl = $this->returnUrl ? $this->returnUrl : t3lib_div::getIndpEnv('HTTP_REFERER');


		$this->redirect = t3lib_div::_GP('redirect');
		$this->redirect = $this->redirect ? $this->redirect : $this->returnUrl;

	}



	/**
	 * Loads $this->extClassConf with the configuration for the CURRENT function of the menu.
	 * If for this array the key 'path' is set then that is expected to be an absolute path to a file which should be included - so it is set in the internal array $this->include_once
	 *
	 * @param	string		The key to MOD_MENU for which to fetch configuration. 'function' is default since it is first and foremost used to get information per "extension object" (I think that is what its called)
	 * @param	string		The value-key to fetch from the config array. If NULL (default) MOD_SETTINGS[$MM_key] will be used. This is usefull if you want to force another function than the one defined in MOD_SETTINGS[function]. Call this in init() function of your Script Class: handleExternalFunctionValue('function', $forcedSubModKey)
	 * @return	void
	 * @see getExternalItemConfig(), $include_once, init()
	 */
	function handleExternalFunctionValue($MM_key='function', $MS_value=NULL)	{
		if (is_null($MS_value)) {
			if ($this->CMD) {
				$MS_value = $this->CMD;
			} else {
				$MS_value = 'tx_damcatedit_cmd_nothing';
			};
		}

		$this->extClassConf = $this->getExternalItemConfig($this->MCONF['name'],$MM_key,$MS_value);
		if (is_array($this->extClassConf) && $this->extClassConf['path'])	{
			$this->include_once[]=$this->extClassConf['path'];
		} else {
			$this->extClassConf = $this->getExternalItemConfig($this->MCONF['name'],$MM_key,'tx_damcatedit_cmd_nothing');
			if (is_array($this->extClassConf) && $this->extClassConf['path'])	{
				$this->include_once[]=$this->extClassConf['path'];
			}
		}
#		$this->MOD_MENU['function'][$MS_value] = $MS_value;
#		$this->MOD_SETTINGS['function'] = $MS_value;
	}





	/**
	 * Main function of the module. Write the content to $this->content
	 *
	 * @return	void
	 */
	function main()	{
		global $BE_USER, $LANG, $BACK_PATH, $TYPO3_CONF_VARS, $HTTP_GET_VARS, $HTTP_POST_VARS;


		//
		// Initialize the template object
		//

		$this->doc = t3lib_div::makeInstance('mediumDoc');
		$this->doc->backPath = $BACK_PATH;
		$this->doc->docType = 'xhtml_trans';



		// Access check...
		// The page will show only if there is a valid page and if this page may be viewed by the user
#		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
#		$access = is_array($this->pageinfo) ? 1 : 0;

		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

		if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{
			$access = TRUE;
		}




			// page-selection permission clause (reading)
		$this->perms_clause = $BE_USER->getPagePermsClause(1);

			// Id a positive id is supplied, ask for the page record with permission information contained:
		if ($this->id > 0)	{
			$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		}

			// If a page-record was returned, the user had read-access to the page.
		if ($this->pageinfo['uid'])	{
				// Get record of parent page
			$this->pidInfo=t3lib_BEfunc::getRecord('pages',$this->pageinfo['pid']);
				// Checking the permissions for the user with regard to the parent page: Can he create new pages, new content record, new page after?
			if ($BE_USER->doesUserHaveAccess($this->pageinfo,8))	{
				$this->newPagesInto=1;
			}
			if ($BE_USER->doesUserHaveAccess($this->pageinfo,16))	{
				$this->newContentInto=1;
			}

			if (($BE_USER->isAdmin()||is_array($this->pidInfo)) && $BE_USER->doesUserHaveAccess($this->pidInfo,8))	{
				$this->newPagesAfter=1;
			}
		} elseif ($BE_USER->isAdmin())	{
				// Admins can do it all
			$this->newPagesInto=1;
			$this->newContentInto=1;
			$this->newPagesAfter=0;
		} else {
				// People with no permission can do nothing
			$this->newPagesInto=0;
			$this->newContentInto=0;
			$this->newPagesAfter=0;
		}


			// If there was a page - or if the user is admin (admins has access to the root) we proceed:
		if ($this->pageinfo['uid'] || $BE_USER->isAdmin())	{
				// Acquiring TSconfig for this module/current page:
			$this->web_list_modTSconfig = t3lib_BEfunc::getModTSconfig($this->pageinfo['uid'],'mod.web_list');
			$this->allowedNewTables = t3lib_div::trimExplode(',',$this->web_list_modTSconfig['properties']['allowedNewTables'],1);

				// Acquiring TSconfig for this module/parent page:
			$this->web_list_modTSconfig_pid = t3lib_BEfunc::getModTSconfig($this->pageinfo['pid'],'mod.web_list');
			$this->allowedNewTables_pid = t3lib_div::trimExplode(',',$this->web_list_modTSconfig_pid['properties']['allowedNewTables'],1);

			$access = TRUE;
		}


$access = TRUE;

		//
		// Main
		//
		if ($access)	{


			//
			// Output page header
			//
			$this->actionTarget = $this->actionTarget ? $this->actionTarget : t3lib_div::linkThisScript();
			$this->doc->form='<form action="'.htmlspecialchars($this->actionTarget).'" method="post" name="editform" enctype="'.$TYPO3_CONF_VARS['SYS']['form_enctype'].'">';

				// JavaScript
			$this->doc->JScodeArray['jumpToUrl'] = '
				var script_ended = 0;
				var changed = 0;

				function jumpToUrl(URL)	{
					document.location = URL;
				}

				function jumpBack()	{
					document.location = "'.$this->returnUrl.'";
				}
				';
			$this->doc->postCode.= $this->doc->wrapScriptTags('
				script_ended = 1;');


			$this->extObjHeader();


				// Draw the header.
			$this->content.= $this->doc->startPage($this->pageTitle);
			$this->content.= $this->doc->header($this->pageTitle);
			$this->content.= $this->doc->spacer(5);


			//
			// Call submodule function
			//

			$this->extObjContent();


			$this->content.= $this->doc->spacer(10);


		} else {
				// If no access
			$this->content.= $this->doc->startPage($LANG->getLL('title'));
			$this->content.= $this->doc->header($LANG->getLL('title'));
			$this->content.= $this->doc->spacer(5);
			$this->content.= $this->doc->section('', $LANG->sL('LLL:EXT:lang/locallang_mod_web_perm.xml:A_Denied',1));
			$this->content.= $this->doc->spacer(10);
		}


	}

	/**
	 * Prints out the module HTML
	 *
	 * @return	string		HTML
	 */
	function printContent()	{
		$this->content.= $this->doc->middle();
		$this->content.= $this->doc->endPage();
		$this->content=$this->doc->insertStylesAndJS($this->content);
		echo $this->content;
	}

	/**
	 * Returns a message that the passed command was wrong
	 *
	 * @return	string 	HTML content
	 */
	function wrongCommandMessageBox()	{
		global  $LANG;

		$content = $GLOBALS['SOBE']->doc->section('',$GLOBALS['SOBE']->doc->icons(2).' '.$LANG->getLL('tx_damcatedit_cmd_nothing.message'));
		if ($GLOBALS['SOBE']->CMD) {
			$content.= $GLOBALS['SOBE']->doc->section('Command:',htmlspecialchars($GLOBALS['SOBE']->CMD), 0,0);
		}
		return $content;
	}

	/**
	 * Send redirect header
	 *
	 * @return	void
	 */
	function redirect()	{
		if ($this->redirect) {
			Header('Location: '.t3lib_div::locationHeaderUrl($this->redirect));
			exit;
		}
	}


// ----------------------------------------------------------



	/**
	 * Button: go back
	 *
	 * @param	array		Params array. Used to build a url with t3lib_div::linkThisScript()
	 * @param	string		Full url which should be the link href
	 * @return	string		Button HTML code
	 */
	function btn_back($params=array(), $absUrl='')	{
		global $LANG, $BACK_PATH;

		if ($absUrl) {
			$url = $absUrl;
		} else {
			$url = t3lib_div::linkThisScript($params);
		}

		$content = '<a href="'.htmlspecialchars($url).'" class="typo3-goBack">'.
					'<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/goback.gif"','width="14" height="14"').' class="absmiddle" alt="" /> '.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.goBack',1).
					'</a>';

		return $content;
	}


}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam_catedit/mod_cmd/index.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam_catedit/mod_cmd/index.php']);
}






// Make instance:
$SOBE = t3lib_div::makeInstance('tx_damcatedit_mod_cmd');
$SOBE->init();

// Include files?
reset($SOBE->include_once);
while(list(,$INC_FILE)=each($SOBE->include_once))	{include_once($INC_FILE);}
$SOBE->checkExtObj();	// Checking for first level external objects

// Repeat Include files! - if any files has been added by second-level extensions
reset($SOBE->include_once);
while(list(,$INC_FILE)=each($SOBE->include_once))	{include_once($INC_FILE);}
$SOBE->checkSubExtObj();	// Checking second level external objects

$SOBE->main();
$SOBE->printContent();
?>