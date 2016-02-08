<?php
class Progos_Recentproducts_Model_Recentproducts extends Mage_Core_Model_Abstract {
  public function getRecentProducts($cat_to_select,$pcount) {
    
    $products = Mage::getModel("catalog/product")
                ->getCollection()
                ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                ->addAttributeToSelect('*')
                ->setOrder('entity_id', 'DESC')
                ->addAttributeToFilter('category_id', array('in' => $cat_to_select))
                ->setPageSize($pcount);

    return $products;
  }
}