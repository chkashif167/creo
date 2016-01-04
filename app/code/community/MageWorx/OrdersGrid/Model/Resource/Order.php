<?php
/**
 * MageWorx
 * Admin Order Grid  extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersGrid
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersGrid_Model_Resource_Order extends Mage_Sales_Model_Resource_Order
{

    /**
     * @param Mage_Sales_Model_Order $order
     * @throws Exception
     */
    public function deleteOrderCompletely(Mage_Sales_Model_Order $order)
    {

        /** @var Mage_Core_Model_Resource $coreResource */
        $coreResource = Mage::getSingleton('core/resource');
        $write = $this->_getWriteAdapter();
        $orderId = $order->getId();

        // delete
        if ($order->getQuoteId()) {
            $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_quote') . "` WHERE `entity_id`=" . $order->getQuoteId());
            $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_quote_address') . "` WHERE `quote_id`=" . $order->getQuoteId());
            $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_quote_item') . "` WHERE `quote_id`=" . $order->getQuoteId());
            $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_quote_payment') . "` WHERE `quote_id`=" . $order->getQuoteId());
        }

        $order->delete();

        $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_order_grid') . "` WHERE `entity_id`=" . $orderId);
        $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_order_address') . "` WHERE `parent_id`=" . $orderId);
        $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_order_item') . "` WHERE `order_id`=" . $orderId);
        $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_order_payment') . "` WHERE `parent_id`=" . $orderId);
        $write->query("DELETE FROM `" . $coreResource->getTableName('sales_payment_transaction') . "` WHERE `order_id`=" . $orderId);
        $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_order_status_history') . "` WHERE `parent_id`=" . $orderId);

        $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_invoice') . "` WHERE `order_id`=" . $orderId);
        $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_creditmemo') . "` WHERE `order_id`=" . $orderId);
        $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_shipment') . "` WHERE `order_id`=" . $orderId);
        $write->query("DELETE FROM `" . $coreResource->getTableName('sales_order_tax') . "` WHERE `order_id`=" . $orderId);

        if (Mage::getConfig()->getModuleConfig('AW_Booking')->is('active', true)) {
            $write->query("DELETE FROM `" . $coreResource->getTableName('aw_booking_orders') . "` WHERE `order_id`=" . $orderId);
        }
    }

    /**
     * @param $orderId
     */
    public function deleteInvoiceAndShipment($orderId)
    {
        /** @var Mage_Core_Model_Resource $coreResource */
        $coreResource = Mage::getSingleton('core/resource');
        $write = $this->_getWriteAdapter();

        $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_shipment') . "` WHERE `order_id` = " . $orderId);
        $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_shipment_grid') . "` WHERE `order_id` = " . $orderId);

        $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_invoice') . "` WHERE `order_id` = " . $orderId);
        $write->query("DELETE FROM `" . $coreResource->getTableName('sales_flat_invoice_grid') . "` WHERE `order_id` = " . $orderId);

        $write->query("UPDATE `" . $coreResource->getTableName('sales_flat_order_item') . "` SET `qty_invoiced` = 0, `qty_shipped` = 0 WHERE `order_id` = " . $orderId);
        $write->query("UPDATE `" . $coreResource->getTableName('sales_flat_order') . "` SET `shipping_invoiced` = 0, `base_shipping_invoiced` = 0 WHERE `entity_id` = " . $orderId);
    }
}