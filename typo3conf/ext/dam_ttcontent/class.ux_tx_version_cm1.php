<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Ingo Renner (ingo@typo3.org)
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



class ux_tx_version_cm1 extends tx_version_cm1 {

	/**
	 * Create visual difference view of two records. Using t3lib_diff library
	 *
	 * @param	string		Table name
	 * @param	array		New version record (green)
	 * @param	array		Old version record (red)
	 * @return	array		Array with two keys (0/1) with HTML content / percentage integer (if -1, then it means N/A) indicating amount of change
	 */
	function createDiffView($table, $diff_1_record, $diff_2_record)	{
		global $TCA;

			// Initialize:
		$pctChange = 'N/A';

			// Check that records are arrays:
		if (is_array($diff_1_record) && is_array($diff_2_record))	{

				// Load full table description and initialize diff-object:
			t3lib_div::loadTCA($table);
			$t3lib_diff_Obj = t3lib_div::makeInstance('t3lib_diff');

				// Add header row:
			$tRows = array();
			$tRows[] = '
				<tr class="bgColor5 tableheader">
					<td>Fieldname:</td>
					<td width="98%" nowrap="nowrap">Colored diff-view:</td>
				</tr>
			';

				// Initialize variables to pick up string lengths in:
			$allStrLen = 0;
			$diffStrLen = 0;

				// Traversing the first record and process all fields which are editable:
			foreach($diff_1_record as $fN => $fV)	{
				if ($TCA[$table]['columns'][$fN] && $TCA[$table]['columns'][$fN]['config']['type']!='passthrough' && !t3lib_div::inList('t3ver_label',$fN))	{

						// Check if it is files:
					$isFiles = FALSE;
					if (strcmp(trim($diff_1_record[$fN]),trim($diff_2_record[$fN])) &&
							$TCA[$table]['columns'][$fN]['config']['type']=='group' &&
							$TCA[$table]['columns'][$fN]['config']['internal_type']=='file')	{

							// Initialize:
						$uploadFolder = $TCA[$table]['columns'][$fN]['config']['uploadfolder'];
						$files1 = array_flip(t3lib_div::trimExplode(',', $diff_1_record[$fN],1));
						$files2 = array_flip(t3lib_div::trimExplode(',', $diff_2_record[$fN],1));

							// Traverse filenames and read their md5 sum:
						foreach($files1 as $filename => $tmp)	{
							$files1[$filename] = @is_file(PATH_site.$uploadFolder.'/'.$filename) ? md5(t3lib_div::getUrl(PATH_site.$uploadFolder.'/'.$filename)) : $filename;
						}
						foreach($files2 as $filename => $tmp)	{
							$files2[$filename] = @is_file(PATH_site.$uploadFolder.'/'.$filename) ? md5(t3lib_div::getUrl(PATH_site.$uploadFolder.'/'.$filename)) : $filename;
						}

							// Implode MD5 sums and set flag:
						$diff_1_record[$fN] = implode(' ',$files1);
						$diff_2_record[$fN] = implode(' ',$files2);
						$isFiles = TRUE;
					}

						// If there is a change of value:
					if (strcmp(trim($diff_1_record[$fN]),trim($diff_2_record[$fN])))	{


							// Get the best visual presentation of the value and present that:
						$val1 = t3lib_BEfunc::getProcessedValue($table,$fN,$diff_2_record[$fN],0,1);
						$val2 = t3lib_BEfunc::getProcessedValue($table,$fN,$diff_1_record[$fN],0,1);

							// Make diff result and record string lenghts:
						$diffres = $t3lib_diff_Obj->makeDiffDisplay($val1,$val2,$isFiles?'div':'span');
						$diffStrLen+= $t3lib_diff_Obj->differenceLgd;
						$allStrLen+= strlen($val1.$val2);

							// If the compared values were files, substituted MD5 hashes:
						if ($isFiles)	{
							$allFiles = array_merge($files1,$files2);
							foreach($allFiles as $filename => $token)	{
								if (strlen($token)==32 && strstr($diffres,$token))	{
									$filename =
										t3lib_BEfunc::thumbCode(array($fN=>$filename),$table,$fN,$this->doc->backPath).
										$filename;
									$diffres = str_replace($token,$filename,$diffres);
								}
							}
						}

############# new hook for post processing of DAM images
						if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/mod/user/ws/class.wslib_gui.php']['postProcessDiffView'])) {
							foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/mod/user/ws/class.wslib_gui.php']['postProcessDiffView'] as $classRef) {
								$hookObject = &t3lib_div::getUserObj($classRef);

								if (method_exists($hookObject, 'postProcessDiffView')) {
									$diffres = $hookObject->postProcessDiffView(
										$table,
										$fN,
										$diff_2_record, // old!
										$diff_1_record, // new!
										$diffres,
										$this
									);
								}
							}
						}
#############

							// Add table row with result:
						$tRows[] = '
							<tr class="bgColor4">
								<td>'.htmlspecialchars($GLOBALS['LANG']->sL(t3lib_BEfunc::getItemLabel($table,$fN))).'</td>
								<td width="98%">'.$diffres.'</td>
							</tr>
						';
					} else {
							// Add string lengths even if value matched - in this was the change percentage is not high if only a single field is changed:
						$allStrLen+=strlen($diff_1_record[$fN].$diff_2_record[$fN]);
					}
				}
			}

				// Calculate final change percentage:
			$pctChange = $allStrLen ? ceil($diffStrLen*100/$allStrLen) : -1;

				// Create visual representation of result:
			if (count($tRows)>1)	{
				$content.= '<table border="0" cellpadding="1" cellspacing="1" class="diffTable">'.implode('',$tRows).'</table>';
			} else {
				$content.= '<span class="nobr">'.$this->doc->icons(1).'Complete match on editable fields.</span>';
			}
		} else $content.= $this->doc->icons(3).'ERROR: Records could strangely not be found!';

			// Return value:
		return array($content,$pctChange);
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam_ttcontent/class.ux_tx_version_cm1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam_ttcontent/class.ux_tx_version_cm1.php']);
}

?>