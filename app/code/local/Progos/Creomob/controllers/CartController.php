<?php

class Progos_Creomob_CartController extends Mage_Core_Controller_Front_Action{
    
    public function indexAction(){
        
        $cart = Mage::getSingleton('checkout/session')->getQuote();
        if($cart==null){
            $data[] = 0;
        }
        
        $currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
        $currency_symbol = Mage::app()->getLocale()->currency( $currency_code )->getSymbol();
        
        $totals = Mage::getSingleton('checkout/cart')->getQuote()->getTotals();
        $subtotal = $totals["subtotal"]->getValue();
        
        //$data['quote']['subtotal'] = $subtotal;
//echo "cart total - >",Mage::helper('checkout/cart')->getItemsCount();
//        if(count($cart->getAllItems())){
//             echo 'items found';die;
//        } else{
//             echo 'items not found';die;
//        }
        foreach ($cart->getAllItems() as $item) {
            $prod['quote_id'] = $item->getId();
            $prod['product_id'] = $item->getProductId();
            
            
            $product = Mage::getModel('catalog/product')->load($prod['product_id']);
            
            $prod['id'] = $product->getId();
            $prod['name'] = $product->getName();
            $prod['img'] = $product->getImageUrl();
            $prod['price'] = $item->getPrice();
            $prod['color_id'] = $product->getData('color');
            $prod['color_value'] = $product->getAttributeText('color');
            $prod['size_id'] = $product->getSize();
            $prod['size_value'] = $product->getAttributeText('size');
            $prod['currency_code'] = $currency_code;
            $prod['currency_symbol'] = $currency_symbol;
            $prod['qty'] = $item->getQty();
            $prod['min_sale_qty'] = $product->getStockItem()->getMinSaleQty();
            $prod['max_sale_qty'] = $product->getStockItem()->getMaxSaleQty();
            
            $data['product'][] = $prod;
        }
        header("Content-Type: application/json");
        print_r(json_encode($data));
        die;
    }
    
    public function addAction(){
        
        
        //get data from angular post
        //$params = json_decode(file_get_contents('php://input'),true);
//        $id = (int)$params->id;
//        $qty = (int)$params->qty;
        
        $id = (int)$this->getRequest()->getParam('id');
        $qty = (int)$this->getRequest()->getParam('qty');
        
        //$configurable_options = $params->configurable_options;
        
        $_product = Mage::getModel('catalog/product')->load($id);
        $cart = Mage::getModel('checkout/cart');
        $res = array();
        
        try{
            $cart->init();
            $cart->addProduct($_product, array('qty' => $qty/*,'options' => $configurable_options*/));
            $cart->save();
            Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
            $res = array(
                'success'=>1,
                'message'=>'Product added successfully'
            );
        } catch(Exception $ex){
            $res = array(
                'success'=>0,
                'message'=>$ex->getMessage()
            );
        }
        header("Content-Type: application/json");
        print_r(json_encode($res));
        die;
    }
    
    public function removeAction(){
        $id = (int)$this->getRequest()->getParam('id');
        $flag = 0;
        
        $session= Mage::getSingleton('checkout/session');
        $quote = $session->getQuote();

        $cart = Mage::getModel('checkout/cart');
        $cartItems = $cart->getItems();
        foreach ($cartItems as $item)
        { 
            if($item->getProductId()==$id){
                $quote->removeItem($item->getId());
                $flag = 1;
            }
        }
        $cart->save();
        Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
        
        header("Content-Type: application/json");
        print_r(json_encode(array($flag)));
        die;
    }
    
    public function cartmetaAction(){
        $sub_total = Mage::getSingleton('checkout/session')->getQuote()->getSubtotal();
        $currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
        $currency_symbol = Mage::app()->getLocale()->currency( $currency_code )->getSymbol();
        $items_count = Mage::helper('checkout/cart')->getItemsCount();
        
        $res = array('sub_total'=>$sub_total,'currency_code'=>$currency_code,
            'currency_symbol'=>$currency_symbol,'items_count'=>$items_count);
        
        header("Content-Type: application/json");
        print_r(json_encode(array($res)));
        die;
    }
}