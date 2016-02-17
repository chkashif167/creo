<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE IF NOT EXISTS `who_also_view` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `session_cod` varchar(30) NOT NULL,
  `product_id` int(10) NOT NULL,
  `product_sku` varchar(10) NOT NULL,
  `product_categories` varchar( 255 ) NOT NULL,
  `ip` varchar(16) NOT NULL,
  `current_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)		
SQLTEXT;

$installer->run($sql);

$installer->endSetup();
	 