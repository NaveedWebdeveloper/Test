<?php
/**
 * MS SQL configuration
 *
 * $Id: mssql.config.php 37022 2010-08-19 19:34:19Z xperseguers $
 *
 * @author Xavier Perseguers <typo3@perseguers.ch>
 *
 * @package TYPO3
 * @subpackage dbal
 */
global $TYPO3_CONF_VARS;

$TYPO3_CONF_VARS['EXTCONF']['dbal']['handlerCfg'] = array(
	'_DEFAULT' => array(
		'type' => 'adodb',
		'config' => array(
			'driver' => 'mssql',
			'useNameQuote' => FALSE,
		),
	),
);

$TYPO3_CONF_VARS['EXTCONF']['dbal']['mapping'] = array(
	'tx_templavoila_tmplobj' => array(
		'mapFieldNames' => array(
			'datastructure' => 'ds',
		),
	),
	'Members' => array(
		'mapFieldNames' => array(
			'pid' => '0',
			'cruser_id' => '1',
			'uid' => 'MemberID',
		),
	),
);

$TYPO3_CONF_VARS['EXTCONF']['dbal']['table2handlerKeys'] = array();
?>