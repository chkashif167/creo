<?php

$installer = $this;

$installer->startSetup();
$installer->run("

    ALTER TABLE {$this->getTable('barcode_label_list')} CHANGE `bll_barcode` `bll_barcode` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

");
$installer->endSetup();
