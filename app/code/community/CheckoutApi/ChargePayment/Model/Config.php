<?php
class CheckoutApi_ChargePayment_Model_Config extends Mage_Payment_Model_Config
{

    /**
     * Retrieve array of credit card types
     *
     * @return array
     */
    public function getCcTypes()
    {
        $_types = Mage::getConfig()->getNode('global/payment/checkout_cc/types')->asArray();
        uasort($_types, array('Mage_Payment_Model_Config', 'compareCcTypes'));
        $types = array();
        foreach ($_types as $data) {
            if (isset($data['code']) && isset($data['name'])) {
                $types[$data['code']] = $data['name'];
            }
        }
        return $types;
    }

    /**
     * Retrieve array of mode type
     *
     * @return array
     */
    public function getApiUrlType()
    {
        $_types = Mage::getConfig()->getNode('global/payment/checkout_apiUrl/types')->asArray();
        $types = array();
        foreach ($_types as $data) {
            if (isset($data['code']) && isset($data['name'])) {
                $types[$data['code']] = $data['name'];
            }
        }
        return $types;
    }
} 