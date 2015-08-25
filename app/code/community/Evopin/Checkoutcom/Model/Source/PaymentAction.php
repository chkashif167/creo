<?php

class Evopin_Checkoutcom_Model_Source_PaymentAction
{
    public function toOptionArray()
    {
        return array(
		    array(
                'value' => Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE,
                'label' => Mage::helper('checkoutcom')->__('Authorize Only')
            ),
            array(
                'value' => Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE,
                'label' => Mage::helper('checkoutcom')->__('Authorize and Capture')
            ),
        );
    }
}