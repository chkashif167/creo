<?php
/**
 * MageWorx
 * Admin Order Grid  extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersGrid
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersGrid_Model_System_Config_Backend_Menu extends Mage_Core_Model_Config_Data
{

    const ENABLED_MENU_ORDERS = 'mageworx_ordersmanagement/ordersgrid/enabled_menu_orders';

    protected function _afterSave() {
        // Remove standard orders link from menu if ordersgrid is enabled
        $enabled = $this->getData('groups/ordersgrid/fields/enabled/value');
        $value = $enabled ? 0 : 1;
        try {
            Mage::getModel('core/config_data')
                ->load(self::ENABLED_MENU_ORDERS, 'path')
                ->setValue($value)
                ->setPath(self::ENABLED_MENU_ORDERS)
                ->save();
                
            // check db setup
            $resource = Mage::getSingleton('core/resource');
            $connection = $resource->getConnection('core_write');
            if (!$connection->tableColumnExists($resource->getTableName('sales/order'), 'order_group_id')) {
                $connection->delete($resource->getTableName('core/resource'), "code =  'mageworx_ordersgrid_setup'");
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }
}
