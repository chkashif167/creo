<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	ALTER TABLE {$this->getTable('mst_pdp_multisides_colors_images')} ADD overlay varchar(500) NOT NULL;
");
$installer->endSetup(); 