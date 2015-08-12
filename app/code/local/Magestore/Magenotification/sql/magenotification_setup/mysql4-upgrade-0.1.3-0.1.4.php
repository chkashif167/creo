<?php
$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('magenotification')};

CREATE TABLE {$this->getTable('magenotification')} (
  `magenotification_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `description` text default '',
  `url` varchar(255) NOT NULL default '',
  `severity` smallint(5) UNSIGNED NOT NULL default 0,
  `is_read` tinyint default 0,
  `is_remove` tinyint default 0,
  `related_extensions` varchar(255) default '',
  `added_date` datetime NOT NULL,
  PRIMARY KEY (`magenotification_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
");

$installer->endSetup(); 