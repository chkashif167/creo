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
 * Backend Block: Product Sorting
 *
 * @category   Magidev
 * @package    Magidev_Sort
 * @author     Magidev Team <support@magidev.com>
 */
class Magidev_Sort_Block_Adminhtml_Catalog_Category_Tab_Sort extends Mage_Core_Block_Template
{

	const SORT_DIRECTION_ASC = 1, SORT_DIRECTION_DESC = 2;
	const SORT_TYPE_REPLACE = 'replace', SORT_TYPE_INSERT = 'insert';

	public function _construct()
	{
		parent::_construct();
		$this->setTemplate('magidev/sort/list.phtml');
		$this->_prepareProducts();
	}

	public function getType()
	{
		return Mage::getStoreConfig('catalog/frontend/merchandising_type');
	}

	public function getScope($attributeCode)
	{
		$attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attributeCode);
		if (!$attribute->getIsGlobal() || $attribute->getIsGlobal() == Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE) {
			return $this->__('STORE VIEW');
		} elseif ($attribute->getIsGlobal() == Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL) {
			return $this->__('GLOBAL');
		} else {
			return $this->__('WEBSITE');
		}
	}

	private function _prepareProducts()
	{
		$_categoryID = $this->getRequest()->getParam('id');
		if (empty($_categoryID)) {
			$this->setCategoryProducts(array());
			return;
		}
		$_category = Mage::getModel('catalog/category');
		if (Mage::getSingleton('core/session')->getMagiBackendStoreId()) {
			$_category->setStoreId(Mage::getSingleton('core/session')->getMagiBackendStoreId());
		}
		$_category->load($_categoryID);
		$_productPositions = $_category->getProductsPosition();
		if (empty($_productPositions)) {
			$this->setCategoryProducts(array());
			return;
		}
		$_arrProducts = array();
		$_model = Mage::getModel('magidev_sort/positions');
		$productModel = Mage::getModel('catalog/product');
		$productModel->setStoreId(Mage::getSingleton('core/session')->getMagiBackendStoreId());
		/** @var  Mage_Catalog_Model_Resource_Product_Collection $collection */
		$collection = $productModel->getCollection();
		$collection
				->addFieldToFilter('entity_id', array('in' => array_keys($_productPositions)))
				->addFieldToFilter('visibility', array('in' => array(2, 4)))
				->addAttributeToSelect('*');
		Mage::getSingleton('cataloginventory/stock')
				->addItemsToProducts($collection);
		$_index = 1;
		foreach ($_productPositions as $_productId => $_position) {
			if (!$_product = $collection->getItemById($_productId)) {
				continue;
			}
			if ($_position == 0) {
				$_position = 2;
			}
			if (!empty($_arrProducts[$_position]) || $_position == 1) {
				$_index++;
				$_position = max($_productPositions) + $_index;
				$_model->updatePosition($_categoryID, $_product->getId(), $_position);
			}
			$_arrProducts[$_position] = $_product;
		}
		if (Mage::getStoreConfig('catalog/frontend/merchandising_sort_direction') == self::SORT_DIRECTION_ASC) {
			ksort($_arrProducts);
		} else {
			krsort($_arrProducts);
		}
		// it need to add new product to end of the list.
		$_arrProducts[] = Mage::getModel('catalog/product');
		$this->setCategoryProducts($_arrProducts);
	}

	public function getColumnCount()
	{
		return Mage::getStoreConfig('catalog/frontend/merchandising_column_count');
	}

	public function getCategoryId()
	{
		return $this->getRequest()->getParam('id');
	}

	public function getPageCount()
	{
		return Mage::getStoreConfig('catalog/frontend/grid_per_page');;
	}

	public function getAdminUrl($route, $params)
	{
		if ($this->getRequest()->getParam('store')) {
			$params['store'] = $this->getRequest()->getParam('store');
		}
		return Mage::helper("adminhtml")->getUrl($route, $params);
	}
}