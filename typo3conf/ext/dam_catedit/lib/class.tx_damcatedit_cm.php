<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 David Steeb, Benjamin Mack (david@b13.de, benni@b13.de)
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
 * Clickmenu code for the dam_catedit extension
 *
 * @author	David Steeb (david@b13.de)
 * @author	Benjamin Mack (benni@b13.de)
 */
require_once(PATH_txdam.'lib/class.tx_dam_db.php');

class tx_damcatedit_cm {
	var $rec;		// the current record (as an array) that was clicked
	var $backRef;	// clickMenu object, see typo3/alt_clickmenu.php

	/**
	 * Entry function that hooks into the main typo3/alt_clickmenu.php,
	 * see ext_tables.php of this extension for more info
	 *
	 * @param $backRef		the clickMenu object
	 * @param $menuItems	the menuItems as an array that are already filled from the main clickmenu 
	 * @param $table	the table that is worked on (tx_dam_cat only)
	 * @param $uid		the item UID that is worked on
	 * @return unknown
	 */
	function main(&$backRef, $menuItems, $table, $uid) {
		if ($table != 'tx_dam_cat') return $menuItems;
		
		$this->backRef = &$backRef;

			// Get record
		$this->rec = t3lib_BEfunc::getRecordWSOL($table, $uid);
		$menuItems = array();

		$root = (!strcmp($uid, '0') ? true : false);

		if (is_array($this->rec) || $root) {

			$lCP = $GLOBALS['BE_USER']->calcPerms(t3lib_BEfunc::getRecord('pages', $root ? tx_dam_db::getPid() : $this->rec['pid']));

				// Edit
			if(!$root && ($GLOBALS['BE_USER']->isPSet($lCP, $table, 'edit')) && !in_array('edit',$this->backRef->disabledItems)) {
				$menuItems['edit'] = $this->DAMcatEdit($table,$uid);
			}

				// New Category
			if (!in_array('new', $this->backRef->disabledItems) && $GLOBALS['BE_USER']->isPSet($lCP, $table, 'new')) {
				$menuItems['new'] = $this->DAMnewSubCat($table, $uid);
			}
			
				// Info
			if(!in_array('info',$this->backRef->disabledItems) && !$root) {
				$menuItems['info']=$this->DAMcatInfo($table, $uid);
			}
				
				// Delete
			$elInfo = array(t3lib_div::fixed_lgd_cs(t3lib_BEfunc::getRecordTitle($table, $this->rec),$GLOBALS['BE_USER']->uc['titleLen']));
			if(!in_array('delete',$this->backRef->disabledItems) && !$root && $GLOBALS['BE_USER']->isPSet($lCP, $table, 'delete')) {
				$menuItems['spacer2']='spacer';
				$menuItems['delete']=$this->DAMcatDelete($table, $uid, $elInfo);
			}
		}
		return $menuItems;
	}

	
	/**
	 * Returns the menu-item for info-popup
	 *
	 * @param $table	always tx_dam_cat
	 * @param $uid		uid of the specified tx_dam_cat item
	 * @return 			clickMenu-object menu-item, ready for output
	 */
	function DAMcatInfo($table, $uid) {
			return $this->backRef->linkItem(
			$this->backRef->label('info'),
			$this->backRef->excludeIcon('<img'.t3lib_iconWorks::skinImg($this->backRef->PH_backPath,'gfx/zoom2.gif','width="12" height="12"').' alt="" />'),
			"top.launchView('".$table."', '".$uid."'); return hideCM();"
		);
	}
	

	/**
	 * Returns the menu-item for editing the item
	 *
	 * @param $table	always tx_dam_cat
	 * @param $uid		uid of the specified tx_dam_cat item
	 * @return 			clickMenu-object menu-item, ready for output
	 */	
	function DAMcatEdit($table, $uid) {
		$url = 'alt_doc.php?edit['.$table.']['.$uid.']=edit';
		return $this->backRef->linkItem(
			$this->backRef->label('edit'),
			$this->backRef->excludeIcon('<img'.t3lib_iconWorks::skinImg($this->backRef->PH_backPath,'gfx/edit2.gif','width="12" height="12"').' alt="" />'),
			$this->backRef->urlRefForCM($url,'returnUrl'),
			1
		);
	}


	/**
	 * Returns the menu-item for adding a new sub category
	 *
	 * @param $table	always tx_dam_cat
	 * @param $uid		uid of the specified tx_dam_cat item
	 * @return 			clickMenu-object menu-item, ready for output
	 */
	function DAMnewSubCat($table, $uid) {
		$pid = $this->rec['pid'];
		$loc = 'top.content'.(!$this->backRef->alwaysContentFrame ? '.list_frame' : '');
		
		$editOnClick = 'if('.$loc.'){'.$loc.'.location.href=top.TS.PATH_typo3+\'';
		if ($this->backRef->listFrame) {
			$editOnClick .= 'alt_doc.php?returnUrl=\'+top.rawurlencode('.$this->backRef->frameLocation($loc.'.document').')+\'&edit['.$table.'][-'.$uid.']=new&defVals['.$table.'][parent_id]='.$uid.'&defVals['.$table.'][pid]='.$this->backRef->id.'\';}';
		} else {
			$editOnClick .= t3lib_extMgm::extRelPath('dam_catedit').'mod_cmd/index.php?CMD=tx_damcatedit_cmd_new&returnUrl=\'+top.rawurlencode('.$this->backRef->frameLocation($loc.'.document').')+\'&vC='.$GLOBALS['BE_USER']->veriCode().'&id='.intval($pid).'&edit['.$table.'][-'.$uid.']=new&defVals['.$table.'][parent_id]='.$uid.'&defVals['.$table.'][pid]='.$this->backRef->id.'\';}';
		}
		return $this->backRef->linkItem(
			$GLOBALS['LANG']->makeEntities($GLOBALS['LANG']->sL('LLL:EXT:dam_catedit/locallang_cm.php:tx_damcatedit_cm1.newSubCat',1)),
			$this->backRef->excludeIcon('<img'.t3lib_iconWorks::skinImg($this->backRef->PH_backPath,'gfx/new_el.gif','width="11" height="12"').' alt="" />'),
			$editOnClick.'return hideCM();'
		);
	}
	
	
	/**
	 * Returns the menu-item for deleting an item
	 *
	 * @param $table	always tx_dam_cat
	 * @param $uid		uid of the specified tx_dam_cat item
	 * @return 			clickMenu-object menu-item, ready for output
	 */	
	function DAMcatDelete($table, $uid, $elInfo) {
		$editOnClick = '';
		$loc = 'top.nav';
		$frameLocation = $loc . '.iframe';
		$editOnClick = 'if('.$loc.' && confirm('.$GLOBALS['LANG']->JScharCode(sprintf($GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:mess.delete'),$elInfo[0])).
			')){this.location=' . $loc . '.setUrl(\'tce_db.php?' .
			'redirect=\'+top.rawurlencode(' . $this->backRef->frameLocation($frameLocation) . ')+\'' .
			'&cmd[' . $table . '][' . $uid . '][delete]=1&prErr=1&vC=' . $GLOBALS['BE_USER']->veriCode() . t3lib_BEfunc::getUrlToken('tceAction') .'\');};';

		return $this->backRef->linkItem(
			$this->backRef->label('delete'),
			$this->backRef->excludeIcon('<img'.t3lib_iconWorks::skinImg($this->backRef->PH_backPath,'gfx/garbage.gif','width="11" height="12"').' alt="" />'),
			$editOnClick.'return false;'
		);
	}
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/dam_catedit/lib/class.tx_damcatedit_cm.php']) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/dam_catedit/lib/class.tx_damcatedit_cm.php']);
}

?>