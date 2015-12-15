<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Model_SalesRule_Quote_Discount extends MageWorx_OrdersEdit_Model_SalesRule_Quote_Discount_Abstract
{

    /**
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @return Mage_SalesRule_Model_Quote_Discount
     */
    protected function _aggregateItemDiscount($item) {
        $this->_transferOldItemDiscount($item); //transfer old discount
        return parent::_aggregateItemDiscount($item);
    }

    /**
     * @param $item
     * @return $this
     */
    protected function _transferOldItemDiscount($item) {        
        if (!Mage::helper('mageworx_ordersedit')->isEnabled()) {
            return $this;
        }
        $orderId = Mage::getSingleton('adminhtml/session_quote')->getOrderId();
        if (!$orderId) {
            return $this;
        }

        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($orderId);
        if (!$order->getId()) {
            return $this;
        }
        
        $data = Mage::app()->getRequest()->getPost('order');
        if (isset($data['coupon']['code']) && empty($data['coupon']['code'])) {
            Mage::getSingleton('adminhtml/session_quote')->setCouponCodeIsDeleted(true);
        }

        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $item->getQuote();
        // if new coupon or deleted - discount do not touch
        if (Mage::getSingleton('adminhtml/session_quote')->getCouponCodeIsDeleted() || ($quote->getCouponCode() && $quote->getCouponCode()!=$order->getCouponCode())) {
            return $this;
        }
        
        $quote->setAppliedRuleIds($order->getAppliedRuleIds());
        
        
        $orderItems = $order->getAllItems();
        /** @var Mage_Sales_Model_Order_Item $orderItem */
        foreach ($orderItems as $orderItem) {
            if ($orderItem->getProductId()==$item->getProductId()) {
                if ($orderItem->getAppliedRuleIds() && $orderItem->getBaseDiscountAmount()>0) {
                    $baseDiscount = ($orderItem->getBaseDiscountAmount() / $orderItem->getQtyOrdered()) * $item->getQty();
                    $discount = ($orderItem->getDiscountAmount() / $orderItem->getQtyOrdered()) * $item->getQty();                    
                    $item->setBaseDiscountAmount($baseDiscount)->setDiscountAmount($discount)->setAppliedRuleIds($orderItem->getAppliedRuleIds());
                } else {
                    $item->setBaseDiscountAmount(0)->setDiscountAmount(0)->setAppliedRuleIds(null);
                }                
                return $this;        
            }
        }
    }
    
    
}
