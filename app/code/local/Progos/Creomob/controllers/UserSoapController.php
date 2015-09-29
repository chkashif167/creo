<?php


require_once dirname(__FILE__).'/SoapController.php';


class Progos_Creomob_UserSoapController extends Progos_Creomob_SoapController {
    
    
    protected function getCustomer($sessionId,$email,$hash){
        $proxy = new SoapClient($this->soapURLv2);
        $filter = array(
            'complex_filter' => array(
                array(
                    'key' => 'email',
                    'value' => array('key' => 'eq', 'value' => $email)
                ),
                array(
                    'key' => 'password_hash',
                    'value' => array('key' => 'eq', 'value' => $hash)
                )
            )
        );
        return $proxy->customerCustomerList($sessionId, $filter);
    }


    public function loginAction() {
        $sessionId = $this->getRequest()->getParam('sid');
        //$sessionId = 'fd5112c2a64ddc4eaa0a391a39258783';
        $login_data = json_decode(file_get_contents('php://input'),true);
        
        $email = $login_data['email'];
        $password = $login_data['password'];
        
        /** @var $session Mage_Customer_Model_Session */
        $session = Mage::getSingleton( 'customer/session' );

        $response = array('success'=>0,'message'=>'','customer'=>array());
        try
        {
            $session->login( $email, $password );
            //$session->setCustomerAsLoggedIn( $session->getCustomer() );
            $customer = $session->getCustomer();
            try{
                $res = $this->getCustomer($sessionId,$customer->getEmail(),$customer->getPasswordHash());
                $orders = $this->getCustomerOrders($customer->getId());
                $response['success'] = 1;
                $response['message'] = 'Login successful';
                $response['customer'] = $res;
                $response['customer_orders'] = $orders;
                
            }catch(Exception $e){
                $response['message'] = $e->getMessage();
            }
            
        }
        catch( Exception $e )
        {
            $response['message'] = $e->getMessage();
        }
        
        header("Content-Type: application/json");
        echo json_encode($response);
        die;
    }
    
    protected function addCustomer($sessionId,$customer){
        $proxy = new SoapClient($this->soapURLv2);
        return $proxy->customerCustomerCreate($sessionId, 
                array('email' => $customer['email'],
                    'firstname' => $customer['firstname'],
                    'lastname' => $customer['lastname'],
                    'password' => $customer['password'],
                    'website_id' => 1,
                    'store_id' => 1,
                    'group_id' => 1)
                );
    }
    
    public function addCustomerAction() {
        $sessionId = $this->getRequest()->getParam('sid');
        $customer_data = json_decode(file_get_contents('php://input'),true);
        

        $response = array('success'=>0,'message'=>'','customer'=>array());
        try
        {
            $res = $this->addCustomer($sessionId,$customer_data);
            $response['success'] = 1;
            $response['message'] = 'Customer added';
            $response['customer'] = $res;
            
        }
        catch( Exception $e )
        {
            $response['error_code'] = $e->getCode();
            $response['message'] = $e->getMessage();
        }
        
        header("Content-Type: application/json");
        echo json_encode($response);
        die;
    }
    
    protected function updateCustomer($sessionId,$customer){
        $proxy = new SoapClient($this->soapURLv2);
        return $proxy->customerCustomerUpdate($sessionId, $customer['customer_id'],
                array('email' => $customer['email'],
                    'firstname' => $customer['firstname'],
                    'lastname' => $customer['lastname'],
                    'website_id' => 1,
                    'store_id' => 1,
                    'group_id' => 1)
                );
    }
    
    public function updateCustomerAction() {
        $sessionId = $this->getRequest()->getParam('sid');
        $customer_data = json_decode(file_get_contents('php://input'),true);
        

        $response = array('success'=>0,'message'=>'','customer'=>array());
        try
        {
            $res = $this->updateCustomer($sessionId,$customer_data);
            $response['success'] = 1;
            $response['message'] = 'Customer updated';
            $response['customer'] = $res;
            
        }
        catch( Exception $e )
        {
            $response['error_code'] = $e->getCode();
            $response['message'] = $e->getMessage();
        }
        
        header("Content-Type: application/json");
        echo json_encode($response);
        die;
    }
    
    public function getCustomerOrders($customerId){
        $orderCollection = Mage::getModel("sales/order")->getCollection()
                           ->addAttributeToSelect('*')
                           ->addFieldToFilter('customer_id', $customerId);
        $orders = array();
        foreach ($orderCollection as $_order)
        {
            $order = array();
            $order['order_id'] = $_order->getRealOrderId() ;
            $order['shipping_address'] = $_order->getShippingAddress();
            $order['grand_total'] = $_order->getGrandTotal();
            $order['currency'] = $_order->getOrderCurrencyCode();
            $order['status_label'] = $_order->getStatusLabel();
            $orders[] = $order;
         }
        return $orders;
    }
    
    public function getCustomerOrdersAction(){
        $customerId = $this->getRequest()->getParam('cid');
        $orders = $this->getCustomerOrders($customerId);
        header("Content-Type: application/json");
        echo json_encode($orders);
        die;
    }
    
    public function getCustomerAddressAction(){
        $customerId = $this->getRequest()->getParam('cid');
        $customer = Mage::getModel('customer/customer')->load($customerId);
        
        $customerAddress = array();
        foreach ($customer->getAddresses() as $address)
        {
           $customerAddress[] = $address->toArray();
        }
        $billing = $customer->getPrimaryBillingAddress();
        $shipping = $customer->getPrimaryShipingAddress();
        $address = array('billing_address'=>$billing,'shipping_address'=>$shipping,
            'addresses'=>$customerAddress);
        header("Content-Type: application/json");
        echo json_encode($customerAddress);
        die;
    }
    
    public function customerSuscribtionAction(){
        $customerId = $this->getRequest()->getParam('cid');
        $subscribe = $this->getRequest()->getParam('sub');
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $customer_email = $customer->getEmail();
        $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($customer_email);

        if($subscribe=="true"){
            Mage::getModel('newsletter/subscriber')->subscribe($customer_email);
        } elseif($subscribe=="false"){
            $subscriber->setStatus(Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED);
            $subscriber->save();
            Mage::getModel('newsletter/subscriber')->loadByEmail($customer_email)->unsubscribe();
        }
        
        echo $subscribe;
    }
    
    public function getCustomerSuscribtionAction(){
        $customerId = $this->getRequest()->getParam('cid');
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $customer_email = $customer->getEmail();
        $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($customer_email);
        
        if(!$subscriber->getId() 
                || $subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED
                || $subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE){
            echo 'false';
        } else {
            echo 'true';
        }
        //print_r($subscriber->debug());
        die();
 
    }
}