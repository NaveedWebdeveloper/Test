<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2006 Robert Lemke (robert@typo3.org)
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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * New content elements wizard for templavoila
 *
 * $Id: db_new_content_el.php 8140 2008-02-04 21:17:33Z dmitry $
 * Originally based on the CE wizard / cms extension by Kasper Skaarhoj <kasper@typo3.com>
 * XHTML compatible.
 *
 * @author		Robert Lemke <robert@typo3.org>
 * @coauthor	Kasper Skaarhoj <kasper@typo3.com>
 */



class ux_tx_templavoila_dbnewcontentel extends tx_templavoila_dbnewcontentel { 

	protected $myLanguageFiles = array();
	private $coreCTypeList = 'header,text,textpic,image,table,bullets,html';

	
	
	/**
	 * Initialize internal variables.
	 *
	 * @return	void
	 */
	function init()	{
		global $BE_USER,$BACK_PATH,$TBE_MODULES_EXT;

			// Setting class files to include:
		if (is_array($TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']))	{
			$this->include_once = array_merge($this->include_once,$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']);
		}

			// Setting internal vars:
		$this->id = intval(t3lib_div::GPvar('id'));
		$this->parentRecord = t3lib_div::GPvar('parentRecord');
		$this->altRoot = t3lib_div::GPvar('altRoot');
		$this->defVals = t3lib_div::GPvar('defVals');

			// Starting the document template object:
		$this->doc = t3lib_div::makeInstance('mediumDoc');
		$this->doc->docType= 'xhtml_trans';
		$this->doc->backPath = $BACK_PATH;
		$this->doc->JScode='';
		#
		### Mansoor Ahmad - Dont know why it used
		#
		//$this->doc->form='<form action="" name="editForm">';

			// Getting the current page and receiving access information (used in main())
		$perms_clause = $BE_USER->getPagePermsClause(1);
		$pageinfo = t3lib_BEfunc::readPageAccess($this->id,$perms_clause);
		$this->access = is_array($pageinfo) ? 1 : 0;


		$this->apiObj = t3lib_div::makeInstance ('tx_templavoila_api');

			// If no parent record was specified, find one:
		if (!$this->parentRecord) {
			$mainContentAreaFieldName = $this->apiObj->ds_getFieldNameByColumnPosition ($this->id, 0);
			if ($mainContentAreaFieldName != FALSE) {
				$this->parentRecord = 'pages:'.$this->id.':sDEF:lDEF:'.$mainContentAreaFieldName.':vDEF:0';
			}
		}
	}
	
	
	
	/**
	 * Creating the module output.
	 *
	 * @return	void
	 * @todo	provide position mapping if no position is given already. Like the columns selector but for our cascading element style ...
	 */
	function main()	{
		global $LANG,$BACK_PATH,$BE_USER;
		if ($this->id && $this->access)	{

				// Creating content
			$this->content='';
			$this->content.=$this->doc->startPage($LANG->getLL('newContentElement'));
			$this->content.=$this->doc->header($LANG->getLL('newContentElement'));
			$this->content.=$this->doc->spacer(5);

			$elRow = t3lib_BEfunc::getRecordWSOL('pages',$this->id);
			$header= t3lib_iconWorks::getIconImage('pages',$elRow,$BACK_PATH,' title="'.htmlspecialchars(t3lib_BEfunc::getRecordIconAltText($elRow,'pages')).'" align="top"');
			$header.= t3lib_BEfunc::getRecordTitle('pages',$elRow,1);
			$this->content.=$this->doc->section('',$header,0,1);
			$this->content.=$this->doc->spacer(10);

				// Wizard
			$wizardCode='';
			$tableRows=array();
			$wizardItems = $this->getWizardItems();

				// Traverse items for the wizard.
				// An item is either a header or an item rendered with a title/description and icon:
			$counter=0;
			// #
			// ### Mansoor Ahmad @ Gosign media. GmbH - Set it for ...
			// #
			$ignoreList = 'list,templavoila_pi1';
			foreach($wizardItems as $key => $wizardItem){
				if ($wizardItem['header'])	{
					if ($counter>0) $tableRows[]='
						<tr>
							<td colspan="3"><br /></td>
						</tr>';
					$tableRows[]='
						<tr class="bgColor5">
							<td colspan="3"><strong>'.htmlspecialchars($wizardItem['header']).'</strong></td>
						</tr>';
				} else {
					$tableLinks=array();

						// href URI for icon/title:
					$newRecordLink = 'index.php?'.$this->linkParams().'&createNewRecord='.rawurlencode($this->parentRecord).$wizardItem['params'];

						// Icon:
					$iInfo = @getimagesize($wizardItem['icon']);
					$tableLinks[]='<a href="'.$newRecordLink.'"><img'.t3lib_iconWorks::skinImg($this->doc->backPath,$wizardItem['icon'],'').' alt="" /></a>';

						// Title + description:
					$tableLinks[]='<a href="'.$newRecordLink.'"><strong>'.htmlspecialchars($wizardItem['title']).'</strong><br />'.nl2br(htmlspecialchars(trim($wizardItem['description']))).'</a>';

						// Finally, put it together in a table row:
					
					
					// #
					// ### Mansoor Ahmad @ Gosign media. GmbH - start
					// #	
						//print_r($wizardItems);
					$actionForm = 'db_new_content_el.php?'.$this->linkParams().'&parentRecord='.t3lib_div::GPvar('parentRecord');
					if(t3lib_div::GPvar('count') == $counter && t3lib_div::GPvar('go_backend_layout_edit') == 1){	
						$tableRows[]='
						<tr>
							<td valign="top" colspan="3">'.
								$this->getEditTable($wizardItem, $actionForm)
							.'
								<a name="'.$wizardItem['tt_content_defValues']['CType'].'" />
							</td>
						</tr>';
					}
					else{
						$tableRows[]='
							<tr>
								<td valign="top"><a name="'.$wizardItem['tt_content_defValues']['CType'].'" />'.implode('</td>
								<td valign="top">',$tableLinks).'</td>
								<td valign="top">'.(($BE_USER->isAdmin() && !in_array($wizardItem['tt_content_defValues']['CType'],explode(',', $ignoreList)))?'<a href="db_new_content_el.php?'.$this->linkParams().'&parentRecord='.t3lib_div::GPvar('parentRecord').'&go_backend_layout_edit=1&count='.$counter.'#'.$wizardItem['tt_content_defValues']['CType'].'"><img src="../../../../typo3/sysext/t3skin/icons/gfx/edit2.gif" /></a>':'').'</td>
							</tr>';
						$editData = array(	'CType' => t3lib_div::GPvar('CType'),
											'title'	=> t3lib_div::GPvar('title'),
											'desc'	=> t3lib_div::GPvar('desc'));
						if($editData['CType'] == $wizardItem['tt_content_defValues']['CType']){
							$this->saveEditTableData($editData, $wizardItem);
						}
						elseif(t3lib_div::GPvar('submit')){
							header('location:'.$actionForm.'#'.t3lib_div::GPvar('CType'));
						}
					}
					// #
					// ### Mansoor Ahmad @ Gosign media. GmbH - end
					// #	
						
					$counter++;
				}
			}
				// Add the wizard table to the content:
			$wizardCode .= $LANG->getLL('sel1',1).'<br /><br />

			<!--
				Content Element wizard table:
			-->
				<table border="0" cellpadding="1" cellspacing="2" id="typo3-ceWizardTable" style="float:left;">
					'.implode('',$tableRows).'
				</table>
				';
			$this->content .= $this->doc->section($LANG->getLL('1_selectType'), $wizardCode, 0, 1);

		} else {		// In case of no access:
			$this->content='';
			$this->content.=$this->doc->startPage($LANG->getLL('newContentElement'));
			$this->content.=$this->doc->header($LANG->getLL('newContentElement'));
			$this->content.=$this->doc->spacer(5);
		}
	}


	/*
	 * @author:	Mansoor Ahmad @ Gosign media. GmbH
	 * @description: Create a form for editing the Info of Contentelement
	*/
	function getEditTable($wizardItem, $actionForm){
		$content = '
		<form enctype="multipart/form-data" name="editceForm" action="'.$actionForm.'" method="post" >
		<table border="0" cellpadding="1" cellspacing="4" style="float:right;background:#FFFFFF;width:100%;height:auto;">
			<tr>
				<td colspan="2">
					<h3>Elementinfo bearbeiten:</h3>
				</td>
			</tr>
			
			<tr>
				<td>
					<span>Name:</span>
				</td>
				<td>
					<input type="text" name="title" size="25" value="'.$wizardItem['title'].'" />
				</td>
			</tr>
			
			<tr>
				<td>
					<span>Beschreibung:</span>
				</td>
				<td>
					<input type="text" name="desc" size="40" value="'.$wizardItem['description'].'" />
				</td>
			</tr>
			
			<tr>
				<td>
					<span>Wizard:</span>
				</td>
				<td>
					<input type="file" name="wizard" />
				</td>
			</tr>
			
			<tr>
				<td>
					<span>Pageview:</span>
				</td>
				<td>
					<input type="file" name="pageview"/>
				</td>
			</tr>
			
			<tr>
				<td>
					
				</td>
				<td>
					<input type="hidden" name="id" value="'.t3lib_div::GPvar('id').'" />
					<input type="hidden" name="CType" value="'.$wizardItem['tt_content_defValues']['CType'].'" />
					<input type="hidden" name="parentRecord" value="'.t3lib_div::GPvar('parentRecord').'" />
					<input type="submit" name="submit" value="Speichern"/>
					
				</td>
			</tr>
		</table>
		</form>
		
		';
		return $content;
	}



	/*
	 * @author:	Mansoor Ahmad @ Gosign media. GmbH
	 * @description: Save and manage the input of submitted form
	 */
	function saveEditTableData($editData, $wizardItem) {
		global $LANG;

		if(t3lib_extMgm::isLoaded('lfeditor')) {
			/** some needed classes and libraries */
			require_once(t3lib_extMgm::extPath('lfeditor') . 'mod1/class.tx_lfeditor_mod1_file_baseXML.php');
			require_once(t3lib_extMgm::extPath('lfeditor') . 'mod1/class.LFException.php');
			require_once(t3lib_extMgm::extPath('lfeditor') . 'mod1/class.sgLib.php');
			require_once(t3lib_extMgm::extPath('lfeditor') . 'mod1/class.typo3Lib.php');

			$fileObj = t3lib_div::makeInstance('tx_lfeditor_mod1_file_baseXML');

			if(in_array($editData['CType'], explode(',',$this->coreCTypeList))){
				$LLFName[0] = 'wizard.'.$editData['CType'].$wizardItem['tt_content_defValues']['imageorient'];
				$LLFName[1] = 'wizard.'.$editData['CType'].$wizardItem['tt_content_defValues']['imageorient'].'.description';
				$fileObj->init('locallang.xml', PATH_typo3conf . 'ext/' . 'go_backend_layout/');

				// #wizard
				$this->setImageForCType('go_backend_layout_images' ,$_FILES["wizard"]["tmp_name"], $_FILES["wizard"]["name"], 'EXT:go_backend_layout/locallang.xml', 'wizard_'.$editData['CType'].$wizardItem['tt_content_defValues']['imageorient'], 125);

				// #pageview
				$this->setImageForCType('go_backend_layout_images' ,$_FILES["pageview"]["tmp_name"], $_FILES["pageview"]["name"], 'EXT:go_backend_layout/locallang.xml', 'pageview_'.$editData['CType'], 257);
			}
			elseif($wizardItem['tt_content_defValues']['CType'] == 'templavoila_pi1'){
				// # Flexform rendering come here if needed in emergency :-), ask me for the way ... mansoor ahmad
			}
			else{
				list($LLFile, $LLFName) = each($this->myLanguageFiles[$editData['CType']]);
				$fileObj->init(basename($LLFile), str_replace('EXT:', PATH_typo3conf.'ext/', dirname($LLFile)));
				
				// #wizard
				$this->setImageForCType($editData['CType'] ,$_FILES["wizard"]["tmp_name"], $_FILES["wizard"]["name"], $LLFile, 'wizard', 125);
		
				// #pageview
				$this->setImageForCType($editData['CType'], $_FILES["pageview"]["tmp_name"], $_FILES["pageview"]["name"], $LLFile, 'pageview', 257);
			}

			$fileObj->readFile();
			
			if(!empty($editData['title']) && $editData['title'] != $wizardItem['title'] && !($wizardItem['tt_content_defValues']['CType'] == 'templavoila_pi1')) {
				$fileObj->setLocalLangData($LLFName[0], $editData['title'], $LANG->lang);
			}
			if(!empty($editData['desc']) && $editData['desc'] != $wizardItem['description'] && !($wizardItem['tt_content_defValues']['CType'] == 'templavoila_pi1')) {
				$fileObj->setLocalLangData($LLFName[1], $editData['desc'], $LANG->lang);
			}
			
			// write new language data
			try {
				$fileObj->writeFile();
			} catch(LFException $e) {
				print_r($e);
			}
		}else {echo 'NEED EXT "lfeditor"!';}
	}


	/*
	 * @author:	Mansoor Ahmad @ Gosign media. GmbH
	 * @description: Set Images in the right Contentelement dir (pi1)
	 */
	 function setImageForCType($CType, $fileTemp, $fileName, $LLFile, $fileNameOutput, $imageWidth){
		require_once(PATH_t3lib . 'class.t3lib_stdgraphic.php');
		$graphicsStd = t3lib_div::makeInstance('t3lib_stdGraphic');

		$uploadDirname = str_replace('EXT:', '', dirname($LLFile));
		$source = $fileTemp;
		$destinationUploaded = PATH_site . 'typo3temp/' . $fileName;
		t3lib_div::upload_copy_move($source, $destinationUploaded);
		$destination = t3lib_extMgm::extPath($uploadDirname) . str_replace($uploadDirname.'_','',$CType) . '/' . $fileNameOutput . '.png';

		$graphicsStd->init();
		$graphicsStd->tempPath = PATH_site . 'typo3temp/';
		$viewArray = $graphicsStd->imageMagickConvert($destinationUploaded, 'png', $imageWidth);
		rename($viewArray[3], $destination);
	 }






















	/**
	 * Returns the array of elements in the wizard display.
	 * For the plugin section there is support for adding elements there from a global variable.
	 *
	 *
	 *
	 * @return	array
	 */
	function wizardArray()	{
		global $LANG,$TBE_MODULES_EXT,$TYPO3_DB,$TCA;

		$GOBACKEND = $LANG;
		$GOBACKEND->includeLLFile('EXT:go_backend_layout/locallang.xml');

		$defVals = t3lib_div::implodeArrayForUrl('defVals', is_array($this->defVals) ? $this->defVals : array());

		$wizardItems = array(
			//'common' => array('header'=>$LANG->getLL('common')),
			'gosign_1' => array(
				'icon'=>'../typo3conf/ext/go_backend_layout/images/wizard_header.png',
				'title'=>$GOBACKEND->getLL('wizard.header'),
				'description'=>$GOBACKEND->getLL('wizard.header.description'),
				'params'=>'&defVals[tt_content][CType]=header'.$defVals,
			),
			'gosign_2' => array(
				'icon'=>'../typo3conf/ext/go_backend_layout/images/wizard_text.png',
				'title'=>$GOBACKEND->getLL('wizard.text'),
				'description'=>$GOBACKEND->getLL('wizard.text.description'),
				'params'=>'&defVals[tt_content][CType]=text'.$defVals,
			),
			'gosign_3' => array(
				'icon'=>'../typo3conf/ext/go_backend_layout/images/wizard_textpic2.png',
				'title'=>$GOBACKEND->getLL('wizard.textpic2'),
				'description'=>$GOBACKEND->getLL('wizard.textpic2.description'),
				'params'=>'&defVals[tt_content][CType]=textpic&defVals[tt_content][imageorient]=2'.$defVals,
			),
			'gosign_4' => array(
				'icon'=>'../typo3conf/ext/go_backend_layout/images/wizard_image2.png',
				'title'=>$GOBACKEND->getLL('wizard.image2'),
				'description'=>$GOBACKEND->getLL('wizard.image2.description'),
				'params'=>'&defVals[tt_content][CType]=image&defVals[tt_content][imageorient]=2'.$defVals,
			),
			'gosign_5' => array(
				'icon'=>'../typo3conf/ext/go_backend_layout/images/wizard_table.png',
				'title'=>$GOBACKEND->getLL('wizard.table'),
				'description'=>$GOBACKEND->getLL('wizard.table.description'),
				'params'=>'&defVals[tt_content][CType]=table'.$defVals,
			),
			'gosign_6' => array(
				'icon'=>'../typo3conf/ext/go_backend_layout/images/wizard_bullets.png',
				'title'=>$GOBACKEND->getLL('wizard.bullets'),
				'description'=>$GOBACKEND->getLL('wizard.bullets.description'),
				'params'=>'&defVals[tt_content][CType]=bullets'.$defVals,
			),
			'gosign_7' => array(
				'icon'=>'../typo3conf/ext/go_backend_layout/images/wizard_html.png',
				'title'=>$GOBACKEND->getLL('wizard.html'),
				'description'=>$GOBACKEND->getLL('wizard.html.description'),
				'params'=>'&defVals[tt_content][CType]=html'.$defVals,
			),
		);

		// #
		// ### Mansoor Ahmad - you set here CE, which don't shown on the wizard list
		// #
		$ignoreList	= 'header,text,textpic,image,bullets,table,media,search,shortcut,div,html,list,menu,uploads,login,mailform,templavoila_pi1,dlstats_pi1,1,go_stopcslide_pi1';
		$wizardItems = $this->parseWizard($ignoreList, $wizardItems, $defVals);

		// Flexible content elements:
		$positionPid = $this->id;
		$dataStructureRecords = array();
		$storageFolderPID = $this->apiObj->getStorageFolderPid($positionPid);

		// Fetch data structures stored in the database:
		$addWhere = $this->buildRecordWhere('tx_templavoila_datastructure');
		$res = $TYPO3_DB->exec_SELECTquery(
			'*',
			'tx_templavoila_datastructure',
			'pid='.intval($storageFolderPID).' AND scope=2' . $addWhere .
				t3lib_BEfunc::deleteClause('tx_templavoila_datastructure').
				t3lib_BEfunc::versioningPlaceholderClause('tx_templavoila_datastructure')
		);
		while(FALSE !== ($row = $TYPO3_DB->sql_fetch_assoc($res))) {
			$dataStructureRecords[$row['uid']] = $row;
		}
/*
		// Fetch static data structures which are stored in XML files:
		if (is_array($GLOBALS['TBE_MODULES_EXT']['xMOD_tx_templavoila_cm1']['staticDataStructures']))	{
			foreach($GLOBALS['TBE_MODULES_EXT']['xMOD_tx_templavoila_cm1']['staticDataStructures'] as $staticDataStructureArr)	{
				$staticDataStructureArr['_STATIC'] = TRUE;
				$dataStructureRecords[$staticDataStructureArr['path']] = $staticDataStructureArr;
			}
		}
*/
		// Fetch all template object records which uare based one of the previously fetched data structures:
		$templateObjectRecords = array();
		$addWhere = $this->buildRecordWhere('tx_templavoila_tmplobj');
		$res = $TYPO3_DB->exec_SELECTquery(
			'*',
			'tx_templavoila_tmplobj',
			'pid='.intval($storageFolderPID).' AND parent=0' . $addWhere .
				t3lib_BEfunc::deleteClause('tx_templavoila_tmplobj').
				t3lib_BEfunc::versioningPlaceholderClause('tx_templavoila_tmpl'), '', 'sorting'
		);

		while(FALSE !== ($row = $TYPO3_DB->sql_fetch_assoc($res))) {
			if (is_array($dataStructureRecords[$row['datastructure']])) {
				$templateObjectRecords[] = $row;
			}
		}

		// Add the filtered set of TO entries to the wizard list:
		$wizardItems['fce']['header'] = $LANG->getLL('fce');
        foreach($templateObjectRecords as $index => $templateObjectRecord) {
            $tmpFilename = 'uploads/tx_templavoila/'.$templateObjectRecord['previewicon'];
            $wizardItems['fce_'.$index]['icon'] = (@is_file(PATH_site.$tmpFilename)) ? ('../' . $tmpFilename) : ('../' . t3lib_extMgm::siteRelPath('templavoila').'res1/default_previewicon.gif');
            $wizardItems['fce_'.$index]['description'] = $templateObjectRecord['description'] ? htmlspecialchars($templateObjectRecord['description']) : $LANG->getLL ('template_nodescriptionavailable');
            $wizardItems['fce_'.$index]['title'] = $templateObjectRecord['title'];
            $wizardItems['fce_'.$index]['params'] = '&defVals[tt_content][CType]=templavoila_pi1&defVals[tt_content][tx_templavoila_ds]='.$templateObjectRecord['datastructure'].'&defVals[tt_content][tx_templavoila_to]='.$templateObjectRecord['uid'].$defVals;
            $index ++;
        }

		// PLUG-INS:
		if (is_array($TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']))	{
			$wizardItems['plugins'] = array('header'=>$LANG->getLL('plugins'));
			reset($TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']);
			while(list($class,$path)=each($TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']))	{
				$modObj = t3lib_div::makeInstance($class);
				$wizardItems = $modObj->proc($wizardItems);
			}
		}

		// Remove elements where preset values are not allowed:
		$this->removeInvalidElements($wizardItems);
		return $wizardItems;
	}

	// #
	// ### Mansoor Ahmad - Add automatly Plugins in the Wizardlist
	// #
	function parseWizard($ignoreList, $wizardItems, $defVals) {
		global $TCA;

		foreach($TCA['tt_content']['types'] as $k => $v){
			if(!in_array($k,$wizardItems) && !in_array($k,explode(',',$ignoreList))){
				if($k){
					foreach($TCA['tt_content']['columns']['CType']['config']['items'] as $k2 => $v2){
						if($TCA['tt_content']['columns']['CType']['config']['items'][$k2]['1'] == $k){
							list($piTitle, $piDesc)	=	$this->getLLValue($TCA['tt_content']['columns']['CType']['config']['items'][$k2]['0'], $k);
							$filePath = $TCA['tt_content']['columns']['CType']['config']['items'][$k2]['2'];
						}
					}
					$filePath = substr($filePath, 0, strrpos($filePath, '/'));
					$extType = (is_file(PATH_site . 'fileadmin/'. $filePath . '/wizard.png'))?'png':'gif';

					$add = array(
						"$k" => array(
							'icon'			=>	$filePath . '/wizard.' . $extType,
							'title'			=>	$piTitle,
							'description'	=>	$piDesc,
							'params'		=>	'&defVals[tt_content][CType]='.$k.$defVals
						)
					);
					$wizardItems = $wizardItems + $add;
				}
			}
		}

		return $wizardItems;
	}

	// #
	// ### Mansoor Ahmad - parse the LL Array
	// #
	function getLLValue($LLValue, $plugin) {
		global $LANG;
		
		$LLArray = explode(':', $LLValue);
		
		if(count($LLArray) == 4 && $LLArray[0] = 'LLL' && $LLArray[1] = 'EXT') {
			$LLFile = 'EXT:'.$LLArray[2];
			$LLFName = $LLArray[3];

			$this->myLanguageFiles[$plugin][$LLFile][] = $LLFName;
			$this->myLanguageFiles[$plugin][$LLFile][] = $LLFName.'.description';
			
			$locallang = $LANG;
			$locallang->includeLLFile($LLFile);
			$return = array($locallang->getLL($LLFName), $locallang->getLL($LLFName.'.description'));
		}
		else {
			$return = array($LLValue, '');
		}
		
		return $return;
	}
}

// Include extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/go_backend_layout/ux_db_new_content_el.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/go_backend_layout/ux_db_new_content_el.php']);
}

?>
