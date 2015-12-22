<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

@ini_set('max_execution_time', 1800);
$installer = $this;

/* @var $installer MageWorx_OrdersEdit_Model_Resource_Setup */

$installer->startSetup();
$tablePrefix = Mage::getConfig()->getTablePrefix();

// 1.0.0
if ($installer->getConnection()->isTableExists($tablePrefix . 'mageworx_orderspro_upload_files') && !$installer->getConnection()->isTableExists($this->getTable('mageworx_ordersedit/upload_files'))) {
    $installer->getConnection()->renameTable($tablePrefix . 'mageworx_orderspro_upload_files', $this->getTable('mageworx_ordersedit/upload_files'));
} elseif ($installer->getConnection()->isTableExists($tablePrefix . 'orderspro_upload_files') && !$installer->getConnection()->isTableExists($this->getTable('mageworx_ordersedit/upload_files'))) {
    $installer->getConnection()->renameTable($tablePrefix . 'orderspro_upload_files', $this->getTable('mageworx_ordersedit/upload_files'));
} else {
    $installer->run("
    -- DROP TABLE IF EXISTS {$this->getTable('mageworx_ordersedit/upload_files')};
    CREATE TABLE IF NOT EXISTS {$this->getTable('mageworx_ordersedit/upload_files')} (
      `entity_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
      `history_id` int(10) unsigned NOT NULL,
      `file_name` varchar(100) NOT NULL,
      `file_size` int(10) unsigned NOT NULL,
      PRIMARY KEY (`entity_id`),
      UNIQUE KEY `IDX_HISTORY` (`history_id`),
      CONSTRAINT `FK_ORDERSPRO_HISTORY_ID` FOREIGN KEY (`history_id`) REFERENCES `{$this->getTable('sales/order_status_history')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='OrdersEdit Upload Files';
    ");
}

if (!$installer->getConnection()->tableColumnExists($installer->getTable('sales/order'), 'is_edited')) {
    $installer->run("ALTER TABLE `{$this->getTable('sales/order')}` ADD `is_edited` tinyint(1) NOT NULL DEFAULT 0;");
}

// Customer Credit start:
if ($installer->getConnection()->tableColumnExists($installer->getTable('sales/order'), 'base_customer_credit_amount')) {
    if (!$installer->getConnection()->tableColumnExists($installer->getTable('sales/order_grid'), 'base_customer_credit_amount')) {
        $installer->run("ALTER TABLE `{$this->getTable('sales/order_grid')}` ADD `base_customer_credit_amount` decimal(12,4) NOT NULL DEFAULT 0;");
    }
    $installer->run("UPDATE `{$this->getTable('sales/order_grid')}` AS sog, `{$this->getTable('sales/order')}` AS so
        SET sog.`base_customer_credit_amount` = IFNULL(so.`base_customer_credit_amount`, 0)
        WHERE sog.`entity_id` = so.`entity_id`");
}

if ($installer->getConnection()->tableColumnExists($installer->getTable('sales/order'), 'customer_credit_amount')) {
    if (!$installer->getConnection()->tableColumnExists($installer->getTable('sales/order_grid'), 'customer_credit_amount')) {
        $installer->run("ALTER TABLE `{$this->getTable('sales/order_grid')}` ADD `customer_credit_amount` decimal(12,4) NOT NULL DEFAULT 0;");
    }
    $installer->run("UPDATE `{$this->getTable('sales/order_grid')}` AS sog, `{$this->getTable('sales/order')}` AS so
        SET sog.`customer_credit_amount` = IFNULL(so.`customer_credit_amount`, 0)
        WHERE sog.`entity_id` = so.`entity_id`");
}
// Customer Credit end.

// Add attribute to quote item
$salesInstaller = new Mage_Sales_Model_Resource_Setup('core_setup');
$salesInstaller->addAttribute(
    'quote_item',
    'ordersedit_is_temporary',
    array(
        'type' => 'int',
        'nullable' => true,
        'grid' => false,
    )
);
$salesInstaller->endSetup();

// Update config fields (rename old)
$installer->updateConfig();

$installer->endSetup();