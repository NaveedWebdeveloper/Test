#
# Table structure for table 'sys_notepad'
#
CREATE TABLE sys_notepad (
  uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
  pid int(11) unsigned DEFAULT '0' NOT NULL,
  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  note text NOT NULL,
  securecode text NOT NULL,
  crdate int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (uid),
  KEY cruser_id (cruser_id),
  KEY parent (pid)
);

