#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
	CType varchar(64),
	header_rte text,
	header_rte2 text,
	myheader text,
	go_teaser_layout text,
	go_content_image int(11) unsigned DEFAULT '0' NOT NULL,
	go_content_linktext varchar(64) DEFAULT '',
);
