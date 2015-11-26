<?php
chdir(dirname(__FILE__));
require_once 'app/Mage.php';
Mage::app();
//$collection = Mage::getResourceModel('catalog/product_collection');
//$collection->addAttributeToSelect('*');
$product = Mage::getModel('catalog/product')->load(50899);


echo "<pre>";
print_r($product->getData());
echo "</pre>";

echo "<pre>";
print_r($product->getCategoryIds());
echo "</pre>";
$catarray = $product->getCategoryIds();

$categories[] = 23;
$product->setCategoryIds($categories);
$product->save();

//$cats = $product->getCategoryIds();
//$collection->addAttributeToFilter('universal_categories', '233');
//$collection->addAttributeToFilter('is_salable',1);
//$collection->addAttributeToFilter('status',1);
	
//$collection->addCategoryIds();
//$collection->addAttributeToFilter('universal_categories','Love');
//$collection->addAttributeToSelect('description');
/*
$collection->getSelect()->limit(10);
echo "<pre>";
print_r($collection->getData());
echo "</pre>";

foreach($collection as $c){
echo "<pre>";
print_r($c->getData());
echo "</pre>";

echo "<pre>";
print_r($c->getCategoryIds());
echo "</pre>";
 

}
*/
