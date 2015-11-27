<?php
require_once 'app/Mage.php';
Mage::app();
$collection = Mage::getResourceModel('catalog/product_collection');
//->joinField('category_id','catalog/category_product','category_id','product_id=entity_id',null,'left')
//->addAttributeToFilter('category_id', '22');
$collection->addAttributeToSelect('*');
$collection->addAttributeToFilter('universal_categories', '155');
$collection->addAttributeToFilter('gender', '25');
//$collection->getSelect()->limit(10);
echo "<pre>";
print_r($collection->getData());
echo "</pre>";

/*
foreach($collection as $c){
echo "<pre>";
print_r($c->getData());
echo "</pre>";
$catrray = $c->getCategoryIds();
if (in_array(22, $catrray)) {
    echo "23 exist";
}else{
    echo "23 not exist";
}
array_push($catrray, 23, 24);
echo "<pre>";
print_r($catrray);
echo "</pre>";

}
*/
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


/*
$product = Mage::getModel('catalog/product')->load(50906);


echo "<pre>";
print_r($product->getData());
echo "</pre>";

echo "<pre>";
print_r($product->getCategoryIds());
echo "</pre>";
$catarray = $product->getCategoryIds();

$categories[] = 22;
$product->setCategoryIds($categories);
$product->save();
*/
