<?php

class CheckoutApi_ChargePayment_Block_Form_Creditcard  extends Mage_Payment_Block_Form_Cc
 {
     /**
      * setting up block template
      */
    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('chargepayment/form/creditcard.phtml');
    }

    private function _getQuote()
    {
        return  Mage::getSingleton('checkout/session')->getQuote();
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


    public function getConfigData($field, $storeId = null)
    {
        return Mage::helper('checkoutapi_chargePayment')->getConfigData($field,'creditcard',$storeId);;
    }

    public  function getPublicKey()
    {
        return $this->getConfigData('publickey');
    }
     public  function getLightBoxUrl()
    {
        return $this->getConfigData('icon_url');
    }
    public  function getThemeColor()
    {
        if($theme = $this->getConfigData('theme_color')) {
            return '#'.$theme;
        }
        return null;
    }
    public  function getButtonColor()
    {
        if($button_color = $this->getConfigData('button_color')) {
            return '#'.$button_color;
        }
        return null;
    }
    public  function getIconColor()
    {
        if($icon_color = $this->getConfigData('icon_color')) {
            return '#'.$icon_color;
        }
        return null;
    }
    public  function getUseCurrencyCode()
    {
        return $this->getConfigData('use_currency_code')?true:false;
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
        $helper = Mage::helper('customer');
        if($helper->isLoggedIn())    {

            $customer = $helper->getCustomer();
            return  $customer->getEmail();
        }
        return  $this->_getQuote()->getBillingAddress()->getEmail();
    }

    public function getName()
    {
        $helper = Mage::helper('customer');
        if($helper->isLoggedIn())    {
            $customer = $helper->getCustomer();
            $customerName = $customer->getFirstname() .' '.$customer->getLastname();
            return  $customerName;
        }

        return  $this->_getQuote()->getBillingAddress()->getName();
    }


    public function getStoreName()
    {
       return  Mage::app()->getStore()->getName();
    }

    public function getPaymentTokenResult($orderid = null)
    {

        $Api = CheckoutApi_Api::getApi(array('mode'=>$this->getConfigData('mode')));
        $secretKey  = $this->getConfigData('privatekey');
        $billingAddress = $this->_getQuote()->getBillingAddress();
        $shippingAddress = $this->_getQuote()->getBillingAddress();
        $orderedItems = $this->_getQuote()->getAllItems();
        $currencyDesc = $this->_getQuote()->getBaseCurrencyCode();
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
        $config['authorization'] = $secretKey ;
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
            'metadata'          =>   array(
                'server'  => Mage::helper('core/http')->getHttpUserAgent(),
                'quoteId' => $this->_getQuote()->getId()
            )
        );

        $config['postedParam'] = array (
            'trackId'           => $orderid,
            'value'             =>    $amountCents,
            "chargeMode"        =>    1,
            'currency'          =>    $currencyDesc,
            'shippingDetails'   =>    $shippingAddressConfig,
            'products'          =>    $products,

        );

        if($this->getConfigData('order_status_capture') == Mage_Paygate_Model_Authorizenet::ACTION_AUTHORIZE ) {
            $config['postedParam']['autoCapture']  = CheckoutApi_Client_Constant::AUTOCAPUTURE_AUTH;
            $config['postedParam']['autoCapTime']  = 0;
        } else {
            $config['postedParam']['autoCapture']  = CheckoutApi_Client_Constant::AUTOCAPUTURE_CAPTURE;
            $config['postedParam']['autoCapTime']  = $this->getConfigData('auto_capture_time');
        }
       $paymentTokenCharge = $Api->getPaymentToken($config);
        $paymentTokenReturn    =   array(
                                    'succes'  => false,
                                    'token'   => '',
                                    'message' => ''
                                  );
        $paymentToken = '';
        if($paymentTokenCharge->isValid()){
            $paymentToken = $paymentTokenCharge->getId();
            $paymentTokenReturn['token'] = $paymentToken ;
            $paymentTokenReturn['succes'] = true;
        }else {
          //  $paymentTokenCharge->printError();
        }

        if(!$paymentToken) {
            $paymentTokenReturn['succes'] = false;
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