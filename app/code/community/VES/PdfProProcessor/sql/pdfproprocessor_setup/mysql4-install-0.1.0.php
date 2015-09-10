<?php

$installer = $this;

$installer->startSetup();
$installer->run("

ALTER TABLE `{$this->getTable('pdfpro/key')}` ADD `logo` VARCHAR( 255 ) NOT NULL AFTER `api_key` 
");

$installer->endSetup();