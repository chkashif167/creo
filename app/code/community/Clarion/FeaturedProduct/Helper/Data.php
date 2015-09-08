<?php
/**
 * Featured Product data helper
 * 
 * @category    Clarion
 * @package     Clarion_FeaturedProduct <magento@clariontechnologies.co.in>
 * @author      Clarion Magento Team
 */
class Clarion_FeaturedProduct_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Number of products home page
     *
     * @var string
     */
    const XML_PATH_NUMBER_OF_PRODUCTS_HOME_PAGE = 'featuredproduct/display/number_of_products_home_page';
    
    /**
     * Number of products category page
     *
     * @var string
     */
    const XML_PATH_NUMBER_OF_PRODUCTS_CAT_PAGE = 'featuredproduct/display/number_of_products_cat_page';
    
    /**
     * Number of products sidebar(left/right)
     *
     * @var string
     */
    const XML_PATH_NUMBER_OF_PRODUCTS_SIDEBAR = 'featuredproduct/display/number_of_products_sidebar';
    
    /**
     * Show Featured Product On
     *
     * @var string
     */
    const XML_PATH_SHOW_FEATURED_PRODUCTS_ON = 'featuredproduct/display/show_featuredproduct';
    
    /**
     * Block Title
     *
     * @var string
     */
    const XML_PATH_BLOCK_TITLE = 'featuredproduct/display/title';
    
    /**
     * Return number of products for home page configured from admin
     *
     * @param integer|string|Mage_Core_Model_Store $store
     * @return int
     */
    public function getNumberOfProductsHomePage($store = null)
    {
        return abs(Mage::getStoreConfig(self::XML_PATH_NUMBER_OF_PRODUCTS_HOME_PAGE, $store));
    }
    
    /**
     * Return number of products for sidebar(left/right) configured from admin
     *
     * @param integer|string|Mage_Core_Model_Store $store
     * @return int
     */
    public function getNumberOfProductsCatPage($store = null)
    {
        return abs(Mage::getStoreConfig(self::XML_PATH_NUMBER_OF_PRODUCTS_CAT_PAGE, $store));
    }
    
    /**
     * Return number of products for catebory page configured from admin
     *
     * @param integer|string|Mage_Core_Model_Store $store
     * @return int
     */
    public function getNumberOfProductsSidebar($store = null)
    {
        return abs(Mage::getStoreConfig(self::XML_PATH_NUMBER_OF_PRODUCTS_SIDEBAR, $store));
    }
    
    /**
     * Return block title configured from admin
     *
     * @param integer|string|Mage_Core_Model_Store $store
     * @return int
     */
    public function getBlockTitle($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_BLOCK_TITLE, $store);
    }
    
    /**
     * Return page names for which we have to show featured products
     *
     * @param integer|string|Mage_Core_Model_Store $store
     * @return string
     */
    public function getShowFeaturedProductsOn($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_SHOW_FEATURED_PRODUCTS_ON, $store);
    }
}
