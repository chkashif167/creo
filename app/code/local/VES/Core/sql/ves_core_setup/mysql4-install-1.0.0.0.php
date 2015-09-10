<?php

$installer = $this;

$installer->startSetup();
$sql = "

DROP TABLE IF EXISTS {$this->getTable('ves_core/key')};
CREATE TABLE {$this->getTable('ves_core/key')} (
  `key_id` int(11) unsigned NOT NULL auto_increment,
  `license_key` varchar(255) NOT NULL default '',
  `license_info` TEXT NOT NULL default '',
  `description` TEXT NOT NULL default '',
  `additional_info` TEXT NOT NULL default '',
  PRIMARY KEY (`key_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

$installer->run($sql);

$installer->endSetup();