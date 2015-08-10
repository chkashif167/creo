<?php

class Progos_Creomob_CheckoutController extends Mage_Core_Controller_Front_Action{
    
    public function indexAction(){
        
    }
    
    public function checkoutDetailsAction(){
        $sub_total = Mage::getSingleton('checkout/session')->getQuote()->getSubtotal();
        $currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
        $currency_symbol = Mage::app()->getLocale()->currency( $currency_code )->getSymbol();
        $items_count = Mage::helper('checkout/cart')->getItemsCount();
        
        $res['cart'] = array('sub_total'=>$sub_total,'currency_code'=>$currency_code,
            'currency_symbol'=>$currency_symbol,'items_count'=>$items_count);
        
        header("Content-Type: application/json");
        print_r(json_encode(array($res)));
        die;
    }
    
    

    public function getShippingAddressAction(){
        $cart = Mage::getSingleton('checkout/session')->getQuote();
        $address = $cart->getShippingAddress();
        
        $data['suffix'] = $address->getSuffix();
        $data['firstname'] = $address->getFirstname();
        $data['lastname'] = $address->getLastname();
        $data['email'] = $address->getEmail();
        $data['company'] = $address->getCompany();
        $data['street'] = $address->getStreet();
        $data['city'] = $address->getCity();
        $data['country_id'] = $address->getCountry_id();
        $data['region'] = $address->getRegion();
        $data['region_id'] = $address->getRegion_id();
        $data['postcode'] = $address->getPostcode();
        $data['telephone'] = $address->getTelephone();
        $data['fax'] = $address->getFax();
        
        header("Content-Type: application/json");
        print_r(json_encode($data));
        die;
    }
    
    public function shippingsetAction(){
        $cart = Mage::getSingleton('checkout/session')->getQuote();
        $shippingAddress = $cart->getShippingAddress();
        
        
        $firstname = ($this->getRequest()->getParam('firstname')!=null)?
                $this->getRequest()->getParam('firstname'):$shippingAddress->getFirstname();
        $middlename = ($this->getRequest()->getParam('middlename')!=null)?
                $this->getRequest()->getParam('middlename'):$shippingAddress->getMiddlename();
        $lastname = ($this->getRequest()->getParam('lastname')!=null)?
                $this->getRequest()->getParam('lastname'):$shippingAddress->getLastname();
        $suffix = ($this->getRequest()->getParam('suffix')!=null)?
                $this->getRequest()->getParam('suffix'):$shippingAddress->getSuffix();
        $company = ($this->getRequest()->getParam('company')!=null)?
                $this->getRequest()->getParam('company'):$shippingAddress->getCompany();
        $street = ($this->getRequest()->getParam('street')!=null)?
                $this->getRequest()->getParam('street'):$shippingAddress->getStreet();
        $city = ($this->getRequest()->getParam('city')!=null)?
                $this->getRequest()->getParam('city'):$shippingAddress->getCity();
        $country_id = ($this->getRequest()->getParam('country_id')!=null)?
                $this->getRequest()->getParam('country_id'):$shippingAddress->getCountry_id();
        $region = ($this->getRequest()->getParam('region')!=null)?
                $this->getRequest()->getParam('region'):$shippingAddress->getRegion();
        $region_id = ($this->getRequest()->getParam('region_id')!=null)?
                $this->getRequest()->getParam('region_id'):$shippingAddress->getRegion_id();
        $postcode = ($this->getRequest()->getParam('postcode')!=null)?
                $this->getRequest()->getParam('postcode'):$shippingAddress->getPostcode();
        $telephone = ($this->getRequest()->getParam('telephone')!=null)?
                $this->getRequest()->getParam('telephone'):$shippingAddress->getTelephone();
        $fax = ($this->getRequest()->getParam('fax')!=null)?
                $this->getRequest()->getParam('fax'):$shippingAddress->getFax();
        
        $shippingAddress
            ->setFirstname($firstname)
            ->setMiddlename($middlename)
            ->setLastname($lastname)
            ->setSuffix($suffix)
            ->setCompany($company)
            ->setStreet($street)
            ->setCity($city)
            ->setCountry_id($country_id)
            ->setRegion($region)
            ->setRegion_id($region_id)
            ->setPostcode($postcode)
            ->setTelephone($telephone)
            ->setFax($fax)->save();
    }
    
    public function paymentmethodsAction(){
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();
        foreach ($payments as $paymentCode=>$paymentModel) {
            $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
            $methods[$paymentCode] = array(
                'label'   => $paymentTitle,
                'value' => $paymentCode,
            );
            //echo $paymentTitle,':',$paymentCode,'<br>';
        }
        //echo '<br><br>';
        header("Content-Type: application/json");
        print_r(json_encode($methods));
        die;
    }
    
    public function currentpaymentmethodAction(){
        $method = Mage::getSingleton('checkout/session')->getQuote()->getPayment()->getMethodInstance();
        
        $title = $method->getTitle();
        $code = $method->getCode();
        $data = array(
            'title'=>$title,
            'code' =>$code
        );
        
        header("Content-Type: application/json");
        print_r(json_encode($data));
        die;
    }
    
    public function shippingmethodsAction(){
        $methods = Mage::getSingleton('shipping/config')->getActiveCarriers();
        foreach($methods as $_ccode => $_carrier)
        {
            $_methodOptions = array();
            if($_methods = $_carrier->getAllowedMethods())
            {
                foreach($_methods as $_mcode => $_method)
                {
                    $_code = $_ccode . '_' . $_mcode;
                    $_methodOptions[] = array('value' => $_code, 'label' => $_method);
                }

                if(!$_title = Mage::getStoreConfig("carriers/$_ccode/title"))
                    $_title = $_ccode;

                $options[] = array('value' => $_methodOptions, 'label' => $_title);
            }
        }
        //echo '<br><br>';
        header("Content-Type: application/json");
        print_r(json_encode($options));
        die;
    }
    
    public function processAction(){
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        
        
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        
        $addressData = array(
            'firstname' => 'kashif',
            'lastname' => 'Nadeem',
            'street' => '1',
            'city' => 'Islamabad',
            'postcode'=>'46000',
            'telephone' => '1234',
            'country_id' => 'PK',
            'region_id' => ''
        );
        $billingAddress = $quote->getBillingAddress()->addData($addressData);
        $shippingAddress = $quote->getShippingAddress()->addData($addressData);
        
        $shippingAddress->setCollectShippingRates(true)->collectShippingRates()->
            setShippingMethod('flatrate_flatrate')->setPaymentMethod('cashondelivery');
        
//        $quote->getPayment()->importData(array('method' => 'cashondelivery'));
//        $quote->collectTotals()->save();
//        $service = Mage::getModel('sales/service_quote', $quote);
//        $service->submitAll();
//        $order = $service->getOrder();
//        $order->setStatus('complete');
//        $order->save(); 
        
        $storeId = Mage::app()->getStore()->getId();
        $checkout = Mage::getSingleton('checkout/type_onepage');
        $checkout->initCheckout();
        $checkout->saveCheckoutMethod('register');
        $checkout->saveShippingMethod('flatrate_flatrate');

        $checkout->savePayment(array('method' => 'cashondelivery'));

        
        
        echo "<br>Processing checkout for customer -> ",$customer->getFirstname()," ",$customer->getLastname();
        echo "<br>Default billing is -> ",$customer->getDefaultBilling();
        echo "<br>Billing Address is -> ",$billingAddress->getFirstname(),' ',$billingAddress->getCity();
        echo "<br>Shipping Address is -> ",$shippingAddress->getFirstname(),' ',$shippingAddress->getCity();
        
        
        try {
            $checkout->saveOrder();
            
            echo '<br>order processed..';
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }

//        $cart->truncate();
//        $cart->save();
//        $cart->getItems()->clear()->save();
    }
    
}
