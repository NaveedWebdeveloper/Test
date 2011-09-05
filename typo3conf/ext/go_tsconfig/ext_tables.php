<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
$tempColumns = array (
	// #
	// ### Mansoor Ahmad @ Gosign media. GmbH - SEO Robots
	// #
	'robots' => array (
		"exclude" => 0,
		"label" => "LLL:EXT:go_tsconfig/locallang_db.xml:pages.tx_gotsconfig_robots",
		"config" => Array (
			"type" => "radio",
			"items" => Array (
				Array("LLL:EXT:go_tsconfig/locallang_db.xml:pages.tx_gotsconfig_robots.0", ""),
				Array("LLL:EXT:go_tsconfig/locallang_db.xml:pages.tx_gotsconfig_robots.1", "index, follow"),
				Array("LLL:EXT:go_tsconfig/locallang_db.xml:pages.tx_gotsconfig_robots.2", "noindex, follow"),
				Array("LLL:EXT:go_tsconfig/locallang_db.xml:pages.tx_gotsconfig_robots.3", "index, nofollow"),
				Array("LLL:EXT:go_tsconfig/locallang_db.xml:pages.tx_gotsconfig_robots.4", "noindex, nofollow"),
			),
			"default" => "",
		)
	),
);

t3lib_div::loadTCA('pages');
t3lib_extMgm::addTCAcolumns('pages',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('pages','robots', '', 'before:abstract,after:shortcut_mode,after:mount_pid_ol,after:urltype');

?>