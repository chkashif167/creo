<?php

class Progos_Creomob_ProductsController extends Mage_Core_Controller_Front_Action {
    
    public function indexAction(){
        
    }
    
    public function productsAction() {
        $categoryId = $this->getRequest()->getParam('cid');
        
        if($categoryId){
        $products = Mage::getModel('catalog/category')->load($categoryId)->getProductCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('thumbnail')
                ->addAttributeToSelect('small_image')
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('price')
                ->addAttributeToSelect('qty')
                ->addAttributeToSelect('status')
                ->load();
        } else{
            $products = Mage::getModel('catalog/product')->getCollection()
                        ->addAttributeToSelect('*');
        }
        

        
        $currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
        foreach ($products as $p2) {
            //$p2 = Mage::getModel('catalog/product')->load($p->getId());
            //$img = (string)Mage::helper('catalog/image')->init($p2, 'small_image')->resize(200,200);
            $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($p2);
            $prod['id'] = $p2->getId();
            $prod['name'] = $p2->getName();
            $prod['img'] = $p2->getImageUrl();
            $prod['price'] = $p2->getPrice();
            $prod['stock_qty'] = $stock->getQty();
            $prod['stock_qty_min'] = $stock->getMinQty();
            $prod['stock_qty_min_sales'] = $stock->getMinSaleQty();
            $prod['status'] = $p2->getStatus();
            $prod['currency'] = $currency_code;

            $data[] = $prod;
        }
        
        header("Content-Type: application/json");
        print_r(json_encode($data));
        die;
    }
    
    public function productAction() {
        $id = $this->getRequest()->getParam('id');
        $product = Mage::getModel('catalog/product')->load($id);
        
        $attributeSetModel = Mage::getModel("eav/entity_attribute_set");
        $attributeSetModel->load($product->getAttributeSetId());
        $attributeSetName  = $attributeSetModel->getAttributeSetName();
        
        //will return all attributes from product's attribute set
        $attributes = Mage::getModel('catalog/product_attribute_api')->items($product->getAttributeSetId());
        
        $color_attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'color');
        $size_attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'size');
        if ($color_attribute->usesSource()) {
            $color_options = $color_attribute->getSource()->getAllOptions(false);
        }
        if ($size_attribute->usesSource()) {
            $size_options = $size_attribute->getSource()->getAllOptions(false);
        }
        
        
        
        
        
        
        $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
        //$stock = $product->getStockItem();
        
         
        $currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
        $currency_symbol = Mage::app()->getLocale()->currency( $currency_code )->getSymbol();
        
        $prod['id'] = $product->getId();
        $prod['type_id'] = $product->getTypeId();
        $prod['name'] = $product->getName();
        $prod['img'] = $product->getImageUrl();
        $prod['price'] = $product->getPrice();
        $prod['status'] = $product->getStatus();
        $prod['stock_qty'] = (int)$stock->getQty();
        $prod['stock_qty_min'] = $stock->getMinQty();
        $prod['stock_qty_min_sales'] = $stock->getMinSaleQty();
        $prod['attribute_set'] = $attributeSetName;
        //$prod['attributes'] = $attributes;
        $prod['color_options'] = $color_options;
        $prod['size_options'] = $size_options;
        $prod['currency'] = $currency_code;
        $prod['currency_symbol'] = $currency_symbol;
        
        
        //handle simple/configurable product
        if($product->getTypeId() == 'simple' ){
            $prod['product_type'] = 'simple';
            $configurableOptions = array();
            
        } elseif($product->getTypeId() == 'configurable') {
            $prod['product_type'] = 'configurable';
            $configurableOptions = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
            //print_r($configurableOptions);die;
            foreach($configurableOptions as $option_row){
                //$configurable_options[] = array($option_row['label']=>$option_row['values']);
                $configurable_options[$option_row['label']] = array('id'=>$option_row['id'],
                    'values'=>$option_row['values']);
            }
        }
        
        $prod['configurable_options']=$configurable_options;
        
        header("Content-Type: application/json");
        print_r(json_encode($prod));
        die;
    }
    
    public function associatedProductsAction(){
        
        $confProdId = (int)$this->getRequest()->getParam('pid');
        $response = array();
        
        if($confProdId){
            //get all associalted products
            
            $request_data = json_decode(file_get_contents('php://input'),true);
            $color = $request_data['color'];
            $size = $request_data['size'];
            $productId = $confProdId; //config product id
            $product = Mage::getModel('catalog/product')->load($productId);
            
            if($product->getTypeId() == "configurable"){
                //product is configurable
                
                $configurable= Mage::getModel('catalog/product_type_configurable')->setProduct($product);
                
                $simpleCollection = $configurable->getUsedProductCollection()
                        ->addAttributeToFilter("Color",array("eq"=>$color))
                        ->addAttributeToFilter("Size",array("eq"=>$size))
                        ->addAttributeToSelect('*')
                        ->addFilterByRequiredOptions();

                //echo $simpleCollection->getSelect(),'<br>';
                if(count($simpleCollection)){
                    
                    
                        $currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
                        $currency_symbol = Mage::app()->getLocale()->currency( $currency_code )->getSymbol();
        
                        foreach($simpleCollection as $product){
                            $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());

                            $prod['id'] = $product->getId();
                            $prod['type_id'] = $product->getTypeId();
                            $prod['name'] = $product->getName();
                            $prod['img'] = $product->getImageUrl();
                            $prod['price'] = $product->getPrice();
                            $prod['status'] = $product->getStatus();
                            $prod['stock_qty'] = (int)$stock->getQty();
                            $prod['stock_qty_min'] = $stock->getMinQty();
                            $prod['stock_qty_min_sales'] = $stock->getMinSaleQty();
                            $prod['currency'] = $currency_code;
                            $prod['currency_symbol'] = $currency_symbol;

                            $response['product'][] = $prod;
                        }
                        
                        $response['success'] = 1;
                        $response['message'] = 'Associated products found';
                } else {
                    $response['success'] = 0;
                    $response['message'] = 'Associated products not found';
                }
                
            } else {
                $response['success'] = 0;
                $response['message'] = 'Product id not configurable';
            }
        } else {
            $response['success'] = 0;
            $response['message'] = 'Product id not provided';
        }
        
        header("Content-Type: application/json");
        print_r(json_encode($response));
        die;
        
    }
    
}