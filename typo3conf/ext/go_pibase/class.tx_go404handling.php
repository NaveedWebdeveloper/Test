<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Caspar Stuebs <caspar@gosign.de>
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
 * Gosign 404 Handling.
 *
 * @author	Caspar Stuebs <caspar@gosign.de>
 */

class tx_go404handling {
	protected $siteID = 4;
	
	function handle404($param, $ref) {
		global $TSFE, $TT;
		
		$TSFE->id = $this->siteID;
		$TSFE->determineId();
		
		$TT->pull();
		
		$this->generatePage();
	}
	
	protected function generatePage() {
		global $TSFE, $TT;
		
		// *******************************************
		// Get compressed $TCA-Array();
		// After this, we should now have a valid $TCA, though minimized
		// *******************************************
		$TSFE->getCompressedTCarray();
		
		// ********************************
		// Starts the template
		// *******************************
		$TT->push('Start Template','');
			$TSFE->initTemplate();
		$TT->pull();
		
		// ********************************
		// Get from cache
		// *******************************
		$TT->push('Get Page from cache','');
			$TSFE->getFromCache();
		$TT->pull();
		
		// ******************************************************
		// Get config if not already gotten
		// After this, we should have a valid config-array ready
		// ******************************************************
		$TSFE->getConfigArray();
		
		// ********************************
		// Convert POST data to internal "renderCharset" if different from the metaCharset
		// *******************************
		$TSFE->convPOSTCharset();
		
		// *******************************************
		// Setting language and locale
		// *******************************************
		$TSFE->settingLanguage();
		$TSFE->settingLocale();
		
		// ********************************
		// Generate page
		// *******************************
		$TSFE->setUrlIdToken();
		
		$TT->push('Page generation','');
			if ($TSFE->doXHTML_cleaning()) {
				require_once(PATH_t3lib.'class.t3lib_parsehtml.php');
			}
			if ($TSFE->isGeneratePage()) {
				$TSFE->generatePage_preProcessing();
				$temp_theScript=$TSFE->generatePage_whichScript();

				if ($temp_theScript) {
					include($temp_theScript);
				} else {
					require_once(PATH_tslib.'class.tslib_pagegen.php');
					include(PATH_tslib.'pagegen.php');
				}
				$TSFE->generatePage_postProcessing();
			} elseif ($TSFE->isINTincScript()) {
				require_once(PATH_tslib.'class.tslib_pagegen.php');
				include(PATH_tslib.'pagegen.php');
			}
		$TT->pull();
		
		// ********************************
		// $TSFE->config['INTincScript']
		// *******************************
		if ($TSFE->isINTincScript())		{
			$TT->push('Non-cached objects','');
				$TSFE->INTincScript();
			$TT->pull();
		}
		
		// ***************
		// Output content
		// ***************
		if ($TSFE->isOutputting())	{
			$TT->push('Print Content','');
			$TSFE->processOutput();
			
			// ***************************************
			// Outputs content / Includes EXT scripts
			// ***************************************
			if ($TSFE->isEXTincScript())	{
				$TT->push('External PHP-script','');
						// Important global variables here are $EXTiS_*, they must not be overridden in include-scripts!!!
					$EXTiS_config = $TSFE->config['EXTincScript'];
					$EXTiS_splitC = explode('<!--EXT_SCRIPT.',$TSFE->content);	// Splits content with the key
					
						// Special feature: Include libraries
					reset($EXTiS_config);
					while(list(,$EXTiS_cPart)=each($EXTiS_config))	{
						if ($EXTiS_cPart['conf']['includeLibs'])	{
							$EXTiS_resourceList = t3lib_div::trimExplode(',',$EXTiS_cPart['conf']['includeLibs'],1);
							$TT->setTSlogMessage('Files for inclusion: "'.implode(', ',$EXTiS_resourceList).'"');
							reset($EXTiS_resourceList);
							while(list(,$EXTiS_theLib) = each($EXTiS_resourceList))	{
								$EXTiS_incFile = $TSFE->tmpl->getFileName($EXTiS_theLib);
								if ($EXTiS_incFile)	{
									require_once($EXTiS_incFile);
								} else {
									$TT->setTSlogMessage('Include file "'.$EXTiS_theLib.'" did not exist!',2);
								}
							}
						}
					}
					
					reset($EXTiS_splitC);
					while(list($EXTiS_c,$EXTiS_cPart)=each($EXTiS_splitC))	{
						if (substr($EXTiS_cPart,32,3)=='-->')	{	// If the split had a comment-end after 32 characters it's probably a split-string
							$EXTiS_key = 'EXT_SCRIPT.'.substr($EXTiS_cPart,0,32);
							if (is_array($EXTiS_config[$EXTiS_key]))	{
								$REC = $EXTiS_config[$EXTiS_key]['data'];
								$CONF = $EXTiS_config[$EXTiS_key]['conf'];
								$content = '';
								include($EXTiS_config[$EXTiS_key]['file']);
								echo $content;	// The script MAY return content in $content or the script may just output the result directly!
							}
							echo substr($EXTiS_cPart,35);
						} else {
							echo ($c?'<!--EXT_SCRIPT.':'').$EXTiS_cPart;
						}
					}
					
				$TT->pull();
			} else {
				echo $TSFE->content;
			}
			$TT->pull();
		}
		
		// ********************************
		// Store session data for fe_users
		// ********************************
		$TSFE->storeSessionData();
		
		// ***********
		// Statistics
		// ***********
		$TYPO3_MISC['microtime_end'] = microtime();
		$TSFE->setParseTime();
		if ($TSFE->isOutputting() && ($TSFE->TYPO3_CONF_VARS['FE']['debug'] || $TSFE->config['config']['debug']))	{
			echo '
		<!-- Parsetime: '.$TSFE->scriptParseTime.' ms-->';
		}
		$TSFE->statistics();
		
		// ******************
		// Hook for end-of-frontend
		// ******************
		$TSFE->hook_eofe();
		
		// ********************
		// Finish timetracking
		// ********************
		$TT->pull();
		
		// *************
		// Debugging Output
		// *************
		if(@is_callable(array($error,'debugOutput'))) {
			$error->debugOutput();
		}
		if (TYPO3_DLOG) {
			t3lib_div::devLog('END of FRONTEND session', 'cms', 0, array('_FLUSH' => TRUE));
		}
		
		// *************
		// Compressions
		// *************
		if ($TYPO3_CONF_VARS['FE']['compressionLevel'])	{
			new gzip_encode($TYPO3_CONF_VARS['FE']['compressionLevel'], false, $TYPO3_CONF_VARS['FE']['compressionDebugInfo']);
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/go_pibase/class.tx_go404handling.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/go_pibase/class.tx_go404handling.php']);
}

?>