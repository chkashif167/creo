<?php
class Progos_Customproducts_Block_Customproducts extends Mage_Core_Block_Template {
  public function getRecentProducts($pids) {
    // call model to fetch data

    $arr_products = array();
    $products = Mage::getModel("customproducts/customproducts")->getCustomProducts($pids);
  
    foreach ($products as $product) {
      $arr_products[] = array(
        'id' => $product->getId(),
        'name' => $product->getName(),
        'url' => $product->getProductUrl(),
        'image' => $product->getImage(),
        'thumb' => $product->getSmallImage(),
        'sku' => $product->getSku(),
        'urlpath'=>$product->getData('url_path'),
        'urlkey'=>$product->getUrlKey(),
        'finalPrice'=>$product->getFinalPrice(),
      );
    }
 
    return $arr_products;
  }
}