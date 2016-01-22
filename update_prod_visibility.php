<?php
    require_once './app/Mage.php';
    Mage::app();
    Mage::setIsDeveloperMode(true);
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    set_time_limit(0);
    ini_set('memory_limit','3048M');
    //for single product
    $product_id = '175333';
    Mage::app('default');
     $_product = Mage::getModel('catalog/product')->load($product_id);
            $_product->setVisibility(4);
            $_product->getResource()->saveAttribute($_product, 'visibility');
    exit;

    /*$categoryid = $_GET['cat'];
    if($categoryid==''){
        echo "Please provide category id";	
        exit;
    }
    $category = new Mage_Catalog_Model_Category();
    $category->load($categoryid);
    $productCollection = $category->getProductCollection();
    $i=0;
    foreach($productCollection as $product){
        $product_id = $product->getId();
        echo "\n".'updating '.$product_id."...<br>";
        Mage::app('default');
        $product = Mage::getModel('catalog/product');
        $product->load($product_id);
        $product->setVisibility(4);
        $product->save(); 
        $i++;
    }*/
?>