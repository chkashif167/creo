<?php

class Progos_Creomob_SoapController extends Mage_Core_Controller_Front_Action{
    
    
    
    
    protected $soapURLv1 = "http://localhost/creo/index.php/api/soap/?wsdl";
//    protected $soapURLv1 = "https://creoroom.com/api/soap/?wsdl";
    
    protected $soapURLv2 = "http://localhost/creo/index.php/api/v2_soap/?wsdl";
//    protected $soapURLv2 = "https://creoroom.com/api/v2_soap/?wsdl";
    
    
    private $API_USER = "creomob"; //webservice user login
    
    
    private $API_KEY = "123456"; //webservice user pass
        
    
    public function loginAction(){
        $client = new SoapClient($this->soapURLv2);
        $token = $client->login($this->API_USER, $this->API_KEY);
        
//        header('Access-Control-Allow-Origin: *');
        header("Content-Type: application/json");
        echo json_encode(array('token'=>$token));
        die;
        
    }
    
    public function verifyTokenAction(){
        $sessionId = $this->getRequest()->getParam('sid');
        $proxy = new SoapClient($this->soapURLv2);
        //use filters to minimize data
        $complexFilter = array(
            'complex_filter' => array(
                array(
                    'key' => 'type',
                    'value' => array('key' => 'in', 'value' => 'configurable')
                ),
                array(
                    'key' => 'category_ids',
                    'value' => array('key' => '', 'value' => array(3,57,58,17,59))
                )
            )
        );
        $valid = false;
        try{
            $result = $proxy->catalogProductList($sessionId,$complexFilter);
            // no exception means token is valid
            $valid = true;
        } catch (Exception $e){
            //exception means token is invalid
            
        }
        
//        header('Access-Control-Allow-Origin: *');
        header("Content-Type: application/json");
        echo $valid;
        die;
    }
    
}
