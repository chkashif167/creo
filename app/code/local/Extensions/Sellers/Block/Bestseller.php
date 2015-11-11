<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product list
 *
 * @category   Mage
 * @package    Extensions_Sellers
 * @author     Jawwad Nissar <jawwad.nissar@progos.org>
 */
class Extensions_Sellers_Block_Bestseller extends Mage_Core_Block_Template {

    /**
     * Default toolbar block name
     *
     * @var string
     */
    protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';

    /**
     * Product Collection
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected $_productCollection;

    /**
     * Retrieve Best Selling Products By Category Collection
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
	protected function _getProductCollection()	
    {
		if (is_null($this->_productCollection)) {
	       $catId = $this->getRequest()->getParam('id');
			/** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
			$collection = Mage::getResourceModel('catalog/product_collection');
			// join sales order items column and count sold products
			$expression = new Zend_Db_Expr("SUM(oi.qty_ordered)");
			$condition = new Zend_Db_Expr("e.entity_id = oi.product_id AND oi.parent_item_id IS NULL");
			$collection->addAttributeToSelect('*')->getSelect()
				->join(array('oi' => $collection->getTable('sales/order_item')),
				$condition,
				array('sales_count' => $expression))
				->group('e.entity_id')
				->order('sales_count' . ' ' . 'desc');
			// join category
			$condition = new Zend_Db_Expr("e.entity_id = ccp.product_id");
			$condition2 = new Zend_Db_Expr("c.entity_id = ccp.category_id");
			$collection->getSelect()->join(array('ccp' => $collection->getTable('catalog/category_product')),
				$condition,
				array())->join(array('c' => $collection->getTable('catalog/category')),
				$condition2,
				array('cat_id' => 'c.entity_id'));
			$condition = new Zend_Db_Expr("c.entity_id = cv.entity_id AND ea.attribute_id = cv.attribute_id");
			// cutting corners here by hardcoding 3 as Category Entiry_type_id
			$condition2 = new Zend_Db_Expr("ea.entity_type_id = 3 AND ea.attribute_code = 'name'");
			$collection->getSelect()->join(array('ea' => $collection->getTable('eav/attribute')),
				$condition2,
				array())->join(array('cv' => $collection->getTable('catalog/category') . '_varchar'),
				$condition,
				array('cat_name' => 'cv.value'));
			// if Category filter is on
			if ($catId) {
//				$collection->getSelect()->where('c.entity_id', array('in' => $catArray));
				$collection->getSelect()->where('c.entity_id = ? ', $catId);
			}
			$this->_productCollection = $collection;
		}
//		echo $collection->getSelect()->__toString();
//		echo "<pre>";
//		print_r($this->_productCollection->getData());
//		echo "</pre>";
//		exit;
        return $this->_productCollection;
        
    }

    /**
     * Retrieve Best Selling Products By Category Collection
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }

    /**
     * Retrieve current view mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->getChild('toolbar')->getCurrentMode();
    }

    /**
     * Prepare Block also get sorting
     * 
     */
    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

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

        return parent::_beforeToHtml();
    }

    /**
     * Retrieve Toolbar block
     *
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function getToolbarBlock()
    {
        if ($blockName = $this->getToolbarBlockName()) {
            if ($block = $this->getLayout()->getBlock($blockName)) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, microtime());
        return $block;
    }

    /**
     * Retrieve additional blocks html
     *
     * @return string
     */
    public function getAdditionalHtml()
    {
        return $this->getChildHtml('additional');
    }

    /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    public function setCollection($collection)
    {
        $this->_productCollection = $collection;
        return $this;
    }

    public function addAttribute($code)
    {
        $this->_getProductCollection()->addAttributeToSelect($code);
        return $this;
    }

    public function getPriceBlockTemplate()
    {
        return $this->_getData('price_block_template');
    }

    /**
     * Retrieve Catalog Config object
     *
     * @return Mage_Catalog_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('catalog/config');
    }

    /**
     * Prepare Sort By fields from Category Data
     *
     * @param Mage_Catalog_Model_Category $category
     * @return Mage_Catalog_Block_Product_List
     */
    public function prepareSortableFieldsByCategory($category) {
        if (!$this->getAvailableOrders()) {
            $this->setAvailableOrders($category->getAvailableSortByOptions());
        }
        $availableOrders = $this->getAvailableOrders();
        if (!$this->getSortBy()) {
            if ($categorySortBy = $category->getDefaultSortBy()) {
                if (!$availableOrders) {
                    $availableOrders = $this->_getConfig()->getAttributeUsedForSortByArray();
                }
                if (isset($availableOrders[$categorySortBy])) {
                    $this->setSortBy($categorySortBy);
                }
            }
        }

        return $this;
    }

    /**
     * Retrieve block cache tags based on product collection
     *
     * @return array
     */
    public function getCacheTags()
    {
        return array_merge(
            parent::getCacheTags(),
            $this->getItemsTags($this->_getProductCollection())
        );
    }


}