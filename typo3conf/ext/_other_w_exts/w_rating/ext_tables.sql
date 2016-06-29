#
# Table structure for table 'tx_wrating_vote'
#
CREATE TABLE tx_wrating_vote (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	
	table_name tinytext,
	record_uid int(11) DEFAULT '0' NOT NULL,
	note int(11) DEFAULT '0' NOT NULL,
	userdata text,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);