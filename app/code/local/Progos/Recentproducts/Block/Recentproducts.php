<?php
class Progos_Recentproducts_Block_Recentproducts extends Mage_Core_Block_Template {
  public function getRecentProducts($cat_to_select,$pcount) {
    // call model to fetch data

    $arr_products = array();
    $products = Mage::getModel("recentproducts/recentproducts")->getRecentProducts($cat_to_select,$pcount);
  
    foreach ($products as $product) {
      $arr_products[] = array(
        'id' => $product->getId(),
        'name' => $product->getName(),
        'url' => $product->getProductUrl(),
        'image' => $product->getImage(),
        'thumb' => $product->getSmallImage(),
      );
    }
 
    return $arr_products;
  }
}