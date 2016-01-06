<?php
 require_once './app/Mage.php';
Mage::app();
Mage::setIsDeveloperMode(true);
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(0);
ini_set('memory_limit','3048M');
//ensure to set current store as product attributes are store specific
//Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
//$productCollection = Mage::getModel('catalog/product')->getCollection();
 $categoryid = $_GET['cat'];
//if($categoryid==''){
//echo "Please provide category id";	
//exit;
//}
$categories = Mage::getModel('catalog/category')
        ->getCollection()
        ->addAttributeToSelect('id')
        ->addIsActiveFilter();
        
foreach($categories as $category){


    //$category = new Mage_Catalog_Model_Category();
    //$category->load($categoryid);
    $productCollection = $category->getProductCollection();
    
	$i=0;
	foreach($productCollection as $product) 
	{
	    $attributeCode = "shipping_details";
	    echo "\n".'updating '.$product->getSku()."...".$i."<br>";
		//echo $product->getName()."<br>";
	    $product = Mage::getModel('catalog/product')
		           ->load($product->getEntityId());
	    $product->setData($attributeCode, "<p>FREE Express shipping on all orders over AED 118 (within UAE).<br>FREE Express shipping on all orders over SAR 250 (within Saudi)<br>AED 40 Flat Rate Express shipping (For All GCC countries)<br>USD 12 Flat Rate Express shipping (For US & Europe)<br>Please see Return and Exchange policy.<br></p>")->getResource()->saveAttribute($product, $attributeCode);
	$i++;
	}
	
}
