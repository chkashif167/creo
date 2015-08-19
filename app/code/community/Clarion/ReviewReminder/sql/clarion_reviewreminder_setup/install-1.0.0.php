<?php
/**
 * @category   Clarion
 * @package    Clarion_ReviewReminder
 * @created    28th Nov, 2014
 * @author     Clarion magento team <magento.team@clariontechnologies.co.in>
 * @purpose    Review reminder installation script 
 * @copyright  Copyright (c) 2014 Clarion Technologies Pvt. Ltd.
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License
 */

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
/**
 * Prepare database for install
 */
$installer->startSetup();

/**
 * Create table 'clarion_review_reminder'
 */
$table = $installer->getConnection()
        ->newTable($installer->getTable('clarion_reviewreminder/reviewreminder'))
         ->addColumn('reminder_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Reminder Id')
        ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Order id')
        ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Customer Id')
        ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Product Id')
        ->addColumn('is_reminder_sent', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is reminder sent')
        ->addColumn('is_review_added', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is review added')
        ->addColumn('reminder_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Reminder count')
        ->addColumn('sent_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'default'   => NULL,
        ), 'Reminder sent time')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'default'   => NULL,
        ), 'Creation Time')
        ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'default'   => NULL,
        ), 'Update Time')
        ->setComment('Clarion reviewreminder Table');
$installer->getConnection()->createTable($table);
$installer->endSetup();