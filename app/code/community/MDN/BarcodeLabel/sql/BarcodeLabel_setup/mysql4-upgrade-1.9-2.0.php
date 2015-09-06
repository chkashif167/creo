<?php

$installer = $this;

$installer->startSetup();
$installer->run("

    CREATE TABLE {$this->getTable('barcode_label_list')}
    (
        `bll_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `bll_barcode` varchar(50) NOT NULL,
        PRIMARY KEY (`bll_id`),
        UNIQUE KEY `bll_barcode` (`bll_barcode`)
    )  ENGINE=INNODB;
    
");
$installer->endSetup();
