<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2005 Kasper Skaarhoj (kasperYYYY@typo3.com)
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
 * Include file extending db_list.inc for use with the web_layout module
 *
 * $Id: class.tx_cms_layout.php 1868 2006-12-12 11:24:10Z ingmars $
 * Revised for TYPO3 3.6 November/2003 by Kasper Skaarhoj
 * XHTML compliant
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */




/**
 * Child class for the Web > Page module
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @package TYPO3
 * @subpackage core
 */
class ux_tx_cms_layout extends tx_cms_layout {


	/**
	 * Draws the preview content for a content element
	 *
	 * @param	string		Content element
	 * @param	boolean		Set if the RTE link can be created.
	 * @return	string		HTML
	 */
	function tt_content_drawItem($row, $isRTE=FALSE)	{
		global $TCA;

		$out='';
		$outHeader='';

			// Make header:
		if ($row['header'] && $row['header_layout']!=100)	{
			$infoArr = Array();
			$this->getProcessedValue('tt_content','header_position,header_layout,header_link',$row,$infoArr);

			$outHeader=  ($row['date'] ? htmlspecialchars($this->itemLabels['date'].' '.t3lib_BEfunc::date($row['date'])).'<br />':'').
					$this->infoGif($infoArr).
					'<b>'.$this->linkEditContent($this->renderText($row['header']),$row).'</b><br />';
		}

			// Make content:
		$infoArr=Array();
		switch($row['CType'])	{
			case 'header':
				if ($row['subheader'])	{
					$this->getProcessedValue('tt_content','layout',$row,$infoArr);
					$out.=	$this->infoGif($infoArr).
							$this->linkEditContent($this->renderText($row['subheader']),$row).'<br />';
				}
			break;
			case 'text':
			case 'textpic':
			case 'image':
				if ($row['CType']=='text' || $row['CType']=='textpic')	{
					if ($row['bodytext'])	{
						$this->getProcessedValue('tt_content','text_align,text_face,text_size,text_color,text_properties',$row,$infoArr);
						$out.= $this->infoGif($infoArr).
								$this->linkEditContent($this->renderText($row['bodytext']),$row).'<br />';
					}
				}
				if ($row['CType']=='textpic' || $row['CType']=='image')	{
					if ($row['image'])	{
						$infoArr=Array();
						$this->getProcessedValue('tt_content','imageorient,imagecols,image_noRows,imageborder,imageheight,image_link,image_zoom,image_compression,image_effects,image_frames',$row,$infoArr);
						$out.=	$this->infoGif($infoArr).
								$this->thumbCode($row,'tt_content','image').'<br />';

						if ($row['imagecaption'])	{
							$infoArr=Array();
							$this->getProcessedValue('tt_content','imagecaption_position',$row,$infoArr);
							$out.=	$this->infoGif($infoArr).
									$this->linkEditContent($this->renderText($row['imagecaption']),$row).'<br />';
						}
					}
###########
					if ($row['tx_damttcontent_files'])	{
						require_once(PATH_txdam.'lib/class.tx_dam_image.php');
						require_once(PATH_txdam.'lib/class.tx_dam_tcefunc.php');
						require_once(PATH_txdam.'lib/class.tx_dam_guifunc.php');
						$config = $TCA['tt_content']['columns']['tx_damttcontent_files']['config'];

						if ($GLOBALS['BE_USER']->workspace !== 0) {
							$workspaceRecord = t3lib_BEfunc::getWorkspaceVersionOfRecord(
								$GLOBALS['BE_USER']->workspace,
								'tt_content',
								$row['uid']
							);

							if ($workspaceRecord) {
								$row = $workspaceRecord;
							}
						}

						$filesArray = tx_dam_db::getReferencedFiles('tt_content', $row['uid'], $config['MM_match_fields'], $config['MM'], 'tx_dam.*');

						foreach($filesArray['rows'] as $rowDAM)	{
							$caption = tx_dam_guiFunc::meta_compileInfoData($rowDAM, '_caption:truncate:100', 'value-string');

							#$imgAttributes['title'] = tx_dam_guiFunc::meta_compileHoverText($rowDAM);
							#$thumb = tx_dam_image::previewImgTag($rowDAM, '', $imgAttributes);
							$thumb = tx_dam_guiFunc::thumbnail($rowDAM);
							$thumb = '<div style="float:left;width:56px; overflow:auto; margin: 2px 5px 2px 0; padding: 5px; background-color:#fff; border:solid 1px #ccc;">'.$thumb.'</div>';
							$thumb = '<div>'.$thumb.$caption.'</div><div style="clear:both"></div>';

							$out.= $thumb;
						}
					}
#############
				}
			break;
			case 'bullets':
				if ($row['bodytext'])	{
					$this->getProcessedValue('tt_content','layout,text_align,text_face,text_size,text_color,text_properties',$row,$infoArr);
					$out.=	$this->infoGif($infoArr).
							$this->linkEditContent($this->renderText($row['bodytext']),$row).'<br />';
				}
			break;
			case 'table':
				if ($row['bodytext'])	{
					$this->getProcessedValue('tt_content','table_bgColor,table_border,table_cellspacing,cols,layout,text_align,text_face,text_size,text_color,text_properties',$row,$infoArr);
					$out.=	$this->infoGif($infoArr).
							$this->linkEditContent($this->renderText($row['bodytext']),$row).'<br />';
				}
			break;
			case 'uploads':
				if ($row['media'])	{
					$this->getProcessedValue('tt_content','media,select_key,layout,filelink_size,table_bgColor,table_border,table_cellspacing',$row,$infoArr);
					$out.=	$this->infoGif($infoArr).
							$this->linkEditContent($this->renderText($row['bodytext']),$row).'<br />';
				}
			break;
			case 'multimedia':
				if ($row['multimedia'])	{
					$out.=	$this->renderText($row['multimedia']).'<br />';
					$out.=	$this->renderText($row['parameters']).'<br />';
				}
			break;
			case 'mailform':
				if ($row['bodytext'])	{
					$this->getProcessedValue('tt_content','pages,subheader',$row,$infoArr);
					$out.=	$this->infoGif($infoArr).
							$this->linkEditContent($this->renderText($row['bodytext']),$row).'<br />';
				}
			break;
			case 'splash':
				if ($row['bodytext'])	{
					$out.=	$this->linkEditContent($this->renderText($row['bodytext']),$row).'<br />';
				}
				if ($row['image'])	{
					$infoArr=Array();
					$this->getProcessedValue('tt_content','imagewidth',$row,$infoArr);
					$out.=	$this->infoGif($infoArr).
							$this->thumbCode($row,'tt_content','image').'<br />';
				}
			break;
			case 'menu':
				if ($row['pages'])	{
					$this->getProcessedValue('tt_content','menu_type',$row,$infoArr);
					$out.=	$this->infoGif($infoArr).
							$this->linkEditContent($row['pages'],$row).'<br />';
				}
			break;
			case 'shortcut':
				if ($row['records'])	{
					$this->getProcessedValue('tt_content','layout',$row,$infoArr);
					$out.=	$this->infoGif($infoArr).
							$this->linkEditContent($row['shortcut'],$row).'<br />';
				}
			break;
			case 'list':
				$this->getProcessedValue('tt_content','layout',$row,$infoArr);
				$out.=	$this->infoGif($infoArr).
						$GLOBALS['LANG']->sL(t3lib_BEfunc::getItemLabel('tt_content','list_type'),1).' '.
						$GLOBALS['LANG']->sL(t3lib_BEfunc::getLabelFromItemlist('tt_content','list_type',$row['list_type']),1).'<br />';

				$out.=	$GLOBALS['LANG']->sL(t3lib_BEfunc::getItemLabel('tt_content','select_key'),1).' '.$row['select_key'].'<br />';

				$infoArr=Array();
				$this->getProcessedValue('tt_content','recursive',$row,$infoArr);
				$out.=	$this->infoGif($infoArr).
						$GLOBALS['LANG']->sL(t3lib_BEfunc::getLabelFromItemlist('tt_content','pages',$row['pages']),1).'<br />';
			break;
			case 'script':
				$out.=	$GLOBALS['LANG']->sL(t3lib_BEfunc::getItemLabel('tt_content','select_key'),1).' '.$row['select_key'].'<br />';
				$out.=	'<br />'.$this->linkEditContent($this->renderText($row['bodytext']),$row).'<br />';
				$out.=	'<br />'.$this->linkEditContent($this->renderText($row['imagecaption']),$row).'<br />';
			break;
			default:
				if ($row['bodytext'])	{
					$out.=$this->linkEditContent($this->renderText($row['bodytext']),$row).'<br />';
				}
			break;
		}

			// Wrap span-tags:
		$out = '
			<span class="exampleContent">'.$out.'</span>';
			// Add header:
		$out = $outHeader.$out;
			// Add RTE button:
		if ($isRTE) {
			$out.= $this->linkRTEbutton($row);
		}

			// Return values:
		if ($this->isDisabled('tt_content',$row))	{
			return $GLOBALS['TBE_TEMPLATE']->dfw($out);
		} else {
			return $out;
		}
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam_ttcontent/class.ux_tx_cms_layout.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam_ttcontent/class.ux_tx_cms_layout.php']);
}

?>