<?php 
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Tagproducts
 * @copyright   Copyright (c) 2014 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)

 */

class Sashas_Tagproducts_Model_Layer extends Mage_Catalog_Model_Layer {
  
	/*  
	 * @see Mage_Catalog_Model_Layer::getProductCollection()
	 */
	public function getProductCollection()
	{
		$widget_params=Mage::registry('sashas_tagproducts_widget_params');
		$tag_ids=str_replace(',','_',$widget_params['tags']);
		 
		if (isset($this->_productCollections['tag_'.$tag_id])) {
			$collection = $this->_productCollections['tag_'.$tag_id];
			 
		} else {
			$collection = Mage::getResourceModel('catalog/product_collection')
			->setStoreId(Mage::app()->getStore()->getStoreId());
		 
			$this->prepareProductCollection($collection);
			$this->_productCollections['tag_'.$tag_id] = $collection;
		}
		 
		return $collection;
	}
	
	
	/*
	 * Initialize product collection
	 *
	 * @return Mage_Catalog_Model_Layer
	 */
	public function prepareProductCollection($collection)
	{
		$widget_params=Mage::registry('sashas_tagproducts_widget_params');		 
		$tag_ids=$widget_params['tags'];
		$tag_ids_array=explode(',',$tag_ids);
		
		$collection
		->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes()) 
		->addMinimalPrice()
		->addFinalPrice()
		->addTaxPercents()
		->addUrlRewrite(Mage::app()->getStore()->getRootCategoryId());  
		 		 
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
		$storeId = Mage::app()->getStore()->getId();
		
		$distinct_condition="";
		if (count($tag_ids_array)>1) {		
			$resource = Mage::getSingleton('core/resource');
			$readConnection = $resource->getConnection('core_read');
			$sql="SELECT GROUP_CONCAT(tag_relation_id) as ids FROM ".Mage::getSingleton('core/resource')->getTableName('tag/relation')." 
									WHERE tag_id IN (".$tag_ids.") AND store_id=".$storeId." 
									GROUP BY product_id  HAVING COUNT(product_id)>1";
			$dublicate_ids = $readConnection->query($sql)->fetchAll();
			 
			$dublicate_ids_array=array();
			foreach ($dublicate_ids as $dub_id) {
				$dub_id_array=explode(',', $dub_id['ids']);
				array_shift($dub_id_array);
				foreach ($dub_id_array as $dub_id_value) 
					$dublicate_ids_array[]=$dub_id_value;				
			}
			$distinct_condition="AND tag_relation.tag_relation_id NOT IN  (".implode(',',$dublicate_ids_array).")";
			$distinct_condition=new Zend_Db_Expr($distinct_condition);
		}		  			 
		$collection->getSelect()->join( array('tag_relation'=> Mage::getSingleton('core/resource')->getTableName('tag/relation')), 'tag_relation.product_id = e.entity_id AND tag_relation.tag_id IN ('.$tag_ids.') AND tag_relation.store_id='.$storeId.' '.$distinct_condition, array());
		//$collection->getSelect()->group(array('tag_relation.product_id'));
		$collection->getSelect()->distinct(true);
		//echo $collection->getSelect();
		return $this;
	}
	
}
?>