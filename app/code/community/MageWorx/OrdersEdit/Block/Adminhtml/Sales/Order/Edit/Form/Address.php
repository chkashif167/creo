<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_Edit_Form_Address extends Mage_Adminhtml_Block_Sales_Order_Address_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('mageworx/ordersedit/edit/address.phtml');
    }

    /**
     * Get shipping/billing address to edit
     * @return Mage_Sales_Model_Order_Address|Mage_Sales_Model_Quote_Address
     */
    protected function _getAddress()
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $this->getOrder();
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = Mage::getSingleton('mageworx_ordersedit/edit')->getQuoteByOrder($order);

        $blockId = Mage::app()->getRequest()->getParam('block_id');
        if ($blockId == 'billing_address') {
            return $quote->getBillingAddress();
        } else {
            return $quote->getShippingAddress();
        }
    }

    /**
     * Get customer who placed the order
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        $customerId = $this->getOrder()->getCustomerId();
        $customer = Mage::getModel('customer/customer')->load($customerId);

        return $customer;
    }

    /**
     * Prepare form to edit billing/shipping address
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        // Set custom renderer for VAT field if needed
        $vatIdElement = $this->_form->getElement('vat_id');
        if ($vatIdElement && $this->getDisplayVatValidationButton() !== false) {
            $vatIdElement->setRenderer(
                $this->getLayout()->createBlock('mageworx_ordersedit/adminhtml_sales_order_edit_form_address_vat')
                    ->setJsVariablePrefix($this->getJsVariablePrefix())
            );
        }

        $this->_form->setId('ordersedit_edit_form');

        return $this;
    }
}