<?php



require_once dirname(__FILE__).'/SoapController.php';



class Progos_Creomob_CartSoapController extends Progos_Creomob_SoapController {
    
    
    public function indexAction(){
        
    }
    
    protected function createCart($sessionId){
        $store = Mage::app()->getStore();
        $stroeId = $store->getId();
        $proxy = new SoapClient($this->soapURLv2);
        
        
        
        return $proxy->shoppingCartCreate($sessionId, $stroeId);
    }
    
    protected function initShipping($sessionId, $quoteId){
        $proxy = new SoapClient($this->soapURLv2);
        //initialize with customer address to cope with magento not calculating subtotal bug
        return $proxy->shoppingCartCustomerAddresses($sessionId, $quoteId, array(array(
            'mode' => 'shipping',
            'firstname' => 'Creo',
            'lastname' => 'Mobile',
            'street' => 'Rehman Baba Road',
            'city' => 'Islamabad',
            'region' => '',
            'postcode' => '46000',
            'country_id' => 'PK',
            'telephone' => '03125511678',
            'is_default_billing' => 1
            ),
            array(
            'mode' => 'billing',
            'firstname' => 'Creo',
            'lastname' => 'Mobile',
            'street' => 'Rehman Baba Road',
            'city' => 'Islamabad',
            'region' => '',
            'postcode' => '46000',
            'country_id' => 'PK',
            'telephone' => '03125511678',
            'is_default_billing' => 1
            ),
            ));
    }
    
    public function createCartAction(){
        $sessionId = $this->getRequest()->getParam('sid');
        $cart_id = $this->createCart($sessionId);
        
        $this->initShipping($sessionId,$cart_id);
        
//        header('Access-Control-Allow-Origin: *');
        header("Content-Type: application/json");
        echo json_encode(array('cart_id'=>$cart_id));
        die;
    }
    
    protected function getCart($sessionId,$quoteId){
        
        $proxy = new SoapClient($this->soapURLv2);
        
        return $proxy->shoppingCartInfo($sessionId, $quoteId);
    }
    
    protected function getCartTotals($sessionId,$quoteId){
        
        $proxy = new SoapClient($this->soapURLv2);
        return $proxy->shoppingCartTotals($sessionId, $quoteId);
    }
    
    public function getShippingMethods($sessionId,$cartId){
        
        $proxy = new SoapClient($this->soapURLv2);
        $result = $proxy->shoppingCartShippingList($sessionId, $cartId); 
        return $result;
    }
    
    public function getPaymentMethods($sessionId,$cartId){
        $proxy = new SoapClient($this->soapURLv2);
        $result = $proxy->shoppingCartPaymentList($sessionId, $cartId); 
        return $result;
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
                $shipping_flatrate_price = Mage::getStoreConfig('carriers/flatrate/price');
//                $cart = Mage::getModel('sales/quote')->load($quoteId);
//                print_r($cart); //die;
//                echo $totalItems = Mage::getModel('sales/quote')->load($quoteId)->getItemsCount();  echo '<br>';
//                echo $subTotal = Mage::getModel('sales/quote')->load($quoteId)->getSubtotal();  echo '<br>';
//                echo $grandTotal = Mage::getModel('sales/quote')->load($quoteId)->getGrandTotal();   echo '<br>';
//                echo $totalQuantity = Mage::getModel('sales/quote')->load($quoteId)->getItemsQty(); die;
                
                $shipping_methods = $this->getShippingMethods($sessionId,$quoteId);
                $payment_methods = $this->getPaymentMethods($sessionId,$quoteId);
                
                $response['success'] = 1;
                $response['message'] = 'Cart data found';
                
                $data = (array)$dataObj;
                $i = 0;
                foreach($data['items'] as $item){
                    $product_id = $item->product_id;
                    $product = Mage::getModel('catalog/product')->load($product_id);
                    $image = (string)Mage::helper('catalog/image')->init($product,'small_image');
//                    
                    $extra['product_id'] = $product_id;
                    $extra['img'] = $image;
                    $extra['img2'] = $product->getImageUrl();
//                    $extra['price'] = $product->getPrice();
                    $extra['color_id'] = $product->getData('color');
                    $extra['color_value'] = $product->getAttributeText('color');
                    $extra['size_id'] = $product->getSize();
                    $extra['size_value'] = $product->getAttributeText('size');
                    $extra['in_stock_qty'] = (int)$product->getStockItem()->getQty();
                    $extra['min_sale_qty'] = $product->getStockItem()->getMinSaleQty();
                    $extra['max_sale_qty'] = $product->getStockItem()->getMaxSaleQty();
                    
                    $extended_items = array_merge((array)$item,$extra);
                    $data['items'][$i] = $extended_items;
                    $i++;
                }
                $data['shipping_flatrate_price'] = $shipping_flatrate_price;
                $data['shipping_methods'] = $shipping_methods;
                $data['payment_methods'] = $payment_methods;
                $response['cart'] = $data;
            } catch(Exception $e){
                $response['error_code'] = $e->getCode();
                $response['message'] = $e->getMessage();
                $response['trace'] = $e->getTraceAsString();
            }
        }
        
//        header('Access-Control-Allow-Origin: *');
        header("Content-Type: application/json");
        echo json_encode($response);
        die;
    }
    
    protected function addProduct($sessionId,$quoteId,$productId,$qty,$custom_options){
        
        $proxy = new SoapClient($this->soapURLv2);
        $options=array();
        foreach ($custom_options as $key=>$val){
            if($val!=''){
                $options[] = array('key'=>$key,'value'=>$val);
            }
        }
        return $proxy->shoppingCartProductAdd($sessionId, $quoteId, array(array(
            'product_id' => $productId,
            'qty' => $qty,
            'options' => $options,
            'bundle_option' => null,
            'bundle_option_qty' => null,
            'links' => null
            )));  
    }
    
    public function addProductAction(){
        
        $sessionId = $this->getRequest()->getParam('sid');
        $quoteId = $this->getRequest()->getParam('qid');
        
        $product = json_decode(file_get_contents("php://input"));
//        $productId = $this->getRequest()->getParam('pid');
//        $qty = $this->getRequest()->getParam('qty');
        $productId = $product->product_id;
        $qty = $product->qty;
        $custom_options = $product->custom_options;
        
        if(!$qty){
            $qty = 1;
        }
        
        $response = array('success'=>0,'message'=>'','res'=>null);
        
        try{
            $res = $this->addProduct($sessionId,$quoteId,$productId,$qty,$custom_options);
            $response['success'] = 1;
            $response['message'] = 'Product added successfully';
            $response['res'] = $res;
        }catch(Exception $e){
                $response['error_code'] = $e->getCode();
                $response['message'] = $e->getMessage();
        }
        
//        header('Access-Control-Allow-Origin: *');
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
        
//        header('Access-Control-Allow-Origin: *');
        header("Content-Type: application/json");
        echo json_encode($response);
        die;
    }
    
    public function setCustomerAddress($sessionId,$qid,$address_array){
        $proxy = new SoapClient($this->soapURLv2);
        $shipping = $address_array;
        $shipping['mode']='shipping';
        $billing = $address_array;
        $billing['mode']='billing';
        return $proxy->shoppingCartCustomerAddresses($sessionId, $qid, array($shipping,$billing)); 
    }
    
    
    protected function setCustomer($sessionId,$cartId,$customer){
        $proxy = new SoapClient($this->soapURLv2);
        $customer['mode'] = 'customer';
        return $proxy->shoppingCartCustomerSet($sessionId, $cartId, $customer);
    }
    
    protected function setGuestCustomer($sessionId,$cartId,$shipping_data){
        $proxy = new SoapClient($this->soapURLv2);
        $customer = array('mode'=>'guest',
            'email'=>$shipping_data['email'],
            'firstname'=>$shipping_data['firstname'],'lastname'=>$shipping_data['lastname']);
        return $proxy->shoppingCartCustomerSet($sessionId, $cartId, $customer);
    }
    
    public function setShippingAddressAction(){
        
        $sessionId = $this->getRequest()->getParam('sid');
        $qid = $this->getRequest()->getParam('qid');
        
        $address_customer_data = json_decode(file_get_contents('php://input'),true);
        $response = array('success'=>0,'message'=>'','res'=>null);
        try{
            $res = $this->setCustomerAddress($sessionId,$qid,$address_customer_data['shipping']);
            
            if(count($address_customer_data['customer']) && !empty((array) $address_customer_data['customer'])){
                $this->setCustomer($sessionId,$qid,$address_customer_data['customer'][0]);
            } else {
                $this->setGuestCustomer($sessionId,$qid,$address_customer_data['shipping']);
            }
            
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
    
    public function updateProductQty($sessionId,$qid,$productId,$qty){
        $proxy = new SoapClient($this->soapURLv2);
        
        return $proxy->shoppingCartProductUpdate($sessionId, $qid, array(array(
                'product_id' => $productId,
                'qty' => $qty,
                'options' => null,
                'bundle_option' => null,
                'bundle_option_qty' => null,
                'links' => null
                )));
    }


    public function updateProductQtyAction(){
        
        $sessionId = $this->getRequest()->getParam('sid');
        $qid = $this->getRequest()->getParam('qid');
        $productId = $this->getRequest()->getParam('pid');
        $qty = $this->getRequest()->getParam('qty');
        
        
        $response = array('success'=>0,'message'=>'','res'=>null);
        try{
            $res = $this->updateProductQty($sessionId,$qid,$productId,$qty);
            
            $response['success'] = 1;
            $response['message'] = 'Quantity updated successfully';
            $response['res'] = $res;
        } catch(Exception $e){
            $response['error_code'] = $e->getCode();
            $response['message'] = $e->getMessage();
        }
        
        
        header("Content-Type: application/json");
        echo json_encode($response);
        die;
        
    }
    
    public function updateCart($sessionId,$qid,$products){
        $proxy = new SoapClient($this->soapURLv2);
        
        return $proxy->shoppingCartProductUpdate($sessionId, $qid, $products);
    }
    
    public function updateCartQtyAction(){
        $sessionId = $this->getRequest()->getParam('sid');
        $qid = $this->getRequest()->getParam('qid');
        
        $request_data = json_decode(file_get_contents('php://input'),true);
        
        $product_update = array();
        foreach($request_data as $product){
            $product_update[] = array("product_id"=>$product['product_id'],"qty"=>$product['qty']);
        }
        
        $response = array('success'=>0,'message'=>'','res'=>null);
        try{
            $res = $this->updateCart($sessionId,$qid,$product_update);
            
            $response['success'] = 1;
            $response['message'] = 'Cart updated successfully';
            $response['res'] = $res;
        } catch(Exception $e){
            $response['error_code'] = $e->getCode();
            $response['message'] = $e->getMessage();
        }
        
        header("Content-Type: application/json");
        echo json_encode($response);
        die;
    }
    
    public function getCurrencyRates(){
        $currencyModel = Mage::getModel('directory/currency');
        $currencies = $currencyModel->getConfigAllowCurrencies();
        $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
        $defaultCurrencies = $currencyModel->getConfigBaseCurrencies();
        $rates=$currencyModel->getCurrencyRates($defaultCurrencies, $currencies);
        $currency_data = array('allowed_currencies'=>$currencies,'base_currency'=>$baseCurrencyCode,
            'currency_rates'=>$rates);
        return $currency_data;
    }
    
    public function getCurrencyRatesAction(){
        $currency_data = $this->getCurrencyRates();
        
        header("Content-Type: application/json");
        echo json_encode($currency_data);
        die;
    }
    
    public function getShippingAddress($sessionId, $quoteId){
        
        $cart = (array)$this->getCart($sessionId,$quoteId);
        $address = $cart['billing_address'];
        return $address;
    }
    
    public function getShippingAddressAction(){
        $sessionId = $this->getRequest()->getParam('sid');
        $qid = $this->getRequest()->getParam('qid');
        
        $data = $this->getShippingAddress($sessionId,$qid);
        
//        header('Access-Control-Allow-Origin: *');
        header("Content-Type: application/json");
        print_r(json_encode($data));
        die;
    }
}
