<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('ngroups')} ADD `category_id` VARCHAR(500) NOT NULL;

");

$installer->endSetup(); 