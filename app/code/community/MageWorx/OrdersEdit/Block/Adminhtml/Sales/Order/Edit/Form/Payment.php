<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_Edit_Form_Payment extends Mage_Adminhtml_Block_Sales_Order_Create_Billing_Method_Form
{
    /**
     * Prepare layout for payment method form
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setTemplate('mageworx/ordersedit/edit/payment_method.phtml');
        return $this;
    }

    /**
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        /** @var Mage_Sales_model_Order $order */
        $order = $this->getOrder() ? $this->getOrder() : Mage::registry('ordersedit_order');
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = Mage::getSingleton('mageworx_ordersedit/edit')->getQuoteByOrder($order);

        return $quote;
    }
}