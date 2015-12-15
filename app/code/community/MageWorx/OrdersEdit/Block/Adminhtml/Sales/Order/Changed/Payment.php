<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_Changed_Payment extends Mage_Adminhtml_Block_Sales_Order_Payment
{
    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->setPayment($this->getQuote()->getPayment());
        return $this;
    }
}