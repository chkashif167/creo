<?php



require_once dirname(__FILE__).'/SoapController.php';



class Progos_Creomob_CheckoutSoapController extends Progos_Creomob_SoapController {

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
            'cc_cid' => $payment_data->cc_ccid,
            'cc_owner' => $payment_data->cc_owner,
            'cc_number' => $payment_data->cc_number,
            'cc_type' => 'VI',//$payment_data->cc_type,
            'cc_exp_year' => $payment_data->cc_exp_year,
            'cc_exp_month' => $payment_data->cc_exp_month
        );
         // add payment method
        $proxy->shoppingCartPaymentMethod($sessionId, $cartId, $paymentMethod);
         // place the order
        return $proxy->shoppingCartOrder($sessionId, $cartId, null, null);
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
    
}
