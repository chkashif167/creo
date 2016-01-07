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
 $domain = $_GET['dom'];
if($categoryid==''){
echo "Please provide category id";	
exit;
}
if($domain==''){
echo "Please provide domain";  
exit;
}

    $category = new Mage_Catalog_Model_Category();
    $category->load($categoryid);
    $productCollection = $category->getProductCollection();
	$i=0;
foreach($productCollection as $product) 
{

    if($domain=='beta'){
        $domain_name = 'http://beta.creoroom.com';
    }else{
        $domain_name = 'https://creoroom.com';
    }

    if($categoryid=='17'){ //Men-tshirt
        $attributeCode = "sizeguidemen";
        $att_text = '<img src="'.$domain_name.'/media/wysiwyg/Man_Size_Guide.jpg" alt="" class="size_guide_image size_guide_men_image" />';
        echo "\n".'updating men products'.$product->getSku()."...".$i."<br>";

    }elseif ($categoryid=='59') {// women tshirt
        $attributeCode = "sizeguidewomen";
        $att_text = '<img src="'.$domain_name.'/media/wysiwyg/Women_Size_Guide.jpg" alt="" class="size_guide_image size_guide_women_image" />';
        echo "\n".'updating women products'.$product->getSku()."...".$i."<br>";
    }

    $product = Mage::getModel('catalog/product')
                   ->load($product->getEntityId());
    $product->setData($attributeCode, $att_text)->getResource()->saveAttribute($product, $attributeCode);
$i++;
}