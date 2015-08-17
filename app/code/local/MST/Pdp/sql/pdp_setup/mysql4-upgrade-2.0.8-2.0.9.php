<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	ALTER TABLE {$this->getTable('mst_pdp_customer_template')} ADD design_title varchar(500) NOT NULL;
    ALTER TABLE {$this->getTable('mst_pdp_customer_template')} ADD design_note TEXT NOT NULL;
");
$installer->endSetup(); 
