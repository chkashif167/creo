<?php

class CheckoutApi_ChargePayment_Block_Form_Localpaymnet  extends Mage_Payment_Block_Form
 {
     /**
      * setting up block template
      */
    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('chargepayment/form/localpayment.phtml');

    }

    /**
     * Retrieve payment configuration object
     *
     * @return Mage_Payment_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('checkoutapi_chargePayment/config');
    }

    public function simulateChargeToken()
    {
//
    }

    public function getConfigData($field, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->getStore();
        }
        $path = 'payment/localpayment/'.$field;
        return Mage::getStoreConfig($path, $storeId);
    }



 }