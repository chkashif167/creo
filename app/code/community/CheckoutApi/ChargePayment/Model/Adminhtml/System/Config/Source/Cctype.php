<?php
class CheckoutApi_ChargePayment_Model_Adminhtml_System_Config_Source_Cctype
{
    public function toOptionArray()
    {
        $options =  array();

        foreach (Mage::getSingleton('checkoutapi_chargePayment/config')->getCcTypes() as $code => $name) {
            $options[] = array(
                'value' => $code,
                'label' => $name
            );
        }

        return $options;
    }
}