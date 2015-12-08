<?php

class Progos_Creomob_ProductsController extends Mage_Core_Controller_Front_Action {
    
    protected $page_size = 100;

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
        $collection->addAttributeToFilter('status',array('eq' => 1));
//                ->addAttributeToFilter('type_id','configurable');
        
            /*$collection->joinField(
                'category_id', 'catalog/category_product', 'category_id', 
                'product_id = entity_id', null, 'left'
            );*/
            
            //$collection->addAttributeToFilter('category_id',array('nin'=>$exc));
        
        
        //applying filters
        if ($filter && $filter == 1) {
            $filters = json_decode(file_get_contents("php://input"), true);
//            print_r($filters);die;
            $category_filters = $filters['category'];
            $category_filters_ids = array();
            
            //foreach ($category_filters as $sub_category) {
            $sub_category = $category_filters['Categories'];
                if (is_array($sub_category)) {
                    foreach ($sub_category as $key => $val) {
                        if ($val == true)
                            $category_filters_ids[] = $key;
                    }
                }
            //}
            $currnt_category_id = $filters['category_meta']['currenct_category_id'];
            
//            print_r($category_filters_ids);
//            echo 'Current category ',$currnt_category_id;die;
            
            $clothing = array(3,57,58,17,59);
            $polos = array(18,60);
            $accessories = array(5,40,42);
            $caps = array(54,55,56);
            $categories_categories = array(6,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39);
            $color_categories = array_merge($clothing,$categories_categories);
            $size_categories = array_merge($clothing,$polos);
            $styles_categories = array_merge($clothing,$categories_categories);
            $gender_categories = array_merge($categories_categories);
            
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
            $styles_filters = $filters['attr']['style'];
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
           
            
            

            if (!empty($category_filters_ids)) {
//                $collection->addCategoryFilter(Mage::getModel('catalog/category')->load(array(implode(',', $category_filters_ids))), true);
                $collection->joinField(
                    'category_id', 'catalog/category_product', 'category_id', 
                    'product_id = entity_id', null, 'left'
                );
                $collection->addAttributeToFilter('category_id',array('in'=>$category_filters_ids));
            }
            
            if (!empty($colors) && in_array($currnt_category_id, $color_categories)) {
                $collection->addAttributeToFilter('color', array('in' => $colors));
            }
            if (!empty($sizes) && in_array($currnt_category_id, $size_categories)) {
                $collection->addAttributeToFilter('size', array('in' => $sizes));
            }
            if (!empty($gender) && in_array($currnt_category_id,$gender_categories)) {
                $collection->addAttributeToFilter('gender', array('in' => $gender));
            }
            if (!empty($styles) && in_array($currnt_category_id,$styles_categories)) {
                
                $collection->addAttributeToFilter('styles', array('in' => $styles));
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
        $products = $collection->addAttributeToSelect('id')
                ->addAttributeToSelect('type')->addAttributeToSelect('name')
                ->addAttributeToSelect('image')->addAttributeToSelect('price')
                ->addAttributeToSelect('qty')->addAttributeToSelect('min_qty')
                ->addAttributeToSelect('min_sale_qty')->addAttributeToSelect('status')
                ->addAttributeToSelect('category_ids')->addAttributeToSelect('small_image')
                ->setPageSize($this->page_size)->setCurPage($page);//->load();
        $total_pages = $collection->getLastPageNumber();
        
        //Assign data to each product
        $currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
        $product_ids = array(); //keeps track of existing product to handle duplicate
//         print_r($products->getData());die();
//        echo $collection->getSelect();die();
//        print_r($products);die();
        foreach ($products as $p2) {//print_r($p2);echo "<hr>";die;
            if(in_array(42,$p2->getCategoryIds())) continue;
//            $p2 = Mage::getModel('catalog/product')->load($p->getId());
            //$img = (string)Mage::helper('catalog/image')->init($p2, 'small_image')->resize(200,200);
            
            $img_to_show = '';
            if($p2->getTypeId()=='simple'){
                $parentIds = Mage::getResourceSingleton('catalog/product_type_configurable')->getParentIdsByChild($p2->getId());
                $img_to_show = (string)Mage::helper('catalog/image')->init($p2,'small_image');
                if (isset($parentIds[0])) {
                    $p2 = Mage::getModel('catalog/product')->load($parentIds[0]);
                }
            } else {
                $img_to_show = (string)Mage::helper('catalog/image')->init($p2,'small_image');
            }
            
            $id = $p2->getId();
            if(in_array($id,$product_ids)) continue;
            array_push($product_ids, $id);
            
       
//            $image = (string)Mage::helper('catalog/image')->init($p2,'small_image');//->resize(600,600);
            $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($p2);
            $prod['id'] = $p2->getId();
            $prod['name'] = $p2->getName();
            $prod['img'] = $img_to_show;//$image;
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
        $data['filters'] = $filters;
        

//        header('Access-Control-Allow-Origin: *');
        header("Content-Type: application/json");
        print_r(json_encode($data));
        die;
    }
    
    public function productsv2Action() {
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
        $collection->addAttributeToFilter('status',array('eq' => 1));
        
        
        //applying filters
        if ($filter && $filter == 1) {
            $filters = json_decode(file_get_contents("php://input"), true);
            
            $attr_filters = $filters['attr'];
            $attr_filters_cleaned = array();
            
            foreach($attr_filters as $attr_name=>$attr_vals){
                if(is_array($attr_vals)){
                    foreach ($attr_vals as $key=>$val){
                        if($val==1){
                            $attr_filters_cleaned[$attr_name][] = $key;
                        }
                    }
                }
            }

            $helper = Mage::helper('creomob/data');
             $attributes = $helper->getAttributesFromCategoryId($categoryId);
            
            foreach ($attr_filters_cleaned as $attr_key=>$vals){
                if(!in_array($attr_key, $attributes)){} continue;
                if(is_array($vals) && !empty($vals)){
                    $collection->addAttributeToFilter($attr_key, array('in' => $vals));
                }
            }
            
            
        }else {
            $collection->addAttributeToFilter("visibility",array("gt"=>1));
        }
        
        
        //load products from collection
        $products = $collection->addAttributeToSelect('id')
                ->addAttributeToSelect('type')->addAttributeToSelect('name')
                ->addAttributeToSelect('image')->addAttributeToSelect('price')
                ->addAttributeToSelect('qty')->addAttributeToSelect('min_qty')
                ->addAttributeToSelect('min_sale_qty')->addAttributeToSelect('status')
                ->addAttributeToSelect('category_ids')->addAttributeToSelect('small_image')
                ->setPageSize($this->page_size)->setCurPage($page);//->load();
        $total_pages = $collection->getLastPageNumber();
        
        //Assign data to each product
        $currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
        $product_ids = array(); //keeps track of existing product to handle duplicate

        foreach ($products as $p2) {//print_r($p2);echo "<hr>";die;
            //do not bring products from create category
            if(in_array(47,$p2->getCategoryIds())) continue;
       
            $img_to_show = '';
            if($p2->getTypeId()=='simple'){
                $parentIds = Mage::getResourceSingleton('catalog/product_type_configurable')->getParentIdsByChild($p2->getId());
                $img_to_show = (string)Mage::helper('catalog/image')->init($p2,'small_image');
                if (isset($parentIds[0])) {
                    $p2 = Mage::getModel('catalog/product')->load($parentIds[0]);
                }
            } else {
                $img_to_show = (string)Mage::helper('catalog/image')->init($p2,'small_image');
            }
            
            $id = $p2->getId();
            if(in_array($id,$product_ids)) continue;
            array_push($product_ids, $id);
            
            $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($p2);
            $prod['id'] = $p2->getId();
            $prod['name'] = $p2->getName();
            $prod['img'] = $img_to_show;//$image;
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
        $data['filters'] = $filters;
        

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

        $productAttributeOptions = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
        // $attributeOptions = array();
        // foreach ($productAttributeOptions as $productAttribute) {
        //     foreach ($productAttribute['values'] as $attribute) {
        //         $attributeOptions[$productAttribute['label']][$attribute['value_index']] = $attribute['store_label'];
        //     }
        // }






        $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
        //$stock = $product->getStockItem();


        $currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
        $currency_symbol = Mage::app()->getLocale()->currency($currency_code)->getSymbol();
        
        $custom_options = array();
        
        //Get product Options
        $options = $product->getOptions();
        if($options){

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
        // $prod['attributes'] = $attributeOptions;
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
                    'attribute_id' => $option_row['attribute_id'],
                    'code' => $option_row['attribute_code'],
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

    public function associatedProductsV2Action() {

        $confProdId = (int) $this->getRequest()->getParam('pid');
        $response = array();

        if ($confProdId) {
            //get all associalted products

            $request_data = json_decode(file_get_contents('php://input'), true);
            // print_r($request_data);die;
            $productId = $confProdId; //config product id
            $product = Mage::getModel('catalog/product')->load($productId);

            if ($product->getTypeId() == "configurable") {
                //product is configurable

                $configurable = Mage::getModel('catalog/product_type_configurable')->setProduct($product);

                $simpleCollection = $configurable->getUsedProductCollection();
                // if($color) $simpleCollection->addAttributeToFilter("Color", array("eq" => $color));
                // if($size) $simpleCollection->addAttributeToFilter("Size", array("eq" => $size));
                foreach ($request_data as $key => $value) {
                    $simpleCollection->addAttributeToFilter($key, array('eq' => $value ));
                }
                
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