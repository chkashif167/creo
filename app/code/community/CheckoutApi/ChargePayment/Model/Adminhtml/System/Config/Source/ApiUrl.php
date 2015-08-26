<?php
class CheckoutAPi_ChargePayment_Model_Adminhtml_System_Config_Source_APiUrl
{
    public function toOptionArray()
    {
        $options = array();

        foreach(Mage::getSingleton('checkoutapi_chargePayment/config')->getApiUrlType()as $code => $name) {
            $options[] = array(
                'value' => $code,
                'label' => $name
            );
        }

        return $options;
    }
}