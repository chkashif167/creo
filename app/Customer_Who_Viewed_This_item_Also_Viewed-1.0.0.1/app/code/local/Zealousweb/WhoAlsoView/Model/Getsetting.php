<?php
class Zealousweb_WhoAlsoView_Model_Getsetting
{
    const XML_PATH_STATUS = 'who_also_view/general_settings/status';
    const XML_PATH_TITLE = 'who_also_view/general_settings/title';
    const XML_PATH_MAX_PRODUCT_COUNT = 'who_also_view/general_settings/max_product_count';
    const XML_PATH_SHOW_IN_STOCK_PRODUCTS = 'who_also_view/general_settings/show_in_stock_products';
    const XML_PATH_SHOW_CAT_PRODUCTS_ONLY = 'who_also_view/general_settings/show_only_categories_products';
    
    protected $isEnabled = null;
    protected $displayTitle = null;
    protected $maxProductDisplay = null;
    protected $showInStockProduct = null;
    protected $showCatProductOnly = null;
    
     public function __construct()
    {
        if(($this->isEnabled = $this->_isEnabled())) {
            $this->displayTitle        = $this->_getDisplayTitle();
            $this->maxProductDisplay   = $this->_getMaxProductDisplay();
            $this->showInStockProduct  = $this->_getShowInStockProducts();
            $this->showCatProductOnly  = $this->_getShowCatProductOnly();
         }
    }
    
    public function isEnabled()
    {
        return (bool) $this->isEnabled;
    }

    public function getDisplayTitle()
    {
        return $this->displayTitle;
    }
    
    public function getMaxProductDisplay()
    {
        return $this->maxProductDisplay;
    }

    public function getshowInStockProduct()
    {
        return $this->showInStockProduct;
    }
    
    public function getshowCatProductOnly()
    {
        return $this->showCatProductOnly;
    }
    
    protected function _isEnabled()
    {
        return $this->_getStoreConfig(self::XML_PATH_STATUS);
    }
    
    protected function _getDisplayTitle()
    {
        return $this->_getStoreConfig(self::XML_PATH_TITLE);
    }
    
    protected function _getMaxProductDisplay()
    {
        return $this->_getStoreConfig(self::XML_PATH_MAX_PRODUCT_COUNT);
    }

    protected function _getShowInStockProducts()
    {
        return $this->_getStoreConfig(self::XML_PATH_SHOW_IN_STOCK_PRODUCTS);
    }
    
    protected function _getShowCatProductOnly()
    {
        return $this->_getStoreConfig(self::XML_PATH_SHOW_CAT_PRODUCTS_ONLY);
    }
    
    protected function _getStoreConfig($xmlPath)
    {
        return Mage::getStoreConfig($xmlPath, Mage::app()->getStore()->getId());
    }
    
}
