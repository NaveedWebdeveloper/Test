#
# Table structure for table 'tx_rlmptvnotes_notes'
#
CREATE TABLE tx_rlmptvnotes_notes (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	note text NOT NULL,
	beusersread blob NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);