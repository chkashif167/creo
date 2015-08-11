<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	CREATE TABLE IF NOT EXISTS {$this->getTable('mst_pdp_texts')} (
		`text_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`text` varchar(255) NOT NULL DEFAULT '',
		`tags` TEXT NOT NULL DEFAULT '',
		`is_popular` smallint(11) NOT NULL DEFAULT '2',
		`status` smallint(11) NOT NULL DEFAULT '1',
		`position` int(11) NOT NULL DEFAULT '0',
		PRIMARY KEY (`text_id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
	
	INSERT INTO {$this->getTable('mst_pdp_texts')} (`text_id`, `text`, `tags`, `is_popular`, `status`, `position`) VALUES
	(1, 'Love you', 'love', 1, 1, 0),
	(2, 'Love conquers all', 'love', 2, 1, 0),
	(3, 'It is never too late to fall in love', 'love', 2, 1, 0),
	(4, 'Love turns winter into summer', 'love', 2, 1, 0),
	(5, 'Love is real, real is love', 'love', 2, 1, 0);
");
$installer->endSetup();
