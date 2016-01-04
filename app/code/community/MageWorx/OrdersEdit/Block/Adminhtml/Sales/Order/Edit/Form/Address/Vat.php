<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_Edit_Form_Address_Vat extends Mage_Adminhtml_Block_Customer_Sales_Order_Address_Form_Renderer_Vat
{
    /**
     * Prepare button to validate VAT number
     *
     * @return Mage_Adminhtml_Block_Widget_Button|null|Varien_Object
     */
    public function getValidateButton()
    {
        if (is_null($this->_validateButton)) {
            /** @var $form Varien_Data_Form */
            $form = $this->_element->getForm();

            $vatElementId = $this->_element->getHtmlId();

            $countryElementId = $form->getElement('country_id')->getHtmlId();
            $validateUrl = Mage::getSingleton('adminhtml/url')
                ->getUrl('adminhtml/customer_system_config_validatevat/validateAdvanced');

            $groupSuggestionMessage = Mage::helper('customer')->__('The customer is currently assigned to Customer Group %s.')
                . ' ' . Mage::helper('customer')->__('Would you like to change the Customer Group for this order?');

            $vatValidateOptions = Mage::helper('core')->jsonEncode(array(
                'vatElementId' => $vatElementId,
                'countryElementId' => $countryElementId,
                'groupIdHtmlId' => 'group_id',
                'validateUrl' => $validateUrl,
                'vatValidMessage' => Mage::helper('customer')->__('The VAT ID is valid. The current Customer Group will be used.'),
                'vatValidAndGroupChangeMessage' => Mage::helper('customer')->__('Based on the VAT ID, the customer would belong to the Customer Group %s.')
                    . "\n" . $groupSuggestionMessage,
                'vatInvalidMessage' => Mage::helper('customer')->__('The VAT ID entered (%s) is not a valid VAT ID. The customer would belong to Customer Group %s.')
                    . "\n" . $groupSuggestionMessage,
                'vatValidationFailedMessage'    => Mage::helper('customer')->__('There was an error validating the VAT ID. The customer would belong to Customer Group %s.')
                    . "\n" . $groupSuggestionMessage,
                'vatErrorMessage' => Mage::helper('customer')->__('There was an error validating the VAT ID.')
            ));

            $optionsVarName = $this->getJsVariablePrefix() . 'VatParameters';
            $beforeHtml = '<script type="text/javascript">var ' . $optionsVarName . ' = ' . $vatValidateOptions
                . ';</script>';
            $this->_validateButton = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'label'       => Mage::helper('customer')->__('Validate VAT Number'),
                'before_html' => $beforeHtml,
                'onclick'     => 'order.validateVat(' . $optionsVarName . ')',
                'class'       => 'mw_br'
            ));
        }
        return $this->_validateButton;
    }
}