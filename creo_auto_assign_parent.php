<?php

chdir(dirname(__FILE__));
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'app/Mage.php';
Mage::app();

$categories = Mage::getModel('catalog/category')
        ->getCollection()
//        ->addAttributeToSelect('*')
        ->addAttributeToSelect('name')
        ->addAttributeToSelect('id')
        ->addAttributeToSelect('level')
        ->addAttributeToSelect('parent_id')
        ->addIsActiveFilter()
        ->addAttributeToFilter('Level',array('gteq'=>'3'));
//print_r($categories->getData());
foreach ($categories as $_category){
       echo "ID ->",$_category->getId(),", Level->",$_category->getLevel(),", Parent->",$_category->getParentId(),":"
               ,", Name->",$_category->getName(),"<br>";
}


echo "<hr>";
echo "<hr>";

echo "Get assosiated products for each category";

foreach ($categories as $_category){
        $categoryId = $_category->getId();
        echo "<hr> Category : ";
        echo $_category->getName(),"(",$_category->getId(),")";
        echo "<br>";
       $products = Mage::getModel('catalog/product')
            ->getCollection()
//            ->addAttributeToSelect('*')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('type_id')
            ->addCategoryFilter($_category)
            ->load();
//       print_r($products->getData());
       
       foreach($products as $product){
           echo " * Products name : ",$product->getName()," (",$product->getTypeId(),")";
           if($product->getTypeId()=="configurable"){
               //product is configurable get all associated products
               $configurable = Mage::getModel('catalog/product_type_configurable')->setProduct($product);
                $simpleCollection = $configurable->getUsedProductCollection()
                        ->addAttributeToSelect('name')
                        ->addAttributeToSelect('id');
                if(count($simpleCollection)){
                    echo "{ simple products -> ";
                    
                    foreach ($simpleCollection as $sp){
                        echo " , ",$sp->getName(), "(".$sp->getId().")";
                        $currentCategories = $sp->getCategoryIds();
                        $current_categories = print_r($currentCategories,true);
                        echo "(current categories -> ",$current_categories,")";
                        if(in_array($categoryId, $currentCategories)){
                        echo "-==assigned==-";
                        }else {
                            echo "-==not assigned==-";
                            echo "assigning $categoryId to product ";
                            $categories = $currentCategories;
                            $categories[] = $categoryId;
                            $sp->setCategoryIds($categories);
                            $sp->save();
//                            Mage::getSingleton('catalog/category_api')
//                                    ->assignProduct($categoryId,$sp->getId());
                        }
                    }
                    echo "}";
                }
           }
       }
       echo "<hr>";
}


       echo "~~~~~~ Executed successfully ~~~~~~~";

?>
