<?php
class CheckoutApi_ChargePayment_Model_Adminhtml_System_Config_Source_PaymentAction
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage_Paygate_Model_Authorizenet::ACTION_AUTHORIZE,
                'label' => Mage::helper('checkoutapi_chargePayment')->__('Authorize Only')
            ),
            array(
                'value' => Mage_Paygate_Model_Authorizenet::ACTION_ORDER,
                'label' => Mage::helper('checkoutapi_chargePayment')->__('Authorize and Capture')
            ),
        );
    }
}