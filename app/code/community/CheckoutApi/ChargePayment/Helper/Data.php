<?php

class CheckoutApi_ChargePayment_Helper_Data  extends Mage_Core_Helper_Abstract
{
    public function getConfigData($field,$section,$storeId = null)
    {
        if (null === $storeId) {
            $storeId = Mage::app()->getStore();
        }
        $path = "payment/$section/".$field;
        return Mage::getStoreConfig($path, $storeId);
    }

    public function getJsPath()
    {
       $mode =  $this->getConfigData('mode','creditcard');
        $js = '<script src="https://www.checkout.com/cdn/js/checkout.js" async ></script>';
       if($mode == 'sandbox') {
           $js ='<script src="http://sandbox.checkout.com/js/v1/checkout.js" async ></script>';
       }
        return $js;
    }
}