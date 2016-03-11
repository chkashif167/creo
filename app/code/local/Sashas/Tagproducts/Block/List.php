<?php 
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Tagproducts
 * @copyright   Copyright (c) 2014 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)

 */

class Sashas_Tagproducts_Block_List extends Mage_Catalog_Block_Product_List {
	
	protected function _construct()
	{
	 	parent::_construct();
	 	 
	}
	
 
	/* 
	 * @see Mage_Catalog_Block_Product_List::getLayer()
	 */
	public function getLayer()
	{  	 	 
		$layer = Mage::registry('current_layer');
		if ($layer) {
			return $layer;
		}
		return Mage::getSingleton('tagproducts/layer');
	}
	
	 
	/*  
	 * @see Mage_Catalog_Block_Product_List::_getProductCollection()
	 */
	protected function _getProductCollection()
	{  
		if (is_null($this->_productCollection)) {
			$layer = $this->getLayer();
			/* @var $layer Mage_Catalog_Model_Layer */
			if ($this->getShowRootCategory()) {
				$this->setCategoryId(Mage::app()->getStore()->getRootCategoryId());
			}
			 	 
			$this->_productCollection = $layer->getProductCollection(); 	
			 
		}
		 
		return $this->_productCollection;
	}
	
 
	/*  
	 * @see Mage_Catalog_Block_Product_List::_beforeToHtml()
	 */
	protected function _beforeToHtml()
	{
		$widget_params=Mage::registry('sashas_tagproducts_widget_params');
		$products_per_page=$widget_params['products_per_page'];
		$this->setColumnCount($widget_params['products_per_row']);
		$this->setSortBy($widget_params['sort_by']);
		 
		$toolbar = $this->getToolbarBlock();
		$block=$this->getLayout()->createBlock('page/html_pager');
		$toolbar->setChild('product_list_toolbar_pager',$block);
		//$toolbar->setAvaiableMode( $widget_params['default_mode']); 
		
		foreach (explode(',',$products_per_page) as $limit) {
			$toolbar->addPagerLimit('grid', $limit);
			$toolbar->addPagerLimit('list', $limit);
		}
		 
		// called prepare sortable parameters
		$collection = $this->_getProductCollection();
	
		// use sortable parameters
		if ($orders = $this->getAvailableOrders()) {
			$toolbar->setAvailableOrders($orders);
		}
		if ($sort = $this->getSortBy()) {
			$toolbar->setDefaultOrder($sort);
		}
		if ($dir = $this->getDefaultDirection()) {
			$toolbar->setDefaultDirection($dir);
		}
		if ($modes = $this->getModes()) {
			$toolbar->setModes($modes);
		}
		 
		// set collection to toolbar and apply sort
		$toolbar->setCollection($collection);
	
		$this->setChild('toolbar', $toolbar);
		Mage::dispatchEvent('catalog_block_product_list_collection', array(
				'collection' => $this->_getProductCollection()
		));
	
		$this->_getProductCollection()->load();
	
		return Mage_Catalog_Block_Product_Abstract::_beforeToHtml();
	}
	 
}

?>