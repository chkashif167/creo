<?php
class Tabs_Extension_Block_Seller extends Mage_Core_Block_Template {
   protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';
   protected $getLoadedProductCollection;
   
   public function getLoadedProductCollection()
    { 
		$time = time();
		$to = date('Y-m-d H:i:s', $time);
		$lastTime = $time - 2592000; // 30*60*60*24
		$from = date('Y-m-d H:i:s', $lastTime);
		$storeId    = Mage::app()->getStore()->getId(); 
        $categoryId = $this->getRequest()->getParam('id');
		$category = Mage::getModel('catalog/category')->load($categoryId);
		$collection = Mage::getResourceModel('sales/order_item_collection')
					    ->addAttributeToSelect('*')
						->addAttributeToFilter('store_id', '1')
						->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to));

		$this->_productCollection = $collection;
        return parent::getLoadedProductCollection();

/*
		foreach($collection as $o  ){
		echo    $product_id = $o->product_id;
		echo "<br />";
		echo     $product_sku = $o->sku;
		echo "<br />";
		echo     $product_name = $o->getName();
		echo "<br />";
			$_product = Mage::getModel('catalog/product')->load($product_id);
			$cats = $_product->getCategoryIds();
			$category_id = $cats[0]; // just get the first id
			echo "<br />";
			print_r($category_id);
			echo "<br />";
*/	
/*
       $id = $this->getRequest()->getParam('id');
       // benchmarking
        $memory = memory_get_usage();
        $time = microtime();
        $catId = $id;
        /** @var $collection Mage_Catalog_Model_Resource_Product_Collection 
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
            $collection->getSelect()->where('c.entity_id = ?', $catId)->limit(5);
        }

        // unfortunately I cound not come up with the sql query that could grab only 1 bestseller for each category
        // so all sorting work lays on php
        $result = array();
        foreach ($collection as $product) {
            /** @var $product Mage_Catalog_Model_Product 
            if (isset($result[$product->getCatId()])) {
                continue;
            }
            $result[$product->getCatId()] = 'Category:' . $product->getCatName() . '; Product:' . $product->getName() . '; Sold Times:'. $product->getSalesCount();
        }
*/       

        
    }

    public function getMode()
    {
        return $this->getChild('toolbar')->getCurrentMode();
    }

    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->getLoadedProductCollection();

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
            'collection' => $this->getLoadedProductCollection()
        ));

        $this->getLoadedProductCollection()->load();

        return parent::_beforeToHtml();
    }

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
        $this->getLoadedProductCollection()->addAttributeToSelect($code);
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
            $this->getItemsTags($this->getLoadedProductCollection())
        );
    }

}