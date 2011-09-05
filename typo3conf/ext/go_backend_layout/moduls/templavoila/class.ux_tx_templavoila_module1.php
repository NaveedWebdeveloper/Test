<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Gosign media.
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
 * XCLASS Extension file for templa voila page module
 *
 * $Id$
 *
 * @author	Mansoor Ahmad --- Gosign media. GmbH
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 */

//require_once(PATH_tslib.'class.tslib_pibase.php');
$LANG->includeLLFile('EXT:go_backend_layout/locallang.xml');

// ### Mansoor need this for his Engine start
//t3lib_div::makeInstance('language');
$LANGPLUGIN = $LANG;
$LANGPLUGIN->includeLLFile('EXT:templavoila/mod1/locallang_db_new_content_el.xml');

require_once(getcwd()."/../../../../typo3/sysext/cms/tslib/class.tslib_content.php");
require_once(getcwd()."/../../../../t3lib/class.t3lib_befunc.php");
require_once(getcwd()."/../../../../t3lib/class.t3lib_div.php");
// ### Mansoor need this for his Engine end

//require_once(getcwd()."/../../../../typo3/sysext/cms/tslib/class.tslib_pibase.php");
class ux_tx_templavoila_module1 extends tx_templavoila_module1 {

	private $stop = 0;

	/**
	 * Renders the display framework of a single sheet. Calls itself recursively
	 *
	 * @param	array		$contentTreeArr: DataStructure info array (the whole tree)
	 * @param	string		$languageKey: Language key for the display
	 * @param	string		$sheet: The sheet key of the sheet which should be rendered
	 * @param	array		$parentPointer: Flexform pointer to parent element
	 * @param	array		$parentDsMeta: Meta array from parent DS (passing information about parent containers localization mode)
	 * @return	string		HTML
	 * @access protected
	 * @see	render_framework_singleSheet()
	 */
	function render_framework_singleSheet($contentTreeArr, $languageKey, $sheet, $parentPointer=array(), $parentDsMeta=array()) {
		global $LANG, $TYPO3_CONF_VARS;
		
		$elementBelongsToCurrentPage = $contentTreeArr['el']['table'] == 'pages' || $contentTreeArr['el']['pid'] == $this->rootElementUid_pidForContent;

		$canEditPage = $GLOBALS['BE_USER']->isPSet($this->calcPerms, 'pages', 'edit');
		$canEditContent = $GLOBALS['BE_USER']->isPSet($this->calcPerms, 'pages', 'editcontent');

		// Prepare the record icon including a content sensitive menu link wrapped around it:
		$recordIcon = '<img'.t3lib_iconWorks::skinImg($this->doc->backPath,$contentTreeArr['el']['icon'],'').' style="text-align: center; vertical-align: middle;" width="18" height="16" border="0" title="'.htmlspecialchars('['.$contentTreeArr['el']['table'].':'.$contentTreeArr['el']['uid'].']').'" alt="" />';
		$menuCommands = array();
		if ($GLOBALS['BE_USER']->isPSet($this->calcPerms, 'pages', 'new')) {
			$menuCommands[] = 'new';
		}
		if ($canEditContent) {
			$menuCommands[] = 'copy,cut,pasteinto,pasteafter,delete';
		}
		else {
			$menuCommands[] = 'copy';
		}

		$titleBarLeftButtons = $this->translatorMode ? $recordIcon : (count($menuCommands) == 0 ? $recordIcon : $this->doc->wrapClickMenuOnIcon($recordIcon,$contentTreeArr['el']['table'], $contentTreeArr['el']['uid'], 1,'&amp;callingScriptId='.rawurlencode($this->doc->scriptID), implode(',', $menuCommands)));
		$titleBarLeftButtons.= $this->getRecordStatHookValue($contentTreeArr['el']['table'],$contentTreeArr['el']['uid']);
		unset($menuCommands);

			// Prepare table specific settings:
		switch ($contentTreeArr['el']['table']) {

			case 'pages' :

				$titleBarLeftButtons .= $this->translatorMode || !$canEditPage ? '' : $this->link_edit('<img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/edit2.gif','').' title="'.htmlspecialchars($LANG->sL('LLL:EXT:lang/locallang_mod_web_list.xml:editPage')).'" alt="" style="text-align: center; vertical-align: middle; border:0;" />',$contentTreeArr['el']['table'],$contentTreeArr['el']['uid']);
				$titleBarRightButtons = '';

				$addGetVars = ($this->currentLanguageUid?'&L='.$this->currentLanguageUid:'');
				$viewPageOnClick = 'onclick= "'.htmlspecialchars(t3lib_BEfunc::viewOnClick($contentTreeArr['el']['uid'], $this->doc->backPath, t3lib_BEfunc::BEgetRootLine($contentTreeArr['el']['uid']),'','',$addGetVars)).'"';
				$viewPageIcon = '<img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/zoom.gif','width="12" height="12"').' title="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.showPage',1).'" hspace="3" alt="" style="text-align: center; vertical-align: middle;" />';
				$titleBarLeftButtons .= '<a href="#" '.$viewPageOnClick.'>'.$viewPageIcon.'</a>';
				
				#
				### Mansoor Ahmad - JavaScript for making CEs Draggable
				#
				$dragAndDropEnable = 0;
				if((strstr($_SERVER["HTTP_USER_AGENT"], "Firefox")) && (strstr($_SERVER["HTTP_USER_AGENT"], "Gecko")) || (strstr($_SERVER["HTTP_USER_AGENT"], "Chrome"))) {
					$dragAndDropEnable = 1;
				}
				
			break;

			case 'tt_content' :

				$elementTitlebarColor = ($elementBelongsToCurrentPage ? $this->doc->bgColor5 : $this->doc->bgColor6);
				$elementTitlebarStyle = 'background-color: '.$elementTitlebarColor;

				$languageUid = $contentTreeArr['el']['sys_language_uid'];

				if (!$this->translatorMode && $canEditContent) {
						// Create CE specific buttons:
					$linkMakeLocal = !$elementBelongsToCurrentPage ? $this->link_makeLocal('<img'.t3lib_iconWorks::skinImg($this->doc->backPath,t3lib_extMgm::extRelPath('templavoila').'mod1/makelocalcopy.gif','').' title="'.$LANG->getLL('makeLocal').'" border="0" alt="" />', $parentPointer) : '';

					// #
					// ### Mansoor Ahmad - I include it on Caspars recommend
					// #
					$linkUnlink = $this->link_unlink('<img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/garbage.gif','').' title="'.$LANG->getLL('deleteRecord').'" border="0" alt="" />', $parentPointer, ($elementBelongsToCurrentPage ? TRUE : FALSE));
					if ($GLOBALS['BE_USER']->recordEditAccessInternals('tt_content', $contentTreeArr['previewData']['fullRow'])) {
						$linkEdit = ($elementBelongsToCurrentPage ? $this->link_edit('<img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/edit2.gif','').' title="'.$LANG->getLL('editrecord').'" border="0" alt="" />',$contentTreeArr['el']['table'],$contentTreeArr['el']['uid']) : '');
					}
					else {
						$linkEdit = '';
					}
					
					// #
					// ### Mansoor Ahmad - I deactivated it for safty way, but now is it active
					// #
					//$titleBarRightButtons = $linkEdit . $this->clipboardObj->element_getSelectButtons($parentPointer,'copy,cut') . $linkMakeLocal . $linkUnlink;
					$titleBarRightButtons = $linkEdit . $this->clipboardObj->element_getSelectButtons($parentPointer) . $linkMakeLocal . $linkUnlink;
				}
				else {
					$titleBarRightButtons = $this->clipboardObj->element_getSelectButtons($parentPointer, 'copy');
				}
				
				
				#
				### Mansoor Ahmad - JavaScript for making CEs Draggable
				#
				$dragAndDropEnable = 0;
				if((strstr($_SERVER["HTTP_USER_AGENT"], "Firefox")) && (strstr($_SERVER["HTTP_USER_AGENT"], "Gecko")) || (strstr($_SERVER["HTTP_USER_AGENT"], "Chrome"))) {
					$makeDraggable = '<script>new Draggable(\''.$contentTreeArr['el']['CType'].'-'.$contentTreeArr['el']['uid'].'\',{
						revert:true,
						ghosting:false
					});</script>';
					$dragAndDropEnable = 1;
				}
				
				
				// #
				// ### Mansoor Ahmad - I disable the Buttons of CE's in Flexform Elements
				// #
				$classBe	=	'';
				$resPages	=	$GLOBALS['TYPO3_DB']->exec_SELECTquery('tx_templavoila_flex', 'pages', 'uid='.$contentTreeArr['el']['pid']);
				$rowPages	=	$GLOBALS['TYPO3_DB']->sql_fetch_assoc($resPages);
				$flexData = t3lib_div::xml2array($rowPages['tx_templavoila_flex']);
				if(empty($elementBelongsToCurrentPage)) {
					$classBe				=	'go_backend_layout_shortcut_ce';
					$titleBarLeftButtons	=	'';
					$titleBarRightButtons	=	$linkEdit . $this->clipboardObj->element_getSelectButtons($parentPointer,'cut') . $linkMakeLocal . $linkUnlink;
					$backLinkToParentCe		=	'<a href="index.php?id='.$contentTreeArr['el']['pid'].'"><u>Springe zum Mutterelement in Seite:</u> <br/><span style="float:left;">' . $this->getPagename($contentTreeArr['el']['pid']) . '</span></a>';
					$makeDraggable			=	'';
				}
				foreach($flexData['data']['sDEF']['lDEF'] as $fieldName => $fieldArray) {
					$uidsLevel1 = explode(',',$fieldArray['vDEF']);
					if(empty($elementBelongsToCurrentPage) && (in_array($contentTreeArr['el']['uid'], $uidsLevel1) == FALSE)) {
						$titleBarLeftButtons	=	'';
						//# Bugfix for 4.4.6
						//$titleBarRightButtons	=	'';
					}
					else {
						break;
					}
				}
				
				$tableWrap1 = '<table id="'.$contentTreeArr['el']['CType'].'-'.$contentTreeArr['el']['uid'].'" style="width: 100%;" class="go_backend_layout_draggable '.$classBe.'" cellspacing="0" cellpadding="0" onmouseup="dropareaPosClass = \'go_backend_layout_droppables_position\'; ceIdForPos = \'\';" onmousedown="ceIdForPos = \''.$contentTreeArr['el']['CType'].'-'.$contentTreeArr['el']['uid'].'\';dropareaPosClass = \'go_backend_layout_droppables_position_act\';"><tr><td>'.$makeDraggable;
				$tableWrap2 = '</td></tr></table>';
				
			break;
		}

			// Prepare the language icon:
		$languageLabel = htmlspecialchars ($this->allAvailableLanguages[$contentTreeArr['el']['sys_language_uid']]['title']);
		$languageIcon = $this->allAvailableLanguages[$languageUid]['flagIcon'] ? '<img src="'.$this->allAvailableLanguages[$languageUid]['flagIcon'].'" title="'.$languageLabel.'" alt="'.$languageLabel.'" style="text-align: center; vertical-align: middle;" />' : ($languageLabel && $languageUid ? '['.$languageLabel.']' : '');

			// If there was a language icon and the language was not default or [all] and if that langauge is accessible for the user, then wrap the  flag with an edit link (to support the "Click the flag!" principle for translators)
		if ($languageIcon && $languageUid>0 && $GLOBALS['BE_USER']->checkLanguageAccess($languageUid) && $contentTreeArr['el']['table']==='tt_content')	{
			$languageIcon = $this->link_edit($languageIcon, 'tt_content', $contentTreeArr['el']['uid'], TRUE);
		}

			// Create warning messages if neccessary:
		$warnings = '';
		if ($this->global_tt_content_elementRegister[$contentTreeArr['el']['uid']] > 1 && $this->rootElementLangParadigm !='free') {
			$warnings .= '<br/>'.$this->doc->icons(2).' <em>'.htmlspecialchars(sprintf($LANG->getLL('warning_elementusedmorethanonce',''), $this->global_tt_content_elementRegister[$contentTreeArr['el']['uid']], $contentTreeArr['el']['uid'])).'</em>';
		}

			// Displaying warning for container content (in default sheet - a limitation) elements if localization is enabled:
		$isContainerEl = count($contentTreeArr['sub']['sDEF']);
		if (!$this->modTSconfig['properties']['disableContainerElementLocalizationWarning'] && $this->rootElementLangParadigm !='free' && $isContainerEl && $contentTreeArr['el']['table'] === 'tt_content' && $contentTreeArr['el']['CType'] === 'templavoila_pi1' && !$contentTreeArr['ds_meta']['langDisable'])	{
			if ($contentTreeArr['ds_meta']['langChildren'])	{
				if (!$this->modTSconfig['properties']['disableContainerElementLocalizationWarning_warningOnly']) {
					$warnings .= '<br/>'.$this->doc->icons(2).' <b>'.$LANG->getLL('warning_containerInheritance').'</b>';
				}
			} else {
				$warnings .= '<br/>'.$this->doc->icons(3).' <b>'.$LANG->getLL('warning_containerSeparate').'</b>';
			}
		}

		$previewContent = $this->render_previewData($contentTreeArr['previewData'], $contentTreeArr['el'], $contentTreeArr['ds_meta'], $languageKey, $sheet);

			// Wrap workspace notification colors:
		if ($contentTreeArr['el']['_ORIG_uid'])	{
			$previewContent = '<div class="ver-element">'.($previewContent ? $previewContent : '<em>[New version]</em>').'</div>';
		}

			// Finally assemble the table:
		$finalContent ='
			'.$tableWrap1.'
			<table id="'.$contentTreeArr['el']['CType'].'" cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom:5px;">
				<tr style="'.$elementTitlebarStyle.';" class="go_backend_layout_ce_header">
					<td style="vertical-align:top;">'.
						'<span class="nobr">'.
						// #
						// ### Mansoor Ahmad - Titlebar Fix
						// #
						//$languageIcon.
						$titleBarLeftButtons.
						$backLinkToParentCe.
						//($elementBelongsToCurrentPage?'':'<em>').htmlspecialchars($contentTreeArr['el']['title']).($elementBelongsToCurrentPage ? '' : '</em>').
						'</span>'.
						$warnings.
					'</td>
					<td nowrap="nowrap" style="text-align:right; vertical-align:top;cursor:move;" >'.
						$titleBarRightButtons.
					'</td>
				</tr>
				<tr>
					<td colspan="2" class="'. $contentTreeArr['el']['CType'] .'">'.
						$this->render_framework_subElements($contentTreeArr, $languageKey, $sheet, $elementBelongsToCurrentPage, $dragAndDropEnable).
						$previewContent.
						$this->render_localizationInfoTable($contentTreeArr, $parentPointer, $parentDsMeta, $elementBelongsToCurrentPage).
					'</td>
				</tr>
			</table>
			'.$tableWrap2.'
		';
		return $finalContent;
	
	}
	
	/**
	 * Returns an HTMLized preview of a certain content element. If you'd like to register a new content type, you can easily use the hook
	 * provided at the beginning of the function.
	 *
	 * @param	array		$row: The row of tt_content containing the content element record.
	 * @return	string		HTML preview content
	 * @access protected
	 * @see		getContentTree(), render_localizationInfoTable()
	 */
	function render_previewContent($row) {
		global $TYPO3_CONF_VARS, $LANG, $LANGPLUGIN, $TCA;
		
		$hookObjectsArr = $this->hooks_prepareObjectsArray ('renderPreviewContentClass');
		$alreadyRendered = FALSE;
		$output = '';
		// ELIO@GOSIGN 13/08/09: For LFEditor Link
		$langFile = '';

			// Hook: renderPreviewContent_preProcess. Set 'alreadyRendered' to true if you provided a preview content for the current cType !
		reset($hookObjectsArr);
		while (list(,$hookObj) = each($hookObjectsArr)) {
			if (method_exists ($hookObj, 'renderPreviewContent_preProcess')) {
				$output .= $hookObj->renderPreviewContent_preProcess ($row, 'tt_content', $alreadyRendered, $this);
			}
		}
		
		if (!$alreadyRendered) {
				// Preview content for non-flexible content elements:
			switch($row['CType'])	{
				case 'table':		//	Table
						$output	=	'<strong>'.$LANG->getLL($row['CType'].'.field.text',1).'</strong>: <br />'.nl2br($row['bodytext']).'<br />'.
									$this->getPiName($LANGPLUGIN->getLL('common_6_title'));
					break;

				case 'splash':		//	Textbox
					$thumbnail = '<strong>'.$LANG->sL(t3lib_BEfunc::getItemLabel('tt_content','image'),1).'</strong><br />';
					$thumbnail .= t3lib_BEfunc::thumbCode($row, 'tt_content', 'image', $this->doc->backPath);
					$text = $this->link_edit('<strong>'.$LANG->sL(t3lib_BEfunc::getItemLabel('tt_content','bodytext'),1).'</strong> '.htmlspecialchars(t3lib_div::fixed_lgd_cs(trim(strip_tags($row['bodytext'])),2000)),'tt_content',$row['uid']);
					$output='<table><tr><td valign="top">'.$text.'</td><td valign="top">'.$thumbnail.'</td></tr></table>'.'<br />';
					break;
				case 'list':		//	Insert Plugin
					
						switch($row['list_type']) {
							case '9':
								$html	=	$this->getTTNews($row);
								break;
							case 'rwe_feuseradmin_pi1':
								$html	=	'<strong>'.$LANG->getLL($row['list_type'].'.field.hinweis',1).'</strong>: <br />'.$LANG->getLL($row['list_type'].'.field.hinweis.content',1).'<br />'.
											$this->getPiName($LANG->sL(t3lib_BEfunc::getLabelFromItemlist('tt_content','list_type',$row['list_type'])));
								break;
							case 'th_mailformplus_pi1':
								$html	=	'<strong>'.$LANG->getLL($row['list_type'].'.field.hinweis',1).'</strong>: <br />'.$LANG->getLL($row['list_type'].'.field.hinweis.content',1).'<br />'.
											'<br /><br /><strong style="margin:2px;padding:2px;border:1px solid #bfbfbf; background-color:#FFFFFF;">'.$LANG->sL(t3lib_BEfunc::getLabelFromItemlist('tt_content','list_type',$row['list_type'])).'</strong><br /><br />';
											// ELIO@GOSIGN 13/08/09 START
											// LFE-Link: we have to set the langfile like this for this plugin
											$typoscript = $this->loadTS($row['pid']); 
											$langFile = $typoscript->setup['plugin.']['tx_thmailformplus_pi1.']['langFile'];
											// ELIO@GOSIGN 13/08/09 END
								break;
						}

						if($html) {
							$output = $this->link_edit($html, 'tt_content', $row['uid']).'<br />';
						}
						else {
							$output = $this->link_edit('<strong>'.$LANG->sL(t3lib_BEfunc::getItemLabel('tt_content','list_type')).'</strong> ' . htmlspecialchars($LANG->sL(t3lib_BEfunc::getLabelFromItemlist('tt_content','list_type',$row['list_type']))).' &ndash; '.htmlspecialchars($extraInfo ? $extraInfo : $row['list_type']), 'tt_content', $row['uid']).'<br />';
						}
					break;
					
				case 'div':				//	Divider
				case 'templavoila_pi1': //	Flexible Content Element: Rendered directly in getContentTree*()

					switch($row['tx_templavoila_to']) {
						default:
							// Render the Rest Flexform Elements
							$html	=	$this->renderFlex($row);
							break;
					}
					
					$output = $html;
					break;
				default:
					// Render the Rest CType Elements
					$output	=	$this->renderPi($row);
			}
			
		}
		// ELIO@GOSIGN 13/08/09 START
		// Add LFEditor link
		if ( $row['CType'] == 'list' && !empty($row['list_type']) ) //  if this is a plugin
			$output .= $this->addLFEditLink( $row['list_type'], $langFile );
		elseif ( $row['CType'] != 'list' ) // if this is a normal CType
			$output .= $this->addLFEditLink( $row['CType'], $langFile );
		// ELIO@GOSIGN 13/08/09 END
		return $output;
	}
	
	
	
	/**
	 * Renders a little table containing previews of translated version of the current content element.
	 *
	 * @author: Mansoor Ahmad - I do some modifications 
	 * @param	array		$contentTreeArr: Part of the contentTreeArr for the element
	 * @param	string		$parentPointer: Flexform pointer pointing to the current element (from the parent's perspective)
	 * @param	array		$parentDsMeta: Meta array from parent DS (passing information about parent containers localization mode)
	 * @return	string		HTML
	 * @access protected
	 * @see	render_framework_singleSheet()
	 */
	function render_localizationInfoTable($contentTreeArr, $parentPointer, $parentDsMeta=array(), $elementBelongsToCurrentPage) {
		global $LANG, $BE_USER;

				// LOCALIZATION information for content elements (non Flexible Content Elements)
		$output = '';
		if ($contentTreeArr['el']['table']=='tt_content' && $contentTreeArr['el']['sys_language_uid']<=0)	{

				// Traverse the available languages of the page (not default and [All])
			$tRows=array();
			foreach($this->translatedLanguagesArr as $sys_language_uid => $sLInfo)	{
				if ($this->MOD_SETTINGS['langDisplayMode'] && ($this->currentLanguageUid != $sys_language_uid)) continue;
				if ($sys_language_uid > 0)	{
					$l10nInfo = '';
					$flagLink_begin = $flagLink_end = '';

					switch((string)$contentTreeArr['localizationInfo'][$sys_language_uid]['mode'])	{
						case 'exists':
							$olrow = t3lib_BEfunc::getRecordWSOL('tt_content',$contentTreeArr['localizationInfo'][$sys_language_uid]['localization_uid']);

							$localizedRecordInfo = array(
								'uid' => $olrow['uid'],
								'row' => $olrow,
								'content' => $this->render_previewContent($olrow)
							);

								// Put together the records icon including content sensitive menu link wrapped around it:
							$recordIcon_l10n = t3lib_iconWorks::getIconImage('tt_content',$localizedRecordInfo['row'],$this->doc->backPath,'class="absmiddle" title="'.htmlspecialchars('[tt_content:'.$localizedRecordInfo['uid'].']').'"');
							if (!$this->translatorMode)	{
								$recordIcon_l10n = $this->doc->wrapClickMenuOnIcon($recordIcon_l10n,'tt_content',$localizedRecordInfo['uid'],1,'&amp;callingScriptId='.rawurlencode($this->doc->scriptID), 'new,copy,cut,pasteinto,pasteafter');
							}
							$l10nInfo =
								$this->getRecordStatHookValue('tt_content', $localizedRecordInfo['row']['uid']).
								(($elementBelongsToCurrentPage)?$recordIcon_l10n:'').
								//htmlspecialchars(t3lib_div::fixed_lgd_cs(strip_tags(t3lib_BEfunc::getRecordTitle('tt_content', $localizedRecordInfo['row'])), 50));

							$l10nInfo.= '<br/>'.$localizedRecordInfo['content'];

							list($flagLink_begin, $flagLink_end) = explode('|*|', $this->link_edit('|*|', 'tt_content', $localizedRecordInfo['uid'], TRUE));
							if ($this->translatorMode)	{
								$l10nInfo.= '<br/>'.$flagLink_begin.'<em>'.$LANG->getLL('clickToEditTranslation').'</em>'.$flagLink_end;
							}

								// Wrap workspace notification colors:
							if ($olrow['_ORIG_uid'])	{
								$l10nInfo = '<div class="ver-element">'.$l10nInfo.'</div>';
							}

							$this->global_localization_status[$sys_language_uid][]=array(
								'status' => 'exist',
								'parent_uid' => $contentTreeArr['el']['uid'],
								'localized_uid' => $localizedRecordInfo['row']['uid'],
								'sys_language' => $contentTreeArr['el']['sys_language_uid']
							);
						break;
						case 'localize':

							if ($this->rootElementLangParadigm =='free')	{
								$showLocalizationLinks = !$parentDsMeta['langDisable'];	// For this paradigm, show localization links only if localization is enabled for DS (regardless of Inheritance and Separate)
							} else {
								$showLocalizationLinks = ($parentDsMeta['langDisable'] || $parentDsMeta['langChildren']);	// Adding $parentDsMeta['langDisable'] here means that the "Create a copy for translation" link is shown only if the parent container element has localization mode set to "Disabled" or "Inheritance" - and not "Separate"!
							}
								// Assuming that only elements which have the default language set are candidates for localization. In case the language is [ALL] then it is assumed that the element should stay "international".
							if ((int)$contentTreeArr['el']['sys_language_uid']===0 && $showLocalizationLinks)	{

									// Copy for language:
								if ($this->rootElementLangParadigm =='free')	{
									$sourcePointerString = $this->apiObj->flexform_getStringFromPointer($parentPointer);
									$onClick = "document.location='index.php?".$this->link_getParameters().'&source='.rawurlencode($sourcePointerString).'&localizeElement='.$sLInfo['ISOcode']."'; return false;";
								} else {
									$params='&cmd[tt_content]['.$contentTreeArr['el']['uid'].'][localize]='.$sys_language_uid;
									$onClick = "document.location='".$GLOBALS['SOBE']->doc->issueCommand($params)."'; return false;";
								}

								$linkLabel = $LANG->getLL('createcopyfortranslation',1).' ('.htmlspecialchars($sLInfo['title']).')';
								$localizeIcon = '<img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/clip_copy.gif','width="12" height="12"').' class="bottom" title="'.$linkLabel.'" alt="" />';

								$l10nInfo = ($elementBelongsToCurrentPage)?'<a href="#" onclick="'.htmlspecialchars($onClick).'">'.$localizeIcon.'</a>':'';
								$l10nInfo .= ($elementBelongsToCurrentPage)?' <em><a href="#" onclick="'.htmlspecialchars($onClick).'">'.$linkLabel.'</a></em>':'';
								$flagLink_begin = '<a href="#" onclick="'.htmlspecialchars($onClick).'">';
								$flagLink_end = '</a>';

								$this->global_localization_status[$sys_language_uid][]=array(
									'status' => 'localize',
									'parent_uid' => $contentTreeArr['el']['uid'],
									'sys_language' => $contentTreeArr['el']['sys_language_uid']
								);
							}
						break;
						case 'localizedFlexform':
								// Here we want to show the "Localized FlexForm" information (and link to edit record) _only_ if there are other fields than group-fields for content elements: It only makes sense for a translator to deal with the record if that is the case.
								// Change of strategy (27/11): Because there does not have to be content fields; could be in sections or arrays and if thats the case you still want to localize them! There has to be another way...
							// if (count($contentTreeArr['contentFields']['sDEF']))	{
								list($flagLink_begin, $flagLink_end) = explode('|*|', $this->link_edit('|*|', 'tt_content', $contentTreeArr['el']['uid'], TRUE));
								$l10nInfo = $flagLink_begin.'<em>[Click to translate FlexForm]</em>'.$flagLink_end;
								$this->global_localization_status[$sys_language_uid][]=array(
									'status' => 'flex',
									'parent_uid' => $contentTreeArr['el']['uid'],
									'sys_language' => $contentTreeArr['el']['sys_language_uid']
								);
							// }
						break;
					}

					// #
					// ### Mansoor Ahmad - clear links if shortcuts were parsed
					// #
					if(empty($elementBelongsToCurrentPage)) {
						$flagLink_begin = '';
						$flagLink_end = '';
					}
					
					if ($l10nInfo && $BE_USER->checkLanguageAccess($sys_language_uid))	{
						$tRows[]='
							<tr class="bgColor4">
								<td width="1%">'.$flagLink_begin.($sLInfo['flagIcon'] ? '<img src="'.$sLInfo['flagIcon'].'" alt="'.htmlspecialchars($sLInfo['title']).'" title="'.htmlspecialchars($sLInfo['title']).'" />' : $sLInfo['title']).$flagLink_end.'</td>
								<td width="99%">'.$l10nInfo.'</td>
							</tr>';
					}
				}
			}

			$output = count($tRows) ? '
				<table border="0" cellpadding="0" cellspacing="1" width="100%" class="lrPadding">
					<tr class="bgColor4-20">
						<td colspan="2">'.$LANG->getLL('element_localizations',1).':</td>
					</tr>
					'.implode('',$tRows).'
				</table>
			' : '';
		}
		return $output;
	}
	
	
	
	// #
	// ### Mansoor Ahmad - render Pi in the Backendlistview 
	// #
	function renderPi($row) {
		global $TCA;
		
		$rowCType = $row['CType'];
		
		foreach($TCA['tt_content']['types'] as $k => $v) {
			$cElements .= $k.',';
		}

		if(in_array($rowCType,explode(',',$cElements)) && is_array($TCA['tt_content']['columns']['CType']['config']['items'])) {
			foreach($TCA['tt_content']['columns']['CType']['config']['items'] as $k => $v) {
				if($TCA['tt_content']['columns']['CType']['config']['items'][$k]['1'] == $rowCType) {
					//print_r($row);

					$filePath = $TCA['tt_content']['columns']['CType']['config']['items'][$k]['2'];
					$filePath = substr($filePath, 0, strrpos($filePath, '/'));
					$extType = (is_file(PATH_site . 'fileadmin/' . $filePath . '/pageview.png'))?'png':'gif';
					$imageFilePath = $filePath . '/pageview.' . $extType;
					$output	=	$this->getCSS($rowCType, $imageFilePath);

					$output .= $this->getParsedFields($TCA['tt_content']['types'][$rowCType]['showitem'], $row);
					$output	.=	$this->getPiName($this->getLLValue($TCA['tt_content']['columns']['CType']['config']['items'][$k]['0']), $rowCType, $row['uid']); 
				}
			}		
		}
		
		return $output;
	}
	
	// #
	// ### Mansoor Ahmad - render TV Flexform in the Backendlistview
	// #
	function renderFlex($row) {
		global $TCA;

		$flexData	= t3lib_div::xml2array($row['tx_templavoila_flex']);
		$resDS	= $GLOBALS['TYPO3_DB']->exec_SELECTquery("*", "tx_templavoila_datastructure", "uid ='" . $row['tx_templavoila_ds'] . "'", "", "sorting");
		$output = '';
		$typeCE = '';
		while($rowDS	=	$GLOBALS['TYPO3_DB']->sql_fetch_assoc($resDS)) {
			$flexDataDS = t3lib_div::xml2array($rowDS['dataprot']);
			$v = 'flex_'.$row['tx_templavoila_to'];

			if(is_array($flexData) && is_array($flexDataDS)) {
				foreach($flexData['data']['sDEF']['lDEF'] as $fieldname => $fieldarray) {
					//print_r($flexDataDS['ROOT']['el'][$fieldname]['tx_templavoila']['eType']);
					if($flexDataDS['ROOT']['el'][$fieldname]['tx_templavoila']['eType'] != 'ce' && !empty($flexData['data']['sDEF']['lDEF'][$fieldname])) {

						$key = $flexDataDS['ROOT']['el'][$fieldname]['TCEforms']['config']['type'];
						$TCA['tt_content']['columns'] = array_merge($TCA['tt_content']['columns'],array( $v => $flexDataDS['ROOT']['el'][$fieldname]['TCEforms']));
						foreach($flexData['data']['sDEF']['lDEF'][$fieldname] as $langKey => $langValue) {
							switch($langKey) {
								case (($langValue)?'vEN':'0'):
									$wrap = '<table class="trans" width="100%"><tr><td valign="top" style="width:20px"><br /><img title="English" alt="English" src="../../../../typo3/gfx/flags/gb.gif"/></td><td valign="top">|</td></tr></table>';
									break;
								default:
									$wrap = '|';
									break;
							}
							$wrap = explode ('|',trim($wrap));
						
							$row[$v] = $langValue;
							$output .= $wrap[0] . $this->getWrapedField($key,$v,$row,0) . $wrap[1];
						}
					}
					elseif($flexDataDS['ROOT']['el'][$fieldname]['tx_templavoila']['eType'] == 'ce') {
						$typeCE = 'ce';
					}
				}
				unset($TCA['tt_content']['columns'][$v]);
				$output	.=	$this->getPiName($rowDS['title'], $typeCE, $row['uid']);
			}
		}
		
		return $output;	
	}
	
	// #
	// ### Mansoor Ahmad - Split and arrange Fields, which gets from the $TCA
	// #
	function getParsedFields($Items, $row) {
		global $TCA;
		
		$output = '';
		
		// get TSconfig
		$TSconfig = t3lib_BEfunc::getPagesTSconfig(3,$rootLine='',$returnPartArray=0);
		$TCEFORM	=	$TSconfig['TCEFORM.']['tt_content.'];
		$border = 0;
		foreach(t3lib_div::trimExplode(',', $Items) as $v) {
			list($field,$label,$palettes,$config,$style) = t3lib_div::trimExplode(';',$v);
			
			if(!empty($field) && $field != 'CType') {
				$output .= '<table cellpadding="0" cellspacing="0" border="0" width="100%" style="width:100%;border:0px solid #CCCCCC;padding-left:2px;">';
				//echo $v . '*_*' . $style . '*_*' . $border . '<br/>';
				if($style && $border > 0) {
					$output .= '<tr><td style="border-bottom:1px solid #CCCCCC;" colspan="8">&nbsp;</td></tr>';
				}
				
				// # Level1
				$output .= '<tr><td>';
				$key = $TCA['tt_content']['columns'][$field]['config']['type'];
				$wrapedField = $this->getWrapedField($key, $field, $row, $TCEFORM, $label);
				$output .= $wrapedField;
				$border = ($wrapedField)?1:0;
				$output .= '</td></tr>';
				
				// # Level 2
				if($palettes) {
					$output .= '<tr>';
					$Items2 = $TCA['tt_content']['palettes'][$palettes]['showitem'];
					foreach(t3lib_div::trimExplode(',', $Items2) as $v2){
						$output .= '<td style="border-bottom:0px solid #CCCCCC;">';
						list($field2,$label2,$plattes2,$config2,$style2) = t3lib_div::trimExplode(';',$v2);
						$key2 = $TCA['tt_content']['columns'][$field2]['config']['type'];
						$wrapedField2 = $this->getWrapedField($key2, $field2, $row, $TCEFORM, $label2);
						$output .= $wrapedField2;
						$border = ($wrapedField2)?2:$border;
						$output .= '</td>';
					}
					$output .= '</tr>';
				}
				$output .= '</table>';
			}
		}
		return $output;
	}
	
	
	// #
	// ### Mansoor Ahmad - return formated and wrapped Fieldstypes
	// #
	function getWrapedField($key, $v, $row, $TCEFORM, $LLName='') {
		global $TCA;
		//echo $v.'<br/>';
		
		$output = '';
		$ignoreList = 'colPos,section_frame,sys_language_uid,l18n_parent,module_sys_dmail_category'; 
		if(in_array($v,explode(',',$ignoreList)) === FALSE) {
			($TCA['tt_content']['columns'][$v]['config']['internal_type'] == 'file' && $TCA['tt_content']['columns'][$v]['config']['show_thumbs'] == '1') ? $key = 'image' : '' ;
			//echo $v . '--' . $row[$v] . '<br /><br />';
			$label = $this->getLLValue((($LLName)?$LLName:$TCA['tt_content']['columns'][$v]['label']), $v, $TCEFORM);
			switch($key) {
				case 'input':
					$output .= ($row[$v]) ?  '<br /><strong>'.$label.'</strong><br />'.((strlen($row[$v]) > 9 && is_numeric($row[$v]))?date("d.m.Y",$row[$v]):$this->getPagename($row[$v])).'<br />': '' ;
					break;
				case 'text':
					$output .= ($row[$v]) ?  '<br /><strong>'.$this->getLLValue($TCA['tt_content']['columns'][$v]['label'], $v, $TCEFORM).'</strong><br />'.str_replace('src="','src="../../../../', nl2br($row[$v])).'<br />': '' ;
					break;
				case 'image':
					$output .= ($row[$v]) ?  '<br /><strong>'.$this->getLLValue($TCA['tt_content']['columns'][$v]['label'], $v, $TCEFORM).'</strong><br />'.$this->getThumbNail($row[$v]).'<br />': '' ;
					break;
				case 'select':
					foreach($TCA['tt_content']['columns'][$v]['config']['items'] as $sk => $sv) {
						($TCA['tt_content']['columns'][$v]['config']['items'][$sk]['1'] == $row[$v]) ? $selectValue = $TCA['tt_content']['columns'][$v]['config']['items'][$sk]['0']:'';
					}
					
					$output .= ($row[$v]) ?  '<br /><strong>'.$this->getLLValue($TCA['tt_content']['columns'][$v]['label'], $v, $TCEFORM).'</strong><br />'.$this->getLLValue($selectValue, $v, $TCEFORM).'<br />': '' ;
					break;
				case 'group':
					$output .= ($row[$v]) ?  '<br /><strong>'.$this->getLLValue($TCA['tt_content']['columns'][$v]['label'], $v, $TCEFORM).'</strong><br />'.str_replace(',','<br />',$this->getThumbNail_DAM($v,$row)).'<br />': '' ;
					break;
				default:
					break;
			}
		}
		return $output;
	}
	
	
	
	
	// #
	// ### Mansoor Ahmad - parse the LL Array
	// #
	function getLLValue($LLValue, $type = '', $TCEFORM = array('')) {
		global $LANG;
		
		// parse Labels over TSconfig
		$TSaltLabel =  $TCEFORM[$type.'.']['altLabels.'][trim(substr(strrchr($LLValue,'.')+1,2))];
		$TSLabel = ($TSaltLabel)?$TSaltLabel:$TCEFORM[$type.'.']['label'];
		$LLValue = (strpos($TSLabel, '.xml') || strpos($TSLabel, '.xml'))?$LLValue = $TSLabel:$LLValue;
		$TSLabel = ($TSLabel == $LLValue)?$TSLabel = '':$TSLabel;
		
		if(strpos($LLValue, '.xml')) {
			$LLFile		=	strstr(substr($LLValue, 0, strrpos($LLValue, '.xml:')), 'EXT:') . '.xml';
			$LLFName	=	str_replace('.xml:', '', strstr($LLValue, '.xml:'));
		}
		else {
			$LLFile		=	strstr(substr($LLValue, 0, strrpos($LLValue, '.php:')), 'EXT:') . '.php';
			$LLFName	=	str_replace('.php:', '', strstr($LLValue, '.php:'));
		}

		if(strlen($LLFile) > 4 && empty($TSLabel)) {
			$locallang = $LANG;
			$locallang->includeLLFile($LLFile);
			return $locallang->getLL($LLFName);
		}
		elseif($TSLabel) {
			return $TSLabel;
		}
		else {
			return $LLValue;
		}
	}
	

	
	// #
	// ### Mansoor Ahmad - Wrap the $name
	// #
	function getPiName($name, $rowCType = '', $rowUid = '') {
		switch($rowCType) {
			case 'ce':
				$content	=	'<br /><br /><strong id="templavoila_pi1-'.$rowUid.'_label"class="go_backend_layout_flex_ce_label">'.
								$name.
								'</strong><br /><br />';
				break;
			default:
				$content	=	'<br /><br />
								<strong id="'.$rowCType.'-'.$rowUid.'_label" class="go_backend_layout_standard_ce_label">'.
								$name.
								'</strong><br /><br />';
				break;
		}
		
		return $content;
	}
	
	
	
	// #
	// ### Mansoor Ahmad - Formate the Pagelink, if is a one, otherweise get you the rowcontent 
	// #
	function getPagename($id) {
		if (is_numeric($id)){
			$query	=	$GLOBALS['TYPO3_DB']->exec_SELECTquery('title', 'pages', 'uid='.$id);
			$res	=	$GLOBALS['TYPO3_DB']->sql_fetch_assoc($query);
			$output	=	$res['title'].' (ID: '.$id.')';
		}
		else{
			if(strlen($id) > 50){
				$output = substr(str_replace('/',' / ',$id), 0, 50) . '...';
			}
			else{
				$output = str_replace('/',' / ',$id);
			}
		}
		return $output;
	}
	
	
	
	// #
	// ### Mansoor Ahmad - convert Image to an Icon for the Backendpreview
	// #
	function getThumbNail($imagerow, $filepath = '../uploads/pics/') {
		((file_exists(PATH_site.'uploads/tx_templavoila/'.$imagerow))?$filepath = '../uploads/tx_templavoila/':'');
		$BE_func = new t3lib_BEfunc();
		foreach(explode(',',$imagerow) as $src) {
			if(file_exists(PATH_site.'uploads/media/'. $src)) {
				$output .= $this->getPagename($src) . '<hr />';
			}
			elseif(file_exists(PATH_typo3conf . $filepath . $src)) {
				$imgFile = PATH_typo3conf . $filepath . $src;
				$data = str_replace(urlencode(PATH_typo3conf), '',str_replace(PATH_typo3, t3lib_div::getIndpEnv('TYPO3_SITE_URL').'typo3/', $BE_func->getThumbNail(PATH_typo3.'thumbs.php', $imgFile,'','100x100')));
				$output .= str_replace('<img', '<img style="margin:2px 2px 2px 1px;border:1px solid;"', $data);
			}
		}
		return  $output;
	}
	
	
	
	// #
	// ### Mansoor Ahmad - support DAM image_field
	// #
	function getThumbNail_DAM($key,$imagerow) {
		global $TCA;
		$isDAM = strpos($TCA['tt_content']['columns'][$key]['config']['allowed'], 'dam');
		if($isDAM !== false) {
			$images = '';
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('d.file_path, d.file_name', 'tx_dam AS d INNER JOIN tx_dam_mm_ref AS m ON d.uid = m.uid_local', 'm.uid_foreign = '.$imagerow['uid'].' AND m.ident = \''.$key .'\'', '', 'sorting_foreign');
			$isImage = $TCA['tt_content']['columns'][$key]['config']['allowed_types'];
			while($row	=	$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				if($isImage) {
						$images .= $this->getThumbNail($row['file_name'],'../'.$row['file_path']);	
				}
				else {
						$images .= $this->getPagename($row['file_path'] . $row['file_name']) . '<hr />,';
				}
			}
			return $images; 
		}
		else {
			return $imagerow[$key];
		}
		
	}
	
	
	
	
	// #
	// ### Mansoor Ahmad - get CSS for the BG Image
	// #
	function getCSS($piName, $imageFilePath) {
		$imgRelPath	=	'../../../' . $imageFilePath;
		$imgAbsPath	=	PATH_site . 'fileadmin/' . $imageFilePath;
		
		if($piName && file_exists($imgAbsPath)) {
			$imgInfo = getimagesize($imgAbsPath);
			$output	=	'<style type="text/css">
							td.templavoila_pi1 td.templavoila_pi1 td.'.$piName.':hover,
							td.templavoila_pi1 td.'.$piName.':hover,
							td.'.$piName.':hover,
							td.'.$piName.' {
								height:'.$imgInfo[1].'px;
								background:url('.$imgRelPath.');
								background-repeat:no-repeat;
								background-position:right top;
								vertical-align:bottom;
								padding-left:2px;
							}
						</style>';
			return $output;
		}
	}
	
	
	
	
	// #
	// ### Mansoor Ahmad - get the Droppable Code
	// #
	function getDroppable($ID) {
		$dropabble = '
					<style type="text/css">
						/* Drag & Drop CSS config*/

						div#go_backend_layout_droppable_'.$ID.'{
							position: relative;
							width:100%;
							background-color:transparent;
							margin:5px 0px 5px 0px;
							border: 3px solid #ccc;
						}
							
						div#go_backend_layout_droppable_'.$ID.'.hover{
							border: 3px dashed #aaa;
							background-color:transparent;
						}
					</style>
					
					<script type="text/javascript">
						startDroppable( \''.$ID.'\' );
					</script>
					';
					
		return $dropabble;
	}







	/*******************************************
	 *
	 * Rendering functions for certain subparts
	 *
	 *******************************************/

	/**
	 * Rendering the preview of content for Page module.
	 * @autor	Mansoor Ahmad - I do some modification
	 * @param	array		$previewData: Array with data from which a preview can be rendered.
	 * @param	array		$elData: Element data
	 * @param	array		$ds_meta: Data Structure Meta data
	 * @param	string		$languageKey: Current language key (so localized content can be shown)
	 * @param	string		$sheet: Sheet key
	 * @return	string		HTML content
	 */
	function render_previewData($previewData, $elData, $ds_meta, $languageKey, $sheet)	{
		global $LANG;

			// General preview of the row:
		$previewContent = is_array($previewData['fullRow']) && $elData['table']=='tt_content' ? $this->render_previewContent($previewData['fullRow']) : '';

			// Preview of FlexForm content if any:

		return $previewContent;
	}







	// ELIO@GOSIGN 13/08/2009
	// Load TS of current page
	function loadTS($pageUid) {
			$sysPageObj = t3lib_div::makeInstance('t3lib_pageSelect');
			$rootLine = $sysPageObj->getRootLine($pageUid);
			$TSObj = t3lib_div::makeInstance('t3lib_tsparser_ext');
			$TSObj->tt_track = 0;
			$TSObj->init();
			$TSObj->runThroughTemplates($rootLine);
			$TSObj->generateConfig();
			return $TSObj;
	}

	// ELIO@GOSIGN 13/08/2009 START
	// ADD LF-Editor link
	function addLFEditLink ( $ext_type, $ll_fileName = '' ) {
		global $TYPO3_CONF_VARS, $LANG, $LANGPLUGIN, $TCA;
		
		// get ext name and pi nr
//		$LLValue = t3lib_BEfunc::getLabelFromItemlist('tt_content','list_type',$ext_type );
		$extKey = substr($ext_type, 0, strrpos($ext_type, '_pi'));
		$piNr	= str_replace('_', '', strstr($ext_type, '_pi'));
		if (!t3lib_extMgm::isLoaded($extKey)) // if no extension
			return '';
		$extPath = t3lib_extMgm::extPath($extKey);
		$extPath = ($extPath[strlen($extPath)-1] == '/') ? substr( $extPath, 0, strlen($extPath)-1 ) : $extPath; // Trim last slash
		$extPath = substr_replace( $extPath, '/', strrpos( $extPath, '/' ), 0 ); // double slash e.g.: typo3conf/ext//extension_name
		
		if ( empty( $ll_fileName ) ) // if not defined, we use the standard locallang.xml in the pi folder
			$ll_fileName = $piNr.'/locallang.xml';
		else {
			// create file name relative to ext dir
			
			$ll_fileName = t3lib_div::getFileAbsFileName( $ll_fileName ); // interpret "EXT:"
			$ep = t3lib_extMgm::extPath($extKey);
			if ( strpos( $ll_fileName, $ep ) !== FALSE ) // if in extension directory
				$ll_fileName = substr( $ll_fileName, strlen($ep));
			else
				return '<span class="LFE_Link" style="color: #806060;">'.$LANG->getLL('LFE_ErrorDirectory').$ll_fileName.'</span></a><br />';
		}
		if (!file_exists(t3lib_extMgm::extPath($extKey).$ll_fileName)) // No link if no locallang
			return '';
		
		// LFE link variables
		$addGetVars = '&id=0&SET[langList]=de&SET[function]=langfile.edit'.($extPath?'&SET[extList]='.$extPath:'').($ll_fileName?'&SET[langFileList]='.$ll_fileName:'');
		$onClick = 'onclick="top.goToModule(\'user_txlfeditorM1\', 0, \''.$addGetVars.'\');this.blur();return false;"';
		// get link title from LL
		$LFE_Link = $LANG->getLL('LFE_Link');
		$output .= '<a '.$onClick.' href="#"><span '.onClick.' class="LFE_Link" style="color: #806060;">' .
					$LFE_Link.'</span></a><br />';
		return $output;
	}
	// ELIO@GOSIGN 13/08/2009 END
	
	
	
	
	/**
	 * @author	Mansoor Ahmad
	 * @company Gosign media. GmbH
	 *
	 * @param	array	Current Input of the tt_content Table
	 *
	 * @return	String	HTML output for the tt_news plugin
	 */
	function getTTNews($row){
		global $GBL, $TCA;
		
		$flexArrayTTNews = t3lib_div::xml2array($row['pi_flexform']);
		$displayTyp = $flexArrayTTNews['data']['sDEF']['lDEF']['what_to_display']['vDEF'];
		
		if($displayTyp == 'LIST'){
			//# Make Instance
			require_once(PATH_typo3 . 'class.db_list_extra.inc');
			$dbList = t3lib_div::makeInstance('localRecordList');
			//require_once(t3lib_extMgm::extPath('tt_news').'lib/class.tx_ttnews_recordlist.php');
			//$dbList = t3lib_div::makeInstance('tx_ttnews_recordlist');
			
			//# Set Attribute
			$dbList->tableList = 'tt_news';
			$dbList->showLimit = ($flexArrayTTNews['data']['s_template']['lDEF']['listLimit']['vDEF'])?$flexArrayTTNews['data']['s_template']['lDEF']['listLimit']['vDEF']:10;				
			$dbList->id = $flexArrayTTNews['data']['s_misc']['lDEF']['pages']['vDEF'];
			$dbList->table = 'tt_news';
			$dbList->setFields = array('uid','title');
			$dbList->noControlPanels = FALSE;
			$dbList->newWizards = FALSE;
			$dbList->displayFields = array('uid','title');
			$dbList->localizationView = TRUE;
			$dbList->allFields = 0;
			$dbList->thumbs = 1;
			$dbList->calcPerms = 16;
			$dbList->fieldArray = array('_LOCALIZATION_');
			
			//# Fill the $GBL
			//print_r($flexArrayTTNews);
			$archiv = ($flexArrayTTNews['data']['sDEF']['lDEF']['archive']['vDEF'] == 1) ? 'archivdate > datetime' : '';
			
			$uidList = '0';
			$uidCatList = $flexArrayTTNews['data']['sDEF']['lDEF']['categorySelection']['vDEF'];
			//$titleCatList = '';
			
			
			$resTTNews = $GLOBALS['TYPO3_DB']->exec_SELECTquery('d.uid, c.title', 'tt_news AS d INNER JOIN tt_news_cat_mm AS cm ON d.uid = cm.uid_local INNER JOIN tt_news_cat AS c ON c.uid = cm.uid_foreign LEFT JOIN tt_news_related_mm AS m ON d.uid = m.uid_local', 'd.pid IN(" '. $dbList->id .'",126) AND cm.uid_foreign IN("'.$uidCatList.'") ',  '',  '');
			while($rowTTNews	=	$GLOBALS['TYPO3_DB']->sql_fetch_assoc($resTTNews)) {
				$uidList .=  ','.$rowTTNews['uid'];
				//$titleCatList .=  $rowTTNews['title'] . ', ';
			}
			
			
			$GBL['tt_news.']['uidList'] = $uidList;
			
			//print_r($GBL);
			
			//# Run Instancefunction
			//$dbList->makeQueryArray('tt_news',14,'uid=103','*');
			//$dbList->doEdit = $this->mayUserEditArticles;
			
			$dbList->start($dbList->id,$dbList->table,0,$dbList->search_field,'-1',$dbList->showLimit);
			
			$dbList->sortField = ($flexArrayTTNews['data']['sDEF']['lDEF']['listOrderBy']['vDEF'])?$flexArrayTTNews['data']['sDEF']['lDEF']['listOrderBy']['vDEF']:'sorting';
			$dbList->sortRev = ($flexArrayTTNews['data']['sDEF']['lDEF']['ascDesc']['vDEF'] == 'desc')?TRUE:FALSE;
			
			$dbList->generateList();

			//$buttons = $dbList->getButtons();
			
			$dbList->backPath = '../../../../typo3/';
			
			/*
			$paramsNew = '&edit['.$dbList->table.']['.$dbList->id.']=new';
			$onClickNew = htmlspecialchars(t3lib_BEfunc::editOnClick($paramsNew,$dbList->backPath,$this->returnUrl));
			$buttons['new_record'] = '<a href="#" class="newWizard" onclick="'.$onClickNew.'">' .
									'<img' . t3lib_iconWorks::skinImg($dbList->backPath, 'gfx/new_el.gif') . ' title="" alt="" />' .
									'</a>';
									
			$paramsEdit = '&edit['.$dbList->table.']['.$dbList->id.']=new';
			$onClickEdit = htmlspecialchars(t3lib_BEfunc::editOnClick($paramsEdit,$dbList->backPath,$this->returnUrl));
			$buttons['edit_record'] = '<a href="#" class="editWizard" onclick="'.$onClickEdit.'">' .
									'<img' . t3lib_iconWorks::skinImg($dbList->backPath, 'gfx/edit_el.gif') . ' title="" alt="" />' .
									'</a>';
			*/						
			//print_r($buttons['new_record']);
			$output = '	<div class="list_options_header">Plugin Einstellungen</div>
						<div class="list_options">
							<span>Darstellungsart:</span> Listenansicht
							<br />
							<span>Anzahl der Nachrichten pro Seite:</span> '.$flexArrayTTNews['data']['s_template']['lDEF']['listLimit']['vDEF'].'
							<br />
							<span>Sotiert nach:</span> '.$dbList->sortField.'
							<br />
							<span>Sortierreihenfolge:</span> '.((!$dbList->sortRev)?'Aufsteigend':'Absteigend').'
							<br />
							<span>Nachrichtenordner:</span> '.$this->getPagename($flexArrayTTNews['data']['s_misc']['lDEF']['pages']['vDEF']).'
						</div>';
						
			$output .= $dbList->getTable('tt_news',$dbList->id,'title,bodytext,datetime,archivedate,_LOCALIZATION_') . $this->getPiName('News');
			$output = str_replace('typo3/gfx/','../../../typo3/gfx/',$output);
			$output = str_replace('href="tce_db.php','href="../../../../typo3/tce_db.php',$output);
			$output = str_replace('db_list.php?id=','../../../../typo3/db_list.php?id=',str_replace('showLimit=2','showLimit=500',$output));
			
			
			
			
			//print_r();
			
			//print_r(t3lib_xml::output($row['pi_flexform']));
			//print_r(t3lib_TCEmain::checkValue('tt_content', 'pi_flexform', 'archive'));
			//$ff_tools = t3lib_div::makeInstance('t3lib_flexformtools');
			//print_r($ff_tools->getAvailableLanguages());
			//print_r($ff_tools->traverseFlexFormXMLData_recurse($row['pi_flexform'],$row['pi_flexform']));
			//$this->pi_getFFvalueFromSheetArray();
			//$output = 
		}
		else {
			$output = $this->getPiName('News');
		}
		
		
			//$output = str_replace('<tr class="c-headLine"><td nowrap="nowrap" class="col-icon"></td>','<tr class="c-headLine"><td nowrap="nowrap" class="col-icon">'.$buttons['new_record'].'</td>',$output);
			//$output = str_replace('../typo3conf','../../../../typo3conf', str_replace('sysext/t3skin/','../../../../typo3/sysext/t3skin/', str_replace('db_list.php', '../../../../typo3/db_list.php', $output)));

			
			/*
			require_once(t3lib_extMgm::extPath('tt_news').'lib/class.tx_ttnews_recordlist.php');
			$listObject = t3lib_div::makeInstance('tx_ttnews_recordlist');
			$listObject->id = $flexArrayTTNews['data']['s_misc']['lDEF']['pages']['vDEF'];
			$listObject->pidList = $dbList->id;
			*/
			
			//$output =  $dbList->HTMLcode;//$listObject->makeOrdinaryList($dbList->table,$dbList->id,'uid,title,datetime,archivedate,tstamp',1,'');
			
		
		return $output;
	}
	
	
	
	
	
	/**
	 * Renders the sub elements of the given elementContentTree array. This function basically
	 * renders the "new" and "paste" buttons for the parent element and then traverses through
	 * the sub elements (if any exist). The sub element's (preview-) content will be rendered
	 * by render_framework_singleSheet().
	 *
	 * Calls render_framework_allSheets() and therefore generates a recursion.
	 *
	 * @author	Lucas Jenﬂ <lucas@gosign.de>
	 *
	 * @param	array		$elementContentTreeArr: Content tree starting with the element which possibly has sub elements
	 * @param	string		$languageKey: Language key for current display
	 * @param	string		$sheet: Key of the sheet we want to render
	 * @return	string		HTML output (a table) of the sub elements and some "insert new" and "paste" buttons
	 * @access protected
	 * @see render_framework_allSheets(), render_framework_singleSheet()
	 */
	function render_framework_subElements($elementContentTreeArr, $languageKey, $sheet, $elementBelongsToCurrentPage, $dragAndDropEnable){
		global $LANG;

		$beTemplate = '';
		$flagRenderBeLayout = false;

			// Define l/v keys for current language:
		$langChildren = intval($elementContentTreeArr['ds_meta']['langChildren']);
		$langDisable = intval($elementContentTreeArr['ds_meta']['langDisable']);

		$lKey = $langDisable ? 'lDEF' : ($langChildren ? 'lDEF' : 'l'.$languageKey);
		$vKey = $langDisable ? 'vDEF' : ($langChildren ? 'v'.$languageKey : 'vDEF');

		if (!is_array($elementContentTreeArr['sub'][$sheet]) || !is_array($elementContentTreeArr['sub'][$sheet][$lKey])) return '';

		$output = '';
		$cells = array();
		$headerCells = array();

				// gets the layout
		$beTemplate = $elementContentTreeArr['ds_meta']['beLayout'];

				// no layout, no special rendering
		$flagRenderBeLayout = ($beTemplate) ? TRUE : FALSE;

		// #
		// ### Mansoor Ahmad - Caspar add this, because the default templavoila engine is to slow for big pages :-) - start
		// #
		if($flagRenderBeLayout) {
			$beTemplateArray = array();
			foreach($elementContentTreeArr['sub'][$sheet][$lKey] as $fieldID => $fieldValuesContent)	{
				if ($elementContentTreeArr['previewData']['sheets'][$sheet][$fieldID]['isMapped'] && is_array($fieldValuesContent[$vKey]))	{
					$beTemplateArray['keys'][] = $fieldID;
				}
			}
			$beTemplateArray['templateParts'] = preg_split('/###('.implode('|', $beTemplateArray['keys']).')###/', $beTemplate);
		}
		// #
		// ### Mansoor Ahmad - Caspar add this, because the default templavoila engine is to slow for big pages :-) - end
		// #

		
		// Check for breakField. If it is set, insert go_stopcslide_pi1 into the selected column and reload. - Lucas Jenﬂ
		$id = intval($_GET['id']);
		
		if(isset($_GET['breakField'])) {
			mysql_query('INSERT INTO tt_content (`pid`, `tstamp`, `crdate`, `sorting`, `CType`) VALUES ('.$id.', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 200, "go_stopcslide_pi1")');  
			$flex = mysql_fetch_assoc(mysql_query('SELECT tx_templavoila_flex FROM pages WHERE uid = '.$id));
			
			if(!$flex['tx_templavoila_flex']) {
				$flex = array();
			}
			else {
				$flex = t3lib_div::xml2array($flex['tx_templavoila_flex']);
			}

			$insertId = mysql_insert_id();

			// #
			// ### Mansoor Ahmad - Caspar fix the Lucas Code for T3 4.3 - start
			// #
			// # Since T3 4.4 I commetet this out!
			//if(is_array($flex['data']['sDEF']['lDEF'][$_GET['breakField']])) {
				$flex['data']['sDEF']['lDEF'][$_GET['breakField']]['vDEF'] = $insertId;
				
				$ff_tools = t3lib_div::makeInstance('t3lib_flexformtools');
				$flex = $ff_tools->flexArray2xml($flex, TRUE);
				
				mysql_query('UPDATE pages SET tx_templavoila_flex = "'.mysql_real_escape_string($flex).'" WHERE uid = '.$id);
				header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id);
			//}
			// #
			// ### Mansoor Ahmad - Caspar fix the Lucas Code for T3 4.3 - end
			// #
		}
		
		// Step up the rootline in order to find source of inheritance. - Lucas Jenﬂ
		$sourcePages = array();
		$row = mysql_fetch_assoc(mysql_query('SELECT pid, tx_templavoila_ds FROM pages WHERE uid = '.$id));
		$pid = $row['pid'];
		$dataStructure = intval($row['tx_templavoila_ds']);
		
		while($pid > 0) {
			$row = mysql_fetch_assoc(mysql_query('SELECT uid, pid, tx_templavoila_flex, tx_templavoila_ds FROM pages WHERE uid = '.$pid));
			
			if(!$dataStructure && $row['tx_templavoila_ds']) {
				$dataStructure = ($row['tx_templavoila_ds']);
			}
			
			if(!$row['tx_templavoila_flex']) {
				$pid = $row['pid'];
				continue;
			}
			
			$flex = t3lib_div::xml2array($row['tx_templavoila_flex']);
			foreach($flex['data']['sDEF']['lDEF'] as $key => $value) {
				if($value['vDEF'] && !isset($sourcePages[$key])) {
					$sourcePages[$key] = $row['uid'];
				}
			}
			$pid = $row['pid'];
		}
		
		// Fetch current page datastructure, in order to determine for which columns contentslide is enabled. - Lucas Jenﬂ
		$row = mysql_fetch_assoc(mysql_query('SELECT dataprot FROM tx_templavoila_datastructure WHERE uid = '.$dataStructure));
		$dataStructure = t3lib_div::xml2array($row['dataprot']);
		$contentSlideEnabled = array();
		foreach($dataStructure['ROOT']['el'] as $field => $content) {
			if(isset($content['tx_templavoila']['TypoScript']) && strstr($content['tx_templavoila']['TypoScript'], 'tx_kbtvcontslide_pi1')) {
				$contentSlideEnabled[$field] = true;
			}
		}
		
			// Traverse container fields:
		foreach($elementContentTreeArr['sub'][$sheet][$lKey] as $fieldID => $fieldValuesContent)	{
			if ($elementContentTreeArr['previewData']['sheets'][$sheet][$fieldID]['isMapped'] && is_array($fieldValuesContent[$vKey]))	{
				$fieldContent = $fieldValuesContent[$vKey];

				$cellContent = '';

					// Create flexform pointer pointing to "before the first sub element":
				$subElementPointer = array (
					'table' => $elementContentTreeArr['el']['table'],
					'uid' => $elementContentTreeArr['el']['uid'],
					'sheet' => $sheet,
					'sLang' => $lKey,
					'field' => $fieldID,
					'vLang' => $vKey,
					'position' => 0
				);

				$canCreateNew = $GLOBALS['BE_USER']->isPSet($this->calcPerms, 'pages', 'new');

				
				// #
				// ### Mansoor Ahmad - Generate infokey - start
				// #
				$goBe_uid		= $subElementPointer['uid'];
				$goBe_pid		= $id;
				$goBe_fieldID	= $fieldID ;
				$goBE_key		= $goBe_fieldID.'-'.$goBe_pid.'-'.$goBe_uid;
				// #
				// ### Mansoor Ahmad - Generate infokey - end
				// #
				
				if (!$this->translatorMode && $canCreateNew)	{
						// "New" and "Paste" icon:
					$newIcon = ($elementBelongsToCurrentPage)?'<img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/new_el.gif','').' style="text-align: center; vertical-align: middle;" vspace="5" hspace="1" border="0" title="'.$LANG->getLL ('createnewrecord').'" alt="" />':'';
					$cellContent .= ($elementBelongsToCurrentPage && $dragAndDropEnable)?'<div id="'.$goBE_key.'_0" class="go_backend_layout_droppables_position" style="" onmouseout="this.setAttribute(\'class\', \'go_backend_layout_droppables_position\');" onmouseover="setDropareaPosClass(\''.$goBE_key.'_0\',\'act\'); this.setAttribute(\'class\', dropareaPosClass);" onmouseup="dropareaPosClass = \'go_backend_layout_droppables_position\'; this.setAttribute(\'class\', \'go_backend_layout_droppables_position\');newPostion = 0;startDroppable( \''.(($elementBelongsToCurrentPage)?$goBE_key:'').'\' );" >':'';
					$cellContent .= $this->link_new($newIcon, $subElementPointer);
					$cellContent .= ($elementBelongsToCurrentPage)?$this->clipboardObj->element_getPasteButtons($subElementPointer):'';
					$cellContent .= ($elementBelongsToCurrentPage && $dragAndDropEnable)?'</div>':'';
					$cellContent .= ($elementBelongsToCurrentPage && $dragAndDropEnable)?$this->getDroppable($goBE_key):'';
				}

					// Render the list of elements (and possibly call itself recursively if needed):
				if (is_array($fieldContent['el_list']))	 {
					foreach($fieldContent['el_list'] as $position => $subElementKey)	{
						$subElementArr = $fieldContent['el'][$subElementKey];

						if ((!$subElementArr['el']['isHidden'] || $this->MOD_SETTINGS['tt_content_showHidden']) && $this->displayElement($subElementArr))	{

								// When "onlyLocalized" display mode is set and an alternative language gets displayed
							if (($this->MOD_SETTINGS['langDisplayMode'] == 'onlyLocalized') && $this->currentLanguageUid>0)	{

									// Default language element. Subsitute displayed element with localized element
								if (($subElementArr['el']['sys_language_uid']==0) && is_array($subElementArr['localizationInfo'][$this->currentLanguageUid]) && ($localizedUid = $subElementArr['localizationInfo'][$this->currentLanguageUid]['localization_uid']))	{
									$localizedRecord = t3lib_BEfunc::getRecordWSOL('tt_content', $localizedUid, '*');
									$tree = $this->apiObj->getContentTree('tt_content', $localizedRecord);
									$subElementArr = $tree['tree'];
								}
							}
							$this->containedElements[$this->containedElementsPointer]++;

								// Modify the flexform pointer so it points to the position of the curren sub element:
							$subElementPointer['position'] = $position;

							$cellContent .= $this->render_framework_allSheets($subElementArr, $languageKey, $subElementPointer, $elementContentTreeArr['ds_meta']);

							if (!$this->translatorMode && $canCreateNew) {
								// "New" and "Paste" icon:
								$newIcon = ($elementBelongsToCurrentPage)?'<img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/new_el.gif','').' style="text-align: center; vertical-align: middle;" vspace="5" hspace="1" border="0" title="'.$LANG->getLL('createnewrecord').'" alt="" />':'';
								$cellContent .= ($elementBelongsToCurrentPage && $dragAndDropEnable)?'<div id="'.$goBE_key.'_'.$position.'" class="go_backend_layout_droppables_position" style="" onmouseout="this.setAttribute(\'class\', \'go_backend_layout_droppables_position\');" onmouseover="setDropareaPosClass(\''.$goBE_key.'_'.$position.'\',\'act\'); this.setAttribute(\'class\', dropareaPosClass);" onmouseup="dropareaPosClass = \'go_backend_layout_droppables_position\'; this.setAttribute(\'class\', \'go_backend_layout_droppables_position\');newPostion = '.$position.';startDroppable( \''.(($elementBelongsToCurrentPage)?$goBE_key:'').'\');" >':'';
								$cellContent .= $this->link_new($newIcon, $subElementPointer);
								$cellContent .= ($elementBelongsToCurrentPage)?$this->clipboardObj->element_getPasteButtons($subElementPointer):'';
								$cellContent .= ($elementBelongsToCurrentPage && $dragAndDropEnable)?'</div>':'';
								$cellContent .= ($elementBelongsToCurrentPage && $dragAndDropEnable)?$this->getDroppable($goBE_key):'';
							}
						}
					}
				}
				else if(isset($contentSlideEnabled[$fieldID])) {
					
					// Show notice of inheritance if there are no content elements. - Lucas Jenﬂ
					$cellContent .= '
<table cellspacing="0" cellpadding="0" style="width: 100%; margin-bottom: 5px;">
		<tr style="background-color: rgb(248, 249, 251);">
			<td style="vertical-align: top; padding: 2px;">
				<strong>Hinweis</strong>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="padding: 2px;">
				<span>Diese Spalte zeigt Inhalt einer &uuml;bergeordneten Seite an.</span> <br/><br/>
				<a href="'.$_SERVER['PHP_SELF'].'?id='.$sourcePages[$fieldID].'">Zur vererbenden Seite springen</a><br/><br/>
				<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&breakField='.$fieldID.'">Vererbung unterbrechen</a><br/>
			</td>
		</tr>
</table>';
				}
					
					// Add cell content to registers:
				if ($flagRenderBeLayout==TRUE){
					// #
					// ### Mansoor Ahmad - add CSS class "go_backend_layout_header"
					// #
					/*
					require_once(PATH_t3lib . 'class.t3lib_treeview.php');
					$pageTree = t3lib_div::makeInstance('t3lib_treeview');
					$pageTree->init('pid=14','sorting');
					$pageTree->getBrowsableTree();
					//$pageTree->getRootRecord(11);
					print_r($pageTree->printTree());
					*/
					/*
					require_once(PATH_t3lib . 'class.t3lib_pagetree.php');
					$pageTree = t3lib_div::makeInstance('t3lib_pagetree');
					$pageTree->init();
					$pageTree->expandAll=1;
					$pageTree->MOUNTS = array('0' => '11');
					$pageTree->setRecs = 0;
					$pageTree->table = 'pages';
					$pageTree->ids = array('13','12','19');
					$pageTree->getBrowsableTree();
					print_r($pageTree->printTree());
					*/
					/*
					if(!($this->stop == 1)){
					require_once(PATH_t3lib . 'class.t3lib_browsetree.php');
					$pageTree = t3lib_div::makeInstance('t3lib_browseTree');
					$pageTree->init();
					$pageTree->MOUNTS = array('0' => '4');
					$pageTree->getBrowsableTree();
					print_r($pageTree->printTree());
					$this->stop = 1;
					}
					*/

					if(!($this->stop == 1)){
						//require (PATH_typo3 . 'mod/web/list/conf.php');
						//require (PATH_typo3 . 'init.php');
						//require (PATH_typo3 . 'template.php');
					
						// require_once(PATH_t3lib.'class.t3lib_page.php');
						// require_once(PATH_t3lib.'class.t3lib_pagetree.php');
						// require_once(PATH_t3lib.'class.t3lib_recordlist.php');
						// require_once(PATH_t3lib.'class.t3lib_clipboard.php');
						// require_once(PATH_t3lib.'class.t3lib_parsehtml.php');
						// require_once(PATH_typo3 . 'class.db_list.inc');
						
						
						
						//$dbList->tested();
						
						//$dblist->allowedNewTables 
						//$dbList->pidSelect = 14;
						
						//$dbList->counter++;

						//print_r($dbList->HTMLcode);
						//echo "before generateList";
						//print_r($dbList->generateList());

						$this->stop = 1;
					}
					
					
					
					// #
					// ### Mansoor Ahmad - This adding is a part of Caspars Optimiezing - start
					// #
					$beTemplateArray['content'][$fieldID] = '<table width="100%" class="beTemplateCell"><tr><td valign="top" id="go_backend_layout_droppable_'.$goBE_key.'_label" class="go_backend_layout_header">'.$LANG->sL($fieldContent['meta']['title'],1).'</td></tr><tr><td id="go_backend_layout_droppable_'.$goBE_key.'" valign="top" style="padding: 5px;">'.$cellContent.'</td></tr></table>';
					// #
					// ### Mansoor Ahmad - This adding is a part of Caspars Optimiezing - end
					// #
					
				} else {
					// Add cell content to registers:
					$headerCells[]='<td valign="top" width="'.round(100/count($elementContentTreeArr['sub'][$sheet][$lKey])).'%" style="background-color: '.$this->doc->bgColor4.'; padding-top:0; padding-bottom:0;">'.$LANG->sL($fieldContent['meta']['title'],1).'</td>';
					$cells[]='<td valign="top" width="'.round(100/count($elementContentTreeArr['sub'][$sheet][$lKey])).'%" style="border: 1px dashed #000; padding: 5px 5px 5px 5px;">'.$cellContent.'</td>';
				}
			}
		}

		if ($flagRenderBeLayout) {
			// removes not used markers
			// #
			// ### Mansoor Ahmad -  This adding is a part of Caspars Optimiezing - start
			// #
			$beTemplate = $beTemplateArray['templateParts'][0];
			foreach($beTemplateArray['keys'] as $num => $fieldID) {
				$beTemplate .= $beTemplateArray['content'][$fieldID] . $beTemplateArray['templateParts'][($num+1)];
			}
			// #
			// ### Mansoor Ahmad -  This adding is a part of Caspars Optimiezing - end
			// #
			return $beTemplate;
		}

			// Compile the content area for the current element (basically what was put together above):
		if (count ($headerCells) || count ($cells)) {
			$output = '
				<table border="0" cellpadding="2" cellspacing="2" width="100%">
					<tr>'.(count($headerCells) ? implode('', $headerCells) : '<td>&nbsp;</td>').'</tr>
					<tr>'.(count($cells) ? implode('', $cells) : '<td>&nbsp;</td>').'</tr>
				</table>
			';
		}

		return $output;
	}
	
	


	
	
	/**
	 * Main function of the module.
	 *
	 * @author	Lucas Jenﬂ <lucas@gosign.de> & Mansoor Ahmad <mansoor@gosign.de>
	 *
	 * @return	void
	 * @access public
	 */
	function main()    {
		global $BE_USER,$LANG,$BACK_PATH;

		if (!is_callable(array('t3lib_div', 'int_from_ver')) || t3lib_div::int_from_ver(TYPO3_version) < 4000000) {
			$this->content = 'Fatal error:This version of TemplaVoila does not work with TYPO3 versions lower than 4.0.0! Please upgrade your TYPO3 core installation.';
			return;
		}

			// Access check! The page will show only if there is a valid page and if this page may be viewed by the user
		if (is_array($this->altRoot))	{
			$access = true;
		} else {
			$pageInfoArr = t3lib_BEfunc::readPageAccess($this->id, $this->perms_clause);
			$access = (intval($pageInfoArr['uid'] > 0));
		}

		if ($access)    {

			$this->calcPerms = $GLOBALS['BE_USER']->calcPerms($pageInfoArr);

				// Define the root element record:
			$this->rootElementTable = is_array($this->altRoot) ? $this->altRoot['table'] : 'pages';
			$this->rootElementUid = is_array($this->altRoot) ? $this->altRoot['uid'] : $this->id;
			$this->rootElementRecord = t3lib_BEfunc::getRecordWSOL($this->rootElementTable, $this->rootElementUid, '*');
			$this->rootElementUid_pidForContent = $this->rootElementRecord['t3ver_swapmode']==0 && $this->rootElementRecord['_ORIG_uid'] ? $this->rootElementRecord['_ORIG_uid'] : $this->rootElementRecord['uid'];

				// Check if we have to update the pagetree:
			if (t3lib_div::_GP('updatePageTree')) {
				t3lib_BEfunc::getSetUpdateSignal('updatePageTree');
			}

				// Draw the header.
			$this->doc = t3lib_div::makeInstance('noDoc');
			$this->doc->docType= 'xhtml_trans';
			$this->doc->backPath = $BACK_PATH;
			$this->doc->divClass = '';
			$this->doc->form='<form action="'.htmlspecialchars('index.php?'.$this->link_getParameters()).'" method="post" autocomplete="off">';

				// Adding classic jumpToUrl function, needed for the function menu. Also, the id in the parent frameset is configured.
			$this->doc->JScode = $this->doc->wrapScriptTags('
				function jumpToUrl(URL)	{ //
					document.location = URL;
					return false;
				}
				if (top.fsMod) top.fsMod.recentIds["web"] = '.intval($this->id).';
			' . $this->doc->redirectUrls() . '
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
								top.fsMod.recentIds["web"]=id;
								top.fsMod.navFrameHighlightedID["web"]="pages"+id+"_"+top.fsMod.currentBank;	// For highlighting

								if (top.content && top.content.nav_frame && top.content.nav_frame.refresh_nav)	{
									top.content.nav_frame.refresh_nav();
								}
							}

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

							if (top.fsMod) top.fsMod.recentIds["web"] = '.intval($this->id).';
						'

			);
			// #
			// ### Mansoor Ahmad - I need this for my Drag & Drop - start
			// #
			$this->doc->JScode .= '<script type="text/javascript" src="../../../../typo3/contrib/scriptaculous/scriptaculous.js?load=effects,dragdrop"></script>
								   <script type="text/javascript" src="../../../../typo3conf/ext/go_backend_layout/lib/draganddrop.js"></script>';
			// #
			// ### Mansoor Ahmad - I need this for my Drag & Drop - end
			// #
				// Set up JS for dynamic tab menu and side bar
			$this->doc->JScode .= $this->doc->getDynTabMenuJScode();
			$this->doc->JScode .= $this->modTSconfig['properties']['sideBarEnable'] ? $this->sideBarObj->getJScode() : '';

				// Setting up support for context menus (when clicking the items icon)
			$CMparts = $this->doc->getContextMenuCode();
			$this->doc->bodyTagAdditions = $CMparts[1];
			$this->doc->JScode.= $CMparts[0];
			$this->doc->postCode.= $CMparts[2];


			if (t3lib_extMgm::isLoaded('t3skin')) {
				// Fix padding for t3skin in disabled tabs
				$this->doc->inDocStyles .= '
table.typo3-dyntabmenu td.disabled, table.typo3-dyntabmenu td.disabled_over, table.typo3-dyntabmenu td.disabled:hover { padding-left: 10px; }
				';
			}

			$this->handleIncomingCommands();

				// Start creating HTML output
			$this->content .= $this->doc->startPage($LANG->getLL('title'));
			$render_editPageScreen = true;
				// Show message if the page is of a special doktype:
			if ($this->rootElementTable == 'pages') {

					// Initialize the special doktype class:
				$specialDoktypesObj =& t3lib_div::getUserObj ('&tx_templavoila_mod1_specialdoktypes','');
				$specialDoktypesObj->init($this);

				$methodName = 'renderDoktype_'.$this->rootElementRecord['doktype'];
				if (method_exists($specialDoktypesObj, $methodName)) {
					$result = $specialDoktypesObj->$methodName($this->rootElementRecord);
					if ($result !== FALSE) {
						$this->content .= $result;
						if ($GLOBALS['BE_USER']->isPSet($this->calcPerms, 'pages', 'edit')) {
							// Edit icon only if page can be modified by user
							$this->content .= '<br/><br/><strong>'.$this->link_edit('<img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/edit2.gif','').' title="'.htmlspecialchars($LANG->sL('LLL:EXT:lang/locallang_mod_web_list.xml:editPage')).'" alt="" style="border: none; vertical-align: middle" /> '.$LANG->sL('LLL:EXT:lang/locallang_mod_web_list.xml:editPage'),'pages',$this->id).'</strong>';
						}
						$render_editPageScreen = false; // Do not output editing code for special doctypes!
					}
				}
				
				// Display editPageScreen even if page is of type shortcut (4) - Lucas Jenﬂ
				if($this->rootElementRecord['doktype'] == 4) {
					$this->content .= '<br/><br/><br/>';
					$render_editPageScreen = true;
				}
			}

			if ($render_editPageScreen) {
					// Render "edit current page" (important to do before calling ->sideBarObj->render() - otherwise the translation tab is not rendered!
				$editCurrentPageHTML = $this->render_editPageScreen();

					// Hook for adding new sidebars or removing existing
				$sideBarHooks = $this->hooks_prepareObjectsArray('sideBarClass');
				foreach ($sideBarHooks as $hookObj)	{
					if (method_exists($hookObj, 'main_alterSideBar')) {
						$hookObj->main_alterSideBar($this->sideBarObj, $this);
					}
				}

					// Show the "edit current page" screen along with the sidebar
				$shortCut = ($BE_USER->mayMakeShortcut() ? '<br /><br />'.$this->doc->makeShortcutIcon('id,altRoot',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']) : '');
				if ($this->sideBarObj->position == 'left' && $this->modTSconfig['properties']['sideBarEnable']) {
					$this->content .= '
						<table cellspacing="0" cellpadding="0" style="width:100%; height:550px; padding:0; margin:0;">
							<tr>
								<td style="vertical-align:top;">'.$this->sideBarObj->render().'</td>
								<td style="vertical-align:top; padding-bottom:20px;" width="99%">'.$editCurrentPageHTML.$shortCut;'</td>
							</tr>
						</table>
					';
				} else {
					$sideBarTop = $this->modTSconfig['properties']['sideBarEnable']  && ($this->sideBarObj->position == 'toprows' || $this->sideBarObj->position == 'toptabs') ? $this->sideBarObj->render() : '';
					$this->content .= $sideBarTop.$editCurrentPageHTML.$shortCut;
				}
			}

		} else {	// No access or no current page uid:

			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->docType= 'xhtml_trans';
			$this->doc->backPath = $BACK_PATH;
			$this->content.=$this->doc->startPage($LANG->getLL('title'));

			$cmd = t3lib_div::_GP ('cmd');
			switch ($cmd) {

					// Create a new page
				case 'crPage' :
						// Output the page creation form
					$this->content .= $this->wizardsObj->renderWizard_createNewPage (t3lib_div::_GP ('positionPid'));
					break;

					// If no access or if ID == zero
				default:
					$this->content.=$this->doc->header($LANG->getLL('title'));
					$this->content.=$LANG->getLL('default_introduction');
			}
		}
		$this->content.=$this->doc->endPage();
	}
	

} // EOF: class go_tx_templavoila_module1 extends tx_templavoila_module1 



if (defined('TYPO3_MODE') &&	 $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/go_backend_layout/moduls/templavoila/class.ux_tx_templavoila_module1.php'])    {
					include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/go_backend_layout/moduls/templavoila/class.ux_tx_templavoila_module1.php']);
}

?>