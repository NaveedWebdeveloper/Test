<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_rlmptvnotes_notes'] = Array (
	'ctrl' => $TCA['tx_rlmptvnotes_notes']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'title,note,beusersread'
	),
	'feInterface' => $TCA['tx_rlmptvnotes_notes']['feInterface'],
	'columns' => Array (
		'title' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:rlmp_tvnotes/locallang_db.php:tx_rlmptvnotes_notes.title',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
			)
		),
		'note' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:rlmp_tvnotes/locallang_db.php:tx_rlmptvnotes_notes.note',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			)
		),
		'beusersread' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:rlmp_tvnotes/locallang_db.php:tx_rlmptvnotes_notes.beusersread',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'be_users',
				'size' => 5,
				'minitems' => 0,
				'maxitems' => 100,
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'title;;;;2-2-2, note;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_rlmptvnotes/rte/];3-3-3, beusersread')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);
?>