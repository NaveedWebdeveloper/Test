<?php
/*
 * @author: Mansoor Ahmad
 * @description: fucking Hook
 *
 */
require_once(PATH_t3lib . 'interfaces/interface.t3lib_localrecordlistgettablehook.php');
class user_go_backend_layout_modify implements t3lib_localRecordListGetTableHook {

	public function getDBlistQuery($table, $pageId, &$additionalWhereClause, &$selectedFieldsList, &$parentObject) {
		global $GBL;
		
		if($table == 'tt_news' && $GBL['tt_news.']['uidList']) {
			$additionalWhereClause .= ' AND tt_news.uid IN(' . $GBL['tt_news.']['uidList'] . ')';
		}
	}
}
?>