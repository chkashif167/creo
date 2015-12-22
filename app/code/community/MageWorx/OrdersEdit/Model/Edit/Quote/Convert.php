<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Model_Edit_Quote_Convert extends Mage_Core_Model_Abstract
{
    /**
     * Convert quote address to order address
     *
     * @param Mage_Sales_Model_Quote_Address $quoteAddress
     * @param Mage_Sales_Model_Order_Address $orderAddress
     * @return Mage_Sales_Model_Order_Address
     */
    public function addressToOrderAddress(Mage_Sales_Model_Quote_Address $quoteAddress, Mage_Sales_Model_Order_Address $orderAddress)
    {
        Mage::helper('core')->copyFieldset('sales_convert_quote_address', 'to_order_address', $quoteAddress, $orderAddress);

        Mage::dispatchEvent('sales_convert_quote_address_to_order_address',
            array('address' => $quoteAddress, 'order_address' => $orderAddress));

        return $orderAddress;
    }

    /**
     * Convert quote payment to order payemnt
     *
     * @param Mage_Sales_Model_Quote_Payment $payment
     * @param Mage_Sales_Model_Order_Payment $orderPayment
     * @return Mage_Sales_Model_Order_Payment
     */
    public function paymentToOrderPayment(Mage_Sales_Model_Quote_Payment $payment, Mage_Sales_Model_Order_Payment $orderPayment)
    {
        Mage::helper('core')->copyFieldset('sales_convert_quote_payment', 'to_order_payment', $payment, $orderPayment);

        Mage::dispatchEvent('sales_convert_quote_payment_to_order_payment',
            array('order_payment' => $orderPayment, 'quote_payment' => $payment));

        return $orderPayment;
    }


    /**
     * Convert quote item to order item. Most part of the code was taken from Mage_Sales_Model_Convert_Quote::itemToOrderItem()
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @param null|Mage_Sales_Model_Order_Item $orderItem
     * @return Mage_Sales_Model_Order_Item
     */
    public function itemToOrderItem(Mage_Sales_Model_Quote_Item_Abstract $item, $orderItem = null)
    {
        if (is_null($orderItem)) {
            $orderItem = Mage::getModel('sales/order_item');
        }

        $orderItem->setStoreId($item->getStoreId())
            ->setQuoteItemId($item->getId())
            ->setQuoteParentItemId($item->getParentItemId())
            ->setProductId($item->getProductId())
            ->setProductType($item->getProductType())
            ->setQtyBackordered($item->getBackorders())
            ->setProduct($item->getProduct())
            ->setBaseOriginalPrice($item->getBaseOriginalPrice())
        ;

        $options = $item->getProductOrderOptions();
        if (!$options) {
            $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
        }
        $orderItem->setProductOptions($options);
        Mage::helper('core')->copyFieldset('sales_convert_quote_item', 'to_order_item', $item, $orderItem);

        if ($item->getParentItem()) {
            $orderItem->setQtyOrdered($orderItem->getQtyOrdered()*$item->getParentItem()->getQty());
        }

        if (!$item->getNoDiscount()) {
            Mage::helper('core')->copyFieldset('sales_convert_quote_item', 'to_order_item_discount', $item, $orderItem);
        }

        Mage::dispatchEvent('sales_convert_quote_item_to_order_item',
            array('order_item'=>$orderItem, 'item'=>$item)
        );
        return $orderItem;
    }
}