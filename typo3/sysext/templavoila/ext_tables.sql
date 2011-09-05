# TYPO3 CVS ID: $Id: ext_tables.sql 4765 2007-01-27 18:11:05Z dmitry $

#
# Table structure for table 'tx_templavoila_tmplobj'
#
CREATE TABLE tx_templavoila_tmplobj (
	uid int(11) DEFAULT '0' NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(30) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3_origuid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	fileref_mtime int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	title varchar(60) DEFAULT '' NOT NULL,
	datastructure varchar(100) DEFAULT '' NOT NULL,
	fileref tinytext NOT NULL,
	templatemapping mediumtext NOT NULL,
	previewicon tinytext NOT NULL,
	description tinytext NOT NULL,
	rendertype varchar(10) DEFAULT '' NOT NULL,
	sys_language_uid int(11) unsigned DEFAULT '0' NOT NULL,
	parent int(11) unsigned DEFAULT '0' NOT NULL,
	rendertype_ref int(11) unsigned DEFAULT '0' NOT NULL,
	localprocessing text NOT NULL,
	fileref_md5 varchar(32) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_templavoila_datastructure'
#
CREATE TABLE tx_templavoila_datastructure (
	uid int(11) DEFAULT '0' NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(30) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3_origuid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	title varchar(60) DEFAULT '' NOT NULL,
	dataprot mediumtext NOT NULL,
	scope tinyint(4) unsigned DEFAULT '0' NOT NULL,
	previewicon tinytext NOT NULL,

	PRIMARY KEY (uid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
	KEY parent (pid)
);

#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
	tx_templavoila_ds varchar(100) DEFAULT '' NOT NULL,
	tx_templavoila_to int(11) DEFAULT '0' NOT NULL,
    tx_templavoila_flex mediumtext NOT NULL,
    tx_templavoila_pito int(11) DEFAULT '0' NOT NULL
);

#
# Table structure for table 'pages'
#
CREATE TABLE pages (
	tx_templavoila_ds varchar(100) DEFAULT '' NOT NULL,
	tx_templavoila_to int(11) DEFAULT '0' NOT NULL,
	tx_templavoila_next_ds varchar(100) DEFAULT '' NOT NULL,
	tx_templavoila_next_to int(11) DEFAULT '0' NOT NULL,
    tx_templavoila_flex mediumtext NOT NULL,

);

#
# Table structure for table 'be_groups'
#
CREATE TABLE be_groups (
	tx_templavoila_access text NOT NULL,
);
