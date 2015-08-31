<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('attributeswatches')};
CREATE TABLE {$this->getTable('attributeswatches')} (
  `attributeswatches_id` int(11) unsigned NOT NULL auto_increment,
  `option_id` int(11) NOT NULL default '0',
  `filename` varchar(255) NOT NULL default '',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`attributeswatches_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 