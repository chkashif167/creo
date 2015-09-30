<?php

class Progos_Creomob_ProductsController extends Mage_Core_Controller_Front_Action {
    
    protected $page_size = 20;

    public function indexAction() {
        
    }

    
    public function productsAction() {
        $categoryId = $this->getRequest()->getParam('cid');
        $search = $this->getRequest()->getParam('s');
        $filter = $this->getRequest()->getParam('filter');
        $page = (int)$this->getRequest()->getParam('page');
        $total_pages = 0;

        $products = null;
        $collection = null;
        
        if($search){
            $collection = Mage::getModel('catalog/product')->getCollection();
            $collection->addAttributeToFilter("name", array("like" => "%$search%"));
        }
        else if($categoryId){
            $category = Mage::getModel('catalog/category')->load($categoryId);
            $category_name = $category->getName();
            $data['category_id'] = $categoryId;
            $data['category_name'] = $category_name;

            $sub_categories = Mage::getModel('catalog/category')->getCategories($categoryId);

            foreach ($sub_categories as $c) {
                $sc = Mage::getModel('catalog/category')->load($c->getId());
                $cat['id'] = $sc->getId();
                $cat['name'] = $sc->getName();
                $cat['img'] = $sc->getImageUrl();
                $data['sub_categories'][] = $cat;
            }
            
            $collection = Mage::getModel('catalog/category')->load($categoryId)->getProductCollection();
        } else {
            $collection = Mage::getModel('catalog/product')->getCollection();
        }
        
//                ->addAttributeToFilter('type_id','configurable');
        
        //applying filters
        if ($filter && $filter == 1) {
            $filters = json_decode(file_get_contents("php://input"), true);
//            print_r($filters);die;
            $category_filters = $filters['category'];
            $category_filters_ids = array();
            
            foreach ($category_filters as $sub_category) {
                if (is_array($sub_category)) {
                    foreach ($sub_category as $key => $val) {
                        if ($val == true)
                            $category_filters_ids[] = $key;
                    }
                }
            }
            
            $color_filters = $filters['attr']['color'];
            $colors = array();
            foreach ($color_filters as $key => $val) {
                if ($val == '1')
                    $colors[] = $key;
            }
            $size_filters = $filters['attr']['size'];
            $sizes = array();
            foreach ($size_filters as $key => $val) {
                if ($val == '1')
                    $sizes[] = $key;
            }
            $gender_filters = $filters['attr']['gender'];
            $gender = array();
            foreach ($gender_filters as $key => $val) {
                if ($val == '1')
                    $gender[] = $key;
            }
            $styles_filters = $filters['attr']['styles'];
            $styles = array();
            foreach ($styles_filters as $key => $val) {
                if ($val == '1')
                    $styles[] = $key;
            }
            $price_filters = $filters['attr']['price'];
            $prices = array('min'=>-1,'max'=>-1);
            foreach ($price_filters as $key => $val) {
                $price_range = array();
                if ($val == '1'){
                    $price_range = explode('-',$key);
                    if($prices['min']==-1 || (int)$price_range[0]<$prices['min']){
                        $prices['min'] = (int)$price_range[0];
                    }
                    if((int)$price_range[1]>$prices['max']){
                        $prices['max'] = (int)$price_range[1];
                    }
                }
            } 
            
//           $collection = Mage::getModel('catalog/product')->getCollection();
//           
//            $collection->addAttributeToFilter("visibility",array("gt"=>1));
            
            

            if (!empty($category_filters_ids)) {
                $collection->addCategoryFilter(Mage::getModel('catalog/category')->load(array(implode(',', $category_filters_ids))), true);
            }
            
            if (!empty($colors)) {
                $collection->addAttributeToFilter('color', array('in' => $colors));
            }
            if (!empty($sizes)) {
                $collection->addAttributeToFilter('size', array('in' => $sizes));
            }
            if (!empty($gender)) {
                $collection->addAttributeToFilter('gender', array('in' => $gender));
            }
            if (!empty($styles)) {
                $collection->addAttributeToFilter('style', array('in' => $styles));
            }
            if($prices['min']!=-1){
                $collection->addAttributeToFilter('price', array('gteq' => $prices['min']));
            }

            if($prices['max']!=-1){
                $collection->addAttributeToFilter('price', array('lteq' => $prices['max']));
            }
        }else {
            $collection->addAttributeToFilter("visibility",array("gt"=>1));
        }
        
        
        //load products from collection
        $products = $collection->addAttributeToSelect('*')
                ->setPageSize($this->page_size)->setCurPage($page);//->load();
        $total_pages = $collection->getLastPageNumber();
        
        //Assign data to each product
        $currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
        foreach ($products as $p2) {//print_r($p2);echo "<hr>";
            //$p2 = Mage::getModel('catalog/product')->load($p->getId());
            //$img = (string)Mage::helper('catalog/image')->init($p2, 'small_image')->resize(200,200);
            
            $image = (string)Mage::helper('catalog/image')->init($p2,'small_image');//->resize(600,600);
            $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($p2);
            $prod['id'] = $p2->getId();
            $prod['name'] = $p2->getName();
            $prod['img'] = $image;
            $prod['img2'] = $p2->getImageUrl();
            $prod['price'] = $p2->getPrice();
            $prod['stock_qty'] = $stock->getQty();
            $prod['stock_qty_min'] = $stock->getMinQty();
            $prod['stock_qty_min_sales'] = $stock->getMinSaleQty();
            $prod['status'] = $p2->getStatus();
            $prod['currency'] = $currency_code;
            $prod['category_id'] = $p2->getCategoryIds(); //$categoryId;
            $prod['category_name'] = $category_name;
            

            $data['products'][] = $prod;
        }
        
        $data['total_pages'] = $total_pages;
        $data['current_page'] = $page;
        

//        header('Access-Control-Allow-Origin: *');
        header("Content-Type: application/json");
        print_r(json_encode($data));
        die;
    }
    
    
    

    public function productAction() {
        $id = $this->getRequest()->getParam('id');
        $product = Mage::getModel('catalog/product')->load($id);

        $attributeSetModel = Mage::getModel("eav/entity_attribute_set");
        $attributeSetModel->load($product->getAttributeSetId());
        $attributeSetName = $attributeSetModel->getAttributeSetName();

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
        $currency_symbol = Mage::app()->getLocale()->currency($currency_code)->getSymbol();
        
        $custom_options = array();
        
        //Get product Options
        $options = $product->getOptions();
        if($options)
        {

            foreach ($options as $option) {

                $options_array['option_id'] = $option->getOptionId();
                $options_array['default_title'] = $option->getDefaultTitle();
                foreach ($option->getValues() as  $values) {
                    $options_array['values'][] = $values->getData();
                }
                $custom_options[] = $options_array;
            }
        }

        $image = (string)Mage::helper('catalog/image')->init($product,'small_image');//->resize(600,600);

        $prod['id'] = $product->getId();
        $prod['type_id'] = $product->getTypeId();
        $prod['name'] = $product->getName();
        $prod['img'] = $image;
        $prod['img2'] = $product->getImageUrl();
        $prod['description'] = $product->getDescription();
        $prod['product_care'] = $product->getDesign();
        $prod['price'] = $product->getPrice();
        $prod['status'] = $product->getStatus();
        $prod['stock_qty'] = (int) $stock->getQty();
        $prod['stock_qty_min'] = $stock->getMinQty();
        $prod['stock_qty_min_sales'] = $stock->getMinSaleQty();
        $prod['attribute_set'] = $attributeSetName;
        //$prod['attributes'] = $attributes;
        $prod['color_options'] = $color_options;
        $prod['size_options'] = $size_options;
        $prod['currency'] = $currency_code;
        $prod['currency_symbol'] = $currency_symbol;
        $prod['custom_options'] = $custom_options;


        //handle simple/configurable product
        if ($product->getTypeId() == 'simple') {
            $prod['product_type'] = 'simple';
            $configurableOptions = array();
        } elseif ($product->getTypeId() == 'configurable') {
            $prod['product_type'] = 'configurable';
            $configurableOptions = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
            //print_r($configurableOptions);die;
            foreach ($configurableOptions as $option_row) {
                //$configurable_options[] = array($option_row['label']=>$option_row['values']);
                $configurable_options[$option_row['label']] = array('id' => $option_row['id'],
                    'values' => $option_row['values']);
            }
        }

        $prod['configurable_options'] = $configurable_options;

//        header('Access-Control-Allow-Origin: *');
        header("Content-Type: application/json");
        print_r(json_encode($prod));
        die;
    }

    public function associatedProductsAction() {

        $confProdId = (int) $this->getRequest()->getParam('pid');
        $response = array();

        if ($confProdId) {
            //get all associalted products

            $request_data = json_decode(file_get_contents('php://input'), true);
            $color = $request_data['color'];
            $size = $request_data['size'];
            $productId = $confProdId; //config product id
            $product = Mage::getModel('catalog/product')->load($productId);

            if ($product->getTypeId() == "configurable") {
                //product is configurable

                $configurable = Mage::getModel('catalog/product_type_configurable')->setProduct($product);

                $simpleCollection = $configurable->getUsedProductCollection();
                if($color) $simpleCollection->addAttributeToFilter("Color", array("eq" => $color));
                if($size) $simpleCollection->addAttributeToFilter("Size", array("eq" => $size));
                
                $simpleCollection ->addAttributeToSelect('*')
                        ->addFilterByRequiredOptions();

                //echo $simpleCollection->getSelect(),'<br>';
                if (count($simpleCollection)) {


                    $currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
                    $currency_symbol = Mage::app()->getLocale()->currency($currency_code)->getSymbol();

                    foreach ($simpleCollection as $product) {
                        $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
                        $image = (string)Mage::helper('catalog/image')->init($product,'small_image');//->resize(600,600);

                        $prod['id'] = $product->getId();
                        $prod['type_id'] = $product->getTypeId();
                        $prod['name'] = $product->getName();
                        $prod['img'] = $image;
                        $prod['img2'] = $product->getImageUrl();
                        $prod['price'] = $product->getPrice();
                        $prod['status'] = $product->getStatus();
                        $prod['stock_qty'] = (int) $stock->getQty();
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

//        header('Access-Control-Allow-Origin: *');
        header("Content-Type: application/json");
        print_r(json_encode($response));
        die;
    }

    
    public function getFilteredProducts($filters) {
        //retrieve category ids from request
        
        $products = $collection->addAttributeToSelect('*');

        return $products;
    }
}