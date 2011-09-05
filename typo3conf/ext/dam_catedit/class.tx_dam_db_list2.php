<?php
/**
 * ************************************************************
 *  Copyright notice
 *
 *  (c) 1999-2003 Kasper Sk�rh�j (kasper@typo3.com)
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
 * **************************************************************/

class tx_dam_db_list {
		// Used in this class:
	var $showIcon = 1;
	var $no_noWrap = 0;
	var $oddColumnsTDAttr ='';			// If set this is <td>-params for odd columns in addElement. Used with db_layout / pages section
	var $backPath='';

		// Not used in this class - but maybe extension classes...
	var $fixedL = 50;						// Max length of strings
	var $headLineCol = '#dddddd';			// Head line color
	var $subHeadLineCol = '#eeeeee';
	var $thumbScript = 'thumbs.php';
	var $thumbs = 0;						// Boolean. Thumbnails on records containing files (pictures)
	var $script = 'index.php';


	var $HTMLcode='';			// String with accumulated HTML content



		// internal
	var $table='';		// set to the tablename if single-table mode
	var $tableList='';	// Specify a list of tables which are the only ones allowed to be displayed.
	var $searchString='';
	var $returnUrl='';
	var $staticParams='';

	var $res;
	var $resultsPerPage=0;		// "LIMIT " in SQL...
	var $firstItemNum=0;
	var $resCount = '';					// This could be set to the total number of items. Used by the fwd_rew_navigation...
	var $eCounter=0;		// Counting the elements no matter what...

	var $fieldArray = array();				// Decides the columns shown. Filled with values that refers to the keys of the data-array. $this->fieldArray[0] is the title column.
	var $addElement_tdParams=array();		// Keys are fieldnames and values are td-parameters to add in addElement();
	var $allFields=true;
	var $setFields=array();

	var $recPath_cache=array();
	var $perms_clause='';
	var $calcPerms=0;
	var $currentTable = array();

	var $duplicateStack=array();


 	var $noControlPanels = 0;
	var $alternateBgColors=0;
	var $showElements = array();

	var $dontShowClipControlPanels=1;


	function init($table)	{
		global $TCA,$BE_USER,$BACK_PATH;

		if (!count($this->showElements)) {
			$this->showElements = array('viewPage','editPage','newPage','unHidePage','movePage','pasteIntoPage','clearPageCache',
							'refresh',
							'permsRec','revertRec','editRec','infoRec','newRec','sortRec','unHideRec','delRec');
		}

		$this->HTMLcode='';

		$this->table=$table;
		$this->thumbs = $BE_USER->uc['thumbnailsByDefault'];
		$this->returnUrl=t3lib_div::_GP('returnUrl');
		$this->showElements = array('cvsExp','refresh','editRec','sortRec','unHideRec','delRec','revertRec');

		$this->showAction=true;

		if (!$GLOBALS['TYPO3_CONF_VARS']['GFX']['thumbnails'])	{
			$this->thumbScript=$BACK_PATH.'gfx/notfound_thumb.gif';
		} else {
			$this->thumbScript=$BACK_PATH.'thumbs.php';
		}
	}




	/**
	 * Setting the field names to display in extended list.
	 * Sets the internal variable $this->setFields
	 *
	 * @return	void
	 */
	function setDispFields()	{

			// Getting from session:
		$dispFields = $GLOBALS['BE_USER']->getModuleData('tx_dam_db_list.php/displayFields');

		$dispFields_in = t3lib_div::_GP('displayFields');
		if (is_array($dispFields_in))	{
			reset($dispFields_in);
			$tKey = key($dispFields_in);
			$dispFields[$tKey] = $dispFields_in[$tKey];
			$GLOBALS['BE_USER']->pushModuleData('tx_dam_db_list.php/displayFields',$dispFields);
		}

			// Setting result:
		$this->setFields = $dispFields;
	}

	/**
	 * Return list of fields that are selected for display
	 *
	 * @param	string		$table: ...
	 * @param	string		$rowlist: ...
	 * @return	string		...
	 */
	function getSelFieldList($table,$rowlist)	{
		global $TCA, $BACK_PATH;
		t3lib_div::loadTCA($table);

			// Init
		$titleCol = $TCA[$table]['ctrl']['label'];
		$thumbsCol = $TCA[$table]['ctrl']['thumbnail'];
#TODO
$l10nEnabled = false;
			// Cleaning rowlist for duplicates and place the $titleCol as the first column always!
		$this->fieldArray=array();
		$this->fieldArray[] = $titleCol;	// Add title column
		if ($this->localizationView && $l10nEnabled)	{
			$this->fieldArray[] = '_LOCALIZATION_';
			$this->fieldArray[] = '_LOCALIZATION_b';
			$addWhere.=' AND '.$TCA[$table]['ctrl']['languageField'].'<=0';
		}
		if (!t3lib_div::inList($rowlist,'_CONTROL_'))	{
		$this->fieldArray[] = '_CONTROL_';
		}
		if ($this->showClipboard)	{
			$this->fieldArray[] = '_CLIPBOARD_';
		}
		if ($this->searchLevels)	{
			$this->fieldArray[]='_PATH_';
		}
			// Cleaning up:
		$this->fieldArray=array_unique(array_merge($this->fieldArray,t3lib_div::trimExplode(',',$rowlist,1)));
		if ($this->noControlPanels)	{
			$tempArray = array_flip($this->fieldArray);
			unset($tempArray['_CONTROL_']);
			unset($tempArray['_CLIPBOARD_']);
			$this->fieldArray = array_keys($tempArray);
		}

			// Creating the list of fields to include in the SQL query:
		$selectFields = $this->fieldArray;
		$selectFields[] = 'uid';
		$selectFields[] = 'pid';
		if ($thumbsCol)	$selectFields[] = $thumbsCol;	// adding column for thumbnails

		if (is_array($TCA[$table]['ctrl']['enablecolumns']))	{
			$selectFields = array_merge($selectFields,$TCA[$table]['ctrl']['enablecolumns']);
		}
		if ($TCA[$table]['ctrl']['type'])	{
			$selectFields[] = $TCA[$table]['ctrl']['type'];
		}
		if ($TCA[$table]['ctrl']['typeicon_column'])	{
			$selectFields[] = $TCA[$table]['ctrl']['typeicon_column'];
		}
		if ($TCA[$table]['ctrl']['versioning'])	{
			$selectFields[] = 't3ver_id';
		}
		if ($l10nEnabled)	{
			$selectFields[] = $TCA[$table]['ctrl']['languageField'];
			$selectFields[] = $TCA[$table]['ctrl']['transOrigPointerField'];
		}
		if ($TCA[$table]['ctrl']['label_alt'])	{
			$selectFields = array_merge($selectFields,t3lib_div::trimExplode(',',$TCA[$table]['ctrl']['label_alt'],1));
		}
		$selectFields = array_unique($selectFields);		// Unique list!
		$selectFields = array_intersect($selectFields,$this->makeFieldList($table,1));		// Making sure that the fields in the field-list ARE in the field-list from TCA!
		$selFieldList = implode(',',$selectFields);		// implode it into a list of fields for the SQL-statement.

		return $selFieldList;
	}

	/**
	 * Traverses the table(s) to be listed and renders the output code for each:
	 * The HTML is accumulated in $this->HTMLcode
	 * Finishes off with a stopper-gif
	 *
	 * @return	void
	 */
	function generateList()	{
		global $TCA;

		$table = $this->table;
		t3lib_div::loadTCA($table);

		$fields = $this->makeFieldList($table);
		if (is_array($this->setFields[$table]))	{
			$fields = array_intersect($fields,$this->setFields[$table]);
		} else {
			$fields = array();
		}
		$this->HTMLcode.= $this->getTable($table,implode(',',$fields));
	}

	/**
	 * Creates the listing of records from a single table
	 *
	 * @param	string		Table name
	 * @param	integer		Page id
	 * @param	string		List of fields to show in the listing. Pseudo fields will be added including the record header.
	 * @return	string		HTML table with the listing for the record.
	 */
	function getTable($table,$rowlist)	{
		global $TCA, $BACK_PATH, $LANG;

			// Loading all TCA details for this table:
		t3lib_div::loadTCA($table);

			// Init
		$addWhere = '';
		$titleCol = $TCA[$table]['ctrl']['label'];
		$thumbsCol = $TCA[$table]['ctrl']['thumbnail'];
		$l10nEnabled = $TCA[$table]['ctrl']['languageField'] && $TCA[$table]['ctrl']['transOrigPointerField'] && !$TCA[$table]['ctrl']['transOrigPointerTable'];

		$selFieldList = $this->getSelFieldList($table,$rowlist);

		$dbCount=0;
		if ($this->pointer->countTotal AND $this->res)	{
			$dbCount = $GLOBALS['TYPO3_DB']->sql_num_rows($this->res);
		}

		$shEl = $this->showElements;
		$out='';
		if ($dbCount)	{

				// half line is drawn
			$theData = Array();
			if (!$this->table && !$rowlist)	{
				$theData[$titleCol] = '<img src="clear.gif" width="'.($GLOBALS['SOBE']->MOD_SETTINGS['bigControlPanel']?'230':'350').'" height="1">';
				if (in_array('_CONTROL_',$this->fieldArray))	$theData['_CONTROL_']='';
			}
#			$out.=$this->addelement('', $theData);

				// Header line is drawn
			$theData = Array();
#			$theData[$titleCol] = '<b>'.$GLOBALS['LANG']->sL($TCA[$table]['ctrl']['title'],1).'</b> ('.$this->resCount.')';
			$theUpIcon = '&nbsp;';

// todo csh
#			$theData[$titleCol].= t3lib_BEfunc::cshItem($table,'',$this->backPath,'',FALSE,'margin-bottom:0px; white-space: normal;');

#			$out.=$this->addelement($theUpIcon, $theData, '', '', 'background-color:'.$this->headLineCol.'; border-bottom:1px solid #000');

				// Fixing a order table for sortby tables
			$this->currentTable=array();
			$currentIdList=array();
			$doSort = ($TCA[$table]['ctrl']['sortby'] && !$this->sortField);

				$prevUid=0;
				$prevPrevUid=0;
				$accRows = array();	// Accumulate rows here
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($this->res))	{
					$accRows[] = $row;
					$currentIdList[] = $row['uid'];
					if ($doSort)	{
						if ($prevUid)	{
							$this->currentTable['prev'][$row['uid']]=$prevPrevUid;
							$this->currentTable['next'][$prevUid]='-'.$row['uid'];
							$this->currentTable['prevUid'][$row['uid']]=$prevUid;
						}
						$prevPrevUid = isset($this->currentTable['prev'][$row['uid']]) ? -$prevUid : $row['pid'];
						$prevUid=$row['uid'];
					}
				}
				$GLOBALS['TYPO3_DB']->sql_free_result($this->res);


				// items
			$itemContentTRows='';
			$this->duplicateStack=array();
			$this->eCounter=$this->pointer->firstItemNum;
			$cc=0;
			$itemContentTRows.= $this->fwd_rwd_nav('rwd');
			foreach($accRows as $row)	{
$this->alternateBgColors = false;
				$cc++;
				$row_bgColor=
					$this->alternateBgColors ?
					(($cc%2)?' class="item"' :' class="item" bgColor="'.t3lib_div::modifyHTMLColor($GLOBALS['SOBE']->doc->bgColor4,+10,+10,+10).'"') :
					' class="item"';

					// Initialization
				$iconfile = t3lib_iconWorks::getIcon($table,$row);
				$alttext = t3lib_BEfunc::getRecordIconAltText($row,$table);
				$recTitle = t3lib_BEfunc::getRecordTitle($table,$row,1);

					// The icon with link
				$theIcon = '<img src="'.$this->backPath.$iconfile.'" width="18" height="16" border="0" title="'.$alttext.'" />';
				$theIcon = $GLOBALS['SOBE']->doc->wrapClickMenuOnIcon($theIcon,$table,$row['uid']);

				$thumbImg = '';
				if ($this->thumbs) {
					$thumbImg = '<div style="margin:2px 0 2px 0;">'.$this->getThumbNail(tx_dam::path_makeAbsolute($row['file_path']).$row['file_name']).'</div>';
				}
					// 	Preparing and getting the data-array
				$theData = Array();
				reset($this->fieldArray);
				while(list(,$fCol)=each($this->fieldArray))	{
					if ($fCol==$titleCol)	{
						$theData[$fCol] = $this->linkWrapItems($table,$row['uid'],$recTitle,$row).$thumbImg;
					} elseif ($fCol=='pid') {
						$theData[$fCol]=$row[$fCol];
					} elseif ($fCol=='_CONTROL_') {
						$theData[$fCol]=$this->makeControl($table,$row);
					} else {
						$theData[$fCol]=t3lib_BEfunc::getProcessedValueExtra($table,$fCol,$row[$fCol],100);
					}
				}
$actionIcon='';
				$itemContentTRows.=$this->addElement($theIcon, $theData, $actionIcon, $row_bgColor, '', true);

					// Thumbsnails?
//				if ($this->thumbs && trim($row[$thumbsCol]))	{
//					$itemContentTRows.=$this->addelement('', Array($titleCol=>$this->thumbCode($row,$table,$thumbsCol)), '', $row_bgColor);
//				}
				$this->eCounter++;
			}
			if($this->eCounter > $this->pointer->firstItemNum) {
				$itemContentTRows.= $this->fwd_rwd_nav('fwd');
			}

				// field header line is drawn:
			$theData = Array();
			foreach($this->fieldArray as $fCol)	{
				$permsEdit = $this->calcPerms&($table=='pages'?2:16);
				if ($fCol=='_CONTROL_') {

					if (!$TCA[$table]['ctrl']['readOnly'])	{


						if ($permsEdit && $this->table && is_array($currentIdList) && in_array('editRec',$shEl))	{
							$editIdList = implode(',',$currentIdList);
							$params='&edit['.$table.']['.$editIdList.']=edit&columnsOnly='.implode(',',$this->fieldArray).'&disHelp=1';
 							$content = '<img'.t3lib_iconWorks::skinImg($this->backPath,'gfx/edit2.gif','width="11" height="12"').' vspace="2" border="0" align="top" title="'.$GLOBALS['LANG']->getLL('editShownColumns').'" />';
							$theData[$fCol].= $this->wrapEditLink($content, $params);
						}
					}
				} else {
					$theData[$fCol]='';
#					$theData[$fCol].='&nbsp;';
					if ($this->table && is_array($currentIdList) && in_array('editRec',$shEl))	{
						if (!$TCA[$table]['ctrl']['readOnly'] && $permsEdit && $TCA[$table]['columns'][$fCol])	{
							$editIdList = implode(',',$currentIdList);
							$params='&edit['.$table.']['.$editIdList.']=edit&columnsOnly='.$fCol.'&disHelp=1';
 							$content = '<img'.t3lib_iconWorks::skinImg($this->backPath,'gfx/edit2.gif','width="11" height="12"').' vspace="2" border="0" align="top" title="'.sprintf($GLOBALS['LANG']->getLL('editThisColumn'),preg_replace("/:$/",'',trim($GLOBALS['LANG']->sL(t3lib_BEfunc::getItemLabel($table,$fCol))))).'" />';
							$theData[$fCol].= $this->wrapEditLink($content, $params);
						}
					} else {
#						$theData[$fCol].='&nbsp;';
					}
					$theData[$fCol].=$this->addSortLink($GLOBALS['LANG']->sL(t3lib_BEfunc::getItemLabel($table,$fCol,'&nbsp;<i>[|]</i>&nbsp;')),$fCol,$table);;
				}
			}
			$out.=$this->addelement($theUpIcon, $theData, '', ' class="c-headLine"', 'border-bottom:1px solid #888');


				// The list of records is added after the header:
			$out.=$itemContentTRows;

			$out.=$this->addelement('', array(), '', '', 'border-top:1px solid #888');

#TODO
$LOISmode=false;
				// ... and it is all wrapped in a table:
			$out='

			<!--
				DB listing of elements:	"'.htmlspecialchars($table).'"
			-->
				<table border="0" cellpadding="0" cellspacing="0" class="typo3-dblist'.($LOISmode?' typo3-dblist-overview':'').'">
					'.$out.'
				</table>';
		}

			// Return content:
		return $out;
	}



	/********************************
	 *
	 * output
	 *
	 ********************************/

	/**
	 * Creates a sort-by link on the input string ($code).
	 * It will automatically detect if sorting should be ascending or descending depending on $this->sortRev.
	 * Also some fields will not be possible to sort (including if single-table-view is disabled).
	 *
	 * @param	string		The string to link (text)
	 * @param	string		The fieldname represented by the title ($code)
	 * @param	string		Table name
	 * @return	string		Linked $code variable
	 */
	function addSortLink($code,$field,$table)	{

		$content = '';

			// Certain circumstances just return string right away (no links):
		if ($field=='_CONTROL_' || $field=='_LOCALIZATION_' || $field=='_CLIPBOARD_' || $this->disableSingleTableView)	return $code;

			// If "_PATH_" (showing record path) is selected, force sorting by pid field (will at least group the records!)
		if ($field=='_PATH_')	$field=pid;

			//	 Create the sort link:
		$sortUrl = $this->listURL('',-1,'sortField,sortRev,table').'&table='.$table.'&sortField='.$field.'&sortRev='.($this->sortRev || ($this->sortField!=$field)?0:1);
		$sortArrow = ($this->sortField==$field?'<img'.t3lib_iconWorks::skinImg($this->backPath,'gfx/red'.($this->sortRev?'up':'down').'.gif','width="7" height="4"').' alt="" />':'');

			// linked sort field:
		$content.= '<a href="'.htmlspecialchars($sortUrl).'">'.$code.
				$sortArrow.
		'</a>';

			// remove sorting
		if($this->sortField==$field) {
#TODO title="Revert to default sorting"
			$sortUrl = $this->listURL('',-1,'sortField,sortRev,table').'&table='.$table.'&sortField=';
			$content.= '&nbsp;<a href="'.htmlspecialchars($sortUrl).'">'.
					'<img'.t3lib_iconWorks::skinImg($this->backPath,'gfx/close.gif','width="11" height="10"').' title="Revert to default sorting" alt="" />'.
			'</a>';
	}

		return $content;
	}

	/**
	 * Creates the control panel for a single record in the listing.
	 *
	 * @param	string		The table
	 * @param	array		The record for which to make the control panel.
	 * @return	string		HTML table with the control panel (unless disabled)
	 */
	function makeControl($table,$row)	{
		global $TCA, $LANG, $BACK_PATH;

			// Return blank, if disabled:
#		if ($this->dontShowClipControlPanels)	return '';

			// Initialize:
		t3lib_div::loadTCA($table);
		$cells=array();

		$shEl = $this->showElements;

			// If the listed table is 'pages' we have to request the permission settings for each page:
		if ($table=='pages')	{
			$localCalcPerms = $GLOBALS['BE_USER']->calcPerms(t3lib_BEfunc::getRecord('pages',$row['uid']));
		}

			// This expresses the edit permissions for this particular element:
		$permsEdit = ($table=='pages' && ($localCalcPerms&2)) || ($table!='pages' && ($this->calcPerms&16));




			// "Edit" link: ( Only if permissions to edit the page-record of the content of the parent page ($this->id)
		if ($permsEdit && in_array('editRec',$shEl))	{
			$params='&edit['.$table.']['.$row['uid'].']=edit';
			$icon = '<img'.t3lib_iconWorks::skinImg($this->backPath,'gfx/edit2'.(!$TCA[$table]['ctrl']['readOnly']?'':'_d').'.gif','width="11" height="12"').' title="'.$LANG->getLL('edit',1).'" alt="" />';
			$cells[] = $this->wrapEditLink($icon, $params);

		}

			// If the extended control panel is enabled OR if we are seeing a single table:
		if ($GLOBALS['SOBE']->MOD_SETTINGS['bigControlPanel'] || $this->table)	{

					// "Info": (All records)
			if (in_array('infoRec',$shEl)) {
			$cells[]='<a href="#" onclick="'.htmlspecialchars('top.launchView(\''.$table.'\', \''.$row['uid'].'\'); return false;').'">'.
					'<img'.t3lib_iconWorks::skinImg($this->backPath,'gfx/zoom2.gif','width="12" height="12"').' title="'.$LANG->getLL('showInfo',1).'" alt="" />'.
					'</a>';
			}

				// If the table is NOT a read-only table, then show these links:
			if (!$TCA[$table]['ctrl']['readOnly'])	{

					// "Revert" link (history/undo)
				if (in_array('revertRec',$shEl))	{
				$cells[]='<a href="#" onclick="'.htmlspecialchars('return jumpExt(\''.$this->backPath.'show_rechis.php?element='.rawurlencode($table.':'.$row['uid']).'\',\'#latest\');').'">'.
						'<img'.t3lib_iconWorks::skinImg($this->backPath,'gfx/history2.gif','width="13" height="12"').' title="'.$LANG->getLL('history',1).'" alt="" />'.
						'</a>';
				}

					// Versioning:
				if (t3lib_extMgm::isLoaded('version'))	{
					$vers = t3lib_BEfunc::selectVersionsOfRecord($table, $row['uid'], $fields='uid');
					if (is_array($vers))	{	// If table can be versionized.
						if (count($vers)>1)	{
							$st = 'background-color: #FFFF00; font-weight: bold;';
							$lab = count($vers)-1;
						} else {
							$st = 'background-color: #9999cc; font-weight: bold;';
							$lab = 'V';
						}

						$cells[]='<a href="'.htmlspecialchars($this->backPath.t3lib_extMgm::extRelPath('version')).'cm1/index.php?table='.rawurlencode($table).'&uid='.rawurlencode($row['uid']).'" class="typo3-ctrl-versioning" style="'.htmlspecialchars($st).'">'.
								$lab.
								'</a>';
					}
				}


					// "Edit Perms" link:
				if ($table=='pages' && in_array('permsRec',$shEl) && $GLOBALS['BE_USER']->check('modules','web_perm'))	{
					$cells[]='<a href="'.htmlspecialchars($this->backPath.'mod/web/perm/index.php?id='.$row['uid'].'&return_id='.$row['uid'].'&edit=1').'">'.
							'<img'.t3lib_iconWorks::skinImg($this->backPath,'gfx/perm.gif','width="7" height="12"').' title="'.$LANG->getLL('permissions',1).'" alt="" />'.
							'</a>';
				}





					// "Up/Down" links
				if ($permsEdit && $TCA[$table]['ctrl']['sortby']  && !$this->sortField  && in_array('sortRec',$shEl))	{	//
					if (isset($this->currentTable['prev'][$row['uid']]))	{	// Up
						$params='&cmd['.$table.']['.$row['uid'].'][move]='.$this->currentTable['prev'][$row['uid']];
						$cells[]='<a href="#" onclick="'.htmlspecialchars('return jumpToUrl(\''.$GLOBALS['SOBE']->doc->issueCommand($params,-1).'\');').'">'.
								'<img'.t3lib_iconWorks::skinImg($this->backPath,'gfx/button_up.gif','width="11" height="10"').' title="'.$LANG->getLL('moveUp',1).'" alt="" />'.
								'</a>';
					} else {
						$cells[]='<img src="clear.gif" '.t3lib_iconWorks::skinImg($this->backPath,'gfx/button_up.gif','width="11" height="10"',2).' alt="" />';
					}
					if ($this->currentTable['next'][$row['uid']])	{	// Down
						$params='&cmd['.$table.']['.$row['uid'].'][move]='.$this->currentTable['next'][$row['uid']];
						$cells[]='<a href="#" onclick="'.htmlspecialchars('return jumpToUrl(\''.$GLOBALS['SOBE']->doc->issueCommand($params,-1).'\');').'">'.
								'<img'.t3lib_iconWorks::skinImg($this->backPath,'gfx/button_down.gif','width="11" height="10"').' title="'.$LANG->getLL('moveDown',1).'" alt="" />'.
								'</a>';
					} else {
						$cells[]='<img src="clear.gif" '.t3lib_iconWorks::skinImg($this->backPath,'gfx/button_down.gif','width="11" height="10"',2).' alt="" />';
					}
				}

					// "Hide/Unhide" links:
				$hiddenField = $TCA[$table]['ctrl']['enablecolumns']['disabled'];
				if ($permsEdit && $hiddenField && $TCA[$table]['columns'][$hiddenField] && in_array('unHideRec',$shEl) && (!$TCA[$table]['columns'][$hiddenField]['exclude'] || $GLOBALS['BE_USER']->check('non_exclude_fields',$table.':'.$hiddenField)))	{
					if ($row[$hiddenField])	{
						$params='&data['.$table.']['.$row['uid'].']['.$hiddenField.']=0';
						$cells[]='<a title="'.$LANG->getLL('unHide'.($table=='pages'?'Page':''),1).'" href="#" onclick="'.htmlspecialchars('return jumpToUrl(\''.$GLOBALS['SOBE']->doc->issueCommand($params,-1).'\');').'">'.
								t3lib_iconWorks::getSpriteIcon('actions-edit-unhide') .
								'</a>';
					} else {
						$params='&data['.$table.']['.$row['uid'].']['.$hiddenField.']=1';
						$cells[]='<a title="'.$LANG->getLL('hide'.($table=='pages'?'Page':''),1).'" href="#" onclick="'.htmlspecialchars('return jumpToUrl(\''.$GLOBALS['SOBE']->doc->issueCommand($params,-1).'\');').'">'.
								t3lib_iconWorks::getSpriteIcon('actions-edit-hide') .
								'</a>';
					}
				}

					// "Delete" link:
//				if ( ($table=='pages' && ($localCalcPerms&4)) || ($table!='pages' && ($this->calcPerms&16)) && in_array('delRec',$shEl) )	{
//					$params='&cmd['.$table.']['.$row['uid'].'][delete]=1';
//					$title = $row['title'].' ('.$row['file_name'].')';
//
//					$cells[]='<a href="#" onclick="if (confirm('.$GLOBALS['LANG']->JScharCode(sprintf($GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:mess.delete'),$title)).')) {jumpToUrl(\''.$GLOBALS['SOBE']->doc->issueCommand($params,-1).'\');} return false;"><img src="'.$this->backPath.'gfx/delete_record.gif" width="12" height="12" border="0" align="top" title="'.$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:cm.delete',1).'" /></a>';
//				}

					// ToDo: weird: quickDelete = true means that there is a confirmation message
					// Todo: quickDelete=true is hardcoded
				$quickDelete = true;

					// "Delete" with confirmation (default)
				if ($quickDelete AND  ( ($table=='pages' && ($localCalcPerms&4)) || ($table!='pages' && ($this->calcPerms&16)) && in_array('delRec',$shEl) ) )	{
					$params = '&cmd[tx_dam_cat]['.$row['uid'].'][delete]=1';
					$title = $row['title'].' ('.$row['file_name'].')';
					$onClick = 'if (confirm('.$GLOBALS['LANG']->JScharCode(sprintf($GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:mess.delete'),$title)).')) {jumpToUrl(\''.$GLOBALS['SOBE']->doc->issueCommand($params,-1).'\');} return false;';
					$cells[] = '<a title="'.$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:cm.delete',1).'" href="#" onclick="'.$onClick.'">'.
								t3lib_iconWorks::getSpriteIcon('actions-edit-delete') .
								'</a>';
				}

					// Todo: Quick delete. Works but without redirect back to the overview.
				if (!$quickDelete AND ( ($table=='pages' && ($localCalcPerms&4)) || ($table!='pages' && ($this->calcPerms&16)) && in_array('delRec',$shEl) ) )	{
					$cmd = 'tx_dam_cmd_filedelete';
					$script = $BACK_PATH.PATH_txdam_rel.'mod_cmd/index.php?CMD='.$cmd.'&vC='.$GLOBALS['BE_USER']->veriCode().'&id='.rawurlencode($row['uid']).'&returnUrl='.t3lib_div::getIndpEnv('TYPO3_REQUEST_URL');
					$cells[] = '<a href="'.htmlspecialchars($script).'">'.
 							'<img'.t3lib_iconWorks::skinImg($this->backPath,'gfx/delete_record.gif', 'width="12" height="12"').' title="'.$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:cm.delete',1).'" alt="" />'.
							'</a>';
				}

			}
		}

			// If the record is edit-locked	by another user, we will show a little warning sign:
		if ($lockInfo=t3lib_BEfunc::isRecordLocked($table,$row['uid']))	{
			$cells[]='<a href="#" onclick="'.htmlspecialchars('alert('.$LANG->JScharCode($lockInfo['msg']).');return false;').'">'.
					'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/recordlock_warning3.gif','width="17" height="12"').' title="'.htmlspecialchars($lockInfo['msg']).'" alt="" />'.
					'</a>';
		}


			// Compile items into a DIV-element:
		return '
											<!-- CONTROL PANEL: '.$table.':'.$row['uid'].' -->
											<div class="typo3-DBctrl">'.implode('',$cells).'</div>';
	}



	/**
	 * Returns a table-row with the content from the fields in the input data array.
	 * OBS: $this->fieldArray MUST be set! (represents the list of fields to display)
	 *
	 * @param	string		$icon is the <img>+<a> of the record. If not supplied the first 'join'-icon will be a 'line' instead
	 * @param	array		$data is the dataarray, record with the fields. Notice: These fields are (currently) NOT htmlspecialchar'ed before being wrapped in <td>-tags
	 * @param	string		$tdAttr is insert in the <td>-tags. Must carry a ' ' as first character
	 * @return	string		HTML content for the table row
	 */
	function addElement($icon, $data, $action='', $tdAttr='', $tdStyle='', $trHover=false)	{

		$noWrap = ($this->no_noWrap) ? '' : ' nowrap="nowrap"';

		$trHover = $trHover ? (' onmouseover="this.style.backgroundColor = \''.$GLOBALS['SOBE']->doc->hoverColorTR.'\';" onmouseout="this.style.backgroundColor = \'\'"') : '';
			// Start up:
		$out='
		<!-- Element, begin: -->
		<tr'.$trHover.'>';


			// Show icon and lines
		if ($this->showAction)	{
			$out.='
			<td valign="top" align="left" nowrap="nowrap"'.$tdAttr.' style="padding: 3px 0px 0px 5px;'.$tdStyle.'">';
			if ($action)	$out.= $action;
			$out.='</td>
			';
		}

			// Show icon and lines
		if ($this->showIcon)	{
			$out.='
			<td valign="top" align="left" nowrap="nowrap"'.$tdAttr.' style="padding-left:5px;'.$tdStyle.'">';
			if ($icon)	$out.= $icon;
			$out.='</td>
			';
		}

			// Init rendering.
		$colsp='';
		$lastKey='';
		$c=0;
		$ccount=0;
		$tdP[0] = $this->oddColumnsTDAttr ? $this->oddColumnsTDAttr : $tdAttr;
		$tdP[1] = $tdAttr;

			// Traverse field array which contains the data to present:
		foreach($this->fieldArray as $vKey)	{
			if (isset($data[$vKey]))	{
				if ($lastKey)	{
					$out.='
						<td valign="top" style="padding-left:5px;'.$tdStyle.'"'.
						$noWrap.
						$tdP[($ccount%2)].
						$colsp.
						$this->addElement_tdParams[$lastKey].
						'>'.$data[$lastKey].'</td>';
				}
				$lastKey=$vKey;
				$c=1;
				$ccount++;
			} else {
				if (!$lastKey) {$lastKey=$vKey;}
				$c++;
			}
			if ($c>1)	{$colsp=' colspan="'.$c.'"';} else {$colsp='';}
		}
		if ($lastKey)	{	$out.='
						<td valign="top" style="padding-left:5px;'.$tdStyle.'"'.$noWrap.$tdP[($ccount%2)].$colsp.$this->addElement_tdParams[$lastKey].'>'.$data[$lastKey].'</td>';	}

			// End row
		$out.='
		</tr>';

			// Return row.
		return $out;
	}



	/**
	 * Creates a forward/reverse button based on the status of ->eCounter, ->pointer->firstItemNum, ->pointer->itemsPerPage
	 *
	 * @param	string		Table name
	 * @return	array		array([boolean], [HTML]) where [boolean] is 1 for reverse element, [HTML] is the table-row code for the element
	 */
	function fwd_rwd_nav($type)	{

		$code='';
		$theData = array();
		$titleCol=$this->fieldArray[0];

		if ($type=='fwd')	{
			if($this->pointer->lastItemNum < ($this->pointer->countTotal-1)) {
				$theData[$titleCol] = $this->fwd_rwd_HTML('fwd');
			}
		} elseif($this->pointer->page) {
			$theData[$titleCol] = $this->fwd_rwd_HTML('rwd');
		}
		$code=$this->addElement('',$theData);
		return $code;
	}

	/********************************
	 *
	 * GUI
	 *
	 ********************************/

	/**
	 * Create the selector box for selecting fields to display from a table:
	 *
	 * @param	string		Table name
	 * @param	boolean		If true, form-fields will be wrapped around the table.
	 * @return	string		HTML table with the selector box (name: displayFields['.$table.'][])
	 */
	function fieldSelectBox($table='',$formFields=1)	{
		global $TCA, $LANG;

			// Init:
		$table = $table ? $table : $this->table;
		t3lib_div::loadTCA($table);
		$formElements = array('','');
		if ($formFields)	{
			$formElements = array('<form action="'.htmlspecialchars($this->listURL()).'" method="post">','</form>');
		}

			// Load already selected fields, if any:
		$setFields = is_array($this->setFields[$table]) ? $this->setFields[$table] : array();

			// Request fields from table:
		$fields = $this->makeFieldList($table);

#TODO??
			// Add pseudo "control" fields
#		$fields['_PATH_'] = '_PATH_';
#		$fields['_LOCALIZATION_'] = '_LOCALIZATION_';
#		$fields['_CONTROL_'] = '_CONTROL_';
#		$fields['_CLIPBOARD_'] = '_CLIPBOARD_';

			// Create an option for each field:
		$opt = array();
		$opt[] = '<option value=""></option>';
		foreach($fields as $fN)	{
			$fL = is_array($TCA[$table]['columns'][$fN]) ? preg_replace('/:$/','',$LANG->sL($TCA[$table]['columns'][$fN]['label'])) : '['.$fN.']';	// Field label
			$opt[] = '
											<option value="'.$fN.'"'.(in_array($fN,$setFields)?' selected="selected"':'').'>'.htmlspecialchars($fL).'</option>';
		}

			// Compile the options into a multiple selector box:
		$lMenu = '
										<select size="'.t3lib_div::intInRange(count($fields)+1,3,8).'" multiple="multiple" name="displayFields['.$table.'][]">'.implode('',$opt).'
										</select>
				';

			// Table with the search box:
		$content.= '
		'.$formElements[0].'
				<!--
					Field selector for extended table view:
				-->
				<table border="0" cellpadding="0" cellspacing="0" class="bgColor4" id="typo3-dblist-fieldSelect">
					<tr>
						<td>'.$lMenu.'</td>
						<td><input type="Submit" name="search" value="&gt;&gt;"></td>
					</tr>
					</table>
			'.$formElements[1].'
		';
		return $content;
	}


	/**
	 * Creates the search box
	 *
	 * @param	boolean		If true, the search box is wrapped in its own form-tags
	 * @return	string		HTML for the search box
	 */
	function getSearchBox($formFields=1)	{

			// Setting form-elements, if applicable:
		$formElements=array('','');
		if ($formFields)	{
			$formElements=array('<form action="'.htmlspecialchars($this->listURL()).'" method="post">','</form>');
		}

#TODO unused??

			// Table with the search box:
		$content.= $formElements[0].'
				<!--
					Search box:
				-->
				<table border="0" cellpadding="0" cellspacing="0" class="bgColor4" id="typo3-dblist-search">
					<tr>
						<td>'.$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:labels.enterSearchString',1).'<input type="text" name="search_field" value="'.htmlspecialchars($this->searchString).'"'.$GLOBALS['TBE_TEMPLATE']->formWidth(10).' /></td>
						<td><input type="submit" name="search" value="'.$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:labels.search',1).'" /></td>
					</tr>
					</table>
				'.$formElements[1];

		return $content;
	}

	/**
	 * Creates the button with link to either forward or reverse
	 *
	 * @param	string		Type: "fwd" or "rwd"
	 * @param	integer		Pointer
	 * @param	string		Table name
	 * @return	string
	 * @access private
	 */
	function fwd_rwd_HTML($type)	{
		$content = '';

		$tParam = $this->table ? '&table='.rawurlencode($this->table) : '';
		switch($type)	{
			case 'fwd':
				$pointer=max(0,$this->pointer->page+1);
				$href = $this->listURL().'&SET[tx_dam_resultPointer]='.$pointer.$tParam;
				$content = '&nbsp;<a href="'.htmlspecialchars($href).'">'.
						'<img src="'.$this->backPath.'gfx/pildown.gif" width="14" height="14" valign="top" border="0" alt="" />'.
						'</a> <i>['.($this->pointer->lastItemNum+1).' - '.min($this->pointer->lastItemNum+1+$this->pointer->itemsPerPage,$this->pointer->countTotal).']</i>';
			break;
			case 'rwd':
				$pointer=max(0,$this->pointer->page-1);
				$href = $this->listURL().'&SET[tx_dam_resultPointer]='.$pointer.$tParam;
				$content = '&nbsp;<a href="'.htmlspecialchars($href).'">'.
						'<img src="'.$this->backPath.'gfx/pilup.gif" width="14" height="14" valign="top" border="0" alt="" />'.
						'</a> <i>['.max(1,$this->pointer->firstItemNum-$this->pointer->itemsPerPage).' - '.($this->pointer->firstItemNum-1).']</i>';
			break;
		}
		return $content;
	}




	/********************************
	 *
	 * internal
	 *
	 ********************************/

	/**
	 * Makes the list of fields to select for a table
	 *
	 * @param	string		Table name
	 * @param	boolean		If set, users access to the field (non-exclude-fields) is NOT checked.
	 * @return	array		Array, where values are fieldnames to include in query
	 */
	function makeFieldList($table, $dontCheckUser=false, $useExludeFieldList=true)	{
		global $TCA,$BE_USER;

			// Init fieldlist array:
		$fieldListArr = array();


			// Check table:
		if (is_array($TCA[$table]))	{
			t3lib_div::loadTCA($table);

			$exludeFieldList = explode(',', $TCA[$table]['interface']['excludeFieldList']);

			// Traverse configured columns and add them to field array, if available for user.
			foreach($TCA[$table]['columns'] as $fN => $fieldValue)	{
				if (($dontCheckUser || ((!$fieldValue['exclude'] || $BE_USER->check('non_exclude_fields',$table.':'.$fN)) && $fieldValue['config']['type']!='passthrough')) AND
					(!$useExludeFieldList || !in_array($fN, $exludeFieldList)))	{
					$fieldListArr[$fN] = $fN;
				}
			}

			// Add special fields:
			if ($dontCheckUser || $BE_USER->isAdmin())	{
				$fieldListArr['uid'] = 'uid';
				$fieldListArr['pid'] = 'pid';
				if ($TCA[$table]['ctrl']['tstamp'])	$fieldListArr[$TCA[$table]['ctrl']['tstamp']] = $TCA[$table]['ctrl']['tstamp'];
				if ($TCA[$table]['ctrl']['crdate'])	$fieldListArr[$TCA[$table]['ctrl']['tstamp']] = $TCA[$table]['ctrl']['tstamp'];
				if ($TCA[$table]['ctrl']['cruser_id'])	$fieldListArr[$TCA[$table]['ctrl']['cruser_id']] = $TCA[$table]['ctrl']['cruser_id'];
				if ($TCA[$table]['ctrl']['sortby'])	$fieldListArr[$TCA[$table]['ctrl']['cruser_id']] = $TCA[$table]['ctrl']['sortby'];
				if ($TCA[$table]['ctrl']['versioning'])	$fieldListArr['t3ver_id'] = 't3ver_id';
			}
		}
			// doesn't make sense, does it?
		unset($fieldListArr['l18n_parent']);
		unset($fieldListArr['l18n_diffsource']);

		return $fieldListArr;
	}

	/**
	 * Returns the path for a certain pid
	 * The result is cached internally for the session, thus you can call this function as much as you like without performance problems.
	 *
	 * @param	integer		The page id for which to get the path
	 * @return	string		The path.
	 */
	function recPath($pid)	{
		if (!isset($this->recPath_cache[$pid]))	{
			$this->recPath_cache[$pid] = t3lib_BEfunc::getRecordPath ($pid,$this->perms_clause,20);
		}
		return $this->recPath_cache[$pid];
	}


	/********************************
	 *
	 * tools
	 *
	 ********************************/


	function wrapEditLink($content, $params) {
		$onClick = t3lib_BEfunc::editOnClick($params,$this->backPath,-1);
		return '<a href="#" onclick="'.htmlspecialchars($onClick).'">'.$content.'</a>';
	}


	/**
	 * Returns the title (based on $code) of a table ($table) with the proper link around. For headers over tables.
	 * The link will cause the display of all extended mode or not for the table.
	 *
	 * @param	string		Table name
	 * @param	string		Table label
	 * @return	string		The linked table label
	 */
	function linkWrapTable($table,$code)	{
		if ($this->table!=$table)	{
			return '<a href="'.htmlspecialchars($this->listURL($table)).'">'.$code.'</a>';
		} else {
			return '<a href="'.htmlspecialchars($this->listURL('','sortField,sortRev,table')).'">'.$code.'</a>';
		}
	}

	/**
	 * Returns the title (based on $code) of a record (from table $table) with the proper link around (that is for 'pages'-records a link to the level of that record...)
	 *
	 * @param	string		Table name
	 * @param	integer		Item uid
	 * @param	string		Item title (not htmlspecialchars()'ed yet)
	 * @param	array		Item row
	 * @return	string		The item title. Ready for HTML output (is htmlspecialchars()'ed)
	 */
	function linkWrapItems($table,$uid,$code,$row)	{
			// Returns the title (based on $code) of a record (from table $table) with the proper link around (that is for "pages"-records a link to the level of that record...)
		if (!strcmp($code,'')) {$code='<i>['.$GLOBALS['LANG']->php3Lang['labels']['no_title'].']</i> - '.t3lib_BEfunc::getRecordTitle($table,$row);}
		if ($table=='pages')	{
			return '<a href="'.$this->listURL($uid).'">'.$code.'</a>';
		} else {
			return $code;
//			return '<a href="javascript:top.launchView(\''.$table.'\','.$uid.');">'.$code.'</a>';	// This launches the show_item-windows
		}
	}

	/**
	 * Creates the URL to this script, including all relevant GPvars
	 * Fixed GPvars are id, table, imagemode, returlUrl, search_field, search_levels and showLimit
	 * The GPvars "sortField" and "sortRev" are also included UNLESS they are found in the $exclList variable.
	 *
	 * @param	string		Alternative id value. Enter blank string for the current id ($this->id)
	 * @param	string		Tablename to display. Enter "-1" for the current table.
	 * @param	string		Commalist of fields NOT to include ("sortField" or "sortRev")
	 * @return	string		URL
	 */
	function listURL($table=-1,$exclList='')	{
		return $this->script.'?'.
			($this->staticParams?$this->staticParams:'').
			($this->returnUrl?"&returnUrl=".rawurlencode($this->returnUrl):"").
			($this->searchString?"&search_field=".rawurlencode($this->searchString):'').
			((!$exclList || !t3lib_div::inList($exclList,'sortField')) && $this->sortField?"&sortField=".rawurlencode($this->sortField):"").
			((!$exclList || !t3lib_div::inList($exclList,'sortRev')) && $this->sortRev?"&sortRev=".rawurlencode($this->sortRev):"")
			;
	}

	/**
	 * Create thumbnail code for record/field
	 *
	 * @param	array		Record array
	 * @param	string		Table (record is from)
	 * @param	string		Field name for which thumbsnail are to be rendered.
	 * @return	string		HTML for thumbnails, if any.
	 */
	function thumbCode($row,$table,$field)	{
#TODO		$file = tx_dam::path_makeAbsolute($row['file_path']).$row['file_name'];
		return t3lib_BEfunc::thumbCode($row,$table,$field,$this->backPath,$this->thumbScript);
	}



	/**
	 * Returns single image tag to thumbnail using a thumbnail script (like thumbs.php)
	 *
	 * @param	string		$theFile must be the proper reference to the file thumbs.php should show
	 * @param	string		$tparams are additional attributes for the image tag
	 * @param	integer		$size is the size of the thumbnail send along to "thumbs.php"
	 * @return	string		Image tag
	 */
	function getThumbNail($theFile,$tparams='',$size='')	{
		global $BACK_PATH;

		return t3lib_BEfunc::getThumbNail($this->thumbScript, $theFile, $tparams, $size);
	}
}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam_catedit/class.tx_dam_db_list2.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam_catedit/class.tx_dam_db_list2.php']);
}


?>