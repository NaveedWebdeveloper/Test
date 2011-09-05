<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


t3lib_div::loadTCA("tt_content");
$tempColumns = array( 
					'header_rte' => Array (
						'l10n_mode' => 'prefixLangTitle',
						'l10n_cat' => 'text',
						'label' => 'LLL:EXT:go_teaser/locallang_db.xml:tt_content.header_rte',
						'config' => Array (
							'type' => 'text',
							'cols' => '48',
							'rows' => '3',
							'wizards' => Array(
								'_PADDING' => 4,
								'_VALIGN' => 'middle',
								'RTE' => Array(
									'notNewRecords' => 1,
									'RTEonly' => 1,
									'type' => 'script',
									'title' => 'LLL:EXT:cms/locallang_ttc.php:bodytext.W.RTE',
									'icon' => 'wizard_rte2.gif',
									'script' => 'wizard_rte.php',
								),
								'table' => Array(
									'notNewRecords' => 1,
									'enableByTypeConfig' => 1,
									'type' => 'script',
									'title' => 'Table wizard',
									'icon' => 'wizard_table.gif',
									'script' => 'wizard_table.php',
									'params' => array('xmlOutput' => 0)
								),
								'forms' => Array(
									'notNewRecords' => 1,
									'enableByTypeConfig' => 1,
									'type' => 'script',
			#						'hideParent' => array('rows' => 4),
									'title' => 'Forms wizard',
									'icon' => 'wizard_forms.gif',
									'script' => 'wizard_forms.php?special=formtype_mail',
									'params' => array('xmlOutput' => 0)
								)
							),
							'softref' => 'typolink_tag,images,email[subst],url'
						)
					),
					'header_rte2' => Array (
						'l10n_mode' => 'prefixLangTitle',
						'l10n_cat' => 'text',
						'label' => 'LLL:EXT:go_teaser/locallang_db.xml:tt_content.header_rte2',
						'config' => Array (
							'type' => 'text',
							'cols' => '48',
							'rows' => '3',
							'wizards' => Array(
								'_PADDING' => 4,
								'_VALIGN' => 'middle',
								'RTE' => Array(
									'notNewRecords' => 1,
									'RTEonly' => 1,
									'type' => 'script',
									'title' => 'LLL:EXT:cms/locallang_ttc.php:bodytext.W.RTE',
									'icon' => 'wizard_rte2.gif',
									'script' => 'wizard_rte.php',
								),
								'table' => Array(
									'notNewRecords' => 1,
									'enableByTypeConfig' => 1,
									'type' => 'script',
									'title' => 'Table wizard',
									'icon' => 'wizard_table.gif',
									'script' => 'wizard_table.php',
									'params' => array('xmlOutput' => 0)
								),
								'forms' => Array(
									'notNewRecords' => 1,
									'enableByTypeConfig' => 1,
									'type' => 'script',
			#						'hideParent' => array('rows' => 4),
									'title' => 'Forms wizard',
									'icon' => 'wizard_forms.gif',
									'script' => 'wizard_forms.php?special=formtype_mail',
									'params' => array('xmlOutput' => 0)
								)
							),
							'softref' => 'typolink_tag,images,email[subst],url'
						)
					),
					'go_content_image' => txdam_getMediaTCA('image_field', 'go_content_image'),
					'go_teaser_layout' => array (
						'exclude' => 0,
						'label' => 'LLL:EXT:go_teaser/locallang_db.xml:tt_content.tx_goteaser.piBoxTeaser_layout',
						'config' => array (
							'type' => 'select',
							'items' => array (
								array('LLL:EXT:go_teaser/locallang_db.xml:tt_content.tx_goteaser.piThemenTeaser_layout.I.0', '0', t3lib_extMgm::extRelPath('go_teaser').'res/selicon_tt_content_tx_goteaser.piThemenTeaser_layout_0.gif'),
								array('LLL:EXT:go_teaser/locallang_db.xml:tt_content.tx_goteaser.piThemenTeaser_layout.I.1', '1', t3lib_extMgm::extRelPath('go_teaser').'res/selicon_tt_content_tx_goteaser.piThemenTeaser_layout_1.gif')
							),
							'size' => 1,
							'maxitems' => 1,
						)
					),
					'go_content_linktext' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:go_teaser/locallang_db.xml:tt_content.go_content_linktext',
						'config' => array (
							'type' => 'input',
							'size' => '20',
							'max' => '64',
						)
					),
);

t3lib_extMgm::addTCAcolumns('tt_content',$tempColumns,1);


$TCA['tt_content']['columns']['go_content_image']['label'] = 'LLL:EXT:go_teaser/locallang_db.xml:tt_content.go_content_image';
$TCA['tt_content']['columns']['go_content_image']['exclude'] = 1;
$TCA['tt_content']['columns']['go_content_image']['config']['show_thumbs'] = 1;
$TCA['tt_content']['columns']['go_content_image']['config']['size'] = 1;
$TCA['tt_content']['columns']['go_content_image']['config']['maxitems'] = 1;
$TCA['tt_content']['columns']['go_content_image']['config']['minitems'] = 0;
$TCA['tt_content']['columns']['go_content_image']['config']['autoSizeMax'] = 1;



// #
// ### piTeaser
// #
$TCA['tt_content']['types'][$_EXTKEY . '_piTeaser']['showitem'] = 'CType;;;button;1-1-1, tx_damttcontent_files, header_rte, header_rte2;;;richtext:rte_transform[flag=rte_enabled|mode=ts];2-2-2, image_link, 
																			--div--;LLL:EXT:go_imageedit_be/locallang_db.xml:tabLabel, tx_goimageeditbe_croped_image,
																			--div--;LLL:EXT:cms/locallang_tca.xml:pages.tabs.access, starttime, endtime, fe_group';

																			
$TCA['tt_content']['imageedit'][$_EXTKEY.'_piTeaser']= Array
											(
											"debug" => 0,						//gibt einige Debugwerte aus
											"imgPath" => '../uploads/pics/', 	// vom Backend aus gesehen
											"rootImgPath" => 'uploads/pics/', 	// vom Frontend aus
											
											//Backend
											"selector" => Array(
												"allowCustomRatio" => 1,		//dieses Flag lässt den benutzer 
																				//das Format des Selectors frei bestimmen
																			
												"lockWH" => 1,					//sperrt die Aktuelle Höhe und Breite
												"formatW" => '211',				//Aus den Werten <FormatW>, <FormatH> wird beim erstmaligen angucken
												"formatH" => '158',				// das Selector-Format berechnet
												
												"minHeight" => 211,
												"minWidth" => 158
											),
											
											"menu" => Array(					
												"displayType" => 0,					// 	1 : HTML-SELECT-BOX;  	
																					//	0 : BUTTONS (nachfolgende Einstellungen)
												"showImageName" => 0,				//Zeigt den Namen des Bildes an
												"showThumbnail" => 1,				//Zeigt ein Thumbnail 
												"showThumbnail_size" => "211x158",	//diesen Ausmaßes
												"showResolution" => 1,				//Zeigt die Auflösung der Bilder im Selector an
												
												"maxImages" =>1,
											),
											
											"adjustResolution" => Array(
												"enabled" => 1,					//Bild runterrechnen ( 1 ) wenn > maxDisplayedWidth & maxDisplayedHeight
												"maxDisplayedWidth" => "700",		//hoechste unangetastete im Backend Angezeigte Auflösung
												"maxDisplayedHeight" => "400",
											),
	
											);																				
																			
t3lib_extMgm::addPlugin(array(
	'LLL:EXT:go_teaser/locallang_db.xml:tt_content.piTeaser.CType', 
	$_EXTKEY . '_piTeaser',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'piTeaser/icon.gif')
,'CType');


// #
// ### piMyElement
// #
$TCA['tt_content']['types'][$_EXTKEY . '_piMyElement']['showitem'] = 'CType, header, myheader, header_link';

t3lib_div::loadTCA("tt_content");
$tempColumns = array( 
	'myheader' => array(
			'label' => 'LLL:EXT:go_teaser/locallang_db.xml:tt_content.myheader',
			'config' => array(
				'type' => 'input',
				'size' => '50',
				'max' => '256',
			),
	),
);
t3lib_extMgm::addTCAcolumns('tt_content',$tempColumns,1);

t3lib_extMgm::addPlugin(array(
	'LLL:EXT:go_teaser/locallang_db.xml:tt_content.piMyElement.CType', 
	$_EXTKEY . '_piMyElement',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'piMyElement/icon.gif')
,'CType');
?>