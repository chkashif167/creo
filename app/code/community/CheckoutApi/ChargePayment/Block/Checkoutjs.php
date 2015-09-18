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
     
     public function getPaymentTokenResult($orderid = null)
     {

         $Api = CheckoutApi_Api::getApi(array('mode'=>$this->getConfigData('mode')));
         $secretKey = $this->getConfigData('privatekey');
         $billingAddress = $this->_getQuote()->getBillingAddress();
         $shippingAddress = $this->_getQuote()->getBillingAddress();
         $orderedItems = $this->_getQuote()->getAllItems();
         $currencyDesc =  Mage::app()->getStore()->getCurrentCurrencyCode();
         $amountCents = $this->getAmount();
         $street = Mage::helper('customer/address')
             ->convertStreetLines($shippingAddress->getStreet(), 2);
         $shippingAddressConfig = array(
             'addressLine1'       =>     $street[0],
             'addressLine2'       =>     $street[1],
             'postcode'           =>     $shippingAddress->getPostcode(),
             'country'            =>     $shippingAddress->getCountry(),
             'city'               =>     $shippingAddress->getCity(),
             // 'phone'              =>     array('number' => $shippingAddress->getTelephone())

         );

         $products = array();
         foreach ($orderedItems as $item ) {
             $product = Mage::getModel('catalog/product')->load($item->getProductId());
             $products[] = array (
                 'name'       =>     $item->getName(),
                 'sku'        =>     $item->getSku(),
                 'price'      =>     $item->getPrice(),
                 'quantity'   =>     $item->getQty(),
                 'image'      =>     Mage::helper('catalog/image')->init($product, 'image')->__toString()
             );
         }

         $config = array();
         $config['authorization'] = $secretKey  ;
         $config['mode'] = $this->getConfigData('mode');
         $config['timeout'] = $this->getConfigData('timeout');
         $street = Mage::helper('customer/address')
             ->convertStreetLines($billingAddress->getStreet(), 2);
         $billingAddressConfig = array(
             'addressLine1'   =>    $street[0],
             'addressLine2'   =>    $street[1],
             'postcode'       =>    $billingAddress->getPostcode(),
             'country'        =>    $billingAddress->getCountry(),
             'city'           =>    $billingAddress->getCity(),
             'phone'          =>    array('number' => $billingAddress->getTelephone()),
         );

         $config['postedParam'] = array (
             'value'             =>    $amountCents,
             "chargeMode"        =>    1,
             'currency'          =>    $currencyDesc,
             'shippingDetails'   =>    $shippingAddressConfig,
             'products'          =>    $products,
             'metadata'          =>   array(
                 'server'  => Mage::helper('core/http')->getHttpUserAgent(),
                 'quoteId' => $this->_getQuote()->getId()

             )
         );

         if($this->getConfigData('payment_action') == Mage_Paygate_Model_Authorizenet::ACTION_AUTHORIZE ) {
             $config['postedParam']['autoCapture']  = CheckoutApi_Client_Constant::AUTOCAPUTURE_AUTH;
             $config['postedParam']['autoCapTime']  = 0;
         } else {
             $config['postedParam']['autoCapture']  = CheckoutApi_Client_Constant::AUTOCAPUTURE_CAPTURE;
             $config['postedParam']['autoCapTime']  = $this->getConfigData('auto_capture_time');
         }
         $paymentTokenCharge = $Api->getPaymentToken($config);
         $paymentTokenReturn    =   array(
             'success'  => false,
             'token'   => '',
             'message' => '',
             'value' => $amountCents
         );
         $paymentToken = '';
         if($paymentTokenCharge->isValid()){
             $paymentToken = $paymentTokenCharge->getId();
             $paymentTokenReturn['token'] = $paymentToken ;
             $paymentTokenReturn['success'] = true;
         }else {
             $paymentTokenCharge->printError();
         }

         if(!$paymentToken) {
             $paymentTokenReturn['success'] = false;
             if($paymentTokenCharge->getEventId()) {
                 $eventCode = $paymentTokenCharge->getEventId();
             }else {
                 $eventCode = $paymentTokenCharge->getErrorCode();
             }
             $paymentTokenReturn['message'] = Mage::helper('payment')->__( $paymentTokenCharge->getExceptionState()->getErrorMessage().
                 ' ( '.$eventCode.')');
         }

         return $paymentTokenReturn;

     }
}