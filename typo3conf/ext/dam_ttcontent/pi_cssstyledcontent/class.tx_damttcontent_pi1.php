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
 * Plugin 'Content rendering' for the 'css_styled_content' extension.
 *
 * $Id: class.tx_cssstyledcontent_pi1.php 1618 2006-07-10 17:24:44Z baschny $
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once(t3lib_extMgm::extPath('css_styled_content').'pi1/class.tx_cssstyledcontent_pi1.php');



/**
 * Plugin class - instantiated from TypoScript.
 * Rendering some content elements from tt_content table.
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @package TYPO3
 * @subpackage tx_cssstyledcontent
 */
class tx_damttcontent_pi1 extends tx_cssstyledcontent_pi1 {


	function addMetaToData ($meta) {
		foreach ($meta as $key => $value) {
			$this->pObj->cObj->data['txdam_'.$key] = $value;
		}
	}

	function removeMetaFromData () {
		foreach ($this->pObj->cObj->data as $key => $value) {
			if (substr($key, 0, 6)=='txdam_') {
				unset($this->pObj->cObj->data[$key]);
			}
		}
	}




	/**
	 * Rendering the IMGTEXT content element, called from TypoScript (tt_content.textpic.20)
	 *
	 * @param	string		Content input. Not used, ignore.
	 * @param	array		TypoScript configuration. See TSRef "IMGTEXT". This function aims to be compatible.
	 * @return	string		HTML output.
	 * @access private
	 * @coauthor	Ernesto Baschny <ernst@cron-it.de>
	 */
	 function render_textpic($content, $conf)	{
		global $TYPO3_CONF_VARS;

			// Look for hook before running default code for function
		if (method_exists($this, 'hookRequest') && $hookObj = &$this->hookRequest('render_textpic'))	{
			return $hookObj->render_textpic($content,$conf);
		}

		$renderMethod = $this->pObj->cObj->stdWrap($conf['renderMethod'], $conf['renderMethod.']);

			// Render using the default IMGTEXT code (table-based)
		if (!$renderMethod || $renderMethod == 'table')	{
			return $this->pObj->cObj->IMGTEXT($conf);
		}

			// Specific configuration for the chosen rendering method
		if (is_array($conf['rendering.'][$renderMethod . '.']))	{
			$conf = $this->pObj->cObj->joinTSarrays($conf, $conf['rendering.'][$renderMethod . '.']);
		}

			// Image or Text with Image?
		if (is_array($conf['text.']))	{
			$content = $this->pObj->cObj->stdWrap($this->pObj->cObj->cObjGet($conf['text.'], 'text.'), $conf['text.']);
		}

		$imgList = trim($this->pObj->cObj->stdWrap($conf['imgList'], $conf['imgList.']));

		if (!$imgList)	{
				// No images, that's easy
			if (is_array($conf['stdWrap.']))	{
				return $this->pObj->cObj->stdWrap($content, $conf['stdWrap.']);
			}
			return $content;
		}

		$imgs = t3lib_div::trimExplode(',', $imgList);
		$imgStart = intval($this->pObj->cObj->stdWrap($conf['imgStart'], $conf['imgStart.']));
		$imgCount = count($imgs) - $imgStart;
		$imgMax = intval($this->pObj->cObj->stdWrap($conf['imgMax'], $conf['imgMax.']));
		if ($imgMax)	{
			$imgCount = t3lib_div::intInRange($imgCount, 0, $conf['imgMax']);	// reduce the number of images.
		}

		$imgPath = $this->pObj->cObj->stdWrap($conf['imgPath'], $conf['imgPath.']);


		if ($this->pObj->cObj->data['imagecaption_position']=='hidden') {
			$hideCaption = true;
		}

			// Global caption
		$caption = '';
		if (!$hideCaption && !$conf['captionEach'] && !$conf['captionSplit'] && !$conf['imageTextSplit'] && is_array($conf['caption.']))	{
			$caption = $this->pObj->cObj->stdWrap($this->pObj->cObj->cObjGet($conf['caption.'], 'caption.'), $conf['caption.']);
		}

			// Positioning
		$position = $this->pObj->cObj->stdWrap($conf['textPos'], $conf['textPos.']);

		$imagePosition = $position&7;	// 0,1,2 = center,right,left
		$contentPosition = $position&24;	// 0,8,16,24 (above,below,intext,intext-wrap)
		$align = $this->pObj->cObj->align[$imagePosition];
		$textMargin = intval($this->pObj->cObj->stdWrap($conf['textMargin'],$conf['textMargin.']));
		if (!$conf['textMargin_outOfText'] && $contentPosition < 16)	{
			$textMargin = 0;
		}

		$colspacing = intval($this->pObj->cObj->stdWrap($conf['colSpace'], $conf['colSpace.']));
		$rowspacing = intval($this->pObj->cObj->stdWrap($conf['rowSpace'], $conf['rowSpace.']));

		$border = intval($this->pObj->cObj->stdWrap($conf['border'], $conf['border.'])) ? 1:0;
		$borderColor = $this->pObj->cObj->stdWrap($conf['borderCol'], $conf['borderCol.']);
		$borderThickness = intval($this->pObj->cObj->stdWrap($conf['borderThick'], $conf['borderThick.']));

		$borderColor = $borderColor?$borderColor:'black';
		$borderThickness = $borderThickness?$borderThickness:1;
		$borderSpace = (($conf['borderSpace']&&$border) ? intval($conf['borderSpace']) : 0);

			// Generate cols
		$cols = intval($this->pObj->cObj->stdWrap($conf['cols'],$conf['cols.']));
		$colCount = ($cols > 1) ? $cols : 1;
		if ($colCount > $imgCount)	{$colCount = $imgCount;}
		$rowCount = ceil($imgCount / $colCount);

			// Generate rows
		$rows = intval($this->pObj->cObj->stdWrap($conf['rows'],$conf['rows.']));
		if ($rows>1)	{
			$rowCount = $rows;
			if ($rowCount > $imgCount)	{$rowCount = $imgCount;}
			$colCount = ($rowCount>1) ? ceil($imgCount / $rowCount) : $imgCount;
		}

			// Max Width
		$maxW = intval($this->pObj->cObj->stdWrap($conf['maxW'], $conf['maxW.']));

		if ($contentPosition>=16)	{	// in Text
			$maxWInText = intval($this->pObj->cObj->stdWrap($conf['maxWInText'],$conf['maxWInText.']));
			if (!$maxWInText)	{
					// If maxWInText is not set, it's calculated to the 50% of the max
				$maxW = round($maxW/100*50);
			} else {
				$maxW = $maxWInText;
			}
		}

			// All columns have the same width:
		$defaultColumnWidth = ceil(($maxW-$colspacing*($colCount-1)-$colCount*$border*($borderThickness+$borderSpace)*2)/$colCount);

			// Specify the maximum width for each column
		$columnWidths = array();
		$colRelations = trim($this->pObj->cObj->stdWrap($conf['colRelations'],$conf['colRelations.']));
		if (!$colRelations)	{
				// Default 1:1-proportion, all columns same width
			for ($a=0;$a<$colCount;$a++)	{
				$columnWidths[$a] = $defaultColumnWidth;
			}
		} else {
				// We need another proportion
			$rel_parts = explode(':',$colRelations);
			$rel_total = 0;
			for ($a=0;$a<$colCount;$a++)	{
				$rel_parts[$a] = intval($rel_parts[$a]);
				$rel_total+= $rel_parts[$a];
			}
			if ($rel_total)	{
				for ($a=0;$a<$colCount;$a++)	{
					$columnWidths[$a] = round(($defaultColumnWidth*$colCount)/$rel_total*$rel_parts[$a]);
				}
				if (min($columnWidths)<=0 || max($rel_parts)/min($rel_parts)>10)	{
					// The difference in size between the largest and smalles must be within a factor of ten.
					for ($a=0;$a<$colCount;$a++)	{
						$columnWidths[$a] = $defaultColumnWidth;
					}
				}
			}
		}
		$image_compression = intval($this->pObj->cObj->stdWrap($conf['image_compression'],$conf['image_compression.']));
		$image_effects = intval($this->pObj->cObj->stdWrap($conf['image_effects'],$conf['image_effects.']));
		$image_frames = intval($this->pObj->cObj->stdWrap($conf['image_frames.']['key'],$conf['image_frames.']['key.']));

			// EqualHeight
		$equalHeight = intval($this->pObj->cObj->stdWrap($conf['equalH'],$conf['equalH.']));
		if ($equalHeight)	{
				// Initiate gifbuilder object in order to get dimensions AND calculate the imageWidth's
			$gifCreator = t3lib_div::makeInstance('tslib_gifbuilder');
			$gifCreator->init();
			$relations_cols = Array();
			for ($a=0; $a<$imgCount; $a++)	{
				$imgKey = $a+$imgStart;
				$imgInfo = $gifCreator->getImageDimensions($imgPath.$imgs[$imgKey]);
				$rel = $imgInfo[1] / $equalHeight;	// relationship between the original height and the wished height
				if ($rel)	{	// if relations is zero, then the addition of this value is omitted as the image is not expected to display because of some error.
					$relations_cols[floor($a/$colCount)] += $imgInfo[0]/$rel;	// counts the total width of the row with the new height taken into consideration.
				}
			}
		}

			// Fetches pictures
		$splitArr = array();
		$splitArr['imgObjNum'] = $conf['imgObjNum'];
		$splitArr = $GLOBALS['TSFE']->tmpl->splitConfArray($splitArr, $imgCount);

		$imageRowsFinalWidths = Array();	// contains the width of every image row
		$imgsTag = array();
		$imgsExtraData = array();
		$origImages = array();

		for ($a=0; $a<$imgCount; $a++)	{
			$imgKey = $a+$imgStart;
			$totalImagePath = $imgPath.$imgs[$imgKey];

			$GLOBALS['TSFE']->register['IMAGE_NUM'] = $a;
			$GLOBALS['TSFE']->register['IMAGE_NUM_CURRENT'] = $a;
			$GLOBALS['TSFE']->register['ORIG_FILENAME'] = $totalImagePath;

			$this->pObj->cObj->data[$this->pObj->cObj->currentValKey] = $totalImagePath;


				// fetch DAM data and provide it as field data prefixed with txdam_
			$media = tx_dam::media_getForFile($totalImagePath, '*');
			if ($media->isAvailable) {
				$this->addMetaToData ($media->getMetaArray());
				$imgsExtraData[$imgKey] = $media->getMetaArray();
			} else {
				$this->removeMetaFromData ();
				$imgsExtraData[$imgKey] = array();
			}
			unset($media);

			$imgObjNum = intval($splitArr[$a]['imgObjNum']);
			$imgConf = $conf[$imgObjNum.'.'];

			if ($equalHeight)	{
				$scale = 1;
				$totalMaxW = $defaultColumnWidth*$colCount;
				$rowTotalMaxW = $relations_cols[floor($a/$colCount)];
				if ($rowTotalMaxW > $totalMaxW)	{
					$scale = $rowTotalMaxW / $totalMaxW;
				}

					// transfer info to the imageObject. Please note, that
				$imgConf['file.']['height'] = round($equalHeight/$scale);

					// other stuff will be calculated accordingly:
				unset($imgConf['file.']['width']);
				unset($imgConf['file.']['maxW']);
				unset($imgConf['file.']['maxH']);
				unset($imgConf['file.']['minW']);
				unset($imgConf['file.']['minH']);
				unset($imgConf['file.']['width.']);
				unset($imgConf['file.']['maxW.']);
				unset($imgConf['file.']['maxH.']);
				unset($imgConf['file.']['minW.']);
				unset($imgConf['file.']['minH.']);
			} else {
				$imgConf['file.']['maxW'] = $columnWidths[($a%$colCount)];
			}

			$titleInLink = $this->pObj->cObj->stdWrap($imgConf['titleInLink'], $imgConf['titleInLink.']);
			$titleInLinkAndImg = $this->pObj->cObj->stdWrap($imgConf['titleInLinkAndImg'], $imgConf['titleInLinkAndImg.']);
			$oldATagParms = $GLOBALS['TSFE']->ATagParams;
			if ($titleInLink)	{
					// Title in A-tag instead of IMG-tag
				$titleText = trim($this->pObj->cObj->stdWrap($imgConf['titleText'], $imgConf['titleText.']));
				if ($titleText)	{
						// This will be used by the IMAGE call later:
					$GLOBALS['TSFE']->ATagParams .= ' title="'. $titleText .'"';
				}
			}

			if ($imgConf || $imgConf['file'])	{
				if ($this->pObj->cObj->image_effects[$image_effects])	{
					$imgConf['file.']['params'] .= ' '.$this->pObj->cObj->image_effects[$image_effects];
				}
				if ($image_frames)	{
					if (is_array($conf['image_frames.'][$image_frames.'.']))	{
						$imgConf['file.']['m.'] = $conf['image_frames.'][$image_frames.'.'];
					}
				}
				if ($image_compression && $imgConf['file'] != 'GIFBUILDER')	{
					if ($image_compression == 1)	{
						$tempImport = $imgConf['file.']['import'];
						$tempImport_dot = $imgConf['file.']['import.'];
						unset($imgConf['file.']);
						$imgConf['file.']['import'] = $tempImport;
						$imgConf['file.']['import.'] = $tempImport_dot;
					} elseif (isset($this->pObj->cObj->image_compression[$image_compression])) {
						$imgConf['file.']['params'] .= ' '.$this->pObj->cObj->image_compression[$image_compression]['params'];
						$imgConf['file.']['ext'] = $this->pObj->cObj->image_compression[$image_compression]['ext'];
						unset($imgConf['file.']['ext.']);
					}
				}
				if ($titleInLink && ! $titleInLinkAndImg)	{
						// Check if the image will be linked
					$link = $this->pObj->cObj->imageLinkWrap('', $totalImagePath, $imgConf['imageLinkWrap.']);
					if ($link)	{
							// Title in A-tag only (set above: ATagParams), not in IMG-tag
						unset($imgConf['titleText']);
						unset($imgConf['titleText.']);
						$imgConf['emptyTitleHandling'] = 'removeAttr';
					}
				}
				$imgsTag[$imgKey] = $this->pObj->cObj->IMAGE($imgConf);
			} else {
				$imgsTag[$imgKey] = $this->pObj->cObj->IMAGE(Array('file' => $totalImagePath)); 	// currentValKey !!!
			}
				// Restore our ATagParams
			$GLOBALS['TSFE']->ATagParams = $oldATagParms;
				// Store the original filepath
			$origImages[$imgKey] = $GLOBALS['TSFE']->lastImageInfo;

			$imageRowsFinalWidths[floor($a/$colCount)] += $GLOBALS['TSFE']->lastImageInfo[0];
		}
			// How much space will the image-block occupy?
		$imageBlockWidth = max($imageRowsFinalWidths)+ $colspacing*($colCount-1) + $colCount*$border*($borderSpace+$borderThickness)*2;
		$GLOBALS['TSFE']->register['rowwidth'] = $imageBlockWidth;
		$GLOBALS['TSFE']->register['rowWidthPlusTextMargin'] = $imageBlockWidth + $textMargin;

			// noRows is in fact just one ROW, with the amount of columns specified, where the images are placed in.
			// noCols is just one COLUMN, each images placed side by side on each row
		$noRows = $this->pObj->cObj->stdWrap($conf['noRows'],$conf['noRows.']);
		$noCols = $this->pObj->cObj->stdWrap($conf['noCols'],$conf['noCols.']);
		if ($noRows) {$noCols=0;}	// noRows overrides noCols. They cannot exist at the same time.

		$rowCount_temp = 1;
		$colCount_temp = $colCount;
		if ($noRows)	{
			$rowCount_temp = $rowCount;
			$rowCount = 1;
		}
		if ($noCols)	{
			$colCount = 1;
			$columnWidths = array();
		}

			// Edit icons:
		$editIconsHTML = $conf['editIcons']&&$GLOBALS['TSFE']->beUserLogin ? $this->pObj->cObj->editIcons('',$conf['editIcons'],$conf['editIcons.']) : '';

			// If noRows, we need multiple imagecolumn wraps
		$imageWrapCols = 1;
		if ($noRows)	{ $imageWrapCols = $colCount; }

			// User wants to separate the rows, but only do that if we do have rows
		$separateRows = $this->pObj->cObj->stdWrap($conf['separateRows'], $conf['separateRows.']);
		if ($noRows)	{ $separateRows = 0; }
		if ($rowCount == 1)	{ $separateRows = 0; }

			// Apply optionSplit to the list of classes that we want to add to each image
		$addClassesImage = $conf['addClassesImage'];
		if ($conf['addClassesImage.'])	{
			$addClassesImage = $this->pObj->cObj->stdWrap($conf['addClassesImage'], $conf['addClassesImage.']);
		}
		$addClassesImageConf = $GLOBALS['TSFE']->tmpl->splitConfArray(array('addClassesImage' => $addClassesImage), $colCount);

			// Render the images
		$images = '';
		for ($c = 0; $c < $imageWrapCols; $c++)	{
			$tmpColspacing = $colspacing;
			if (($c==$imageWrapCols-1 && $imagePosition==2) || ($c==0 && ($imagePosition==1||$imagePosition==0))) {
					// Do not add spacing after column if we are first column (left) or last column (center/right)
				$tmpColspacing = 0;
			}

			$thisImages = '';
			$allRows = '';
			$maxImageSpace = 0;
			for ($i = $c; $i<count($imgsTag); $i=$i+$imageWrapCols)	{
				$colPos = $i%$colCount;
				if ($separateRows && $colPos == 0) {
					$thisRow = '';
				}

				$this->addMetaToData($imgsExtraData[$i]);


					// Render one image
				$imageSpace = $origImages[$i][0] + $border*($borderSpace+$borderThickness)*2;
				$GLOBALS['TSFE']->register['IMAGE_NUM'] = $i;
				$GLOBALS['TSFE']->register['IMAGE_NUM_CURRENT'] = $i;
				$GLOBALS['TSFE']->register['ORIG_FILENAME'] = $origImages[$i]['origFile'];
				$GLOBALS['TSFE']->register['imagewidth'] = $origImages[$i][0];
				$GLOBALS['TSFE']->register['imagespace'] = $imageSpace;
				$GLOBALS['TSFE']->register['imageheight'] = $origImages[$i][1];
				if ($imageSpace > $maxImageSpace)	{
					$maxImageSpace = $imageSpace;
				}
				$thisImage = '';
				$thisImage .= $this->pObj->cObj->stdWrap($imgsTag[$i], $conf['imgTagStdWrap.']);
				if (!$hideCaption && ($conf['captionEach'] || $conf['captionSplit'] || $conf['imageTextSplit']))	{
					$thisCaption = $this->pObj->cObj->stdWrap($this->pObj->cObj->cObjGet($conf['caption.'], 'caption.'), $conf['caption.']);
					$thisImage .= $thisCaption;
				}
				if ($editIconsHTML)	{
					$thisImage .= $this->pObj->cObj->stdWrap($editIconsHTML, $conf['editIconsStdWrap.']);
				}
				if ($conf['netprintApplicationLink'])	{
					$thisImage .= $this->pObj->cObj->netprintApplication_offsiteLinkWrap($thisImage, $origImages[$i], $conf['netprintApplicationLink.']);
				}
				$thisImage = $this->pObj->cObj->stdWrap($thisImage, $conf['oneImageStdWrap.']);
				$classes = '';
				if ($addClassesImageConf[$colPos]['addClassesImage'])	{
					$classes = ' ' . $addClassesImageConf[$colPos]['addClassesImage'];
				}
				$thisImage = str_replace('###CLASSES###', $classes, $thisImage);

				if ($separateRows)	{
					$thisRow .= $thisImage;
				} else {
					$allRows .= $thisImage;
				}
				$GLOBALS['TSFE']->register['columnwidth'] = $maxImageSpace + $tmpColspacing;
				if ($separateRows && ($colPos == ($colCount-1) || $i+1==count($imgsTag)))	{
					// Close this row at the end (colCount), or the last row at the final end
					$allRows .= $this->pObj->cObj->stdWrap($thisRow, $conf['imageRowStdWrap.']);
				}
			}
			if ($separateRows)	{
				$thisImages .= $allRows;
			} else {
				$thisImages .= $this->pObj->cObj->stdWrap($allRows, $conf['noRowsStdWrap.']);
			}
			if ($noRows)	{
					// Only needed to make columns, rather than rows:
				$images .= $this->pObj->cObj->stdWrap($thisImages, $conf['imageColumnStdWrap.']);
			} else {
				$images .= $thisImages;
			}
		}

			// Add the global caption, if not split
		if ($caption)	{
			$images .= $caption;
		}

			// CSS-classes
		$captionClass = '';
		$classCaptionAlign = array(
			'center' => 'csc-textpic-caption-c',
			'right' => 'csc-textpic-caption-r',
			'left' => 'csc-textpic-caption-l',
		);
		$captionAlign = $this->pObj->cObj->stdWrap($conf['captionAlign'], $conf['captionAlign.']);
		if ($captionAlign)	{
			$captionClass = $classCaptionAlign[$captionAlign];
		}
		$borderClass = '';
		if ($border)	{
			$borderClass = 'csc-textpic-border';
		}

			// Multiple classes with all properties, to be styled in CSS
		$class = '';
		$class .= ($borderClass? ' '.$borderClass:'');
		$class .= ($captionClass? ' '.$captionClass:'');
		$class .= ($equalHeight? ' csc-textpic-equalheight':'');
		$addClasses = $this->pObj->cObj->stdWrap($conf['addClasses'], $conf['addClasses.']);
		$class .= ($addClasses ? ' '.$addClasses:'');

			// Do we need a width in our wrap around images?
		$imgWrapWidth = '';
		if ($position == 0 || $position == 8)	{
				// For 'center' we always need a width: without one, the margin:auto trick won't work
			$imgWrapWidth = $imageBlockWidth;
		}
		if ($rowCount > 1)	{
				// For multiple rows we also need a width, so that the images will wrap
			$imgWrapWidth = $imageBlockWidth;
		}
		if ($caption)	{
				// If we have a global caption, we need the width so that the caption will wrap
			$imgWrapWidth = $imageBlockWidth;
		}

			// Wrap around the whole image block
		$GLOBALS['TSFE']->register['totalwidth'] = $imgWrapWidth;
		if ($imgWrapWidth)	{
			$images = $this->pObj->cObj->stdWrap($images, $conf['imageStdWrap.']);
		} else {
			$images = $this->pObj->cObj->stdWrap($images, $conf['imageStdWrapNoWidth.']);
		}

		$output = $this->pObj->cObj->cObjGetSingle($conf['layout'], $conf['layout.']);
		$output = str_replace('###TEXT###', $content, $output);
		$output = str_replace('###IMAGES###', $images, $output);
		$output = str_replace('###CLASSES###', $class, $output);

		if ($conf['stdWrap.'])	{
			$output = $this->pObj->cObj->stdWrap($output, $conf['stdWrap.']);
		}

		$this->removeMetaFromData ();

		return $output;
	}




	/**
	 * Returns an object reference to the hook object if any
	 *
	 * @param	string		Name of the function you want to call / hook key
	 * @return	object		Hook object, if any. Otherwise null.
	 */
	function &hookRequest($functionName)	{
		global $TYPO3_CONF_VARS;

			// Hook: menuConfig_preProcessModMenu
		if ($TYPO3_CONF_VARS['EXTCONF']['dam_ttcontent']['pi1_hooks'][$functionName]) {
			$hookObj = &t3lib_div::getUserObj($TYPO3_CONF_VARS['EXTCONF']['dam_ttcontent']['pi1_hooks'][$functionName]);
			if (method_exists ($hookObj, $functionName)) {
				$hookObj->pObj = &$this;
				return $hookObj;
			}
		}
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam_ttcontent/pi_cssstyledcontent/class.tx_damttcontent_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam_ttcontent/pi_cssstyledcontent/class.tx_damttcontent_pi1.php']);
}
?>