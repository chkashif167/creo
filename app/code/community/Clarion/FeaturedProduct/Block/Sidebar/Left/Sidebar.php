<?php
/**
 * Featured Product Sidebar block
 * 
 * @category    Clarion
 * @package     Clarion_FeaturedProduct
 * @author      Clarion Magento Team <magento@clariontechnologies.co.in>
 * 
 */
class Clarion_FeaturedProduct_Block_Sidebar_Left_Sidebar extends Mage_Catalog_Block_Product_Abstract
{  
    /**
     * Default value for number of products to display
     */
    const DEFAULT_NUMBER_OF_PRODUCT = 4;
    
    /**
     * Default block title
     */
    const DEFAULT_BLOCK_TITLE = "Featured Products";

    /**
     * number of products to display
     *
     * @var null
     */
    protected $_numberOfProduct;
    
    /**
     * show featured products block on the page
     *
     * @var string
     */
    protected $_showFeaturedPrdoctsOnPage;
    
    /**
     * Prepare and return product collection
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection|Object|Varien_Data_Collection
     */
    protected function _getProductCollection()
    {
        /** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());
        /**
        * Add all attributes and apply pricing logic to products collection
        * to get correct values in different products lists.
        * E.g. crosssells, upsells, new products, recently viewed
        */
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->addAttributeToFilter('is_featured_product','1')
            ->setPageSize($this->getNumberOfProduct())
            ->setCurPage($this->getCurrentPage());
        //get products randomly
        //$collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
        //echo $collection->load()->getSelect();
        return $collection;
    }
    
    /**
     * Get how much products should be displayed at once.
     *
     * @return int
     */
    public function getNumberOfProduct()
    {
        if (null === $this->_numberOfProduct) {
            $configNumberOfProduct = Mage::Helper('clarion_featuredproduct')->getNumberOfProductsSidebar();
            $this->_numberOfProduct = empty($configNumberOfProduct) ? self::DEFAULT_NUMBER_OF_PRODUCT : $configNumberOfProduct;
        }
        return $this->_numberOfProduct;
    }
    
    /**
     * Prepare collection with featured products
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        $this->setProductCollection($this->_getProductCollection());
        return parent::_beforeToHtml();
    }
    
    /**
     * Get block title.
     *
     * @return string block title
     */
    public function getBlockTitle() {
        $blockTitle = Mage::Helper('clarion_featuredproduct')->getBlockTitle();
        return (is_null($blockTitle)) ? self::DEFAULT_BLOCK_TITLE : $blockTitle;
    }
    
    /**
     * show featured products block on the page
     * @param $page string page name like home,category
     * @return boolean
     */
    public function isDisplayFeaturedProducts()
    {
        $configPageNames = Mage::Helper('clarion_featuredproduct')->getShowFeaturedProductsOn();
        $pageNames = explode(',', $configPageNames);
        if(in_array($this->getFeaturedPrdoctsOnPage(), $pageNames)){
          return true;  
        } else {
            return false;
        }
    }
    
    /**
     * get featured products block on the page.
     *
     * @return string page name
     */
    public function getFeaturedPrdoctsOnPage()
    {
        return $this->_showFeaturedPrdoctsOnPage;
    }
    
     /**
     * Set featured products block on the page.
     *
     * @param $page
     * @return Clarion_FeaturedProduct_Block_List_List
     */
    public function setFeaturedPrdoctsOnPage($page)
    {
        $this->_showFeaturedPrdoctsOnPage = $page;
        return $this;
    }
    
    /**
     * Get a pager
     *
     * @return string|null
     */
    public function getPager()
    {
       $pager = $this->getChild('featured.product.left.sidebar.pager');
        if ($pager) {
            $productsPerPage = $this->getNumberOfProduct();
            $pager->setAvailableLimit(array($productsPerPage => $productsPerPage));
            $pager->setTotalNum($this->_getProductCollection()->getSize());
            $pager->setCollection($this->_getProductCollection());
            $pager->setShowPerPage(false);
            $pager->setShowAmounts(false);
            return $pager->toHtml();
        }

        return null;
    }
    
    /**
     * Fetch the current page for the featured product list
     *
     * @return int
     */
    public function getCurrentPage()
    {
        $currentPage ='';
        $listBlock = $this->getLayout()->getBlock('featured.product.left.sidebar');
        if ($listBlock) {
            $currentPage = abs(intval($this->getRequest()->getParam('p')));
            if ($currentPage < 1) {
                $currentPage = 1;
            }
        }
        return $currentPage ? $currentPage : 1;
    }
}