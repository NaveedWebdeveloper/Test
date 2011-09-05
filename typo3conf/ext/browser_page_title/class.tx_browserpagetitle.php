<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2003 Your Name (your@email.com)
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
 * Plugin 'browser_page_title'.
 *
 * Replaces specified PAGES (or PAGES_LANGUAGE_OVERLAY) table field name between '{}', given in the Setup of TS template, by field value (eg {title}).
 * If field value or title is blank, title is replaced by a default title or by the page title.
 *
 * @author	Bertrand Mure <bertrand.mure@worldonline.fr>
 */

class tx_browserpagetitle	{
    var $cObj;
    var $title = '';
    var $defaultTitle = '';
    var $fieldName = '';
    var $fieldData = '';
    var $dynamicTitle = false;	// Boolean : true if title contains a field name, false if not
    var $debugMode = 0;		// Enable writing properties values to screen
    
	
	/* 
	* Main function of the class. Get TS config and return modified title
	*/
	function getTitle ($content, $conf)	{
		
		$this->title = $GLOBALS['TSFE']->tmpl->setup['plugin.']['browser_page_title.']['currentTitle'];
		$this->defaultTitle = $GLOBALS['TSFE']->tmpl->setup['plugin.']['browser_page_title.']['defaultTitle'];
		$this->getField();
		
		// If fieldData or title is blank we use default title
		if(($this->dynamicTitle && !$this->fieldData && $this->defaultTitle) || (!$this->title))	{
			$this->title = $this->defaultTitle;
			$this->getField();
		}
		
		// If fieldData or title is still blank we use page title (can't be blank)
		if(($this->dynamicTitle && !$this->fieldData) || (!$this->title))	{
			$this->title = '{title}';
			$this->getField();
		}
		
		if($this->debugMode)
			$this->debug();
		
		return  str_replace ('{'.$this->fieldName.'}', $this->fieldData, $this->title);
	}
	
	
	/* 
	* Get field's name and data
	*/
	function getField() {
		
		$begPos = strpos($this->title, '{');
		$endPos = strpos($this->title, '}');
		
		if($endPos > 0){
			$this->fieldName = substr($this->title, $begPos+1, $endPos-($begPos+1));
			$this->dynamicTitle = true;
		}
		else
			$this->dynamicTitle = false;
		
		if($this->fieldName)
			$this->fieldData = $GLOBALS['TSFE']->page["$this->fieldName"];
	}
	
	
	/* 
	* Debug function
	*/
	function debug(){
		echo 'title: '.$this->title.'<br>';
		echo 'defaultTitle: '.$this->defaultTitle.'<br>';
		echo 'fieldName: '.$this->fieldName.'<br>';
		echo 'fieldData: '.$this->fieldData.'<br>';
		echo 'dynamicTitle: '.($this->dynamicTitle ? 'TRUE' : 'FALSE').'<br>';
		echo '<br>';
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/browser_page_title/class.tx_browserpagetitle.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/browser_page_title/class.tx_browserpagetitle.php']);
}
?>