<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	CREATE TABLE IF NOT EXISTS {$this->getTable('mst_pdp_customer_template')} (
		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`product_id` int(11) NOT NULL,
		`customer_id` int(11) NOT NULL,
		`filename` varchar(500) NOT NULL,
		`status` smallint(6) NOT NULL DEFAULT '1',
		`created_time` datetime DEFAULT NULL,
		`update_time` datetime DEFAULT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
");
$installer->endSetup(); 
