<?php

$installer = $this;

$installer->startSetup();
$storeIds = Mage::getModel('core/store')->getCollection()->getAllIds();
$groupIds = Mage::getModel('customer/group')->getCollection()->getAllIds();
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('pdfpro/key')};
CREATE TABLE {$this->getTable('pdfpro/key')} (
  `entity_id` int(11) unsigned NOT NULL auto_increment,
  `api_key` varchar(255) NOT NULL default '',
  `store_ids` varchar(255) NOT NULL default '',
  `customer_group_ids` varchar(255) NOT NULL default '',
  `priority` int(11) unsigned NOT NULL default 0,
  `comment` text NOT NULL default '',
  `is_default` smallint(6) NOT NULL default '0',
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();