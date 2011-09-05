<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004-2006 Rene Fritz (r.fritz@colorcube.de)
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
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Module 'Categories' for the 'dam_catedit' extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 */



	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require ('conf.php');
require ($BACK_PATH.'init.php');
require ($BACK_PATH.'template.php');
$LANG->includeLLFile('EXT:dam_catedit/mod1/locallang.xml');

$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]



require_once (PATH_t3lib.'class.t3lib_scbase.php');

require_once(PATH_txdam.'lib/class.tx_dam_scbase.php');

$LANG->includeLLFile('EXT:lang/locallang_mod_web_list.xml');

#require_once(t3lib_extmgm::extPath('dam_catedit').'class.tx_dam_db_list.php');
require_once(t3lib_extmgm::extPath('dam_catedit').'class.tx_dam_db_list2.php');
require_once(PATH_txdam.'lib/class.tx_dam_sysfolder.php');
require_once(t3lib_extmgm::extPath('dam_catedit').'lib/class.tx_damcatedit_db.php');

require_once(PATH_txdam.'lib/class.tx_dam_browsetrees.php');


class tx_damcatedit_module1 extends tx_dam_SCbase {

	public $moduleContent;


	/**
	 * Main function of the module. Write the content to $this->content
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA,$TYPO3_CONF_VARS;


		$this->defaultPid = tx_dam_db::getPid();
		$this->id = $this->id ? $this->id : $this->defaultPid;


		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

		if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{

				// Draw the header.
			$this->doc = t3lib_div::makeInstance('template');
			$this->doc->backPath = $BACK_PATH;
			$this->doc->setModuleTemplate(t3lib_extMgm::extRelPath('dam_catedit') . 'mod1/mod_template.html');
			$this->doc->form='<form action="" method="post">';
			$this->doc->styleSheetFile2 = t3lib_extMgm::extRelPath('dam') . 'res/css/stylesheet.css';

			if (t3lib_div::compat_version('4.2.0')) {  
				$this->doc->getContextMenuCode();
			} else {
				$CMparts = $this->doc->getContextMenuCode();
				$this->doc->bodyTagAdditions = $CMparts[1];
				$this->doc->JScode .= $CMparts[0];
				$this->doc->postCode .= $CMparts[2];
			}
			
				// Add JavaScript functions to the page:
			$this->doc->JScode=$this->doc->wrapScriptTags('
				function jumpToUrl(URL)	{	//
					window.location.href = URL;
					return false;
				}
				function jumpExt(URL,anchor)	{	//
					var anc = anchor?anchor:"";
					window.location.href = URL+(T3_THIS_LOCATION?"&returnUrl="+T3_THIS_LOCATION:"")+anc;
					return false;
				}
				function jumpSelf(URL)	{	//
					window.location.href = URL+(T3_RETURN_URL?"&returnUrl="+T3_RETURN_URL:"");
					return false;
				}

				function setHighlight(id)	{	//
					top.fsMod.recentIds["dam_cat"]=id;
					top.fsMod.navFrameHighlightedID["dam_cat"]="pages"+id+"_"+top.fsMod.currentBank;	// For highlighting

				}
				'.$this->doc->redirectUrls(t3lib_div::getIndpEnv('REQUEST_URI')).'
				function editRecords(table,idList,addParams,CBflag)	{	//
					window.location.href="'.$BACK_PATH.'alt_doc.php?returnUrl='.rawurlencode(t3lib_div::getIndpEnv('REQUEST_URI')).
						'&edit["+table+"]["+idList+"]=edit"+addParams;
				}
				function editList(table,idList)	{	//
					var list="";

						// Checking how many is checked, how many is not
					var pointer=0;
					var pos = idList.indexOf(",");
					while (pos!=-1)	{
						if (cbValue(table+"|"+idList.substr(pointer,pos-pointer))) {
							list+=idList.substr(pointer,pos-pointer)+",";
						}
						pointer=pos+1;
						pos = idList.indexOf(",",pointer);
					}
					if (cbValue(table+"|"+idList.substr(pointer))) {
						list+=idList.substr(pointer)+",";
					}

					return list ? list : idList;
				}

				if (top.fsMod) top.fsMod.recentIds["dam_cat"] = '.intval($this->id).';

			');

				// title in document body
			$this->content.= $this->doc->header($LANG->getLL('title'));
			$this->content.= $this->doc->spacer(5);

				// render module content
			$this->content.= $this->moduleContent();
			
			$page = $this->doc->startPage($LANG->getLL('title'));
			$page.= $this->doc->moduleBody(
				array(),
				$this->getDocHeaderButtons(),
				$this->getTemplateMarkers()
			);
			$page .= $this->doc->endPage();

			$this->content = $page;
			
		} else {
				// If no access or if ID == zero

			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->backPath = $BACK_PATH;

			$this->content.=$this->doc->startPage($LANG->getLL('title'));
			$this->content.=$this->doc->header($LANG->getLL('title'));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->spacer(10);
		}
	}

	/**
	 * Prints out the module HTML
	 */
	function printContent()	{
		echo $this->content;
	}

	/**
	 * Generates the module content
	 */
	function moduleContent()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA,$TYPO3_CONF_VARS;

//		$content='This is the GET/POST vars sent to the script:<BR>'.
//			'GET:'.t3lib_div::view_array($GLOBALS['HTTP_GET_VARS']).'<BR>'.
//			'POST:'.t3lib_div::view_array($GLOBALS['HTTP_POST_VARS']).'<BR>'.
//			'';
//		$content=$this->doc->section('Message #1:',$content,0,1);

		$cmd = t3lib_div::_GPmerged('SLCMD');

		if (is_array($cmd['SELECT']['txdamCat'])) {
			$uid = intval(key($cmd['SELECT']['txdamCat']));
		}

		$treedb = t3lib_div::makeInstance('tx_damcatedit_db');
		$treedb->init('tx_dam_cat', 'parent_id');
		$treedb->setPidList($this->id);
// TODO overlay cat records with BE users language
		$treedb->where_default .= ' AND sys_language_uid=0';


		if (isset($uid) OR $GLOBALS['BE_USER']->isAdmin()) {
			$recCount = $treedb->countSubRecords($uid);
		} else {

				// the trees
			$browseTrees = t3lib_div::makeInstance('tx_dam_browseTrees');
				// show only categories:
			$selClass = array('txdamCat' => $TYPO3_CONF_VARS['EXTCONF']['dam']['selectionClasses']['txdamCat']);
			$browseTrees->initSelectionClasses($selClass, 'tx_dam_catedit_navframe.php');

			$mounts = $browseTrees->getMountsForTreeClass('txdamCat');
			$recCount = count($mounts);
		}


		$this->selection->pointer->setTotalCount($recCount);

		if($this->selection->pointer->countTotal) {


			$dblist = t3lib_div::makeInstance('tx_dam_db_list');
			$dblist->init('tx_dam_cat');

			$dblist->backPath = $BACK_PATH;
			$dblist->returnURL = t3lib_div::linkThisScript(array('SLCMD[SELECT][txdamCat]['.$uid.']'=>'1'));
			$dblist->staticParams = '&SLCMD[SELECT][txdamCat]['.$uid.']=1';
			$dblist->calcPerms = $BE_USER->calcPerms($this->pageinfo);
			$dblist->alternateBgColors=$this->modTSconfig['properties']['alternateBgColors']?1:0;
			$dblist->thumbs = false;

			$dblist->pointer = $this->selection->pointer;

			$dblist->searchString = trim(t3lib_div::_GP('search_field'));
			$dblist->sortField = t3lib_div::_GP('sortField');
			$dblist->sortRev = t3lib_div::_GP('sortRev');


			$dblist->setDispFields();
			#		$fieldList	= 'tx_dam_cat.'.implode(',tx_dam_cat.',t3lib_div::trimExplode(',',$dblist->setFields['tx_dam_cat'],1));
			#		$this->selection->qg->query['FROM']['tx_dam_cat']=$fieldList;

			$defaultSortBy = ($TCA['tx_dam_cat']['ctrl']['default_sortby']) ? $GLOBALS['TYPO3_DB']->stripOrderBy($TCA['tx_dam_cat']['ctrl']['default_sortby']) : '';
			$sortby = $TCA['tx_dam_cat']['ctrl']['sortby'] ? $TCA['tx_dam_cat']['ctrl']['sortby'] : '';
			$orderFields = ($defaultSortBy) ? $defaultSortBy : $sortby;
			$orderFieldsArr = t3lib_div::trimExplode(',', $orderFields);
			foreach($orderFieldsArr as $k=>$v) {
				$orderFieldsArr[$k] = 'tx_dam_cat.' .$v;
			}
			$orderBy = implode(',', $orderFieldsArr);

			if ($dblist->sortField)	{
				if (in_array($dblist->sortField,$dblist->makeFieldList('tx_dam_cat',1)))	{
					$orderBy = 'tx_dam_cat.'.$dblist->sortField;
					if ($dblist->sortRev)	$orderBy.=' DESC';
				}
			}

			$treedb->setResReturn(true);
			$treedb->setSortFields($orderBy);

			if (isset($uid) OR $GLOBALS['BE_USER']->isAdmin()) {
				$dblist->res = $treedb->getSubRecords($uid, 'tx_dam_cat.*');
			} else {
				$uids = implode(',',$mounts);
				$dblist->res = $treedb->getRecords ($uids, 'tx_dam_cat.*');
			}



	#TODO ???				// It is set, if the clickmenu-layer is active AND the extended view is not enabled.
#			$dblist->dontShowClipControlPanels = $CLIENT['FORMSTYLE'] && !$BE_USER->uc['disableCMlayers'];

			$dblist->generateList();

			$this->moduleContent.= '<form action="'.$dblist->listURL().'" method="post" name="dblistForm">';
			$this->moduleContent.= $dblist->HTMLcode;
			$this->moduleContent.= '<input type="hidden" name="cmd_table" /><input type="hidden" name="cmd" /></form>';
			$this->moduleContent.= $dblist->fieldSelectBox();
		}
		
		return $this->moduleContent;
		
	}

	/**
	 * Gets the filled markers that are used in the HTML template.
	 *
	 * @return	array		The filled marker array
	 */
	protected function getTemplateMarkers() {
		$markers = array(
			'CONTENT'   => $this->content,
			'TITLE'     => $GLOBALS['LANG']->getLL('title'),
		);

		return $markers;
	}

	/**
	 * Gets the buttons that shall be rendered in the docHeader.
	 *
	 * @return	array		Available buttons for the docHeader
	 */
	protected function getDocHeaderButtons() {
		$buttons = array(
			'addcat'=> $this->getAddCategoryButton(),
			'shortcut' => $this->getShortcutButton(),
		);

		return $buttons;
	}

	/**
	 * Gets the button to add a new category.
	 *
	 * @return	string		HTML representiation of the add category button
	 */
	protected function getAddCategoryButton($table='tx_dam_cat') {
		global $BE_USER,$LANG,$BACK_PATH,$TCA,$TYPO3_CONF_VARS;

		$cmd = t3lib_div::_GPmerged('SLCMD');

		if (is_array($cmd['SELECT']['txdamCat'])) {
			$uid = intval(key($cmd['SELECT']['txdamCat']));
 		}
		
		$sysfolder = t3lib_div::makeInstance('tx_dam_sysfolder');
		$sysfolderUid = $sysfolder->getPidList();
		
		$result = '<a href="' . $BACK_PATH . t3lib_extMgm::extRelPath('dam_catedit') . 'mod_cmd/index.php?CMD=tx_damcatedit_cmd_new&returnUrl=' . rawurlencode(t3lib_div::getIndpEnv('REQUEST_URI')) . '&vC=' . $GLOBALS['BE_USER']->veriCode() . '&edit[' . $table . '][-' . $uid . ']=new&defVals[' . $table . '][parent_id]=' . $uid . '&defVals[' . $table . '][pid]='. $this->backRef->id. '">';
 
		if (t3lib_div::int_from_ver(TYPO3_version) < 4004000) {
			$result .= '<img' . t3lib_iconWorks::skinImg($BACK_PATH,'gfx/new_el.gif','width="11" height="12"') . ' alt="" />';
		} else {
			$result .= t3lib_iconWorks::getSpriteIcon('actions-document-new', array('title' => $LANG->sL('LLL:EXT:lang/locallang_core.xml:cm.createnew',1)));
		}

		$result .= '</a>';
		return $result;
 	}
	
	
	/**
	 * Gets the button to set a new shortcut in the backend (if current user is allowed to).
	 *
	 * @return	string		HTML representiation of the shortcut button
	 */
	protected function getShortcutButton() {
		$result = '';
		if ($GLOBALS['BE_USER']->mayMakeShortcut()) {
			$result = $this->doc->makeShortcutIcon('', 'function', $this->MCONF['name']);
		}

		return $result;
	}
	
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam_catedit/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam_catedit/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_damcatedit_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>