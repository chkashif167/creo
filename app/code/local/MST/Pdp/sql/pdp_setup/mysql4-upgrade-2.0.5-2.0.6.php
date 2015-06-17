<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	ALTER TABLE {$this->getTable('mst_pdp_multisides')} ADD price decimal(10, 2) DEFAULT 0;
	ALTER TABLE {$this->getTable('mst_pdp_multisides')} ADD overlay varchar(500) NOT NULL;
");
$installer->endSetup(); 