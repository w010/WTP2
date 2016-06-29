#
# Table structure for table 'tx_sitecedris_swregio'
#
CREATE TABLE tx_sitecedris_swregio (
  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,
  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(3) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,
  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  fe_group varchar(100) DEFAULT '0' NOT NULL,
  title varchar(255) DEFAULT '' NOT NULL,
  url varchar(255) DEFAULT '' NOT NULL,
  description tinytext,

  PRIMARY KEY (uid),
  KEY parent (pid)
);


#
# Table structure for table 'tx_sitecedris_bglink'
#
CREATE TABLE tx_sitecedris_bglink (
  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,
  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(3) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  sorting int(11) unsigned DEFAULT '0' NOT NULL,

  label varchar(255) DEFAULT '' NOT NULL,
  link varchar(255) DEFAULT '' NOT NULL,

  PRIMARY KEY (uid),
  KEY parent (pid)
);







#
# Table structure for table 'tt_news'
#
CREATE TABLE tt_news (
     tx_sitecedris_background int(11) DEFAULT '0' NOT NULL,
     tx_sitecedris_background_links text,
     tx_sitecedris_author int(11) DEFAULT '0' NOT NULL,

     tx_sitecedris_sw_arbeidsmarktregio int(11) DEFAULT '0' NOT NULL,
     tx_sitecedris_sw_bedrijven varchar(255) DEFAULT '' NOT NULL,
     tx_sitecedris_praktik_function varchar(255) DEFAULT '' NOT NULL,
     tx_sitecedris_praktik_phone varchar(255) DEFAULT '' NOT NULL,
     tx_sitecedris_praktik_email varchar(255) DEFAULT '' NOT NULL,

     tx_sitecedris_praktik_logo varchar(255) DEFAULT '' NOT NULL,
     tx_sitecedris_praktik_title varchar(255) DEFAULT '' NOT NULL
);



#
# Table structure for table 'tx_sitecedris_background_mm'
#
CREATE TABLE tx_sitecedris_background_mm (
	uid_local int(11) DEFAULT '0' NOT NULL,
	uid_foreign int(11) DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,
	tablenames varchar(255) DEFAULT '' NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);




#
# Table structure for table 'fe_users'
#
CREATE TABLE fe_users (
     tx_sitecedris_sw_postadres varchar(32) DEFAULT '' NOT NULL,
     tx_sitecedris_sw_cole varchar(8) DEFAULT '' NOT NULL,
     tx_sitecedris_sw_provincie varchar(32) DEFAULT '' NOT NULL,
     tx_sitecedris_sw_arbeidsmarktregio varchar(64) DEFAULT '' NOT NULL,
     tx_sitecedris_sw_citysecond varchar(256) DEFAULT '' NOT NULL
);




#
# Table structure for table 'be_users'
#
CREATE TABLE be_users (
     tx_sitecedris_image varchar(128) DEFAULT '' NOT NULL
);


