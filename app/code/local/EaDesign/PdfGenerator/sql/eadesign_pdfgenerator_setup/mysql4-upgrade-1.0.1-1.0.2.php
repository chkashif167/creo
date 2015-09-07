<?php

$installer = $this;

$installer->getConnection()->addColumn($installer->getTable('eadesign/pdfgenerator'), 'template_store_id', array(
    'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
    'unsigned' => true,
    'nullable' => false,
    'default' => '0',
    'comment' => 'The store id'
));
