<?php



require_once dirname(__FILE__).'/SoapController.php';



class Progos_Creomob_CartSoapController extends Progos_Creomob_SoapController {
    
    
    public function indexAction(){
        
    }
    
    protected function createCart($sessionId){
        $store = Mage::app()->getStore();
        $stroeId = $store->getId();
        $proxy = new SoapClient($this->soapURLv2);
        
        //initialize with customer address to cope with magento not calculating subtotal bug
        $proxy->shoppingCartCustomerAddresses($sessionId, $stroeId, array(array(
            'mode' => '',
            'firstname' => 'Creo',
            'lastname' => 'Mobile',
            'street' => 'Rehman Baba Road',
            'city' => 'Islamabad',
            'region' => '',
            'postcode' => '46000',
            'country_id' => 'PK',
            'telephone' => '03125511678',
            'is_default_billing' => 1
            )));
        
        return $proxy->shoppingCartCreate($sessionId, $stroeId);
    }
    
    public function createCartAction(){
        $sessionId = $this->getRequest()->getParam('sid');
        $cart_id = $this->createCart($sessionId);
        
        header("Content-Type: application/json");
        echo json_encode(array('cart_id'=>$cart_id));
        die;
    }
    
    protected function getCart($sessionId,$quoteId){
        
        $proxy = new SoapClient($this->soapURLv2);
        
        //initialize with customer address to cope with magento not calculating subtotal bug
        $proxy->shoppingCartCustomerAddresses($sessionId, $quoteId, array(array(
            'mode' => '',
            'firstname' => 'Creo',
            'lastname' => 'Mobile',
            'street' => 'Rehman Baba Road',
            'city' => 'Islamabad',
            'region' => '',
            'postcode' => '46000',
            'country_id' => 'PK',
            'telephone' => '03125511678',
            'is_default_billing' => 1
            ))); 
        
        
        return $proxy->shoppingCartInfo($sessionId, $quoteId);
    }
    
    protected function getCartTotals($sessionId,$quoteId){
        
        $proxy = new SoapClient($this->soapURLv2);
        return $proxy->shoppingCartTotals($sessionId, $quoteId);
    }
    
    public function getCartAction(){
        
        $sessionId = $this->getRequest()->getParam('sid');
        $quoteId = $this->getRequest()->getParam('qid');
        
        $response = array('success'=>0,'message'=>'','cart'=>array());
        
        if(!$quoteId){
//            $quoteId = $this->createCart($sessionId);
            $response['message'] = 'Quote ID ( cart ID) not provided';
        } else {
            try{
                $dataObj = $this->getCart($sessionId,$quoteId);

                //$quote = Mage::getModel('sales/quote')->load($quoteId);
                
                $response['success'] = 1;
                $response['message'] = 'Cart data found';
                
                $data = (array)$dataObj;
                $i = 0;
                foreach($data['items'] as $item){
                    $product_id = $item->product_id;
                    $product = Mage::getModel('catalog/product')->load($product_id);
//                    
                    $extra['product_id'] = $product_id;
                    $extra['img'] = $product->getImageUrl();
//                    $extra['price'] = $product->getPrice();
                    $extra['color_id'] = $product->getData('color');
                    $extra['color_value'] = $product->getAttributeText('color');
                    $extra['size_id'] = $product->getSize();
                    $extra['size_value'] = $product->getAttributeText('size');
                    $extra['min_sale_qty'] = $product->getStockItem()->getMinSaleQty();
                    $extra['max_sale_qty'] = $product->getStockItem()->getMaxSaleQty();
                    
                    $extended_items = array_merge((array)$item,$extra);
                    $data['items'][$i] = $extended_items;
                    $i++;
                }
                $response['cart'] = $data;
            } catch(Exception $e){
                $response['error_code'] = $e->getCode();
                $response['message'] = $e->getMessage();
            }
        }
        
        header("Content-Type: application/json");
        echo json_encode($response);
        die;
    }
    
    protected function addProduct($sessionId,$quoteId,$productId,$qty){
        
        $proxy = new SoapClient($this->soapURLv2);
        
        return $proxy->shoppingCartProductAdd($sessionId, $quoteId, array(array(
            'product_id' => $productId,
            'qty' => $qty,
            'options' => null,
            'bundle_option' => null,
            'bundle_option_qty' => null,
            'links' => null
            )));  
    }
    
    public function addProductAction(){
        
        $sessionId = $this->getRequest()->getParam('sid');
        $quoteId = $this->getRequest()->getParam('qid');
        
        $productId = $this->getRequest()->getParam('pid');
        $qty = $this->getRequest()->getParam('qty');
        
        if(!$qty){
            $qty = 1;
        }
        
        $response = array('success'=>0,'message'=>'','res'=>null);
        
        try{
            $res = $this->addProduct($sessionId,$quoteId,$productId,$qty);
            $response['success'] = 1;
            $response['message'] = 'Product added successfully';
            $response['res'] = $res;
        }catch(Exception $e){
                $response['error_code'] = $e->getCode();
                $response['message'] = $e->getMessage();
        }
        
        header("Content-Type: application/json");
        echo json_encode($response);
        die;
    }
    
    
    protected function removeProduct($sessionId,$productId){
        
        $proxy = new SoapClient($this->soapURLv2);
        return $proxy->catalogProductDelete($sessionId, $productId);    
    }
    
    public function removeProductAction(){
        
        $sessionId = $this->getRequest()->getParam('sid');
        
        $productId = $this->getRequest()->getParam('pid');
        
        $response = array('success'=>0,'message'=>'','res'=>null);
        
        try{
            $res = $this->removeProduct($sessionId,$productId);
            $response['success'] = 1;
            $response['message'] = 'Product removed successfully';
            $response['res'] = $res;
        }catch(Exception $e){
                $response['error_code'] = $e->getCode();
                $response['message'] = $e->getMessage();
        }
        
        header("Content-Type: application/json");
        echo json_encode($response);
        die;
    }
}