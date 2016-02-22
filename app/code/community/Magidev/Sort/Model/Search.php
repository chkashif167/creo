<?php
/**
 * MagiDev
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade MagiDev Package to newer
 * versions in the future. If you wish to customize Package for your
 * needs please refer to http://www.magidev.com for more information.
 *
 * @category    Magidev
 * @package     Magidev_Sort
 * @copyright   Copyright (c) 2014 MagiDev. (http://www.magidev.com)
 */

/**
 * Model of the Package: Search Products
 *
 * @category   Magidev
 * @package    Magidev_Sort
 * @author     Magidev Team <support@magidev.com>
 */
class Magidev_Sort_Model_Search extends Mage_Sitemap_Model_Resource_Catalog_Product
{
	private $_query='';
	private $_categoryId=null;

	private $_alias='main_table';

	/**
	 * Init resource model (catalog/product)
	 */
	protected function _construct()
	{
		$this->_init('catalog/product', 'entity_id');
		if(@class_exists('Enterprise_License_Model_Observer')){
			$this->_alias='e';
		}
	}

	public function setQuery($query){
		$this->_query=$query;
		return $this;
	}

	public function setCategoryId($id){
		$this->_categoryId=$id;
		return $this;
	}

	/**
	 * Get product collection array
	 *
	 * @param int $storeId
	 * @return array
	 */
	public function getCollection($storeId)
	{
		/* @var $store Mage_Core_Model_Store */
		$store = Mage::app()->getStore($storeId);
		if (!$store) {
			return false;
		}
		$_limit=intval(Mage::getStoreConfig('catalog/frontend/merchandising_sort_search'));
		$this->_select = $this->_getWriteAdapter()->select()
				->from(array($this->_alias => $this->getMainTable()), array($this->getIdFieldName(), 'sku'))
				->join(
						array('w' => $this->getTable('catalog/product_website')),
						$this->_alias.'.entity_id = w.product_id',
						array()
				)
				->join(
						array('c'=>$this->getTable('catalog/category_product')),
						'c.product_id = '.$this->_alias.'.entity_id',
						array()
				)
				->where("{$this->_alias}.entity_id NOT IN(SELECT product_id FROM catalog_category_product WHERE category_id={$this->_categoryId} ) AND ({$this->_alias}.sku LIKE '{$this->_query}%' OR name_table.value LIKE '%{$this->_query}%') ")
				->group($this->_alias.'.entity_id')
				->limit((($_limit>0)?$_limit:10));
		$this->_addProductData();
		$storeId = (int)$store->getId();
		$this->_addFilter($storeId, 'visibility',
				Mage::getSingleton('catalog/product_visibility')->getVisibleInSiteIds(), 'in'
		);
		$collection=new Varien_Data_Collection();
		$query = $this->_getWriteAdapter()->query($this->_select);
		while ($row = $query->fetch()) {
			$entity=$this->_prepareObject($row);
			$collection->addItem($entity);
		}
		return $collection;
	}

	/**
	 * Prepare catalog object
	 *
	 * @param array $row
	 * @return Varien_Object
	 */
	protected function _prepareObject(array $row)
	{
		$entity = new Varien_Object();
		$entity->setId($row[$this->getIdFieldName()]);
		$entity->setSku($row['sku']);
		$entity->setPrice(number_format($row['price'], 2, '.', ''));
		$entity->setName($row['name']);
		$entity->setEditUrl( Mage::helper("adminhtml")->getUrl("adminhtml/catalog_product/edit",array('id'=>$entity->getId())) );
		$entity->setDeleteUrl( Mage::helper("adminhtml")->getUrl("adminhtml/sortproduct/delete",array('id'=>$entity->getId(),'categoryId'=>$this->_categoryId)) );
		$entity->setStatusUrl( Mage::helper("adminhtml")->getUrl("adminhtml/sortproduct/status",array('id'=>$entity->getId())) );
		$entity->setQuickEditUrl( Mage::helper("adminhtml")->getUrl("adminhtml/sortproduct/edit",array('id'=>$entity->getId())) );
		$entity->setImage($this->_getImageUrl($row['image']));
		$entity->setIsInStock(Mage::getModel('cataloginventory/stock_item')->loadByProduct($entity->getId())->getIsInStock());
		return $entity;
	}

	private function _getImageUrl( $_image ){
		$_image=Mage::getModel('catalog/product_image')
				->setDestinationSubdir('thumbnail')
				->setWidth(135)
				->setHeight(135)
				->setBaseFile($_image)
				->resize();
		if(!$_image->isCached()){
			$_image->saveFile();
		}
		return $_image->getUrl();
	}

	private function _addProductData()
	{
		$productAttributes = array('name', 'price', 'image');
		foreach ($productAttributes as $attributeCode) {
			$alias = $attributeCode . '_table';
			$attribute = Mage::getSingleton('eav/config')
					->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
			$this->_select->join(
					array($alias => $attribute->getBackendTable()),
					"{$this->_alias}.entity_id = $alias.entity_id AND $alias.attribute_id={$attribute->getId()}",
					array($attributeCode => 'value')
			);
		}
		return $this;
	}

	/**
	 * Prepare product
	 *
	 * @deprecated after 1.7.0.2
	 *
	 * @param array $productRow
	 * @return Varien_Object
	 */
	protected function _prepareProduct(array $productRow)
	{
		return $this->_prepareObject($productRow);
	}

	/**
	 * Retrieve entity url
	 *
	 * @param array $row
	 * @param Varien_Object $entity
	 * @return string
	 */
	protected function _getEntityUrl($row, $entity)
	{
		return !empty($row['request_path']) ? $row['request_path'] : 'catalog/product/view/id/' . $entity->getId();
	}

	/**
	 * Loads product attribute by given attribute code
	 *
	 * @param string $attributeCode
	 * @return Mage_Sitemap_Model_Resource_Catalog_Abstract
	 */
	protected function _loadAttribute($attributeCode)
	{
		$attribute = Mage::getSingleton('catalog/product')->getResource()->getAttribute($attributeCode);

		$this->_attributesCache[$attributeCode] = array(
				'entity_type_id' => $attribute->getEntityTypeId(),
				'attribute_id' => $attribute->getId(),
				'table' => $attribute->getBackend()->getTable(),
				'is_global' => $attribute->getIsGlobal() == Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
				'backend_type' => $attribute->getBackendType()
		);
		return $this;
	}
}