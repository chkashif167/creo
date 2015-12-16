<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_Edit_Form_Coupons extends Mage_Adminhtml_Block_Widget //MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_Coupons
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_coupons');
        $this->setTemplate('mageworx/ordersedit/edit/coupons.phtml');
    }

    /**
     * @return string
     */
    public function getCouponCode()
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $this->getOrder();
        $changes = Mage::helper('mageworx_ordersedit/edit')->getPendingChanges($order->getEntityId());
        if (isset($changes['coupon_code']))
        {
            $couponCode = $changes['coupon_code'];
        } else {
            $couponCode = $order->getCouponCode();
            if (!$couponCode) {
                $couponCode = '';
            }
        }

        return $couponCode;
    }

}