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
class Extensions_Sellers_Block_Newcollection extends Mage_Core_Block_Template {

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
     * Category Label
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected $_categoryLabel;

    /**
     * Retrieve New Products By Category Collection
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
	protected function _getProductCollection()	
    {
		if (is_null($this->_productCollection)) {
			$month = Mage::getStoreConfig('sellers_options/newcollection'); 
			$getdays = $month['month'];
 	        $catId = $this->getRequest()->getParam('id');
			$collection = Mage::getModel('catalog/product')
							->getCollection()
							->joinField(
									'category_id', 'catalog/category_product', 'category_id', 
									'product_id = entity_id', null, 'left'
								)
								->addAttributeToSelect('*')
								->addAttributeToFilter('category_id', array($catId))			
								->addAttributeToSort('created_at', 'desc')
								->addFieldToFilter('created_at', array('gt' => date("Y-m-d H:i:s", strtotime('- "'.$getdays.'" day'))));
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
     * Retrieve New Products By Category Collection
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }

    /**
     * Retrieve Category Label
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getCategoryLabel()
    {
        return $this->_getCategoryLabel();
    }

    /**
     * Retrieve Category Label
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
	protected function _getCategoryLabel()	
    {
		if (is_null($this->_categoryLabel)) {
 	        $catId = $this->getRequest()->getParam('id');
			$_category = Mage::getModel('catalog/category')->load($catId);
			$this->_categoryLabel = $_category->getName();
		}
        return $this->_categoryLabel;
        
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