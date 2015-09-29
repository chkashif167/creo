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
        $order = array();
        foreach ($orderCollection as $_order)
        {
            $order['order_id'] = $_order->getRealOrderId() ;
            $order['shipping_address'] = $_order->getShippingAddress();
            $order['grand_total'] = $_order->getGrandTotal();
            $order['status_label'] = $_order->getStatusLabel();
         }
        return $order;
    }
    
}