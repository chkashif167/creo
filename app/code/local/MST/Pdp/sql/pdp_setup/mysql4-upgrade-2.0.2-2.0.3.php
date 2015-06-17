<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	CREATE TABLE IF NOT EXISTS {$this->getTable('mst_pdp_json_files')} (
		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`filename` varchar(500) NOT NULL DEFAULT '',
		`description` TEXT NOT NULL DEFAULT '',
		`status` smallint(11) NOT NULL DEFAULT '1',
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
");
$installer->endSetup();
