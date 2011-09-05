<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2006 Rene Fritz (r.fritz@colorcube.de)
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
 * Class for updating the db
 *
 * @author	 Rene Fritz <r.fritz@colorcube.de>
 * @package TYPO3-DAM
 * @subpackage tx_dam
 */
class ext_update  {

	/**
	 * Main function, returning the HTML content of the module
	 *
	 * @return	string		HTML
	 */
	function main()	{

		$content = '';

		$content .= '<p>Following functions modify the database which might be needed due to changed default behaviour of content elements.</p>';

		$updateAction = t3lib_div::_GP('updateAction');
	
		if ($updateAction === 'do_imagecaption_position_hidden')	{
			$updateContent = $this->perform_update_tt_content_imagecaption_position('hidden');
		}
		if ($updateAction === 'do_imagecaption_position_default')	{
			$updateContent = $this->perform_update_tt_content_imagecaption_position('default');
		}

		if ($updateContent)	{
			$content .= '<div class="bgColor5" style="margin:2em 0 1em 0; padding: 0.5em; border:1px solid #aaa">'.$updateContent.'</div>';
		}
		
		
		//
		// captions
		//
			
		$onClickHidden = "document.location.href='".t3lib_div::linkThisScript(array('updateAction'=>'do_imagecaption_position_hidden'))."'; return false;";
		$onClickVisible = "document.location.href='".t3lib_div::linkThisScript(array('updateAction'=>'do_imagecaption_position_default'))."'; return false;";
			
		$content .= '<br /><h3>Image caption display</h3>
				<p>When css_styled_content is used for rendering, this extension can change the rendering so captions can be fetched from DAM for the content elements Image and Text w/image (see extension options).</p>
				<p>Captions might be visible now (coming from DAM) where no captions were needed. With the following functions...</p>
				<ul>
				  <li>all unused captions can be set hidden</li>
				  <li>all hidden captions can be set visible again</li>
				</ul>

			<input onclick="'.htmlspecialchars($onClickHidden).'" type="submit" value="Set unused captions hidden"> ' .
					'<input onclick="'.htmlspecialchars($onClickVisible).'" type="submit" value="unhide captions"></form>
		';
			
		
		return $content;
	}

	/**
	 * Checks how many rows are found and returns true if there are any
	 *
	 * @return	boolean
	 */
	function access()	{
		
		$this->checkMMforeign();

		return true;
	}


	/**
	 * Do the DB update for tt_content.imagecaption_position
	 * 
	 * @return	string		HTML
	 */
	function perform_update_tt_content_imagecaption_position($position)	{

		$content = '';
		$values = array();
		
		if ($position==='hidden') {
			$values['imagecaption_position'] = 'hidden';
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tt_content', 'LENGTH(imagecaption)=0', $values);
			$content .= '<p>Updated <em>Image</em> and <em>Text w/image</em> content elements. Set caption to be hidden where captions are unused.</p>';
		} else {
			$values['imagecaption_position'] = '';
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tt_content', 'imagecaption_position='.$GLOBALS['TYPO3_DB']->fullQuoteStr('hidden', 'tt_content'), $values);
			$content .= '<p>Updated <em>Image</em> and <em>Text w/image</em> content elements. Set hidden captions to be visible.</p>';
		}
		return $content;
	}
	

	/**
	 * Checks if mmforeign or T3 V 4.1 is installed and print a waring in EM when needed
	 *
	 * @return	void
	 */
	function checkMMforeign()	{
		
		$mmforeign = t3lib_extMgm::isLoaded('mmforeign');
		$isFourOne = t3lib_div::int_from_ver(TYPO3_branch)>=t3lib_div::int_from_ver('4.1');

		if (!$mmforeign AND !$isFourOne) {
			$GLOBALS['SOBE']->content.=$GLOBALS['SOBE']->doc->section('WARNING: Extension \'mmforeign\' needs to be installed!','',0,1,3);
		} elseif ($mmforeign AND $isFourOne) {
			$GLOBALS['SOBE']->content.=$GLOBALS['SOBE']->doc->section('NOTE: Extension \'mmforeign\' may not be needed with TYPO3 V4.1!','',0,1,1);
		}
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam_ttcontent/class.ext_update.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam_ttcontent/class.ext_update.php']);
}


?>