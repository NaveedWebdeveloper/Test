<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Michael Grundkötter <typo3@queo-flow.com>
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once t3lib_extMgm::extPath('queo_speedup', 'res/Minify_CSS_UriRewriter.php');

/**
 * Plugin 'Silent compressor' for the 'queo_speedup' extension.
 *
 * @author	Michael Grundkötter <typo3@queo-flow.com>
 * @package	TYPO3
 * @subpackage	tx_queospeedup
 */
class tx_queospeedup_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_queospeedup_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_queospeedup_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'queo_speedup';	// The extension key.
	var $pi_checkCHash = true;
	
	const NO_COMPRESSION = 'none';
	
	const CSS_MINIFY = 'minify';
	const CSS_YIU = 'yuicss';
	
	const JS_MIN = 'jsmin';
	const JS_MINPLUS = 'jsminplus';
	const JS_YUI = 'yuijs';
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();

		$GLOBALS['TSFE']->pSetup['includeCSS.'] = $this->getCompressedCSSFilesConfig($conf['csscomp']);
		$jsFile = $this->getCompressedJSFile($conf['jscomp']);
		if(!empty($jsFile)) {
			$GLOBALS['TSFE']->pSetup['includeJS.'] = array('compressed' => $jsFile);
		}
		
		return '<!-- compression of css and js done by queo_speedup -->';
	}
	
	/**
	 * Configures YUI compressor. 
	 * @param array $conf typoscript options
	 */
	private function configureYUI($conf) {
		if($conf['jarpath.']['isrelative'] && $conf['jarpath.']['isrelative'] == 1) {
			$conf['jarpath'] = $_SERVER['DOCUMENT_ROOT'] . $conf['jarpath'];
		}
		Minify_YUICompressor::$jarFile = $conf['jarpath'];
 		Minify_YUICompressor::$tempDir = $conf['tmpdir'];//sys_get_temp_dir();
 		Minify_YUICompressor::$javaExecutable = $conf['javapath'];
	}
	
	/**
	 * Returns compressed content (done with given compression type, see constants)
	 * @param string $content
	 * @param string $method
	 * @return string
	 */
	private function compress($content, $method) {
		switch($method) {
			
			case self::CSS_MINIFY:
				require_once t3lib_extMgm::extPath('queo_speedup', 'res/Minify_CSS_Compressor.php');
				return Minify_CSS_Compressor::process($content);
				break;
			
			case self::CSS_YIU:
				require_once t3lib_extMgm::extPath('queo_speedup', 'res/YUICompressor.php');
				$this->configureYUI($this->conf);
				return Minify_YUICompressor::minifyCSS($content, array('nomunge' => true, 'line-break' => 1000));
				break;

			case self::JS_YUI:
				require_once t3lib_extMgm::extPath('queo_speedup', 'res/YUICompressor.php');
				$this->configureYUI($this->conf);
				return Minify_YUICompressor::minifyJs($content, array('line-break' => 1000));
 				break;
				
			case self::JS_MIN:
				require_once PATH_typo3 . 'contrib/jsmin/jsmin.php';
				return JSMin::minify($content);
				break;
				
			case self::JS_MINPLUS:
				require_once t3lib_extMgm::extPath('queo_speedup', 'res/JSMinPlus.php');
				return JSMinPlus::minify($content);
				break;
				
			default:
			case self::NO_COMPRESSION:
				//unknown compression type
				return $content;
				break;
			
		}
	}
	
	/**
	 * Returns the file name and path of the compressed css file.
	 * @param string $compressionMethod
	 * @return array ts config
	 */
	private function getCompressedCSSFilesConfig($compressionMethod = self::CSS_MINIFY) {
		$newFiles = array();
		foreach($this->getExternalCSS() as $media => $cssContent) {
			if(empty($cssContent)) {
				//maybe the file is empty at all
				continue;
			}
			$path = 'typo3temp/speedup_' . $media . '_'.substr(md5($cssContent), 0, 10).'.css';
			if (!@is_file(PATH_site . $path)) {
				t3lib_div::writeFile(PATH_site. $path, $this->compress($cssContent, $compressionMethod));
			}
			$newFiles['file' . $media] = $path;
			$newFiles['file' . $media . '.'] = array('media' => $media);
		}
		return $newFiles;
	}
	
	/**
	 * Returns the file name and path of the compressed js file.
	 * @param string $compressionMethod
	 * @return string
	 */
	private function getCompressedJSFile($compressionMethod = self::JS_MIN) {
		$jsContent = $this->getExternalJS();
		if(empty($jsContent)) {
			//maybe the file is empty at all
			return '';
		}
		$path = 'typo3temp/speedup_js_'.substr(md5($jsContent), 0, 10).'.js';
		if (!@is_file(PATH_site . $path))	{
			t3lib_div::writeFile(PATH_site. $path, $this->compress($jsContent, $compressionMethod));
		}
		return $path;
	}
	
	/**
	 * Returns content of all included css files. Does also rewrite relative
	 * urls to absolute urls! Array contains different keys for various media types
	 * 
	 * @return array
	 */
	private function getExternalCSS() {
		$content = array();
		if (!is_array($GLOBALS['TSFE']->pSetup['includeCSS.'])) $GLOBALS['TSFE']->pSetup['includeCSS.'] = array();
		foreach ($GLOBALS['TSFE']->pSetup['includeCSS.'] as $key => $value) {
			if(is_array($value)) {
				continue;
			}
			if(!empty($GLOBALS['TSFE']->pSetup['includeCSS.'][$key . '.']['media'])) {
				$media = $GLOBALS['TSFE']->pSetup['includeCSS.'][$key . '.']['media'];
			} else {
				$media = 'all';
			}
			$fileName = $GLOBALS['TSFE']->tmpl->getFileName($value);
			//remove @charset stuff for webkit browsers
			$cssContent = preg_replace('%@charset.*;%Ui', '', file_get_contents($fileName))."\n";
			$content[$media] .= Minify_CSS_UriRewriter::rewrite($cssContent, dirname($fileName));
		}
		return $content;
	}
	
	/**
	 * Returns combined content of all included js files.
	 * @return string
	 */
	private function getExternalJS() {
		$content = '';
		if (!is_array($GLOBALS['TSFE']->pSetup['includeJS.'])) $GLOBALS['TSFE']->pSetup['includeJS.'] = array();
		foreach ($GLOBALS['TSFE']->pSetup['includeJS.'] as $key => $value) {
			$content .= file_get_contents($GLOBALS['TSFE']->tmpl->getFileName($value))."\n";
		}
		return $content;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/queo_speedup/pi1/class.tx_queospeedup_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/queo_speedup/pi1/class.tx_queospeedup_pi1.php']);
}

?>