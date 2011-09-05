<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


t3lib_extMgm::addPageTSConfig('
	# ***************************************************************************************
	# CONFIGURATION of RTE in table "tx_rlmptvnotes_notes", field "note"
	# ***************************************************************************************
	RTE.config.tx_rlmptvnotes_notes.note {
		hidePStyleItems = H1, H4, H5, H6
		proc.exitHTMLparser_db = 1
		proc.exitHTMLparser_db {
			keepNonMatchedTags = 1
			tags.font.allowedAttribs = color
			tags.font.rmTagIfNoAttrib = 1
			tags.font.nesting = global
		}
	}
');

	// Register our class at some hooks in templavoila:
$GLOBALS ['TYPO3_CONF_VARS']['EXTCONF']['templavoila']['mod1']['renderFrameworkClass'][] = 'EXT:rlmp_tvnotes/class.tx_rlmptvnotes_templavoilamod1.php:tx_rlmptvnotes_templavoilamod1';
$GLOBALS ['TYPO3_CONF_VARS']['EXTCONF']['templavoila']['mod1']['menuConfigClass'][] = 'EXT:rlmp_tvnotes/class.tx_rlmptvnotes_templavoilamod1.php:tx_rlmptvnotes_templavoilamod1';

	// Register our class at a hook in TCEmain:
$GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:rlmp_tvnotes/class.tx_rlmptvnotes_tcemain.php:tx_rlmptvnotes_tcemain';

?>