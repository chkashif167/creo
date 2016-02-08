<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced Reports
 * @version   1.0.1
 * @build     539
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


$installer = $this;
$installer->startSetup();
try {
    $installer->run("CREATE INDEX product_id ON {$this->getTable('sales/order_item')} (product_id);");
    $installer->run("CREATE INDEX created_at ON {$this->getTable('sales/order_item')} (created_at);");
    $installer->run("CREATE INDEX parent_item_id ON {$this->getTable('sales/order_item')} (parent_item_id);");
} catch (Exception $e) {
}
$installer->endSetup();
