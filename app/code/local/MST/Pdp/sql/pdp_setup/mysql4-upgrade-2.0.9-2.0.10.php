<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	ALTER TABLE {$this->getTable('mst_pdp_multisides')} ADD color_code varchar(100) NOT NULL;
    ALTER TABLE {$this->getTable('mst_pdp_multisides')} ADD color_name varchar(100) NOT NULL;
    ALTER TABLE {$this->getTable('mst_pdp_multisides')} ADD background_type varchar(10) NOT NULL DEFAULT 'image';
");
$installer->endSetup(); 
