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
    
    public function processPaymentAction(){
        $sessionId = $this->getRequest()->getParam('sid');
        $cartId = $this->getRequest()->getParam('qid');
        
        $response = array('success'=>0,'message'=>'','res'=>null);
        try {
            $res = $this->processPayment($sessionId,$cartId);
            
            $cart = Mage::getModel('sales/quote')->load($cartId);
            $cart->removeAllItems();
            //$cart->truncate();
            $cart->save();
            //$cart->getItems()->clear()->save();
            
            $response['success'] = 1;
            $response['message'] = 'Shipping added successfully';
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
