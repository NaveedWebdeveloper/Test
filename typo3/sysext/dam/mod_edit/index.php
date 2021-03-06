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
 * Command module for edit command
 * This is more or less the same as mod_cmd, but in the future it might include more functions useful for editor modules.
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-ModCmd
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   93: class tx_dam_edit extends tx_dam_SCbase
 *  202:     function init()
 *  289:     function handleExternalFunctionValue($MM_key='function', $MS_value=NULL)
 *  317:     function main()
 *  414:     function jumpToUrl(URL)
 *  418:     function jumpBack()
 *  422:     function navFrameReload()
 *  473:     function printContent()
 *
 *              SECTION: misc stuff
 *  496:     function extObjAccess()
 *  511:     function redirect($updateNavFrame=false)
 *
 *              SECTION: Item stuff
 *  537:     function compileFilesAndRecordsData()
 *
 *              SECTION: GUI stuff
 *  580:     function makePageHeader()
 *  597:     function wrongCommandMessageBox()
 *  622:     function accessDeniedMessageBox($msg='')
 *  636:     function buttonBack($linesBefore=1)
 *  659:     function getFormInputField ($field, $value, $size=0)
 *
 * TOTAL FUNCTIONS: 15
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



unset($MCONF);
require ('conf.php');
require ($BACK_PATH.'init.php');
require ($BACK_PATH.'template.php');

require_once(PATH_txdam.'lib/class.tx_dam_scbase.php');

$LANG->includeLLFile('EXT:dam/mod_cmd/locallang.xml');
$LANG->includeLLFile('EXT:dam/mod_edit/locallang.xml');


// Module is available to everybody - submodules may deny access
// $BE_USER->modAccess($MCONF,1);



require_once(PATH_t3lib.'class.t3lib_basicfilefunc.php');
require_once(PATH_txdam.'lib/class.tx_dam_tce_file.php');
require_once(PATH_txdam.'lib/class.tx_dam_listfiles.php');



/**
 * Script class for the DAM command script
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-ModCmd
 */
class tx_dam_edit extends tx_dam_SCbase {


	/**
	 * GP parameter: CMD
	 * 'CMD' is the parameter which defines the action command like tx_dam_cmd_filedelete or tx_dam_cmd_filerename that should be called for the passed parameter.
	 * Of course not all commands can handle all parameter/item types. Eg tx_dam_cmd_filedelete can handle items passed with GP parameter: file.
	 */
	 var $CMD;

	/**
	 * GP parameter: file
	 * 'file' is the parameter that passes the name of the file (including path) the action should be processed for.
	 *
	 * An array of files can be passed with 'file' in principle, but currently there's no command supporting that.
	 * After processing this variable is an array no matter if only one or more items were passed.
	 *
	 * @var array
	 */
	 var $file = array();

	/**
	 * GP parameter: record
	 * 'record' is the parameter that passes info's about the record the action should be processed for.
	 * supported formats:
	 * &record=table:uid
	 * &record=table:uid,uid_2,6,45,8
	 *
	 * A comma list of uid's can be passed with 'record' in principle, but currently there's no command supporting that.
	 * After processing this variable is an array no matter if only one or more items were passed.
	 *
	 * @var array
	 */
	 var $record = array();





	/**
	 * GP parameter: redirect
	 *
	 * Will be used as url to return to automatically (for silent comands). If not set $this->returnUrl will be used.
	 */
	var $redirect;

	/**
	 * GP parameter: returnUrl
	 *
	 * This url will be used for manual return. Means for 'back' buttons etc.
	 */
	var $returnUrl;



	/**
	 * If set to FALSE the user is not allowed to access the current module action.
	 * Might be set by sub-modules.
	 * The meaning of this flag is that the submudule might decide that the user don have enough right to perform the action. This can be the
	 * example: $this->pObj->actionAccess = tx_dam::access_checkFileOperation('deleteFolder');
	 */
	var $actionAccess = NULL;

	/**
	 * internal
	 */

	/**
	 * The action for the form tag.
	 * Might be set by sub-modules.
	 */
	var $actionTarget = '';

	/**
	 * the page title
	 */
	var $pageTitle = '[no title]';

	/**
	 * tx_dam_tce_file object
	 */
	var $TCEfile;



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


			// Initialize file GPvar
		if (is_array($param = t3lib_div::_GP('file'))) {
			$this->file = $param;
		} elseif ($param) {
			$this->file[] = $param;
		}

			// Initialize record GPvar
		if ($param = t3lib_div::_GP('record')) {
			if (is_array($param)) {
				$this->record = $param;
			} else {
				list($table, $uidList) = explode(':', $param);
				$this->record[$table] = $uidList;
			}

			foreach ($this->record as $table => $uidList) {
				if (is_array($GLOBALS['TCA'][$table]) AND $uidList=$GLOBALS['TYPO3_DB']->cleanIntList($uidList)) {
					$this->record[$table] = explode(',', $uidList);
				} else {
					unset($this->record[$table]);
				}
			}
		}

			// Initialize target GPvar
		$this->target = t3lib_div::_GP('target');

			// Initialize data GPvar - may be used with forms
		$this->data = t3lib_div::_GP('data');



		$this->returnUrl = t3lib_div::_GP('returnUrl');
		$this->returnUrl = $this->returnUrl ? $this->returnUrl : t3lib_div::getIndpEnv('HTTP_REFERER');


		$this->redirect = t3lib_div::_GP('redirect');
		$this->redirect = $this->redirect ? $this->redirect : $this->returnUrl;


		//
		// Init TCE-file-functions object:
		// has object: ->fileProcessor = t3lib_div::makeInstance('tx_dam_extFileFunctions');
		//

		$this->TCEfile = t3lib_div::makeInstance('tx_dam_tce_file');
		$this->TCEfile->init();
		$this->TCEfile->overwriteExistingFiles(t3lib_div::_GP('overwriteExistingFiles'));

		$this->pageTitle = $GLOBALS['LANG']->getLL('tx_dam_edit.title');

		parent::init();
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
			}
		}

		if ($MS_value) {
			$this->extClassConf = $this->getExternalItemConfig($this->MCONF['name'],$MM_key,$MS_value);
			if (is_array($this->extClassConf) && $this->extClassConf['path'])	{
				require_once($this->extClassConf['path']);
			}
		}
	}



	/**
	 * Main function of the module. Write the content to $this->content
	 *
	 * @return	void
	 */
	function main()	{
		global $BE_USER, $LANG, $BACK_PATH, $TYPO3_CONF_VARS, $HTTP_GET_VARS, $HTTP_POST_VARS;


		$access = false;
		
		$this->errorMessages = array();

		$this->media = NULL;			
		
		$editorList = array();
		
		$this->actionTarget = $this->actionTarget ? $this->actionTarget : t3lib_div::linkThisScript(array('returnUrl' => $this->returnUrl, 'redirect' => $this->redirect));



		//
		// get media that should be edited
		//


		if ($this->file) {
			foreach ($this->file as $key => $filename) {
				if (!tx_dam::access_checkFile($filename)) {
					$this->errorMessages['file'][] = tx_dam::file_normalizePath($filename);
					unset($this->file[$key]);
				}
			}
			if ($this->file) {
				$this->media = tx_dam::media_getForFile($this->file[0]);
				if (!$this->media->isAvailable) {
					$this->errorMessages['file'][] = $this->media->filename;
					unset($this->media);
				}
			}


		} elseif ($this->record AND $this->defaultPid) {
			foreach ($this->record as $table => $uidList) {

				$where = array();
				$where['enableFields'] = tx_dam_db::deleteClause($table);
				$where['pidList'] = $table.'.pid IN ('.$this->defaultPid.')';
				$where['uid'] = $table.'.uid IN ('.implode(',',$uidList).')';

				$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid', $table, implode(' AND ', $where), '', '', '', 'uid');
				if ($rows) {
					$this->record[$table] = array_keys($rows);
				} else {
					$this->errorMessages['record'][$table] = $uidList;
					unset($this->record[$table]);
				}
			}
			if ($this->record['tx_dam']) {
					// reduce passed files/records to just one item
					// it will be done here because later editors might want to get multiple resources (eg. blending images) so we keep the infrastructure
				reset($this->record['tx_dam']);
					// just one uid
				$uid = current($this->record['tx_dam']);
			
				$this->media = tx_dam::media_getByUid($uid);
				if (!$this->media->isAvailable) {
					$this->errorMessages['file'][] = $this->media->filename;
					unset($this->media);
				}
			}
		}


		$access = ($this->hasExtObjDefined() OR is_object($this->media));




		//
		// Main
		//


			// a valid file is selected
		if ($access)	{
			
			$success = false;
			
				// an editor is not already defined by CMD
			if (!$this->hasExtObjDefined() AND is_object($this->media)) {
				if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['editorClasses']))	{
					foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['editorClasses'] as $idName => $classRessource)	{
						if (is_object($editorList[$idName] = t3lib_div::getUserObj($classRessource)))      {
							if (!$editorList[$idName]->isValid($this->media)) {
								unset($editorList[$idName]);
							}
						}
					}
				}

				if (!count ($editorList)) {
						// error message no valid editor found for file
					$this->errorMessages['error'][] = sprintf($LANG->getLL('messageNoEditorFound', 1),$this->media->filename);
					
				} elseif (count ($editorList)==1) {

					// activate extObj
					$this->errorMessages['error'][] = 'TODO: activate extObj';
					if (!$this->CMD) {
						$this->CMD = key($editorList);
					}
					$this->handleExternalFunctionValue('function', $this->CMD);
				}
				
				// selection of multiple editors is below
			}
			
			
			
				// an editor is selected
			if ($this->hasExtObjDefined())	{
				$this->checkExtObj();	// Checking for first level external objects
				$this->checkSubExtObj();	// Checking second level external objects		
			}			



			if (is_object($this->extObj))	{
			
			
				$this->extObjCmdInit();
		
		
				//
				// Initialize the template object
				//
		
				if (!is_object($this->doc)) {
					$this->doc = t3lib_div::makeInstance('template'); 
					$this->doc->backPath = $BACK_PATH;
					$this->doc->setModuleTemplate(t3lib_extMgm::extRelPath('dam') . 'res/templates/mod_edit.html');
					$this->doc->styleSheetFile2 = t3lib_extMgm::extRelPath('dam') . 'res/css/stylesheet.css';
					$this->doc->docType = 'xhtml_trans';	
				}
		
		
				//
				// check access
				//
		
		
				$this->actionAccess = $this->extObjAccess();

				if ($access AND $this->actionAccess)	{
		
					$success = true;
		
					//
					// Output page header
					//
		
					$this->doc->form = '<form action="'.htmlspecialchars($this->actionTarget).'" method="post" name="editform" enctype="'.$TYPO3_CONF_VARS['SYS']['form_enctype'].'">';
					$this->doc->form .= '<input type="hidden" name="CMD" value="'.$this->CMD.'" />';
					
						// JavaScript
					$this->doc->JScodeArray['jumpToUrl'] = '
						var script_ended = 0;
						var changed = 0;
		
						function jumpToUrl(URL)	{
							document.location.href = URL;
						}
		
						function jumpBack()	{
							document.location.href = "'.htmlspecialchars($this->redirect).'";
						}
		
						function navFrameReload() {
							if (top.content && top.content.nav_frame && top.content.nav_frame.refresh_nav)	{
								// top.content.nav_frame.refresh_nav();
							}
						}
						';
					$this->doc->postCode.= $this->doc->wrapScriptTags('
						script_ended = 1;');
		
		
					$this->makePageHeader();
		
		
					//
					// Call submodule function
					//

					$this->extObjContent();
					$this->markers['CONTENT'] = $this->content; 

				} else {
					$access = false;
					$this->errorMessages['error'][] = sprintf($LANG->getLL('messageCmdDenied', true),$this->pageTitle);
				}
			}
		}
		

		if (!is_object($this->doc)) {
			$this->doc = t3lib_div::makeInstance('template'); 
			$this->doc->backPath = $BACK_PATH;
			$this->doc->setModuleTemplate(t3lib_extMgm::extRelPath('dam') . 'res/templates/mod_edit.html');
			$this->doc->styleSheetFile2 = t3lib_extMgm::extRelPath('dam') . 'res/css/stylesheet.css';
			$this->doc->docType = 'xhtml_trans';
		}


			// provide a selector when multiple editors are available
		if ($access AND (count($editorList)>1)) {
			
			$this->makePageHeader();
			
			$messages = array();
			$messages[] = '<div style="margin: 1em 3em 2em 3em;">'.sprintf($LANG->getLL('messageAvailableEditors', true),$this->media->filename).'</div>';
						
			$messages[] = '<div style="padding-left:3em; display:table-cell">';
			foreach ($editorList as $idName => $editorObj) {
				
				$button = $this->button ($editorObj->getIcon(), $editorObj->getLabel(), $editorObj->getDescription(), $this->actionTarget.'&CMD='.$idName, '', ' style="display:block;"');
				$messages[] = '<div style="margin-bottom:0.8em">'.$button.'</div>';

			}
			$messages[] = '</div>';
			
			
			
			$this->content.= $GLOBALS['SOBE']->getMessageBox ($this->pageTitle, $messages, $this->buttonBack(0), 2);
			
			$success = true;
		}

			
		if (!$access OR !$success)	{
				// If no access


			$this->makePageHeader();

			$messages = array();

			foreach ($this->errorMessages as $type => $items) {
				if ($items) {
					if ($type!=='error' AND $headerText = $LANG->getLL($type,1)) {
						$messages[] = '<h4>'.$LANG->getLL($type, true).'</h4>';
					}
					foreach ($items as $item) {
						$messages[] = '<p>'.htmlspecialchars($item).'</p>';
					}
				}
			}

				// file do not exist ...
			if (!$access)	{
				$this->content.= $this->accessDeniedMessageBox(implode('', $messages));
			} else {
				$this->content.= $this->errorMessageBox(implode('', $messages));
			}
		}
	}


	/**
	 * Prints out the module HTML
	 *
	 * @return	string		HTML
	 */
	function printContent()	{
		$this->content = $this->doc->startPage($this->pageTitle);     
		$this->content.= $this->doc->moduleBody($this->pageinfo, $this->docHeaderButtons, $this->markers);
		$this->content.= $this->doc->endPage();

		$this->content=$this->doc->insertStylesAndJS($this->content);
		echo $this->content;
	}





	/********************************
	 *
	 *	misc stuff
	 *
	 ********************************/

	/**
	 * Checks if a submodule is defined.
	 *
	 * @return	boolean
	 */
	function hasExtObjDefined() {
		return (is_array($this->extClassConf) && $this->extClassConf['name']);
	}
	
	/**
	 * Calls the 'cmdInit' function of the submodule if present.
	 *
	 * @return	boolean	Default is true
	 */
	function extObjCmdInit()	{
		if (is_callable(array($this->extObj,'cmdInit'))) {
			return $this->extObj->cmdInit();
		} else {
			return true;
		}
	}

	/**
	 * Calls the 'accessCheck' function of the submodule if present.
	 *
	 * @return	boolean	Default is true
	 */
	function extObjAccess()	{
		if (is_callable(array($this->extObj,'accessCheck'))) {
			return $this->extObj->accessCheck();
		} else {
			return true;
		}
	}


	/**
	 * Send redirect header
	 *
	 * @param 	boolean 	$updateNavFrame If set the navigation frame will be updated
	 * @return	void
	 */
	function redirect($updateNavFrame=false)	{
		if ($this->redirect) {
			if ($updateNavFrame) {
				$this->content .= $this->doc->wrapScriptTags('
							navFrameReload();
							jumpBack();');
			} else {
				header('Location: '.t3lib_div::locationHeaderUrl($this->redirect));
				exit;
			}
		}
	}



	/********************************
	 *
	 *	Item stuff
	 *
	 ********************************/

	/**
	 * Compiles meta/fielInfo data for file and record items
	 *
	 * @return array Item array. Key is uid or md5 of filepath
	 */
	function compileFilesAndRecordsData() {

		$items = array();

		if (count($this->file)) {
			foreach ($this->file as $filepath) {
				$fileInfo = tx_dam::file_compileInfo($filepath, true);
				$meta = tx_dam::meta_getDataForFile($fileInfo, '*');
				if (!is_array($meta)) {
					$fileType = tx_dam::file_getType ($filepath);
					$meta = array_merge($fileInfo, $fileType);
					$meta['uid'] = 0;
				}
				$id = $meta['uid'] ? $meta['uid'] : md5(tx_dam::file_absolutePath($fileInfo));
				$items[$id] = array_merge($meta, $fileInfo);
			}

		} elseif (count($this->record['tx_dam'])) {
			foreach ($this->record['tx_dam'] as $uid) {
				if ($meta = tx_dam::meta_getDataByUid($uid, '*')) {
					$fileInfo = tx_dam::file_compileInfo($meta, true);
					$items[$meta['uid']] = array_merge($meta, $fileInfo);
				}
			}
		}

		return $items;
	}



	/********************************
	 *
	 *	GUI stuff
	 *
	 ********************************/


	/**
	 * Render page header (title)
	 *
	 * @return	void
	 */
	function makePageHeader()	{
		$this->extObjHeader();

		if (is_callable(array($this->extObj,'getContextHelp'))) {
			$this->markers['CSH'] = $this->extObj->getContextHelp();
		}
	}


	/**
	 * Returns a message box that the passed command was wrong
	 *
	 * @return	string 	HTML content
	 */
	function wrongCommandMessageBox()	{
		global  $LANG;

		$content = '';

		if ($GLOBALS['SOBE']->CMD) {
			$msg[] = $LANG->getLL('tx_dam_cmd_nothing.messageUnknownCmd',1);
			$msg[] = 'Command: '.htmlspecialchars($GLOBALS['SOBE']->CMD);
		}
		else {
			$msg[] = $LANG->getLL('tx_dam_cmd_nothing.messageNoCmd',1);
		}

		$content .= $GLOBALS['SOBE']->getMessageBox ($LANG->getLL('error'), $msg, $this->buttonBack(0), 2);

		return $content;
	}


	/**
	 * Returns an access denied message box
	 *
	 * @param 	mixed 	$info Additional content as string or array (will be wrapped in <p> tags.
	 * @return	string 	HTML content
	 */
	function accessDeniedMessageBox($msg='')	{
		global  $LANG;

		$content = $GLOBALS['SOBE']->getMessageBox ($LANG->getLL('accessDenied'), $msg, $this->buttonBack(0), 2);
		return $content;
	}


	/**
	 * Returns an error message box
	 *
	 * @param 	mixed 	$info Additional content as string or array (will be wrapped in <p> tags.
	 * @return	string 	HTML content
	 */
	function errorMessageBox($msg='')	{
		global  $LANG;

		$content = $GLOBALS['SOBE']->getMessageBox ($LANG->getLL('error'), $msg, $this->buttonBack(0), 2);
		return $content;
	}


	/**
	 * Renders a 'back' button
	 *
	 * @param	float		Padding-top for the div-section as em
	 * @return string HTML
	 */
	function buttonBack($linesBefore=1) {
		$content = '';
		if ($linesBefore) {
			$content .= '

	<!-- Spacer element -->
	<div style="padding-top: '.number_format((float)$linesBefore,1).'em;"></div>
';
		}

		$content .= $this->btn_back('',$this->returnUrl);
		return $content;
	}


	/**
	 * Can be used to generate simple form fields
	 *
	 * @param string $field field name
	 * @param string $value Value for the input field
	 * @param integer $size Can be used to set a specific size for the input field in em's.
	 * @return string Input field with label wrapped in div tag
	 */
	function getFormInputField ($field, $value, $size=0) {
		global $LANG, $TCA;

		t3lib_div::loadTCA('tx_dam');

		$size = $size ? $size : $TCA['tx_dam']['columns'][$field]['config']['size'];
		$size = $size ? $size : 20;

		$max = $TCA['tx_dam']['columns'][$field]['config']['max'];
		$max = $max ? $max : 256;

		return '<div style="margin-bottom:0.8em">
					<strong>'.tx_dam_guifunc::getFieldLabel($field).'</strong><br />
					<input type="text" name="data['.$field.']" value="'.$value.'" style="width:'.$size.'em;" maxlength="'.$max.'" />
				</div>';
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_edit/index.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_edit/index.php']);
}






// Make instance:
$SOBE = t3lib_div::makeInstance('tx_dam_edit');
$SOBE->init();

$SOBE->checkExtObj();	// Checking for first level external objects
$SOBE->checkSubExtObj();	// Checking second level external objects

$SOBE->main();
$SOBE->printContent();
?>