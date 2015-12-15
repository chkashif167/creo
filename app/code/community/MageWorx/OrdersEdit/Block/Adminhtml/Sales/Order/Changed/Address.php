<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_Changed_Address extends Mage_Adminhtml_Block_Template
{
    /** @var string  */
    protected $_template = 'mageworx/ordersedit/changed/address.phtml';

    /**
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getAddress()
    {
        /** @var Mage_Sales_model_Quote $quote */
        $quote = $this->getQuote();
        $address = ($this->getAddressType() == 'shipping') ? $quote->getShippingAddress() : $quote->getBillingAddress();

        return $address;
    }
}