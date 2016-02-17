<?php
class Progos_Customproducts_Model_Customproducts extends Mage_Core_Model_Abstract {
  public function getCustomProducts($pids) {
    //print_r($pids);exit;
    
    $products = Mage::getModel("catalog/product")
                ->getCollection()
                ->addAttributeToSelect('*')
                ->setOrder('entity_id', 'DESC')
                ->addAttributeToFilter('entity_id', array('in' => $pids));
                
//$products->getSelect()->order("find_in_set(entity_id,'".implode(',',$pids)."')");
    return $products;
  }
}