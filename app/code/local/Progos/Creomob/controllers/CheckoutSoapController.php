<?php



require_once dirname(__FILE__).'/SoapController.php';



class Progos_Creomob_CheckoutSoapController extends Progos_Creomob_SoapController {

    public function indexAction(){

    }
    
    
    protected function processPayment($sessionId,$cartId){
        $proxy = new SoapClient($this->soapURLv2);
        $proxy->shoppingCartShippingMethod($sessionId, $cartId, 'freeshipping_freeshipping');

        $paymentMethod =  array(
            'po_number' => null,
            'method' => 'cashondelivery',
            'cc_cid' => null,
            'cc_owner' => null,
            'cc_number' => null,
            'cc_type' => null,
            'cc_exp_year' => null,
            'cc_exp_month' => null
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
            
            $customer = json_decode(file_get_contents("php://input"));
            if(count($customer) && !empty((array) $customer)){
                $this->setCustomer($sessionId,$cartId,$customer[0]);
            } else {
//                $this->setGuestCustomer($sessionId,$cartId);
            }
            
            
            $res = $this->processPayment($sessionId,$cartId);
            
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
    
}
