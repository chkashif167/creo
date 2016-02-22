<?php

$installer = $this;

$installer->startSetup();
/*
$installer->run("

ALTER TABLE  {$this->getTable('newsletter_subscriber')} ADD  `custom_subscriber_name` VARCHAR( 200 ) NOT NULL;
ALTER TABLE  {$this->getTable('newsletter_subscriber')} ADD  `custom_subscriber_telephone` VARCHAR( 50 ) NOT NULL;

    ");
*/
$installer->endSetup(); 