<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_Coupons extends Mage_Adminhtml_Block_Widget//Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_coupons');
        $this->setTemplate('mageworx/ordersedit/coupons/form.phtml');
    }

    /**
     * @return null|string
     * @throws Exception
     */
    public function getCouponCode()
    {
        if ($this->getParentBlock()) {
            /** @var Mage_Sales_Model_Order $order */
            $order = $this->getParentBlock()->getOrder();
        } elseif ($orderId = $this->getRequest()->getParam('order_id')) {
            /** @var Mage_Sales_Model_Order $order */
            $order = Mage::getModel('sales/order')->load($orderId);
        } else {
            return null;
        }

        $couponCode = $order->getCouponCode();
        return $couponCode;
    }

}