<?php
require_once('app/Mage.php');
umask(0);
Mage::app('default');
Mage::app ()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
$productCollection = Mage::getModel('catalog/product')->getCollection();

foreach($productCollection as $product) 
{
	if( $product->getId() == '50906' ){
//		echo "<pre>";
//		print_r($product->getData());
//		$product->setName('name changed');
//	    $product->save();
		
		$product->setStoreId(2)->setName('arabic name')->save();
	}

//    $product = Mage::getModel('catalog/product')->load($_product->getEntityId());
//    $product->setData('add_ten_pence', 1)->getResource()->saveAttribute($product, 'add_ten_pence');
}