<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2006 Rene Fritz (r.fritz@colorcube.de)
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
 * database functions for a table that is organized like a tree with parent_id (pid)
 *
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 */

class tx_damcatedit_db {

	/**
	 * Database table to get the tree data from.
	 */
	var $table;

	/**
	 * Defines the field of $table which is the parent id field (like pid for table pages).
	 */
	var $parentField;

	/**
	 * Default set of fields selected from the tree table.
	 * @see setFields()
	 */
	var $fieldList = 'uid';

	/**
	 * List of other fields which are ALLOWED to set
	 * @see setFields()
	 */
	var $defaultList = 'uid,pid,tstamp,sorting,deleted,perms_userid,perms_groupid,perms_user,perms_group,perms_everybody,crdate,cruser_id';

	var $sorting='';


	var $mm_table='';
	var $mm_sorting='';
	var $mm_prependTableName=FALSE;

	var $pidList='';
	var $pidListWhere='';


	var $where_default = '';


	var $resReturn = false;




	/**
	 * Initialize the object.
	 * The class will be setup for BE use. See setEnableFields().
	 *
	 * @param	string		Table name
	 * @param	string		Parent-id field name. If empty $TCA[$this->table]['ctrl']['treeParentField'] will be used.
	 * @return	void
	 */
	function init($table, $parentField='')	{
		global $TCA, $TSFE;

		$this->table = $table;
		t3lib_div::loadTCA($table);
		$this->parentField = $parentField ? $parentField : $TCA[$this->table]['ctrl']['treeParentField'];

		$this->setFields();
		$this->setSortFields();
		$this->setEnableFields((is_object($TSFE)?'FE':'BE'));

		# $this->where_del = $this->enableFields('delete');
		# $this->where_del_hid = $this->enableFields('delete,disabled');
		# $this->where_frontend = $this->enableFields('delete,disabled,starttime,endtime,fe_group');
		$this->where_default = $this->enableFields('delete');
	}

#TODO
	function initMM($table, $prependTableName=FALSE, $sortFields='sorting')	{
		$this->mm_table = $table;
		$this->mm_prependTableName = $prependTableName;
		$this->mm_sorting = ' ORDER BY '.$sortFields;
	}


	/**
	 * Sets the internal pid-list.
	 *
	 * @param	string		Commalist of ids
	 * @return	void
	 */
	function setPidList ($pidList)  {
		$this->pidList = $pidList;
		$this->pidListWhere = $pidList ? ' AND pid IN ('.$pidList.')' : '';
	}


	/**
	 * Sets the internal fieldList.
	 * The field list will be used for SELECT.
	 *
	 * @param	string		Commalist of fields
	 * @param	boolean		If set, the fieldnames will be set no matter what. Otherwise the field name must be found as key in $TCA['pages']['columns']
	 * @return	void
	 */
	function setFields($fields='',$noCheck=0)	{
		global $TCA;

		$fields = $fields ? $fields : 'uid,pid,'.$TCA[$this->table]['ctrl']['label'];
		$fieldArr = explode(',',$fields);

		$setFields=array();
		foreach($fieldArr as $field) {
			if ($noCheck || is_array($TCA[$this->table]['columns'][$field]) || t3lib_div::inList($this->defaultList,$field))	{
				$setFields[]=$field;
			}
		}
		$this->fieldList = implode(',',$setFields);
	}


	/**
	 * Sets the internal sorting fields.
	 * The field list will be used for SELECT.
	 *
	 * @param	string		Commalist of fields
	 * @return	void
	 */
	function setSortFields($sortFields='')	{
		global $TCA;

		if($sortFields) {
			$this->sorting = $sortFields;
		} else {
			$defaultSortBy = ($TCA[$this->table]['ctrl']['default_sortby']) ? $GLOBALS['TYPO3_DB']->stripOrderBy($TCA[$this->table]['ctrl']['default_sortby']) : '';
			$sortby = $TCA[$this->table]['ctrl']['sortby'] ? $TCA[$this->table]['ctrl']['sortby'] : '';
			$this->sorting = ($defaultSortBy) ? $defaultSortBy : $sortby;
		}
	}


	/**
	 * Sets the internal where clause for enable-fields..
	 * The field list will be used as enable-fields.
	 *
	 *
	 * @param	string		Commalist of fields. "FE" set the proper frontend fields, "BE" for backend.
	 * @return	void
	 * @see enableFields()
	 */
	function setEnableFields ($fields)  {
		if ($fields=='FE') {
			$this->where_default = $this->enableFields('delete,disabled,starttime,endtime,fe_group');
		} elseif ($fields=='BE') {
			$this->where_default = $this->enableFields('delete');
		} else {
			$this->where_default = $this->enableFields($fields);
		}
	}



	/*******************************************
	 *
	 * common record functions (using SQL queries)
	 *
	 *******************************************/

	/**
	 * Sets a flag which let all record functions return the query result not the records.
	 *
	 * @param	boolean		Commalist of ids
	 * @return	void
	 */
	function setResReturn ($resReturn=false)  {
		$this->resReturn = $resReturn;
	}




	/**
	 * Gets records with uid IN $uids
	 * You can set $field to a list of fields (default is '*')
	 * Additional WHERE clauses can be added by $where (fx. ' AND blabla=1')
	 *
	 * @param	integer		UIDs of records
	 * @param	string		Commalist of fields to select
	 * @param	string		Additional WHERE clause, eg. " AND blablabla=0"
	 * @param 	boolean 	Enable sorting
	 * @return	array		Returns the rows if found, otherwise empty array
	 */
	function getRecords ($uids, $fields='', $where='', $sorting=true)	{
		$fields = $fields?$fields:$this->fieldList;

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $this->table, $this->table.'.uid IN ('.$uids.')'.$where.$this->where_default.$this->pidListWhere, '', ($sorting?$this->sorting:''));
		if(!$res) echo $GLOBALS['TYPO3_DB']->sql_error();

		if($this->resReturn) return $res;

		$rows = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$rows[$row['uid']]=$row;
		}
		
		return $rows;
	}



	/*******************************************
	 *
	 * root-record functions (using SQL queries)
	 *
	 *******************************************/


	/**
	 * Returns an array with rows of root-records with parent_id=0
	 *
	 * @param	string		List of fields to select
	 * @param	string		Additional WHERE clause, eg. " AND blablabla=0"
	 * @param 	boolean 	Enable sorting
	 * @return	array		Returns the rows if found, otherwise empty array
	 */
	 function getRootRecords ($fields='',$where='',$sorting=true)	{
		$fields = $fields?$fields:$this->fieldList;

		return $this->getSubRecords ('0',$fields,$where,$sorting);
	}


	/**
	 * Returns a commalist of record ids of root records (parent_id=0)
	 *
	 * @param	string		Additional WHERE clause, eg. " AND blablabla=0"
	 * @return	string		Comma-list of record ids of root records
	 */
	function getRootRecordsIdList($where='')	{
		$rows = $this->getSubRecords (0,'uid',$where,false);
		return implode(',',array_keys($rows));
	}




	/*******************************************
	 *
	 * sub-record functions (using SQL queries)
	 *
	 *******************************************/


	/**
	 * Returns an array with rows for subrecords with parent_id=$uid
	 *
	 * @param	integer		UIDs of records
	 * @param	string		List of fields to select (default is '*')
	 * @param	string		Additional WHERE clause, eg. " AND blablabla=0"
	 * @param 	boolean 	Enable sorting
	 * @return	array		Returns the rows if found, otherwise empty array
	 */
	function getSubRecords ($uid, $fields='', $where='', $sorting=true)	{
		$fields = $fields?$fields:$this->fieldList;

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $this->table, $this->parentField.'='.intval($uid).$where.$this->where_default.$this->pidListWhere, '', ($sorting?$this->sorting:''));
		if(!$res) echo $GLOBALS['TYPO3_DB']->sql_error();

		if($this->resReturn) return $res;

		$rows = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$rows[$row['uid']]=$row;
		}
		return $rows;
	}


	/**
	 * Count subrecords with parent_id=$uid
	 * Additional WHERE clauses can be added by $where (fx. ' AND blabla=1')
	 *
	 * @param	integer		UIDs of records
	 * @param	string		Additional WHERE clause, eg. " AND blablabla=0"
	 * @return	integer		Count of subrecords
	 */
	function countSubRecords($uid,$where='')	{
		$row = $this->getSubRecords ($uid,'COUNT(*)',$where,false);
		reset($row);
		$row = current($row);
		return intval($row['COUNT(*)']);
	}




	/*******************************************
	 *
	 * tree functions (using $this->tree)
	 *
	 *******************************************/


	/**
	 * Build the tree.
	 *
	 * @param	integer		UID of the start record records. Use 0 if you want to get from root.
	 * @param	integer		Walk $depth levels deep into the tree.
	 * @param	integer		determines at which level in the tree to start collecting uid's. Zero means 'start right away', 1 = 'next level and out'
	 * @param	string		Commalist of fields to select
	 * @param	string		Additional WHERE clause, eg. " AND blablabla=0"
	 * @return	void
	 */
	function buildTree($uid, $depth, $beginLevel=0, $fields='', $where='')	{

		$fields = $fields?$fields:$this->fieldList;

		$depth=intval($depth);
		$beginLevel=intval($beginLevel);
		$uid=intval($uid);

		$this->tree=array();
		$pointer=&$this->tree;
		$this->treeLookup=array();

		$this->_walkTree($uid, $depth-1, $beginLevel-1, $fields, $where, $pointer);
	}

	function _walkTree($uid, $depth, $beginLevel, $fields, $where, &$pointer)	{

		if ($depth>0)	{
			$rows = $this->getSubRecords ($uid,$fields,$where);
			foreach ($rows as $rowUid => $row)	{
				if ($beginLevel<=0)	{
					$pointer[$uid]['--subLevel--'][$rowUid]=$row;
					$this->treeLookup[$rowUid]=&$pointer[$uid]['--subLevel--'][$rowUid];
					$this->treeLookup[$uid]['--subLevel--']=&$pointer[$uid]['--subLevel--'];
				}
				if ($depth>1)	{
					$this->_walkTree($rowUid, $depth-1, $beginLevel-1, $fields, $where, $pointer[$uid]['--subLevel--']);
				}
			}
		}
	}



#TODO

	/**
	 * Returns a commalist of record ids for a query (eg. 'WHERE parent_id IN (...)')
	 * $uid_list is a comma list of record ids
	 * $rdepth is an integer >=0 telling how deep to dig for uids under each entry in $uid_list
	 */
	function getSubRecordsIdList($uid_list,$depth=1,$beginLevel=0,$where=' ')	{
		$depth = t3lib_div::intInRange($depth,0);

		$uid_list_arr = array_unique(t3lib_div::trimExplode(',',$uid_list,1));
		$uid_list='';
		reset($uid_list_arr);
		while(list(,$val)=each($uid_list_arr))	{
			$val = t3lib_div::intInRange($val,0);
			if ($val)	$uid_list.=$val.','.$this->getTreeList($val,$depth,$beginLevel,$where);
		}
		return preg_replace('/,$/','',$uid_list);
	}

	/**
	 * Returns a commalist of record ids (including the ones from $uid_list) for a query (eg. 'WHERE parent_id IN (...)')
	 * $uid_list is a comma list of record ids
	 * $depth is an integer >=0 telling how deep to dig for uids under each entry in $uid_list
	 */
	function getRecordsIdList($uid_list,$depth=0,$beginLevel=0,$where=' ')	{

		$uid_list_prepend = $uid_list;
		$uid_list=$this->getSubRecordsIdList($uid_list,$depth,$beginLevel,$where);

		return str_replace(',,','',$uid_list_prepend.','.$uid_list);
	}

	function getTreeList($uid,$depth,$beginLevel=0,$where='')	{
		/* Generates a list of Page-uid's from $id. List does not include $id itself

		 Returns the list with a comma in the end (if any pages selected!)
		 $begin is an optional integer that determines at which level in the tree to start collecting uid's. Zero means 'start right away', 1 = 'next level and out'
		*/
		$depth=intval($depth);
		$beginLevel=intval($beginLevel);
		$id=intval($uid);
		$theList='';

		if ($uid && $depth>0)	{
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', $this->table, $this->parentField.'='.intval($uid).$where.$this->where_default, '', $this->sorting);
			if(!$res) echo $GLOBALS['TYPO3_DB']->sql_error();
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
				if ($beginLevel<=0)	{
					$theList.=$row['uid'].',';
				}
				if ($depth>1)	{
					$theList.=$this->getTreeList($row['uid'], $depth-1, $beginLevel-1, $where);
				}
			}
		}

		return $theList;
	}



	function getRootLine ($uid,$selFields='',$where=' ',$MP='')	{

		if ($selFields=='') {
#TODO
			$selFields = t3lib_div::uniqueList('pid,uid,title,nav_title,hidden,fe_group,'.$this->parentField);
		}
		$MPA=array();
		if ($MP)	{
			$MPA=explode(',',$MP);
			reset($MPA);
			while(list($MPAk)=each($MPA))	{
				$MPA[$MPAk]=explode('-',$MPA[$MPAk]);
			}
		}

		$loopCheck = 20;
		$theRowArray = Array();
		$output=Array();
		$uid = intval($uid);
		while ($uid!=0 && $loopCheck>0)	{
			$loopCheck--;
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($selFields, $this->table, 'uid='.intval($uid).$where.$this->where_default);
			if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
				if (count($MPA))	{
					$curMP=end($MPA);
					if (!strcmp($row['uid'],$curMP[0]))	{

						array_pop($MPA);
						$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($selFields, $this->table, 'uid='.intval($curMP[1]).$where.$this->where_default);
						$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
						$row['_MOUNTED_FROM']=$curMP[0];
						if (!is_array($row))	return array();	// error - no record...
					}
				}
				$uid = $row[$this->parentField];
				$theRowArray[]=$row;
			} else {
				$theRowArray='';
				break;
			}
		}


		if (is_array($theRowArray) && !count($MPA))	{
			reset($theRowArray);
			$c=count($theRowArray);
			while(list($key,$val)=each($theRowArray))	{
				$c--;
				$output[$c]=$val;
			}
		}
		ksort($output);

		return $output;
	}

/*
	function getPathFromRootline($rl,$len=20)	{
			// Calculates the page based on the rootLine (as input)
		if (is_array($rl))	{
			$c=count($rl);
			$path = '';
			for ($a=0;$a<$c;$a++)	{
				if ($rl[$a]['uid'])	{
					$path.='/'.t3lib_div::fixed_lgd(strip_tags($rl[$a]['title']),$len);
				}
			}
			return $path;
		}
	}
*/


	//------------------------- MM related -------------------------------------

	/**
	 * insert a relation from a data record to a tree-record
	 */
	function writeMM($dataRecordUid,$treeRecordUid)	{

			// delete all relations:
		$res = $GLOBALS['TYPO3_DB']->exec_DELETEquery($this->mm_table, 'uid_local='.(int)$dataRecordUid.' AND foreign_uid='.(int)$treeRecordUid.($this->mm_prependTableName?' AND tablenames='.$GLOBALS['TYPO3_DB']->fullQuoteStr($this->table,$this->mm_table):''));

		$sort=0; // what to set here???
		$data = array(
			'uid_local' => $dataRecordUid,
			'uid_foreign' => $treeRecordUid,
			'sorting' => $sort,
		);
		if ($this->mm_prependTableName)	{
			$data['tablenames'] = $this->table;
		}
		$GLOBALS['TYPO3_DB']->exec_INSERTquery($this->mm_table, $data);

// !!!!!! update the relation counter in the data table

	}




	/*******************************************
	 *
	 * misc functions
	 *
	 *******************************************/

	/**
	 * Returns a part of a WHERE clause which will filter out records with start/end times or hidden/fe_groups fields set to values that should de-select them according to the current time, preview settings or user login.
	 * Is using the $TCA arrays "ctrl" part where the key "enablefields" determines for each table which of these features applies to that table.
	 *
	 * @param	array		Commalist you can pass where itmes can be "disabled", "starttime", "endtime", "fe_group" (keys from "enablefields" in TCA) and if set they will make sure that part of the clause is not added. Thus disables the specific part of the clause. For previewing etc.
	 * @param	string		Table name found in the $TCA array(default $this->table)
	 * @see tslib_cObj::enableFields(), deleteClause()
	 */
	function enableFields($useFields='delete,disabled,starttime,endtime,fe_group',$table='')	{
		if (!is_array($useFields)) {
			$useFields = t3lib_div::trimExplode(',',$useFields,1);
		}
		$table=$table?$table:$this->table;
		$ctrl = $GLOBALS['TCA'][$table]['ctrl'];
		$query='';
		if (is_array($ctrl))	{
			if ($ctrl['delete'] && in_array('delete',$useFields))	{
				$query.=' AND '.$table.'.'.$ctrl['delete'].'=0';
			}
			if (is_array($ctrl['enablecolumns']))	{
				if ($ctrl['enablecolumns']['disabled'] && in_array('disabled',$useFields))	{
					$field = $table.'.'.$ctrl['enablecolumns']['disabled'];
					$query.=' AND '.$field.'=0';
				}
				if ($ctrl['enablecolumns']['starttime'] && in_array('starttime',$useFields))	{
					$field = $table.'.'.$ctrl['enablecolumns']['starttime'];
					$query.=' AND ('.$field.'<='.$GLOBALS['SIM_EXEC_TIME'].')';
				}
				if ($ctrl['enablecolumns']['endtime'] && in_array('endtime',$useFields))	{
					$field = $table.'.'.$ctrl['enablecolumns']['endtime'];
					$query.=' AND ('.$field.'=0 OR '.$field.'>'.$GLOBALS['SIM_EXEC_TIME'].')';
				}
				if ($ctrl['enablecolumns']['fe_group'] && in_array('fe_group',$useFields))	{
					$field = $table.'.'.$ctrl['enablecolumns']['fe_group'];
					$gr_list = $GLOBALS['TSFE']->gr_list;
					if (!strcmp($gr_list,''))	$gr_list=0;
					$query.=' AND '.$field.' IN ('.$gr_list.')';
				}
			}
		} else {die ('NO entry in the \$TCA-array for \''.$table.'\'');}

		return $query;
	}




//---------------------------------------------------------------------------------------------------

	/**
	 * Returns an array with rows for subrecords with parent_id=$uid
	 *
	 * @param	integer		UID of record
	 * @param	string		List of fields to select (default is '*')
	 * @param	string		Additional WHERE clause, eg. " AND blablabla=0"
	 * @param	string		$table: ...
	 * @param	string		$where: ...
	 * @return	array		Returns the rows if found, otherwise empty array
	 */
	function _getSubRecords ($uidList,$level=1,$fields='*',$table='tx_dam_cat',$where='')	{
		$rows = array();

		while ($level && $uidList)	{
			$level--;

			$newIdList = array();
			t3lib_div::loadTCA($table);
			$ctrl = $GLOBALS['TCA'][$table]['ctrl'];
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $table, $ctrl['treeParentField'].' IN ('.$uidList.') '.$where.' AND '.$table.'.'.$ctrl['delete'].'=0');
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
				$rows[$row['uid']] = $row;
				$newIdList[] = $row['uid'];
			}
			$uidList = implode(',', $newIdList);

		}


		return $rows;
	}


	/**
	 * Returns a commalist of sub record ids
	 *
	 * @param	integer		UIDs of record
	 * @param	string		Additional WHERE clause, eg. " AND blablabla=0"
	 * @param	string		$table: ...
	 * @param	string		$where: ...
	 * @return	string		Comma-list of record ids
	 */
	function _getSubRecordsIdList($uidList,$level=1,$table='tx_dam_cat',$where='')	{
		$rows = tx_dam_db::getSubRecords ($uidList,$level,'uid',$table,$where);
		return implode(',',array_keys($rows));
	}



	/**
	 * Returns true if the tablename $checkTable is allowed to be created on the page with record $pid_row
	 *
	 * @param	array		Record for parent page.
	 * @param	string		Table name to check
	 * @return	boolean		Returns true if the tablename $checkTable is allowed to be created on the page with record $pid_row
	 */
	function isTableAllowedForThisPage($pid_row, $checkTable)	{
		global $TCA, $PAGES_TYPES;
		if (!is_array($pid_row))	{
			if ($GLOBALS['BE_USER']->user['admin'])	{
				return true;
			} else {
				return false;
			}
		}
			// be_users and be_groups may not be created anywhere but in the root.
		if ($checkTable=='be_users' || $checkTable=='be_groups')	{
			return false;
		}
			// Checking doktype:
		$doktype = intval($pid_row['doktype']);
		if (!$allowedTableList = $PAGES_TYPES[$doktype]['allowedTables'])	{
			$allowedTableList = $PAGES_TYPES['default']['allowedTables'];
		}
		if (strstr($allowedTableList,'*') || t3lib_div::inList($allowedTableList,$checkTable))	{		// If all tables or the table is listed as a allowed type, return true
			return true;
		}
	}



	function get_mm_fileList($local_table, $local_uid, $select='', $whereClause='', $groupBy='', $orderBy='', $limit=100, $MM_table='tx_dam_mm_ref') {

		$select = $select ? $select : 'tx_dam.uid, tx_dam.title, tx_dam.file_path, tx_dam.file_name, tx_dam.file_type' ;

		if(!$orderBy) {
			$orderBy = $MM_table.'.sorting';
		}

		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
			$select,
			$local_table,
			$MM_table,
			'tx_dam',
			'AND '.$local_table.'.uid IN ('.$local_uid.') '.$whereClause,
			$groupBy,
			$orderBy,
			$limit
		);
		$files = array();
		$rows = array();
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$files[$row['uid']] = $row['file_path'].$row['file_name'];
			$rows[$row['uid']] = $row;
		}

		return array('files' => $files, 'rows' => $rows);
	}

	/**
	 * Make a list of references to foreign tables (eg. tt_content) by a mm-relation to the tx_dam table
	 *
	 * @param	integer		$tx_dam_uid: ...
	 * @param	string		$select: ...
	 * @param	string		$whereClause: ...
	 * @param	string		$groupBy: ...
	 * @param	string		$orderBy: ...
	 * @param	integer		$limit: ...
	 * @param	string		$MM_table: ...
	 * @return	array		...
	 */
	function get_mm_refList($tx_dam_uid, $select='', $whereClause='', $groupBy='', $orderBy='', $limit=100, $MM_table='tx_dam_mm_ref') {

		if(!$orderBy) {
			$orderBy = $MM_table.'.tablenames';
		}

		$res = tx_damcatedit_db::exec_SELECT_mm_refList(
			$tx_dam_uid,
			$select,
			$whereClause,
			$groupBy,
			$orderBy,
			$limit,
			$MM_table
		);

		$rows = array();
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$rows[$row['uid']] = $row;
		}

		return $rows;
	}



	/**
	 * Make a list of references to foreign tables (eg. tt_content) by a mm-relation to the tx_dam table
	 *
	 * @param	integer		$tx_dam_uid: ...
	 * @param	string		$select: ...
	 * @param	string		$whereClause: ...
	 * @param	string		$groupBy: ...
	 * @param	string		$orderBy: ...
	 * @param	string		$limit: ...
	 * @param	string		$MM_table: ...
	 * @return	mixed		...
	 */
	function exec_SELECT_mm_refList($tx_dam_uid, $select='', $whereClause='', $groupBy='', $orderBy='', $limit=100, $MM_table='tx_dam_mm_ref') {

		if(!$orderBy) {
			$orderBy = $MM_table.'.tablenames';
		}

		$select = $select ? $select : 'tx_dam.uid, tx_dam.title, tx_dam.file_path, tx_dam.file_name, tx_dam.file_type, '.$MM_table.'.tablenames, '.$MM_table.'.ident' ;
		$whereClause.= $tx_dam_uid ? ' AND tx_dam.uid IN ('.$tx_dam_uid.')' : '';



		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
			$select,
			'tx_dam',
			$MM_table,
			'',
			$whereClause,
			$groupBy,
			$orderBy,
			$limit
		);

		return $res;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam_catedit/lib/class.tx_damcatedit_db.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam_catedit/lib/class.tx_damcatedit_db.php']);
}


?>
