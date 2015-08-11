<?php
    require_once 'app/Mage.php';
    umask(0);
    Mage::app("default");
    Mage::getSingleton('core/session', array('name' =--> 'frontend'));
    $_customer = Mage::getSingleton('customer/session')->getCustomer();
    if(!$_customer->isLoggedIn()){ die(); } //If someone is hitting this file and is not a logged in customer, kill the script (security level 1). You can remove this if you want, but WTF!
 
    $product_id = $_POST['id']; //id of product we want to purchase that was posted to this script
 
    //Shipping / Billing information gather
    $firstName = $_customer_data->getFirstname(); //get customers first name
    $lastName = $_customer_data->getLastname(); //get customers last name
    $customerAddressId = Mage::getSingleton('customer/session')->getCustomer()->getDefaultBilling(); //get default billing address from session
 
    //if we have a default billing addreess, try gathering its values into variables we need
    if ($customerAddressId){
        $address = Mage::getModel('customer/address')->load($customerAddressId);
        $street = $address->getStreet();
        $city = $address->getCity();
        $postcode = $address->getPostcode();
        $phoneNumber = $address->getTelephone();
        $countryId = $address->getCountryId();
        $regionId = $address->getRegionId();
    // otherwise, setup some custom entry values so we don't have a bunch of confusing un-descriptive orders in the backend
    }else{
        $address = 'No address';
        $street = 'No street';
        $city = 'No City';
        $postcode = 'No post code';
        $phoneNumber = 'No phone';
        $countryId = 'No country';
        $regionId = 'No region';       
    }
 
    //Start a new order quote and assign current customer to it.
    $quote = Mage::getModel('sales/quote')->setStoreId(Mage::app('default')->getStore('default')->getId());
    $quote->assignCustomer($_customer);
    
    
    
    
 
//Now we get all items in the cart to make sure the purchase is legit
$session = Mage::getSingleton('checkout/session');
$items = $session->getQuote()->getAllVisibleItems();
$foundProducts = 'false'; //extra helper to exit the script if failed
 
//Now we must loop over all the found items in the cart and scrub each one against our incoming product id
foreach ($items as $item) {//<- plural to singular , becareful
    $item_to_product = Mage::getModel('catalog/product')->loadByAttribute('name',$item->getName());
    if($item_to_product->getId() == $product_id){
        $foundProduct = 'true';
    }
 
}
//If foundProducts is still false, we break from the rest of the script
if($foundProduct == 'false'){ die(); }
    
    
    
 
    //Low lets setup a shipping / billing array of current customer's session
    $addressData = array(
        'firstname' => $firstName,
        'lastname' => $lastName,
        'street' => $street,
        'city' => $city,
        'postcode'=>$postcode,
        'telephone' => $phoneNumber,
        'country_id' => $countryId,
        'region_id' => $regionId
    );
    //Add address array to both billing AND shipping address objects.  
    $billingAddress = $quote->getBillingAddress()->addData($addressData);
    $shippingAddress = $quote->getShippingAddress()->addData($addressData);
 
    //Set shipping objects rates to true to then gather any accrued shipping method costs a product main contain
    $shippingAddress->setCollectShippingRates(true)->collectShippingRates()->
    setShippingMethod('flatrate_flatrate')->setPaymentMethod('checkmo');
 
    //Set quote object's payment method to check / money order to allow progromatic entries of orders
    //(kind of hard to programmatically guess and enter a customer's credit/debit cart so only money orders are allowed to be entered via api)
    $quote->getPayment()->importData(array('method' => 'checkmo'));
 
    //Save collected totals to quote object
    $quote->collectTotals()->save();
 
    //Feed quote object into sales model
    $service = Mage::getModel('sales/service_quote', $quote);
 
    //submit all orders to MAGE
    $service->submitAll();
 
    //Setup order object and gather newly entered order
    $order = $service->getOrder();
 
    //Now set newly entered order's status to complete so customers can enjoy their goods.
        //(optional of course, but most would like their orders created this way to be set to complete automagicly)
    $order->setStatus('complete');
 
    //Finally we save our order after setting it's status to complete.
    $order->save();     
    
    
    
    //We must remove the product from the customers cart now so they can't re-order the same product (unless we want that at some point).
$cartHelper = Mage::helper('checkout/cart');
$session = Mage::getSingleton('checkout/session');
$items = $session->getQuote()->getAllVisibleItems();
foreach ($items as $item) {
    $item_to_product = Mage::getModel('catalog/product')->loadByAttribute('name',$item->getName());
    if($item_to_product->getId() == $product_id){
        $itemId = $item->getItemId();
        $cartHelper->getCart()->removeItem($itemId)->save();
    }
}
?>