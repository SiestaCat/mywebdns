-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:         		 5.7.40 - MySQL Community Server (GPL)
-- --------------------------------------------------------

CREATE TABLE acldomain (
  ID int(10) unsigned NOT NULL auto_increment,
  IDDOM int(10) unsigned NOT NULL default '0',
  IP int(10) unsigned NOT NULL default '0',
  NETMASK int(2) unsigned NOT NULL default '0',
  TYPE char(3) NOT NULL default '',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;

--
-- Dumping data for table 'acldomain'
--

--
-- Table structure for table 'configuration'
--

CREATE TABLE configuration (
  ID int(2) unsigned NOT NULL auto_increment,
  VERSION char(7) NOT NULL default '',
  UPDATES int(2) unsigned NOT NULL default '0',
  LANGUAGE char(2) NOT NULL default '',
  RECORD_NS int(2) unsigned NOT NULL default '0',
  RECORD_MX int(2) unsigned NOT NULL default '0',
  RECORD_A int(2) unsigned NOT NULL default '0',
  RECORD_PTR int(2) unsigned NOT NULL default '0',
  RECORD_CNAME int(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;

--
-- Dumping data for table 'configuration'
--


INSERT INTO configuration VALUES (1,'1.1',11,'uk',2,2,5,5,2);


--
-- Table structure for table 'dns'
--

CREATE TABLE dns (
  ID int(5) unsigned NOT NULL auto_increment,
  DNSFQDN varchar(60) NOT NULL default '',
  DNSDESCR varchar(60) default NULL,
  DNSIP int(10) unsigned NOT NULL default '0',
  DATA date NOT NULL,
  DIRNAMED varchar(60) NOT NULL default '',
  INCLUDEZONENAMED varchar(60) NOT NULL default '',
  DIRZONES varchar(60) NOT NULL default '',
  RNDCRELOAD varchar(60) NOT NULL default '',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;

--
-- Dumping data for table 'dns'
--



--
-- Table structure for table 'domain'
--

CREATE TABLE domain (
  ID int(10) unsigned NOT NULL auto_increment,
  NAME varchar(60) NOT NULL default '',
  LEVEL int(1) NOT NULL default '0',
  DATA date NOT NULL,
  TTL int(5) unsigned default '86400',
  HOSTDNS varchar(60) NOT NULL default '',
  ROOTDNS varchar(60) NOT NULL default '',
  SERIAL int(10) unsigned NOT NULL default '0',
  REFRESH int(5) unsigned NOT NULL default '86400',
  RETRY int(5) unsigned NOT NULL default '7200',
  EXPIRE int(7) unsigned NOT NULL default '2592000',
  MINIMUM int(5) unsigned NOT NULL default '86400',
  ZONETYPE char(1) NOT NULL default 'M',
  ZONEMASTERTYPE char(1) NOT NULL default 'M',
  STATE char(1) NOT NULL default 'A',
  MD5 varchar(32) NOT NULL default '',
  IDDNS int(5) unsigned NOT NULL default '0',
  LOCKDEL int(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (ID,NAME)
) ENGINE=MyISAM;

--
-- Dumping data for table 'domain'
--



--
-- Table structure for table 'ipdnsforwarders'
--

CREATE TABLE ipdnsforwarders (
  ID int(5) unsigned NOT NULL auto_increment,
  IDDOM int(10) unsigned NOT NULL default '0',
  IP int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;

--
-- Dumping data for table 'ipdnsforwarders'
--



--
-- Table structure for table 'ipdnsmaster'
--

CREATE TABLE ipdnsmaster (
  ID int(5) unsigned NOT NULL auto_increment,
  IDDOM int(10) unsigned NOT NULL default '0',
  IP int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;

--
-- Dumping data for table 'ipdnsmaster'
--



--
-- Table structure for table 'mysql_auth'
--

CREATE TABLE mysql_auth (
  USERNAME varchar(25) NOT NULL default '',
  PASSWORD varchar(25) NOT NULL default '',
  GROUPS varchar(25) NOT NULL default '',
  FULLNAME varchar(50) NOT NULL default '',
  PRIMARY KEY  (USERNAME)
) ENGINE=MyISAM;

--
-- Dumping data for table 'mysql_auth'
--


--
-- Table structure for table 'mysql_auth_group'
--

CREATE TABLE mysql_auth_group (
  ID int(5) unsigned NOT NULL auto_increment,
  GROUPS varchar(25) NOT NULL default '',
  PRIMARY KEY  (ID,GROUPS)
) ENGINE=MyISAM;

--
-- Dumping data for table 'mysql_auth_group'
--


INSERT INTO mysql_auth_group VALUES (1,'administration');

--
-- Table structure for table 'recordmaster'
--

CREATE TABLE recordmaster (
  ID int(10) unsigned NOT NULL auto_increment,
  IDDOM int(10) unsigned NOT NULL default '0',
  NAME varchar(60) default NULL,
  TTL int(5) unsigned default '86400',
  TYPE varchar(10) NOT NULL default '',
  PRIORITY int(2) unsigned default NULL,
  HOSTTARGET varchar(60) default NULL,
  IP int(10) unsigned default NULL,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;

--
-- Dumping data for table 'recordmaster'
--



--
-- Table structure for table 'recordreverse'
--

CREATE TABLE recordreverse (
  ID int(10) unsigned NOT NULL auto_increment,
  IDDOM int(10) unsigned NOT NULL default '0',
  IP varchar(60) NOT NULL default '',
  TTL int(5) unsigned default '86400',
  TYPE varchar(10) NOT NULL default '',
  PRIORITY int(2) unsigned default NULL,
  HOSTTARGET varchar(60) default NULL,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;

--
-- Dumping data for table 'recordreverse'
--



