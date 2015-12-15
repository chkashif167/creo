<?php
/**
 * MageWorx
 * Admin Order Grid  extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersGrid
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

@ini_set('max_execution_time', 1800);
$installer = $this;

/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
$tablePrefix = Mage::getConfig()->getTablePrefix();

// 1.0.0
if ($installer->getConnection()->isTableExists($tablePrefix . 'mageworx_orderspro_order_group') && !$installer->getConnection()->isTableExists($this->getTable('mageworx_ordersgrid/order_group'))) {
    $installer->getConnection()->renameTable($tablePrefix . 'mageworx_orderspro_order_group', $this->getTable('mageworx_ordersgrid/order_group'));
} elseif ($installer->getConnection()->isTableExists($tablePrefix . 'orderspro_order_group') && !$installer->getConnection()->isTableExists($this->getTable('mageworx_ordersgrid/order_group'))) {
    $installer->getConnection()->renameTable($tablePrefix . 'orderspro_order_group', $this->getTable('mageworx_ordersgrid/order_group'));
} else {
    $installer->run("
        -- DROP TABLE IF EXISTS {$this->getTable('mageworx_ordersgrid/order_group')};
        CREATE TABLE IF NOT EXISTS {$this->getTable('mageworx_ordersgrid/order_group')} (
          `order_group_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
          `title` varchar(50) NOT NULL,
          PRIMARY KEY (`order_group_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='OrdersPro Group';


        INSERT IGNORE INTO {$this->getTable('mageworx_ordersgrid/order_group')} (`order_group_id`, `title`) VALUES
        (1, 'Archived'),
        (2, 'Deleted');
    ");
}

if (!$installer->getConnection()->tableColumnExists($installer->getTable('sales/order_grid'), 'customer_email')) {
    $installer->run("ALTER TABLE `{$this->getTable('sales/order_grid')}` ADD `customer_email` varchar(255) DEFAULT NULL;");
}
if (!$installer->getConnection()->tableColumnExists($installer->getTable('sales/order_grid'), 'customer_group_id')) {
    $installer->run("ALTER TABLE `{$this->getTable('sales/order_grid')}` ADD `customer_group_id` smallint(5) DEFAULT NULL;");
}
if (!$installer->getConnection()->tableColumnExists($installer->getTable('sales/order_grid'), 'tax_amount')) {
    $installer->run("ALTER TABLE `{$this->getTable('sales/order_grid')}` ADD `tax_amount` decimal(12,4) NOT NULL DEFAULT 0;");
}
if (!$installer->getConnection()->tableColumnExists($installer->getTable('sales/order_grid'), 'total_qty_ordered')) {
    $installer->run("ALTER TABLE `{$this->getTable('sales/order_grid')}` ADD `total_qty_ordered` decimal(12,4) NOT NULL DEFAULT 0;");
}
if (!$installer->getConnection()->tableColumnExists($installer->getTable('sales/order_grid'), 'discount_amount')) {
    $installer->run("ALTER TABLE `{$this->getTable('sales/order_grid')}` ADD `discount_amount` decimal(12,4) NOT NULL DEFAULT 0;");
}
if (!$installer->getConnection()->tableColumnExists($installer->getTable('sales/order_grid'), 'coupon_code')) {
    $installer->run("ALTER TABLE `{$this->getTable('sales/order_grid')}` ADD `coupon_code` varchar(255) DEFAULT NULL;");
}
if (!$installer->getConnection()->tableColumnExists($installer->getTable('sales/order_grid'), 'total_refunded')) {
    $installer->run("ALTER TABLE `{$this->getTable('sales/order_grid')}` ADD `total_refunded` decimal(12,4) NOT NULL DEFAULT 0;");
}
if (!$installer->getConnection()->tableColumnExists($installer->getTable('sales/order_grid'), 'shipping_method')) {
    $installer->run("ALTER TABLE `{$this->getTable('sales/order_grid')}` ADD `shipping_method` varchar(255) NOT NULL DEFAULT '';");
}
if (!$installer->getConnection()->tableColumnExists($installer->getTable('sales/order_grid'), 'is_edited')) {
    $installer->run("ALTER TABLE `{$this->getTable('sales/order_grid')}` ADD `is_edited` tinyint(1) NOT NULL DEFAULT 0;");
}
if (!$installer->getConnection()->tableColumnExists($installer->getTable('sales/order_grid'), 'base_tax_amount')) {
    $installer->run("ALTER TABLE `{$this->getTable('sales/order_grid')}` ADD `base_tax_amount` decimal(12,4) NOT NULL DEFAULT 0;");
}
if (!$installer->getConnection()->tableColumnExists($installer->getTable('sales/order_grid'), 'base_discount_amount')) {
    $installer->run("ALTER TABLE `{$this->getTable('sales/order_grid')}` ADD `base_discount_amount` decimal(12,4) NOT NULL DEFAULT 0;");
}
if (!$installer->getConnection()->tableColumnExists($installer->getTable('sales/order_grid'), 'base_total_refunded')) {
    $installer->run("ALTER TABLE `{$this->getTable('sales/order_grid')}` ADD `base_total_refunded` decimal(12,4) NOT NULL DEFAULT 0;");
}
if (!$installer->getConnection()->tableColumnExists($installer->getTable('sales/order_grid'), 'shipping_description')) {
    $installer->run("ALTER TABLE `{$this->getTable('sales/order_grid')}` ADD `shipping_description` varchar(255) NOT NULL DEFAULT '';");
}
if (!$installer->getConnection()->tableColumnExists($installer->getTable('sales/order_grid'), 'weight')) {
    $installer->run("ALTER TABLE `{$this->getTable('sales/order_grid')}` ADD `weight` decimal(12,4) DEFAULT NULL;");
}

$installer->run("
    UPDATE `{$this->getTable('sales/order_grid')}` AS sog, `{$this->getTable('sales/order')}` AS so
        SET
            sog.`customer_email` = so.`customer_email`,
            sog.`customer_group_id` = so.`customer_group_id`,
            sog.`tax_amount` = IFNULL(so.`tax_amount`, 0),
            sog.`total_qty_ordered` = IFNULL(so.`total_qty_ordered`, 0),
            sog.`discount_amount` = IFNULL(so.`discount_amount`, 0),
            sog.`coupon_code` = so.`coupon_code`,
            sog.`total_refunded` = IFNULL(so.`total_refunded`, 0),
            sog.`shipping_method` = IFNULL(so.`shipping_method`, ''),
            sog.`base_tax_amount` = IFNULL(so.`base_tax_amount`, 0),
            sog.`base_discount_amount` = IFNULL(so.`base_discount_amount`, 0),
            sog.`base_total_refunded` = IFNULL(so.`base_total_refunded`, 0),
            sog.`shipping_description` = IFNULL(so.`shipping_description`, ''),
            sog.`weight` = so.`weight`
        WHERE
            sog.`entity_id` = so.`entity_id`
            ");

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

if (!$installer->getConnection()->tableColumnExists($installer->getTable('sales/order_grid'), 'order_group_id')) {
    $installer->getConnection()->addColumn(
        $installer->getTable('sales/order_grid'),
        'order_group_id',
        'tinyint(3) UNSIGNED NOT NULL DEFAULT 0'
    );
}

if (!$installer->getConnection()->tableColumnExists($installer->getTable('sales/order'), 'order_group_id')) {
    $installer->getConnection()->addColumn(
        $installer->getTable('sales/order'),
        'order_group_id',
        'tinyint(3) UNSIGNED NOT NULL DEFAULT 0'
    );
}

// Update config fields (rename old)
$installer->updateConfig();

$installer->endSetup();
