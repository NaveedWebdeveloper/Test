<?php
/**
 * This hook is registered in ext_localconf.php and deletes all files which have been 
 * created in typo3temp by this extension before.
 * 
 * @author grundkoetter
 *
 */
class user_clearcache_hook {
	
	/**
	 * Is called in t3lib_tcemain every time the be user clears the cache.
	 * @param array $params
	 * @param t3lib_tcemain $tceMain
	 */
	public function clearCache($params, $tceMain) {
		$path = PATH_site . 'typo3temp/';
		$handle = opendir($path);
		while($fileName = readdir($handle)) {
			if(	preg_match('%speedup_js_[a-f0-9]{10}\.js%', $fileName) ||
				preg_match('%speedup_(all|print|handheld|screen)_[a-f0-9]{10}\.css%', $fileName)) {
					unlink($path . $fileName);
			}
		}
		closedir($handle);
	}
	
}
?>