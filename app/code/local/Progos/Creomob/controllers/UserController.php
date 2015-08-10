<?php

class Progos_Creomob_UserController extends Mage_Core_Controller_Front_Action{
    
    public function indexAction(){
        //print_r(Mage::getSingleton('customer/session')->getUser());
        if (Mage::app()->isInstalled() && Mage::getSingleton('customer/session')->isLoggedIn()) {
            //print_r( Mage::getSingleton('customer/session')->getCustomer());die;
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $data['id'] = $customer->getId();
            $data['firstname'] = $customer->getFirstname();
            $data['lastname'] = $customer->getLastname();
            $data['email'] = $customer->getEmail();
            $data['is_active'] = $customer->getIsActive();
        } else {
            $data[] =  array(0);
        }
        
        header("Content-Type: application/json");
        print_r(json_encode($data));
        die;
    }
    
    public function loginAction(){
        
        $email = $this->getRequest()->getParam('email');
        $password = $this->getRequest()->getParam('password');
        
        /** @var $session Mage_Customer_Model_Session */
        $session = Mage::getSingleton( 'customer/session' );

        try
        {
            $session->login( $email, $password );
            $session->setCustomerAsLoggedIn( $session->getCustomer() );
            echo '1';
            //print_r($session);
            return true;
        }
        catch( Exception $e )
        {
            echo '0';
            return false;
        }
        
        
    }
    
    public function userAction(){
    }


    public function soaptestAction(){
        
        $host = "localhost/creo/index.php";
        $proxy = new SoapClient('http://'.$host.'/api/v2_soap/?wsdl'); // TODO : change url
        $sessionId = $proxy->login('creomob', '123456'); // TODO : change login and pwd if necessary

        $result = $proxy->catalogCategoryTree($sessionId);
        var_dump($result);
    }
}