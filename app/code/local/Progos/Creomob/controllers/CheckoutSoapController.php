<?php



require_once dirname(__FILE__).'/SoapController.php';



class Progos_Creomob_CheckoutSoapController extends Progos_Creomob_SoapController {
    
    
    protected $checkout_endpoint = "https://api2.checkout.com/v2/";
    protected $private_key = "sk_103764da-6f8b-443d-8bef-66454522b6b0";
    protected $checkout_endpoint_sandbox = "https://sandbox.checkout.com/api2/v2/";
    protected $private_key_sandbox = "sk_test_f89deda7-f8df-4fe0-88af-0027b863a345";
    protected $payment_mode = 'live'; //test/live

    public function indexAction(){

    }
    
    
    protected function processPayment($sessionId,$cartId,$payment_data){
        $proxy = new SoapClient($this->soapURLv2);
        //freeshipping_freeshipping,tablerate_bestway,flatrate_flatrate/matrixrate_matrixrate
        //matrixrate_matrixrate_3
        $payment_method = $payment_data->payment_method;
        $shipment_method = $payment_data->shipment_method;
        $proxy->shoppingCartShippingMethod($sessionId, $cartId, $shipment_method);
        
        
        $paymentMethod =  array(
            'po_number' => null,
            'method' => $payment_method,
            'cc_cid' => $payment_data->cc_cid,
            'cc_owner' => $payment_data->cc_owner,
            'cc_number' => $payment_data->cc_number,
            'cc_type' => $payment_data->cc_type,//'VI',//
            'cc_exp_year' => $payment_data->cc_exp_year,
            'cc_exp_month' => $payment_data->cc_exp_month
        );
        
        // add payment method
        $proxy->shoppingCartPaymentMethod($sessionId, $cartId, $paymentMethod);
        
        if($payment_method=='creditcardpci_ignore'){
            //place order manually
            $cc_process_res = $this->creditCardPciCharge($sessionId,$cartId,$payment_data->customer[0],
                    $payment_data->cc_checkout_card_token);
            
            $cc_process_res_json = json_decode($cc_process_res);
            $response_code = (int)$cc_process_res_json->responseCode;
            $response_message = $cc_process_res_json->responseMessage;
            
            
            $response['checkout_res'] = $cc_process_res_json;
            
            if($response_code==10000){
                //payment is approved
                // now load cart from cart id
                
                $quote = Mage::getModel('sales/quote')->load($cartId);
                $quote_id = $quote->getId();
                
                $quote->collectTotals()->save();
                $service = Mage::getModel('sales/service_quote', $quote);
                $service->submitAll();
                $order = $service->getOrder();
                
                $response['success'] = 1; //set to 1 when process is complete
                $response['message'] = 'Payment approved, order processed ';
                $response['quote_id'] = $quote_id;
                //$response['order'] = $order;
            } else {
                $response['success'] = 0;
                $response['message'] = 'Payment was not successful';
            }
            
            
            
            header("Content-Type: application/json");
            echo json_encode($response);
            die;
        } else {
            // place the order automatically
            return $proxy->shoppingCartOrder($sessionId, $cartId, null, null);
        }

         
    }
    
    protected function setCustomer($sessionId,$cartId,$customer){
        $proxy = new SoapClient($this->soapURLv2);
        
        $customer->mode = 'customer';
        return $proxy->shoppingCartCustomerSet($sessionId, $cartId, $customer);
    }
    
    protected function setGuestCustomer($sessionId,$cartId){
        $proxy = new SoapClient($this->soapURLv2);
        
        $customer = array('mode'=>'guest');
        return $proxy->shoppingCartCustomerSet($sessionId, $cartId, $customer);
    }
    
    public function processPaymentAction(){
        $sessionId = $this->getRequest()->getParam('sid');
        $cartId = $this->getRequest()->getParam('qid');
        
        
        $response = array('success'=>0,'message'=>'','res'=>null);
        try {
            
            $payment_data = json_decode(file_get_contents("php://input"));
//            if(count($customer) && !empty((array) $customer)){
//                $this->setCustomer($sessionId,$cartId,$customer[0]);
//            } else {
////                $this->setGuestCustomer($sessionId,$cartId);
//            }
            
            $payment_method = $payment_data->payment_method;
            $shipment_method = $payment_data->shipment_method;
            $res = $this->processPayment($sessionId,$cartId,$payment_data);
            
            $cart = Mage::getModel('sales/quote')->load($cartId);
            $cart->removeAllItems();
            //$cart->truncate();
            $cart->save();
            //$cart->getItems()->clear()->save();
            
            $response['success'] = 1;
            $response['message'] = 'Order processed successfully';
            $response['res'] = $res;
        } catch(Exception $e){
            $response['error_code'] = $e->getCode();
            $response['message'] = $e->getMessage();
        }
        
        header("Content-Type: application/json");
        echo json_encode($response);
        die;
    }
    
    public function shippingMethodsAction(){
        
        $sessionId = $this->getRequest()->getParam('sid');
        $cartId = $this->getRequest()->getParam('qid');
        
        $proxy = new SoapClient($this->soapURLv2);
        $result = $proxy->shoppingCartShippingList($sessionId, $cartId); 
        var_dump($result);
        
        
    }
    
    
    public function paymentMethodsAction(){
        
        $sessionId = $this->getRequest()->getParam('sid');
        $cartId = $this->getRequest()->getParam('qid');
        $proxy = new SoapClient($this->soapURLv2);
        
        $quote = Mage::getModel('sales/quote')->load($cartId);
        echo '<hr><br>Cart';
        $result = $proxy->shoppingCartInfo($sessionId, $cartId);
var_dump($result);
        echo '<hr>';
        
        
        echo 'Country -> ', $quote->getBillingAddress()->getCountry();
        $paymentMethods = Mage::getModel('payment/config')->getAllMethods($storeid=null);
        //print_r(json_encode((array)$paymentMethods['cashondelivery']));
        
        $result = $proxy->shoppingCartPaymentList($sessionId, $cartId); 
        var_dump($result);
        
        
    }
    
    protected function getCartTotals($sessionId,$cartId){
        $proxy = new SoapClient($this->soapURLv2);
        
        $result = $proxy->shoppingCartTotals($sessionId, $cartId);
        
        return $result;
    }
    
    
    protected function getCartInfo($sessionId,$cartId){
        $proxy = new SoapClient($this->soapURLv2);
        
        $result = $proxy->shoppingCartInfo($sessionId, $cartId);
        
        return $result;
    }
    
    protected function creditCardPciCharge($sessionId,$cartId,$customer,$card_token_data){
        
        $card_token = $card_token_data->id;
        $customer_email = $customer->email;
//        $totals = $this->getCartTotals($sessionId,$cartId);
        $cart_info = $this->getCartInfo($sessionId,$cartId);
        $value = round($cart_info->grand_total,2);
        $currency = $cart_info->quote_currency_code;
        
        $url = $this->checkout_endpoint;
        $key = $this->private_key;
        if($this->payment_mode=='test'){
            $url = $this->checkout_endpoint_sandbox;
            $key = $this->private_key_sandbox;
        }
        
        $req_data = array('email'=>$customer_email,'value'=>$value,'currency'=>$currency,
            'cardToken'=>$card_token);
        $req_data_json = json_encode($req_data);
        
//        return $req_data;
        $header[] = 'Authorization: '.$key;
        $header[] = 'Content-type: application/json; charset=utf-8';
        $header[] = 'Content-Length: '.  strlen($req_data_json);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url.'charges/token');
        curl_setopt($curl, CURLOPT_HTTPHEADER,$header);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $req_data_json);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
        
        $respone = curl_exec($curl);
        curl_close($curl);
        return $respone;
    }
    
    
}
