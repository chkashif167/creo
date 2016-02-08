<?php
class Progos_Recentproducts_Block_Recentproducts extends Mage_Core_Block_Template {
  public function getRecentProducts() {
    // call model to fetch data
    $arr_products = array();
    $products = Mage::getModel("recentproducts/recentproducts")->getRecentProducts();
  
    foreach ($products as $product) {
      $arr_products[] = array(
        'id' => $product->getId(),
        'name' => $product->getName(),
        'url' => $product->getProductUrl(),
        'image' => $product->getProductImageUrl(),
      );
    }
  
    return $arr_products;
  }
}