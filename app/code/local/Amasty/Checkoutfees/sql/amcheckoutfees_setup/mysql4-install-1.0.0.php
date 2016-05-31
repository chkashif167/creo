<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */
$installer = $this;

/**
 * Create table 'amcheckoutfees/fees'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('amcheckoutfees/fees'))
    ->addColumn('fees_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true,
    ), 'Fee Id'
    )
    ->addColumn('enabled', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array('unsigned' => true, 'nullable' => false,), 'Enabled')
    ->addColumn('input', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('unsigned' => true, 'nullable' => false,), 'Input type')
    ->addColumn('position_cart', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('unsigned' => true, 'nullable' => false,), 'Input type')
    ->addColumn('position_checkout', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('unsigned' => true, 'nullable' => false,), 'Input type')
    ->addColumn('sort', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('unsigned' => true, 'nullable' => false,), 'Sort')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'Name')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, null, array('unsigned' => true, 'nullable' => false,))
    ->addColumn('conditions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, null, array('unsigned' => true, 'nullable' => false,))
    ->addColumn('actions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, null, array('unsigned' => true, 'nullable' => false,))
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Description')
    ->addColumn('stores', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Stores')
    ->addColumn('cust_groups', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Customer Groups')
    ->addIndex(('enabled'), array('enabled'), array('enabled'))
    ->addIndex(('sort'), array('sort'), array('sort'))
    ->setComment('Amasty Fees Table');

$installer->getConnection()->createTable($table);

/**
 * Create table 'amcheckoutfees/fees_data'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('amcheckoutfees/feesData'))
    ->addColumn('fees_data_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true,
    ), 'Fee Data Id'
    )
    ->addColumn('fees_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('unsigned' => true, 'nullable' => false,), 'Related to specified Fee Id')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 1000, array('unsigned' => true, 'nullable' => false,))
    ->addColumn('sort', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('unsigned' => true, 'nullable' => false,))
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array('unsigned' => true, 'nullable' => false,))
    ->addColumn('price_type', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array('unsigned' => true, 'nullable' => false,))
    ->addColumn('is_default', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array('unsigned' => true, 'nullable' => false,))
    ->addIndex(('fees_id'), array('fees_id'), array('fees_id'))
    ->addIndex(('sort'), array('sort'), array('sort'))
    ->setComment('Amasty Fee Options Data Table');

$installer->getConnection()->createTable($table);


/**
 * Add attributes for sales entities
 */
$entityAttributesCodes = array(
    'amcheckoutfees_fees' => Varien_Db_Ddl_Table::TYPE_VARCHAR,
);
foreach ($entityAttributesCodes as $code => $type) {
    $installer->addAttribute('quote', $code, array('type' => $type, 'visible' => false));
    $installer->addAttribute('order', $code, array('type' => $type, 'visible' => false));
}


$entityAttributesCodes = array(
    'amcheckoutfees_amount'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'base_amcheckoutfees_amount' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
);
foreach ($entityAttributesCodes as $code => $type) {
    $installer->addAttribute('creditmemo', $code, array('type' => $type, 'visible' => false));
    $installer->addAttribute('quote_address', $code, array('type' => $type, 'visible' => false));
    $installer->addAttribute('order', $code, array('type' => $type, 'visible' => false));
    $installer->addAttribute('invoice', $code, array('type' => $type, 'visible' => false));
}

$entityAttributesCodes = array(
    'amcheckoutfees_amount_refunded'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'amcheckoutfees_amount_invoiced'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'base_amcheckoutfees_amount_refunded' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'base_amcheckoutfees_amount_invoiced' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
);
foreach ($entityAttributesCodes as $code => $type) {
    $installer->addAttribute('order', $code, array('type' => $type, 'visible' => false));
}


$session = Mage::getSingleton('admin/session');
$session->setReloadAclFlag(true);
$session->refreshAcl();


$this->endSetup(); 