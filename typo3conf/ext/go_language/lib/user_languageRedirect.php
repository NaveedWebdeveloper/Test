<?php
/**
 * Automatic Language Redirect according to the Broser Language setting
 *
 * @author	Elio Wahlen <vorname@gosign.de>
 * @package	TYPO3
 * @subpackage	tx_golanguage
 */

require_once(PATH_site.'typo3conf/ext/go_language/pi1/class.tx_golanguage_pi1.php');

function user_checkRedirect() {
	$pageID = $GLOBALS['TSFE']->id;
	
	$langIdFromBrowser = false;
	$tmpId = t3lib_div::_GET('L');
	$pageIdGet = t3lib_div::_GET('id');
	$langObj = t3lib_div::makeInstance('tx_golanguage_pi1');
	if ( count($langObj->sysLanguages) < 2 ) { // no multiple languages, no redirect
		return;
	}
	// Language has been set via L-Parameter OR page has been set via id=x
	if((is_numeric($tmpId) && isset($langObj->sysLanguages[$tmpId])) || !empty($pageIdGet) ) {
		$myLanguageUid = $tmpId;
	} // first visit: get Language from Browser
	else {
		$browserLanguage = t3lib_div::getIndpEnv('HTTP_ACCEPT_LANGUAGE');
		$tmpLanguageList = array();
		
		$myLanguageUid = 0;
		if ( !empty($browserLanguage)) { // if browser informed us about preferred languages
			$browserLanguage = explode(',',$browserLanguage);
			$lang_order = array();
			foreach($browserLanguage as &$brLang) {
				// split the preferred language infos
				$info_array = explode(';',$brLang);
				$brLang = $info_array[0];
				$q = substr( $info_array[1], strpos($info_array[1],'=')+1 );
				$lang_order[$brLang] = empty($q) ? '1' : $q;
			}
			arsort($lang_order,SORT_NUMERIC); // sort by preferrence
			foreach( $lang_order as $key => $brLang ) {
				// check if it is an active language
				foreach ($langObj->sysLanguages as $lang) {
					$key = substr( $key, 0, 2); // skip the second part of de-de / de-at etc...
					if ( (strcasecmp( $lang['lg_iso_2'], $key ) == 0 ) && // if lang matches
					   ( !isset($lang['disabled_in_menu']) || ($lang['disabled_in_menu'] == 0) ) ) { // do not take menu-disabled languages
						$tmpLanguageList[$lang['uid']] = $brLang;
					}
				}
			}
			if (count($tmpLanguageList)) { // if any language fitted, take the first from the list
				reset($tmpLanguageList);
				$myLanguageUid = key($tmpLanguageList);
			}
		}
		t3lib_div::_GETset($myLanguageUid, 'L');
		
		// get the typo3 language code. if it is not defined, assume the lg_iso_2 code
		$lang_code = strtolower(empty($langObj->sysLanguages[$myLanguageUid]['lg_typo3']) ? $langObj->sysLanguages[$myLanguageUid]['lg_iso_2'] : $langObj->sysLanguages[$myLanguageUid]['lg_typo3']);
		$langIdFromBrowser = TRUE;
	}
	
	if($langIdFromBrowser) {
		$cObj=t3lib_div::makeInstance('tslib_cObj');
		$suffix = empty($lang_code) ? '?L='.$myLanguageUid : $lang_code.'/';
		$redirect_link = t3lib_div::getIndpEnv('TYPO3_REQUEST_DIR').$suffix;
		header('Location: ' . $redirect_link);

		// realurl funktioniert ungecached nicht ;( daher kann das hier leider nicht benutzt werden:
		//header('Location: ' . $cObj->getTypoLink_URL($pageID, array('L' => $myLanguageUid)));
		exit();
	}
}

?>