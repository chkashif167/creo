<?php
class Extensions_Sales_Block_Bestseller extends Mage_Core_Block_Template {
   protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';
   protected $getLoadedProductCollection;
   
   public function __construct()
    { 
		parent::getLoadedProductCollection();
       $id = $this->getRequest()->getParam('id');
       // benchmarking
        $memory = memory_get_usage();
        $time = microtime();
        $catId = $id;
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
            $collection->getSelect()->where('c.entity_id = ?', $catId)->limit(5);
        }else{
            $collection->getSelect()->limit(3);
		}
		$this->getLoadedProductCollection = $collection;
 	    $this->setCollection($collection);
        
    }

	public function  getPagerHtml(){
    	return $this->getChildHtml('pager');
	}
	
	public function  _prepareLayout(){
		parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
        $pager->setAvailableLimit(array(2=>2,4=>4,8=>8,'all'=>'all'));
        $pager->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
        return $this;

	}
}