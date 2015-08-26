<?php

class CheckoutApi_ChargePayment_Block_Form_Creditcardpci  extends Mage_Payment_Block_Form_Cc
 {
     /**
      * setting up block template
      */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('chargepayment/form/creditcardpci.phtml');

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

 }