<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	ALTER TABLE {$this->getTable('mst_pdp_multisides_colors')} ADD color_code varchar(100) NOT NULL;
    ALTER TABLE {$this->getTable('mst_pdp_multisides_colors')} ADD color_name varchar(100) NOT NULL DEFAULT '';
");
$installer->endSetup(); 
