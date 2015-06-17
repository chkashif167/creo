<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	ALTER TABLE {$this->getTable('mst_pdp_images')} ADD image_name varchar(500);
	ALTER TABLE {$this->getTable('mst_pdp_images')} ADD price decimal(10, 2);
");
$installer->endSetup(); 
