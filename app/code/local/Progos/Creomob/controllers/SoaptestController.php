<?php


require_once dirname(__FILE__).'/SoapController.php';

class Progos_Creomob_SoaptestController extends Progos_Creomob_SoapController {
    
    
    public function indexAction(){
        
    }

    
    public function categorytreeAction(){
        $sessionId = $this->getRequest()->getParam('sid');
        $proxy = new SoapClient($this->soapURLv2);

        try{
            $result = $proxy->catalogCategoryTree($sessionId);
            var_dump($result);
        }catch(Exception $ex){
            echo "Error : ".$ex->getMessage();
        }
    }
    
    public function createCartAction(){
        $store = Mage::app()->getStore();
        $stroeId = $store->getId();
        $sessionId = $this->getRequest()->getParam('sid');
        $proxy = new SoapClient($this->soapURLv2);
        $shoppingCartId = $proxy->shoppingCartCreate($sessionId, $stroeId);
        echo $shoppingCartId;
    }
    
    public function addProductToCartAction(){
        
        $sessionId = $this->getRequest()->getParam('sid');
        $quoteId = 94;
        $proxy = new SoapClient($this->soapURLv2); 

        $result = $proxy->shoppingCartProductAdd($sessionId, $quoteId, array(array(
        'product_id' => '11',
        'sku' => 'simple_product',
        'qty' => '5',
        'options' => null,
        'bundle_option' => null,
        'bundle_option_qty' => null,
        'links' => null
        )));   


        var_dump($result);
    }
    
    public function listCartProductsAction(){
        $sessionId = $this->getRequest()->getParam('sid');
        $quoteId = 94;
        $proxy = new SoapClient($this->soapURLv2); 
        

        $result = $proxy->shoppingCartProductList($sessionId, $quoteId);
        var_dump($result);
    }


    public function checkoutDetailsSoapAction(){
        
//        $host = "localhost/creo/index.php";
//        $proxy = new SoapClient('http://'.$host.'/api/v2_soap/?wsdl'); // TODO : change url
//        $sessionId = $proxy->login('creomob', '123456'); // TODO : change login and pwd if necessary
//
//        $result = $proxy->catalogCategoryTree($sessionId);
//        var_dump($result);
        
        $host = "localhost/creo/index.php"; //our online shop url
        $client = new SoapClient("http://".$host."/api/soap/?wsdl"); //soap handle
        $apiuser= "creomob"; //webservice user login
        $apikey = "123456"; //webservice user pass
        $action = "sales_order.list"; //an action to call later (loading Sales Order List)
        try { 

          $sess_id= $client->login($apiuser, $apikey); //we do login
          
          
          
          $quoteId = $client->call( $sess_id, 'cart.create', array( 'magento_store' ) );
            $arrProducts = array(
                    array(
                            "product_id" => "1",
                            "qty" => 2,
                            "options" => null
                    ),
                    array(
                            "sku" => "testSKU",
                            "quantity" => 4
                    )
            );
            $resultCartProductAdd = $client->call(
                    $sess_id,
                    "cart_product.add",
                    array(
                            $quoteId,
                            $arrProducts
                    )
            );


        //print_r($client->call($sess_id, $action));
        $result = $client->call($sess_id, 'cart_product.list');
        var_dump ($result);
//        foreach ($result as $p){
//            echo $p->getName(),'<br>';
//        }
        }
        catch (Exception $e) { //while an error has occured
            echo "==> Error: ".$e->getMessage(); //we print this
               exit();
        }
    }

}