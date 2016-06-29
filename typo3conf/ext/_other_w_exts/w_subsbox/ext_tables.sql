#
# Table structure for table 'tx_wsubsbox_emails'
#
CREATE TABLE tx_wsubsbox_emails (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	address tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);