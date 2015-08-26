<?php
 class CheckoutApi_ChargePayment_Block_Checkoutjs extends Mage_Core_Block_Template
{
     private function _getQuote()
     {
         return  Mage::getSingleton('checkout/session')->getQuote();
     }

     public  function getPublicKey()
     {
         return $this->getConfigData('publickey');
     }

     public function getAmount()
     {
         return   $this->_getQuote()->getGrandTotal()*100;

     }

     public function getCurrency()
     {
         return   Mage::app()->getStore()->getCurrentCurrencyCode();

     }

     public function getEmailAddress()
     {
         return  $this->_getQuote()->getBillingAddress()->getEmail();

     }

     public function getName()
     {
         return  $this->_getQuote()->getBillingAddress()->getFirstname(). ' '. $this->_getQuote()->getBillingAddress()->getLastname();

     }

     public function getConfigData($field, $storeId = null)
     {
         if (null === $storeId) {
             $storeId = $this->getStore();
         }
         $path = 'payment/creditcard/'.$field;
         return Mage::getStoreConfig($path, $storeId);
     }

     public function getStoreName()
     {
         return  Mage::app()->getStore()->getName();
     }



     public function isSelected()
     {

        return $this->_getQuote()->getPayment()->getMethod() == 'creditcard';
     }
}