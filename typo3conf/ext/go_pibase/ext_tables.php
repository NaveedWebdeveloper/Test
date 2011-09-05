<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_div::loadTCA('pages');
$TCA['pages']['columns']['content_from_pid']['exclude'] = 1;

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['columns']['imagecols']['exclude'] = 1;

$TCA['tt_content']['columns']['tx_dam_files']['exclude'] = 1;
$TCA['tt_content']['columns']['tx_dam_files']['config']['show_thumbs'] = 0;
$TCA['tt_content']['columns']['tx_dam_files']['config']['size'] = 1;
$TCA['tt_content']['columns']['tx_dam_files']['config']['maxitems'] = 1;
$TCA['tt_content']['columns']['tx_dam_files']['config']['minitems'] = 0;
$TCA['tt_content']['columns']['tx_dam_files']['config']['autoSizeMax'] = 1;

$TCA['tt_content']['columns']['tx_dam_images']['exclude'] = 1;
$TCA['tt_content']['columns']['tx_dam_images']['config']['show_thumbs'] = 0;
$TCA['tt_content']['columns']['tx_dam_images']['config']['size'] = 1;
$TCA['tt_content']['columns']['tx_dam_images']['config']['maxitems'] = 1;
$TCA['tt_content']['columns']['tx_dam_images']['config']['minitems'] = 0;
$TCA['tt_content']['columns']['tx_dam_images']['config']['autoSizeMax'] = 1;

?>