<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('ngroups')} ADD `categories_hide` int(10) NOT NULL;

");

$installer->endSetup(); 