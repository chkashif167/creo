<?php
/**
 * Fetchr
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * https://fetchr.zendesk.com/hc/en-us/categories/200522821-Downloads
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to ws@fetchr.us so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Fetchr Magento Extension to newer
 * versions in the future. If you wish to customize Fetchr Magento Extension (Fetchr Shipping) for your
 * needs please refer to http://www.fetchr.us for more information.
 *
 * @author     Islam Khalil
 * @package    Fetchr Shipping
 * Used in creating options for fulfilment|delivery config value selection
 * @copyright  Copyright (c) 2015 Fetchr (http://www.fetchr.us)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Fetchr_Shipping_Model_Observer{

    public function getCCTrackingNo($observer) {
        //Check IF the Auto Push Is Enabled
        $autoCCPush     = Mage::getStoreConfig('carriers/fetchr/autoccpush');
        $invoice        = $observer->getEvent()->getInvoice();
        $order          = $invoice->getOrder();
        $paymentType    = $order->getPayment()->getMethodInstance()->getCode();

        if(strstr($paymentType, 'paypal')){
            $paymentType = 'paypal';
        }
        switch ($paymentType) {
            case 'cashondelivery':
            case 'phoenix_cashondelivery':
                $paymentType    = 'COD';
            break;
            case 'ccsave':
                $paymentType    = 'CCOD';
            break;
            case 'paypal':
            default:
                $paymentType    = 'cd';
            break;
        }

        if($autoCCPush == true && ($paymentType == 'CCOD' || $paymentType == 'cd') ){
            return $this->pushCCOrder($order, '', $paymentType);
        }
    }

    public function getCODTrackingNo($observer) {
        //Check IF the Auto Push Is Enabled
        $autoCODPush    = Mage::getStoreConfig('carriers/fetchr/autocodpush');
        $order          = $observer->getEvent()->getOrder();
        $paymentType    = $order->getPayment()->getMethodInstance()->getCode();

        if(strstr($paymentType, 'paypal')){
            $paymentType = 'paypal';
        }
        switch ($paymentType) {
            case 'cashondelivery':
            case 'phoenix_cashondelivery':
                $paymentType    = 'COD';
            break;
            case 'ccsave':
                $paymentType    = 'CCOD';
            break;
            case 'paypal':
            default:
                $paymentType    = 'cd';
            break;
        }
        if($autoCODPush == true && $paymentType == 'COD'){
            return $this->pushCODOrder($order);
        }
    }

    public function pushOrderAfterShipmentCreation($observer)
    {
        $shipment               = $observer->getEvent()->getShipment();
        $order                  = $shipment->getOrder();
        $collection             = Mage::getModel('sales/order')->loadByIncrementId($order->getIncrementId());
        $shippingmethod         = $order->getShippingMethod();
        $paymentType            = $order->getPayment()->getMethodInstance()->getCode();
        $autoCODPush            = Mage::getStoreConfig('carriers/fetchr/autocodpush');
        $autoCCPush             = Mage::getStoreConfig('carriers/fetchr/autoccpush');

        // Get the selected shipping methods from the config of Fetchr Shipping
        // And Include them as they are fethcr. Refer to ---> https://docs.google.com/document/d/1oUosCu2at0U7rWCg24cN-gZHwfdCPPcIgkd6APHMthQ/edit?ts=567671b3
        $activeShippingMethods  = Mage::getStoreConfig('carriers/fetchr/activeshippingmethods');
        $activeShippingMethods  = explode(',', $activeShippingMethods);

        if(strstr($paymentType, 'paypal')){
            $paymentType = 'paypal';
        }
        switch ($paymentType) {
            case 'cashondelivery':
            case 'phoenix_cashondelivery':
                $paymentType    = 'COD';
            break;
            case 'ccsave':
                $paymentType    = 'CCOD';
            break;
            case 'paypal':
            default:
                $paymentType    = 'cd';
            break;
        }

        $shippingmethod     = explode('_', $shippingmethod);

        //if ( ($shippingmethod == 'fetchr_same_day' || $shippingmethod == 'fetchr_next_day') && $paymentType == 'COD' && $autoCODPush == false){
        if( in_array($shippingmethod[0], $activeShippingMethods) && $paymentType == 'COD' && $autoCODPush == false ){
            return $this->pushCODOrder($order, $shipment);
        //}elseif( ($shippingmethod == 'fetchr_same_day' || $shippingmethod == 'fetchr_next_day') && ($paymentType == 'CCOD' || $paymentType == 'cd') && $autoCCPush == false){
        }elseif(in_array($shippingmethod[0], $activeShippingMethods) && ($paymentType == 'CCOD' || $paymentType == 'cd') && $autoCCPush == false){
            return $this->pushCCOrder($order, $shipment, $paymentType);
        }
    }

    protected function pushCODOrder($order, $shipment='' )
    {
        $collection         = Mage::getModel('sales/order')->loadByIncrementId($order->getIncrementId());
        $store              = Mage::app()->getStore();
        $storeTelephone     = Mage::getStoreConfig('general/store_information/phone');
        $storeAddress       = Mage::getStoreConfig('general/store_information/address');
        $shippingmethod     = $order->getShippingMethod();
        $paymentType        = 'COD';

        // Get the selected shipping methods from the config of Fetchr Shipping
        // And Include them as they are fethcr. Refer to ---> https://docs.google.com/document/d/1oUosCu2at0U7rWCg24cN-gZHwfdCPPcIgkd6APHMthQ/edit?ts=567671b3
        $activeShippingMethods  = Mage::getStoreConfig('carriers/fetchr/activeshippingmethods');
        $activeShippingMethods  = explode(',', $activeShippingMethods);


        //if ($collection->getData() && ($shippingmethod == 'fetchr_same_day' || $shippingmethod == 'fetchr_next_day') && $paymentType == 'COD') {
          if ($collection->getData() && $paymentType == 'COD') {
            $resource   = Mage::getSingleton('core/resource');
            $adapter    = $resource->getConnection('core_read');

            //Get the selected Fetchr Shipping method and put it in the datERP comment
            $shippingmethod     = explode('_', $shippingmethod);

            if(in_array($shippingmethod[0], $activeShippingMethods) || $shippingmethod[0] == 'fetchr'){
                $selectedShippingMethod = $shippingoption;

                try {
                    foreach ($order->getAllVisibleItems() as $item) {
                        //echo "<pre>";print_r($item);
                        if ($item['product_type'] == 'configurable') {
                            $item['qty_shipped'] = (isset($item['qty_shipped']) ? $item['qty_shipped'] : $item['qty_ordered']);
                            
                            //Hnadling & sympol chars in the items name
                            $item['name'] = str_replace("&", ' And ', $item['name']);

                            $itemArray[] = array(
                                'client_ref' => $order->getIncrementId(),
                                'name' => $item['name'],
                                'sku' => $item['sku'],
                                'quantity' => ($item['qty_shipped'] != $item['qty_ordered'] ? $item['qty_shipped'] : $item['qty_ordered']),
                                'merchant_details' => array(
                                    'mobile' => $storeTelephone,
                                    'phone' => $storeTelephone,
                                    'name' => $store->getFrontendName(),
                                    'address' => $storeAddress,
                                ),
                                'COD' => $order->getShippingAmount(),
                                'price' => $item['price'],
                                'is_voucher' => 'No',
                            );
                        } else {
                            $item['qty_shipped'] = (isset($item['qty_shipped']) ? $item['qty_shipped'] : $item['qty_ordered']);
                            
                            //Hnadling & sympol chars in the items name
                            $item['name'] = str_replace("&", ' And ', $item['name']);

                            $itemArray[] = array(
                                'client_ref' => $order->getIncrementId(),
                                'name' => $item['name'],
                                'sku' => $item['sku'],
                                'quantity' => ($item['qty_shipped'] != $item['qty_ordered'] ? $item['qty_shipped'] : $item['qty_ordered']),
                                'merchant_details' => array(
                                    'mobile' => $storeTelephone,
                                    'phone' => $storeTelephone,
                                    'name' => $store->getFrontendName(),
                                    'address' => $storeAddress,
                                ),
                                'COD' => $order->getShippingAmount(),
                                'price' => $item['price'],
                                'is_voucher' => 'No',
                            );
                        }
                    }

                    //handling the grand total for partial shipment
                    $shippedGrandTotal = 0;
                    foreach ($itemArray as $items) {
                        //echo "<pre>";print_r($items);die("ge");
                        $shippedGrandTotal += $items['price'] * $items['quantity'];
                    }

                    //Add the shipping price
                    $shippedGrandTotal = $shippedGrandTotal + $order->getShippingAmount();

                    $discountAmount = 0;
                    if ($order->getDiscountAmount()) {
                        $discountAmount = abs($order->getDiscountAmount());
                    }

                    $address        = $order->getShippingAddress()->getData();
                    //$grandtotal     = ($order->getGrandTotal() > $shippedGrandTotal) ? $shippedGrandTotal : $order->getGrandTotal() ;
                    $discount       = $discountAmount;
                    //print_r($grandtotal);die("helloz");
                    $this->serviceType  = Mage::getStoreConfig('carriers/fetchr/servicetype');
                    $this->userName     = Mage::getStoreConfig('carriers/fetchr/username');
                    $this->password     = Mage::getStoreConfig('carriers/fetchr/password');
                    $ServiceType        = $this->serviceType;

                    //Handling Special chars in the address
                    foreach ($address as $key => $value) {
                        $address[$key] = str_replace("&", ' And ', $address[$key]); 
                    }

                    switch ($ServiceType) {
                        case 'fulfilment':
                        $dataErp[] = array(
                            'order' => array(
                                'items' => $itemArray,
                                'details' => array(
                                    'status' => '',
                                    'discount' => $discount,
                                    'grand_total' => $shippedGrandTotal,//$grandtotal,
                                    'customer_email' => $order->getCustomerEmail(),
                                    'order_id' => $order->getIncrementId(),
                                    'customer_firstname' => $address['firstname'],
                                    'payment_method' => $paymentType,
                                    'customer_mobile' => $address['telephone'],
                                    'customer_lastname' => $address['lastname'],
                                    'order_country' => $address['country_id'],
                                    'order_address' => $address['street'].', '.$address['city'].', '.$address['country_id'],
                                ),
                            ),
                        );
                        break;
                        case 'delivery':
                        $dataErp = array(
                            'username' => $this->userName,
                            'password' => $this->password,
                            'method' => 'create_orders',
                            'pickup_location' => $storeAddress,
                            'data' => array(
                                array(
                                    'order_reference' => $order->getIncrementId(),
                                    'name' => $address['firstname'].' '.$address['lastname'],
                                    'email' => $order->getCustomerEmail(),
                                    'phone_number' => $address['telephone'],
                                    'address' => $address['street'],
                                    'city' => $address['city'],
                                    'payment_type' => $paymentType,
                                    'amount' => $shippedGrandTotal,
                                    'description' => 'No',
                                    'comments' => $selectedShippingMethod,
                                ),
                            ),
                        );
                    }

                    $result[$order->getIncrementId()]['request_data'] = $dataErp;
                    $result[$order->getIncrementId()]['response_data'] = $this->_sendDataToErp($dataErp, $order->getIncrementId());

                    $response = $result[$order->getIncrementId()]['response_data'];
                    $comments = '';

                    if(!is_array($response)){        
                        $response = explode('.', $response);
                        $comments  .= '<strong>Fetchr Comment:</strong> the order was NOT pushed due to '.$response[0].' Error' ;
                        $order->setStatus('pending');
                        $order->addStatusHistoryComment($comments, false);
                    }else{
                        // Setting The Comment in the Order view
                        if($ServiceType == 'fulfilment'){
                            $tracking_number    = $response['tracking_no'];
                            $response['status'] = ($response['success'] == true ? 'success' : 'faild');

                            if($response['awb'] == 'SKU not found'){
                                $comments  .= '<strong>Fetchr Comment:</strong> One Of The SKUs Are Not Added to Fetchr System, Please Contact one of Fetchr\'s Account Managers for More Details';
                                $order->setStatus('pending');
                                $order->addStatusHistoryComment($comments, false);
                            }else{
                                $comments  .= '<strong>Fetchr Status: Tracking URL </strong> http://track.fetchr.us/track.php?tracking_number='.$tracking_number;
                                $order->setStatus('processing');
                                $order->addStatusHistoryComment($comments, false);
                            }

                        }elseif($ServiceType == 'delivery') {
                            $tracking_number    = $response[key($response)];
                            $comments  .= '<strong>Fetchr Status: Tracking URL </strong> http://track.fetchr.us/track.php?tracking_number='.$tracking_number;
                            $order->setStatus('processing');
                            $order->addStatusHistoryComment($comments, false);
                        }
                    }
                    
                    //COD Order Shipping And Invoicing
                    if($response['status'] == 'success'){
                        try {
                            //Get Order Qty
                            $qty = array();
                            foreach ($order->getAllVisibleItems() as $item) {
                                $product_id             = $item->getProductId();
                                $Itemqty                = $item->getQtyOrdered() - $item->getQtyShipped() - $item->getQtyRefunded() - $item->getQtyCanceled();
                                $qty[$item->getId()]    = $Itemqty;
                            }

                            //Invoicing
                            if($order->canInvoice()) {
                                $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
                                $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
                                $invoice->setState(Mage_Sales_Model_Order_Invoice::STATE_OPEN)->save();

                                $transactionSave = Mage::getModel('core/resource_transaction')
                                ->addObject($invoice)
                                ->addObject($invoice->getOrder());

                                $transactionSave->save();
                                Mage::log('Order '.$orderId.' has been invoiced!', null, 'fetchr.log');
                            }else{
                                Mage::log('Order '.$orderId.' cannot be invoiced!', null, 'fetchr.log');
                            }

                            //Create Shipment When Auto Push Is OFF
                            if(!empty($shipment)){
                                //$shipment = $shipment->getData();
                                $trackdata = array();
                                $trackdata['carrier_code'] = 'fetchr';
                                $trackdata['title'] = 'Fetchr';
                                $trackdata['number'] = $tracking_number;
                                $track = Mage::getModel('sales/order_shipment_track')
                                    ->setShipment($shipment)
                                    ->setData('title', $trackdata['title']) // User syntax correct name here
                                    ->setData('number', $trackdata['number'])
                                    ->setData('carrier_code', $trackdata['carrier_code']) // use code that matches DB code for ship method here
                                    ->setData('order_id', $shipment->getData('order_id'));

                                $track->save();
                            }else{
                                //Create Shipment When Auto Push Is ON
                                if ($order->canShip()) {
                                    $shipment = $order->prepareShipment($qty);

                                    $trackdata = array();
                                    $trackdata['carrier_code'] = 'fetchr';
                                    $trackdata['title'] = 'Fetchr';
                                    $trackdata['url'] = 'http://track.fetchr.us/track.php?tracking_number='.$tracking_number;
                                    $trackdata['number'] = $tracking_number;
                                    $track = Mage::getModel('sales/order_shipment_track')->addData($trackdata);

                                    $shipment->addTrack($track);
                                    //$shipment->register();
                                    $transactionSave = Mage::getModel('core/resource_transaction')
                                    ->addObject($shipment)
                                    ->addObject($shipment->getOrder())
                                    ->save();

                                    Mage::log('Order '.$orderId.' has been shipped!', null, 'fetchr.log');
                                } else {
                                    Mage::log('Order '.$orderId.' cannot be shipped!', null, 'fetchr.log');
                                }
                            }

                        }catch (Exception $e) {
                            $order->addStatusHistoryComment('Exception occurred during automaticallyInvoiceShipCompleteOrder action. Exception message: '.$e->getMessage(), false);
                            $order->save();
                        }
                    }
                    //End COD Order Shipping And Invoicing

                    unset($dataErp, $itemArray);

                } catch (Exception $e) {
                        echo (string) $e->getMessage();
                    }
            }

        }
    }

    protected function pushCCOrder($order, $shipment='', $paymentType = '')
    {
        $collection        = Mage::getModel('sales/order')->loadByIncrementId($order->getIncrementId());
        $store             = Mage::app()->getStore();
        $storeTelephone    = Mage::getStoreConfig('general/store_information/phone');
        $storeAddress      = Mage::getStoreConfig('general/store_information/address');
        $shippingmethod    = $order->getShippingMethod();
        //$paymentType       = 'CCOD';

        // Get the selected shipping methods from the config of Fetchr Shipping
        // And Include them as they are fethcr. Refer to ---> https://docs.google.com/document/d/1oUosCu2at0U7rWCg24cN-gZHwfdCPPcIgkd6APHMthQ/edit?ts=567671b3
        $activeShippingMethods  = Mage::getStoreConfig('carriers/fetchr/activeshippingmethods');
        $activeShippingMethods  = explode(',', $activeShippingMethods);

        //if ($collection->getData() && ($shippingmethod == 'fetchr_same_day' || $shippingmethod == 'fetchr_next_day') && $paymentType == 'CCOD' ) {
        if( $collection->getData() && ($paymentType == 'CCOD' || $paymentType == 'cd') ){
            $resource = Mage::getSingleton('core/resource');
            $adapter = $resource->getConnection('core_read');

            $shippingmethod     = explode('_', $shippingmethod);

            if(in_array($shippingmethod[0], $activeShippingMethods) || $shippingmethod[0] == 'fetchr'){
                $selectedShippingMethod = $shippingoption;
                try {
                    foreach ($order->getAllVisibleItems() as $item) {
                        if ($item['product_type'] == 'configurable') {
                            if( isset($item['qty_shipped']) && $item['qty_shipped'] != '0'){
                               $item['qty_shipped'] = $item['qty_shipped']; 
                            }else{
                                $item['qty_shipped'] = $item['qty_ordered'];
                            }
                            
                            //Hnadling & sympol char in the items name
                            $item['name'] = str_replace("&", ' And ', $item['name']);
                            
                            $itemArray[] = array(
                                'client_ref' => $order->getIncrementId(),
                                'name' => $item['name'],
                                'sku' => $item['sku'],
                                'quantity' => ($item['qty_shipped'] != $item['qty_ordered']) ? $item['qty_shipped'] : $item['qty_ordered'],//$item['qty_ordered'],
                                'merchant_details' => array(
                                    'mobile' => $storeTelephone,
                                    'phone' => $storeTelephone,
                                    'name' => $store->getFrontendName(),
                                    'address' => $storeAddress,
                                ),
                                'COD' => $order->getShippingAmount(),
                                'price' => $item['price'],
                                'is_voucher' => 'No',
                            );
                        } else {
                            if( isset($item['qty_shipped']) && $item['qty_shipped'] != '0'){
                               $item['qty_shipped'] = $item['qty_shipped']; 
                            }else{
                                $item['qty_shipped'] = $item['qty_ordered'];
                            }
                            //Hnadling & sympol chars in the items name
                            $item['name'] = str_replace("&", ' And ', $item['name']);

                            $itemArray[] = array(
                                'client_ref' => $order->getIncrementId(),
                                'name' => $item['name'],
                                'sku' => $item['sku'],
                                'quantity' => ($item['qty_shipped'] != $item['qty_ordered']) ? $item['qty_shipped'] : $item['qty_ordered'],//$item['qty_ordered'],
                                'merchant_details' => array(
                                    'mobile' => $storeTelephone,
                                    'phone' => $storeTelephone,
                                    'name' => $store->getFrontendName(),
                                    'address' => $storeAddress,
                                ),
                                'COD' => $order->getShippingAmount(),
                                'price' => $item['price'],
                                'is_voucher' => 'No',
                            );
                        }
                    }
                    
                    $discountAmount = 0;
                    if ($order->getDiscountAmount()) {
                        $discountAmount = abs($order->getDiscountAmount());
                    }

                    //handling the grand total for partial shipment
                    $shippedGrandTotal = 0;
                    foreach ($itemArray as $items) {
                        $shippedGrandTotal += $items['price'] * $items['quantity'];
                    }
                    //Add the shipping price
                    $shippedGrandTotal = $shippedGrandTotal + $order->getShippingAmount();

                    $address        = $order->getShippingAddress()->getData();
                    $grandtotal     = $order->getGrandTotal();
                    $discount       = $discountAmount;

                    $this->serviceType  = Mage::getStoreConfig('carriers/fetchr/servicetype');
                    $this->userName     = Mage::getStoreConfig('carriers/fetchr/username');
                    $this->password     = Mage::getStoreConfig('carriers/fetchr/password');
                    $ServiceType        = $this->serviceType;

                    //Handling Special chars in the address
                    foreach ($address as $key => $value) {
                        $address[$key] = str_replace("&", ' And ', $address[$key]); 
                    }

                    switch ($ServiceType) {
                        case 'fulfilment':
                        $dataErp[] = array(
                            'order' => array(
                                'items' => $itemArray,
                                'details' => array(
                                    'status' => '',
                                    'discount' => $discount,
                                    'grand_total' => '0',//$grandtotal,
                                    'customer_email' => $order->getCustomerEmail(),
                                    'order_id' => $order->getIncrementId(),
                                    'customer_firstname' => $address['firstname'],
                                    'payment_method' => $paymentType,
                                    'customer_mobile' => ($address['telephone']?$address['telephone']:'N/A'),
                                    'customer_lastname' => $address['lastname'],
                                    'order_country' => $address['country_id'],
                                    'order_address' => $address['street'].', '.$address['city'].', '.$address['country_id'],
                                ),
                            ),
                        );
                        break;
                        case 'delivery':
                        $dataErp = array(
                            'username' => $this->userName,
                            'password' => $this->password,
                            'method' => 'create_orders',
                            'pickup_location' => $storeAddress,
                            'data' => array(
                                array(
                                    'order_reference' => $order->getIncrementId(),
                                    'name' => $address['firstname'].' '.$address['lastname'],
                                    'email' => $order->getCustomerEmail(),
                                    'phone_number' => ($address['telephone']?$address['telephone']:'N/A'),
                                    'address' => $address['street'],
                                    'city' => $address['city'],
                                    'payment_type' => $paymentType,
                                    'amount' => '0',//$grandtotal,
                                    'description' => 'No',
                                    'comments' => $selectedShippingMethod,
                                ),
                            ),
                        );
                    }

                    $result[$order->getIncrementId()]['request_data'] = $dataErp;
                    $result[$order->getIncrementId()]['response_data'] = $this->_sendDataToErp($dataErp, $order->getIncrementId());

                    $response = $result[$order->getIncrementId()]['response_data'];
                    $comments = '';

                    // Setting The Comment in the Order view
                    if($ServiceType == 'fulfilment' ){

                        $tracking_number    = $response['tracking_no'];
                        $response['status'] = ($response['success'] == true ? 'success' : 'faild');

                        if($response['awb'] == 'SKU not found'){
                            $comments  .= '<strong>Fetchr Comment:</strong> One Of The SKUs Are Not Added to Fetchr System, Please Contact one of Fetchr\'s Account Managers for More Details';
                            $order->setStatus('pending');
                            $order->addStatusHistoryComment($comments, false);
                        }else{
                            $comments  .= '<strong>Fetchr Status : Tracking URL </strong> http://track.fetchr.us/track.php?tracking_number='.$tracking_number;
                            $order->setStatus('processing');
                            $order->addStatusHistoryComment($comments, false);
                        }

                    }elseif ($ServiceType == 'delivery') {
                        $tracking_number    = $response[key($response)];
                        $comments  .= '<strong>Fetchr Status : Tracking URL </strong> http://track.fetchr.us/track.php?tracking_number='.$tracking_number;
                        $order->setStatus('processing');
                        $order->addStatusHistoryComment($comments, false);
                    }

                    //CCOD Order Shipping And Invoicing
                    if( $response['status'] == 'success'){
                        try {
                            //Get Order Qty
                            $qty = array();
                            foreach ($order->getAllVisibleItems() as $item) {
                                $product_id             = $item->getProductId();
                                $Itemqty                = $item->getQtyOrdered() - $item->getQtyShipped() - $item->getQtyRefunded() - $item->getQtyCanceled();
                                $qty[$item->getId()]    = $Itemqty;
                            }

                            //Create Shipment When Auto Push Is OFF
                            if(!empty($shipment)){
                                //$shipment = $shipment->getData();
                                $trackdata = array();
                                $trackdata['carrier_code'] = 'fetchr';
                                $trackdata['title'] = 'Fetchr';
                                $trackdata['number'] = $tracking_number;
                                $track = Mage::getModel('sales/order_shipment_track')
                                    ->setShipment($shipment)
                                    ->setData('title', $trackdata['title']) // User syntax correct name here
                                    ->setData('number', $trackdata['number'])
                                    ->setData('carrier_code', $trackdata['carrier_code']) // use code that matches DB code for ship method here
                                    ->setData('order_id', $shipment->getData('order_id'));

                                $track->save();
                            }

                            //Create Shipment When Auto Push Is On
                            if ($order->canShip()) {
                                $shipment = $order->prepareShipment($qty);

                                $trackdata = array();
                                $trackdata['carrier_code'] = 'fetchr';
                                $trackdata['title'] = 'Fetchr';
                                $trackdata['number'] = $tracking_number;
                                $track = Mage::getModel('sales/order_shipment_track')->addData($trackdata);

                                $shipment->addTrack($track);
                                //$shipment->register();
                                $transactionSave = Mage::getModel('core/resource_transaction')
                                ->addObject($shipment)
                                ->addObject($shipment->getOrder())
                                ->save();

                                Mage::log('Order '.$orderId.' has been shipped!', null, 'fetchr.log');
                            } else {
                                Mage::log('Order '.$orderId.' cannot be shipped!', null, 'fetchr.log');
                            }

                        }catch (Exception $e) {
                            $order->addStatusHistoryComment('Exception occurred during automaticallyInvoiceShipCompleteOrder action. Exception message: '.$e->getMessage(), false);
                            $order->save();
                        }
                    }
                    //End COD Order Shipping And Invoicing

                    unset($dataErp, $itemArray);
                } catch (Exception $e) {
                    echo (string) $e->getMessage();
                }
            }
        }
    }

    protected function _sendDataToErp($data, $orderId)
    {
        $response = null;

        try {
            $this->accountType  = Mage::getStoreConfig('carriers/fetchr/accounttype');
            $this->serviceType  = Mage::getStoreConfig('carriers/fetchr/servicetype');
            $this->userName     = Mage::getStoreConfig('carriers/fetchr/username');
            $this->password     = Mage::getStoreConfig('carriers/fetchr/password');

            $ServiceType = $this->serviceType;
            $accountType = $this->accountType;
            switch ($accountType) {
                case 'live':
                $baseurl = Mage::getStoreConfig('fetchr_shipping/settings/liveurl');
                break;
                case 'staging':
                $baseurl = Mage::getStoreConfig('fetchr_shipping/settings/stagingurl');
            }
            switch ($ServiceType) {
                case 'fulfilment':
                    $ERPdata        = 'ERPdata='.json_encode($data);
                    $merchant_name  = "MENA360 API";
                    $ch     = curl_init();
                    $url    = $baseurl.'/client/apifulfilment/';
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $ERPdata.'&erpuser='.$this->userName.'&erppassword='.$this->password.'&merchant_name='.$this->userName);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($ch);
                    curl_close($ch);

                    $decoded_response = json_decode($response, true);

                    // validate response
                    if(!is_array($decoded_response)){
                        return $response;
                    }

                    if ($decoded_response['awb'] == 'SKU not found') {
                        $store = Mage::app()->getStore();
                        $cname = $store->getFrontendName();
                        $ch = curl_init();
                        $url = 'http://www.menavip.com/custom/smail.php';
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, 'orderId='.$orderId.'&cname='.$cname);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $output = curl_exec($ch);
                        curl_close($ch);
                    }

                    if ($decoded_response['tracking_no'] != '0') {
                        return $decoded_response;
                    }
                break;
                case 'delivery':
                    $data_string = 'args='.json_encode($data);
                    $ch = curl_init();
                    $url = $baseurl.'/client/api/';
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($ch);
                    curl_close($ch);

                    // validate response
                    $decoded_response   = json_decode($response, true);
                    if(!is_array($decoded_response)){
                        return $response;
                    }

                    $response = $decoded_response;

                    Mage::log('Order '.$orderId.' has been pushed!', null, 'fetchr.log');
                    Mage::log('Order data: '.print_r($data, true), null, 'fetchr.log');
                    return $response;
                break;
            }
        } catch (Exception $e) {
            echo (string) $e->getMessage();
        }
    }
}
