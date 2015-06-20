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
 * Catalog breadcrumbs
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Breadcrumbs extends Mage_Core_Block_Template
{
    /**
     * Retrieve HTML title value separator (with space)
     *
     * @param mixed $store
     * @return string
     */
    public function getTitleSeparator($store = null)
    {
        $separator = (string)Mage::getStoreConfig('catalog/seo/title_separator', $store);
        return ' ' . $separator . ' ';
    }

    /**
     * Preparing layout
     *
     * @return Mage_Catalog_Block_Breadcrumbs
     */
    protected function _prepareLayout()
    {
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb('home', array(
                'label'=>Mage::helper('catalog')->__('Home'),
                'title'=>Mage::helper('catalog')->__('Go to Home Page'),
                'link'=>Mage::getBaseUrl()
            ));

            $title = array();
            $path  = Mage::helper('catalog')->getBreadcrumbPath();

            foreach ($path as $name => $breadcrumb) {
                $breadcrumbsBlock->addCrumb($name, $breadcrumb);
                $title[] = $breadcrumb['label'];
            }

            if ($headBlock = $this->getLayout()->getBlock('head')) {
                $headBlock->setTitle(join($this->getTitleSeparator(), array_reverse($title)));
            }
        }
        return parent::_prepareLayout();
    }
}
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
 * Catalog flat abstract helper
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Catalog_Helper_Flat_Abstract extends Mage_Core_Helper_Abstract
{
    /**
     * Catalog Flat index process code
     *
     * @var null|string
     */
    protected $_indexerCode = null;

    /**
     * Store catalog Flat index process instance
     *
     * @var Mage_Index_Model_Process|null
     */
    protected $_process = null;

    /**
     * Flag for accessibility
     *
     * @var bool
     */
    protected $_isAccessible = null;

    /**
     * Flag for availability
     *
     * @var bool
     */
    protected $_isAvailable = null;

    /**
     * Check if Catalog Flat Data has been initialized
     *
     * @param null|bool|int|Mage_Core_Model_Store $store Store(id) for which the value is checked
     * @return bool
     */
    abstract public function isBuilt($store = null);

    /**
     * Check if Catalog Category Flat Data is enabled
     *
     * @param mixed $deprecatedParam this parameter is deprecated and no longer in use
     *
     * @return bool
     */
    abstract public function isEnabled($deprecatedParam = false);

    /**
     * Check if Catalog Category Flat Data is available
     * without lock check
     *
     * @return bool
     */
    public function isAccessible()
    {
        if (is_null($this->_isAccessible)) {
            $this->_isAccessible = $this->isEnabled()
                && $this->getProcess()->getStatus() != Mage_Index_Model_Process::STATUS_RUNNING;
        }
        return $this->_isAccessible;
    }

    /**
     * Check if Catalog Category Flat Data is available for use
     *
     * @return bool
     */
    public function isAvailable()
    {
        if (is_null($this->_isAvailable)) {
            $this->_isAvailable = $this->isAccessible() && !$this->getProcess()->isLocked();
        }
        return $this->_isAvailable;
    }

    /**
     * Retrieve Catalog Flat index process
     *
     * @return Mage_Index_Model_Process
     */
    public function getProcess()
    {
        if (is_null($this->_process)) {
            $this->_process = Mage::getModel('index/process')
                ->load($this->_indexerCode, 'indexer_code');
        }
        return $this->_process;
    }
}
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
 * Catalog flat helper
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Helper_Category_Flat extends Mage_Catalog_Helper_Flat_Abstract
{
    /**
     * Catalog Category Flat Is Enabled Config
     */
    const XML_PATH_IS_ENABLED_FLAT_CATALOG_CATEGORY = 'catalog/frontend/flat_catalog_category';

    /**
     * Catalog Flat Category index process code
     */
    const CATALOG_CATEGORY_FLAT_PROCESS_CODE = 'catalog_category_flat';

    /**
     * Catalog Category Flat index process code
     *
     * @var string
     */
    protected $_indexerCode = self::CATALOG_CATEGORY_FLAT_PROCESS_CODE;

    /**
     * Store catalog Category Flat index process instance
     *
     * @var Mage_Index_Model_Process|null
     */
    protected $_process = null;

    /**
     * Check if Catalog Category Flat Data is enabled
     *
     * @param bool $skipAdminCheck this parameter is deprecated and no longer in use
     *
     * @return bool
     */
    public function isEnabled($skipAdminCheck = false)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_IS_ENABLED_FLAT_CATALOG_CATEGORY);
    }

    /**
     * Check if Catalog Category Flat Data has been initialized
     *
     * @param null|bool|int|Mage_Core_Model_Store $store Store(id) for which the value is checked
     * @return bool
     */
    public function isBuilt($store = null)
    {
        return Mage::getResourceSingleton('catalog/category_flat')->isBuilt($store);
    }

    /**
     * Check if Catalog Category Flat Data has been initialized
     *
     * @deprecated after 1.7.0.0 use Mage_Catalog_Helper_Category_Flat::isBuilt() instead
     *
     * @return bool
     */
    public function isRebuilt()
    {
        return $this->isBuilt();
    }
}
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

class Mage_Catalog_Helper_Output extends Mage_Core_Helper_Abstract
{
    /**
     * Array of existing handlers
     *
     * @var array
     */
    protected $_handlers;

    /**
     * Template processor instance
     *
     * @var Varien_Filter_Template
     */
    protected $_templateProcessor = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        Mage::dispatchEvent('catalog_helper_output_construct', array('helper'=>$this));
    }

    protected function _getTemplateProcessor()
    {
        if (null === $this->_templateProcessor) {
            $this->_templateProcessor = Mage::helper('catalog')->getPageTemplateProcessor();
        }

        return $this->_templateProcessor;
    }

    /**
     * Adding method handler
     *
     * @param   string $method
     * @param   object $handler
     * @return  Mage_Catalog_Helper_Output
     */
    public function addHandler($method, $handler)
    {
        if (!is_object($handler)) {
            return $this;
        }
        $method = strtolower($method);

        if (!isset($this->_handlers[$method])) {
            $this->_handlers[$method] = array();
        }

        $this->_handlers[$method][] = $handler;
        return $this;
    }

    /**
     * Get all handlers for some method
     *
     * @param   string $method
     * @return  array
     */
    public function getHandlers($method)
    {
        $method = strtolower($method);
        return isset($this->_handlers[$method]) ? $this->_handlers[$method] : array();
    }

    /**
     * Process all method handlers
     *
     * @param   string $method
     * @param   mixed $result
     * @param   array $params
     * @return unknown
     */
    public function process($method, $result, $params)
    {
        foreach ($this->getHandlers($method) as $handler) {
            if (method_exists($handler, $method)) {
                $result = $handler->$method($this, $result, $params);
            }
        }
        return $result;
    }

    /**
     * Prepare product attribute html output
     *
     * @param   Mage_Catalog_Model_Product $product
     * @param   string $attributeHtml
     * @param   string $attributeName
     * @return  string
     */
    public function productAttribute($product, $attributeHtml, $attributeName)
    {
        $attribute = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeName);
        if ($attribute && $attribute->getId() && ($attribute->getFrontendInput() != 'media_image')
            && (!$attribute->getIsHtmlAllowedOnFront() && !$attribute->getIsWysiwygEnabled())) {
                if ($attribute->getFrontendInput() != 'price') {
                    $attributeHtml = $this->escapeHtml($attributeHtml);
                }
                if ($attribute->getFrontendInput() == 'textarea') {
                    $attributeHtml = nl2br($attributeHtml);
                }
        }
        if ($attribute->getIsHtmlAllowedOnFront() && $attribute->getIsWysiwygEnabled()) {
            if (Mage::helper('catalog')->isUrlDirectivesParsingAllowed()) {
                $attributeHtml = $this->_getTemplateProcessor()->filter($attributeHtml);
            }
        }

        $attributeHtml = $this->process('productAttribute', $attributeHtml, array(
            'product'   => $product,
            'attribute' => $attributeName
        ));

        return $attributeHtml;
    }

    /**
     * Prepare category attribute html output
     *
     * @param   Mage_Catalog_Model_Category $category
     * @param   string $attributeHtml
     * @param   string $attributeName
     * @return  string
     */
    public function categoryAttribute($category, $attributeHtml, $attributeName)
    {
        $attribute = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Category::ENTITY, $attributeName);

        if ($attribute && ($attribute->getFrontendInput() != 'image')
            && (!$attribute->getIsHtmlAllowedOnFront() && !$attribute->getIsWysiwygEnabled())) {
            $attributeHtml = $this->escapeHtml($attributeHtml);
        }
        if ($attribute->getIsHtmlAllowedOnFront() && $attribute->getIsWysiwygEnabled()) {
            if (Mage::helper('catalog')->isUrlDirectivesParsingAllowed()) {
                $attributeHtml = $this->_getTemplateProcessor()->filter($attributeHtml);
            }
        }
        $attributeHtml = $this->process('categoryAttribute', $attributeHtml, array(
            'category'  => $category,
            'attribute' => $attributeName
        ));
        return $attributeHtml;
    }
}
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
 * Catalog Product Flat Helper
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Helper_Product_Flat extends Mage_Catalog_Helper_Flat_Abstract
{
    /**
     * Catalog Product Flat Config
     */
    const XML_PATH_USE_PRODUCT_FLAT          = 'catalog/frontend/flat_catalog_product';
    const XML_NODE_ADD_FILTERABLE_ATTRIBUTES = 'global/catalog/product/flat/add_filterable_attributes';
    const XML_NODE_ADD_CHILD_DATA            = 'global/catalog/product/flat/add_child_data';

    /**
     * Path for flat flag model
     */
    const XML_PATH_FLAT_FLAG                 = 'global/catalog/product/flat/flag/model';

    /**
     * Catalog Flat Product index process code
     */
    const CATALOG_FLAT_PROCESS_CODE = 'catalog_product_flat';

    /**
     * Catalog Product Flat index process code
     *
     * @var string
     */
    protected $_indexerCode = self::CATALOG_FLAT_PROCESS_CODE;

    /**
     * Catalog Product Flat index process instance
     *
     * @var Mage_Index_Model_Process|null
     */
    protected $_process = null;

    /**
     * Store flags which defines if Catalog Product Flat functionality is enabled
     *
     * @deprecated after 1.7.0.0
     *
     * @var array
     */
    protected $_isEnabled = array();

    /**
     * Catalog Product Flat Flag object
     *
     * @var Mage_Catalog_Model_Product_Flat_Flag
     */
    protected $_flagObject;

    /**
     * Retrieve Catalog Product Flat Flag object
     *
     * @return Mage_Catalog_Model_Product_Flat_Flag
     */
    public function getFlag()
    {
        if (is_null($this->_flagObject)) {
            $className = (string)Mage::getConfig()->getNode(self::XML_PATH_FLAT_FLAG);
            $this->_flagObject = Mage::getSingleton($className)
                ->loadSelf();
        }
        return $this->_flagObject;
    }

    /**
     * Check Catalog Product Flat functionality is enabled
     *
     * @param int|string|null|Mage_Core_Model_Store $store this parameter is deprecated and no longer in use
     *
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_USE_PRODUCT_FLAT);
    }

    /**
     * Check if Catalog Product Flat Data has been initialized
     *
     * @param null|bool|int|Mage_Core_Model_Store $store Store(id) for which the value is checked
     * @return bool
     */
    public function isBuilt($store = null)
    {
        if ($store !== null) {
            return $this->getFlag()->isStoreBuilt(Mage::app()->getStore($store)->getId());
        }
        return $this->getFlag()->getIsBuilt();
    }

    /**
     * Check if Catalog Product Flat Data has been initialized for all stores
     *
     * @return bool
     */
    public function isBuiltAllStores()
    {
        $isBuildAll = true;
        foreach(Mage::app()->getStores(false) as $store) {
            /** @var $store Mage_Core_Model_Store */
            $isBuildAll = $isBuildAll && $this->isBuilt($store->getId());
        }
        return $isBuildAll;
    }

    /**
     * Is add filterable attributes to Flat table
     *
     * @return int
     */
    public function isAddFilterableAttributes()
    {
        return intval(Mage::getConfig()->getNode(self::XML_NODE_ADD_FILTERABLE_ATTRIBUTES));
    }

    /**
     * Is add child data to Flat
     *
     * @return int
     */
    public function isAddChildData()
    {
        return intval(Mage::getConfig()->getNode(self::XML_NODE_ADD_CHILD_DATA));
    }
}
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


class Mage_Catalog_Model_Config extends Mage_Eav_Model_Config
{
    const XML_PATH_LIST_DEFAULT_SORT_BY     = 'catalog/frontend/default_sort_by';

    protected $_attributeSetsById;
    protected $_attributeSetsByName;

    protected $_attributeGroupsById;
    protected $_attributeGroupsByName;

    protected $_productTypesById;

    /**
     * Array of attributes codes needed for product load
     *
     * @var array
     */
    protected $_productAttributes;

    /**
     * Product Attributes used in product listing
     *
     * @var array
     */
    protected $_usedInProductListing;

    /**
     * Product Attributes For Sort By
     *
     * @var array
     */
    protected $_usedForSortBy;

    protected $_storeId = null;

    const XML_PATH_PRODUCT_COLLECTION_ATTRIBUTES = 'frontend/product/collection/attributes';

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/config');
    }

    /**
     * Set store id
     *
     * @param integer $storeId
     * @return Mage_Catalog_Model_Config
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Return store id, if is not set return current app store
     *
     * @return integer
     */
    public function getStoreId()
    {
        if ($this->_storeId === null) {
            return Mage::app()->getStore()->getId();
        }
        return $this->_storeId;
    }

    public function loadAttributeSets()
    {
        if ($this->_attributeSetsById) {
            return $this;
        }

        $attributeSetCollection = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->load();

        $this->_attributeSetsById = array();
        $this->_attributeSetsByName = array();
        foreach ($attributeSetCollection as $id=>$attributeSet) {
            $entityTypeId = $attributeSet->getEntityTypeId();
            $name = $attributeSet->getAttributeSetName();
            $this->_attributeSetsById[$entityTypeId][$id] = $name;
            $this->_attributeSetsByName[$entityTypeId][strtolower($name)] = $id;
        }
        return $this;
    }

    public function getAttributeSetName($entityTypeId, $id)
    {
        if (!is_numeric($id)) {
            return $id;
        }
        $this->loadAttributeSets();

        if (!is_numeric($entityTypeId)) {
            $entityTypeId = $this->getEntityType($entityTypeId)->getId();
        }
        return isset($this->_attributeSetsById[$entityTypeId][$id]) ? $this->_attributeSetsById[$entityTypeId][$id] : false;
    }

    public function getAttributeSetId($entityTypeId, $name)
    {
        if (is_numeric($name)) {
            return $name;
        }
        $this->loadAttributeSets();

        if (!is_numeric($entityTypeId)) {
            $entityTypeId = $this->getEntityType($entityTypeId)->getId();
        }
        $name = strtolower($name);
        return isset($this->_attributeSetsByName[$entityTypeId][$name]) ? $this->_attributeSetsByName[$entityTypeId][$name] : false;
    }

    public function loadAttributeGroups()
    {
        if ($this->_attributeGroupsById) {
            return $this;
        }

        $attributeSetCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
            ->load();

        $this->_attributeGroupsById = array();
        $this->_attributeGroupsByName = array();
        foreach ($attributeSetCollection as $id=>$attributeGroup) {
            $attributeSetId = $attributeGroup->getAttributeSetId();
            $name = $attributeGroup->getAttributeGroupName();
            $this->_attributeGroupsById[$attributeSetId][$id] = $name;
            $this->_attributeGroupsByName[$attributeSetId][strtolower($name)] = $id;
        }
        return $this;
    }

    public function getAttributeGroupName($attributeSetId, $id)
    {
        if (!is_numeric($id)) {
            return $id;
        }

        $this->loadAttributeGroups();

        if (!is_numeric($attributeSetId)) {
            $attributeSetId = $this->getAttributeSetId($attributeSetId);
        }
        return isset($this->_attributeGroupsById[$attributeSetId][$id]) ? $this->_attributeGroupsById[$attributeSetId][$id] : false;
    }

    public function getAttributeGroupId($attributeSetId, $name)
    {
        if (is_numeric($name)) {
            return $name;
        }

        $this->loadAttributeGroups();

        if (!is_numeric($attributeSetId)) {
            $attributeSetId = $this->getAttributeSetId($attributeSetId);
        }
        $name = strtolower($name);
        return isset($this->_attributeGroupsByName[$attributeSetId][$name]) ? $this->_attributeGroupsByName[$attributeSetId][$name] : false;
    }

    public function loadProductTypes()
    {
        if ($this->_productTypesById) {
            return $this;
        }

        /*
        $productTypeCollection = Mage::getResourceModel('catalog/product_type_collection')
            ->load();
        */
        $productTypeCollection = Mage::getModel('catalog/product_type')
            ->getOptionArray();

        $this->_productTypesById = array();
        $this->_productTypesByName = array();
        foreach ($productTypeCollection as $id=>$type) {
            //$name = $type->getCode();
            $name = $type;
            $this->_productTypesById[$id] = $name;
            $this->_productTypesByName[strtolower($name)] = $id;
        }
        return $this;
    }

    public function getProductTypeId($name)
    {
        if (is_numeric($name)) {
            return $name;
        }

        $this->loadProductTypes();

        $name = strtolower($name);
        return isset($this->_productTypesByName[$name]) ? $this->_productTypesByName[$name] : false;
    }

    public function getProductTypeName($id)
    {
        if (!is_numeric($id)) {
            return $id;
        }

        $this->loadProductTypes();

        return isset($this->_productTypesById[$id]) ? $this->_productTypesById[$id] : false;
    }

    public function getSourceOptionId($source, $value)
    {
        foreach ($source->getAllOptions() as $option) {
            if (strcasecmp($option['label'], $value)==0 || $option['value'] == $value) {
                return $option['value'];
            }
        }
        return null;
    }

    /**
     * Load Product attributes
     *
     * @return array
     */
    public function getProductAttributes()
    {
        if (is_null($this->_productAttributes)) {
            $this->_productAttributes = array_keys($this->getAttributesUsedInProductListing());
        }
        return $this->_productAttributes;
    }

    /**
     * Retrieve Product Collection Attributes from XML config file
     * Used only for install/upgrade
     *
     * @return array
     */
    public function getProductCollectionAttributes() {
        $attributes = Mage::getConfig()
            ->getNode(self::XML_PATH_PRODUCT_COLLECTION_ATTRIBUTES)
            ->asArray();
        return array_keys($attributes);;
    }

    /**
     * Retrieve resource model
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Config
     */
    protected function _getResource()
    {
        return Mage::getResourceModel('catalog/config');
    }

    /**
     * Retrieve Attributes used in product listing
     *
     * @return array
     */
    public function getAttributesUsedInProductListing() {
        if (is_null($this->_usedInProductListing)) {
            $this->_usedInProductListing = array();
            $entityType = Mage_Catalog_Model_Product::ENTITY;
            $attributesData = $this->_getResource()
                ->setStoreId($this->getStoreId())
                ->getAttributesUsedInListing();
            Mage::getSingleton('eav/config')
                ->importAttributesData($entityType, $attributesData);
            foreach ($attributesData as $attributeData) {
                $attributeCode = $attributeData['attribute_code'];
                $this->_usedInProductListing[$attributeCode] = Mage::getSingleton('eav/config')
                    ->getAttribute($entityType, $attributeCode);
            }
        }
        return $this->_usedInProductListing;
    }

    /**
     * Retrieve Attributes array used for sort by
     *
     * @return array
     */
    public function getAttributesUsedForSortBy() {
        if (is_null($this->_usedForSortBy)) {
            $this->_usedForSortBy = array();
            $entityType     = Mage_Catalog_Model_Product::ENTITY;
            $attributesData = $this->_getResource()
                ->getAttributesUsedForSortBy();
            Mage::getSingleton('eav/config')
                ->importAttributesData($entityType, $attributesData);
            foreach ($attributesData as $attributeData) {
                $attributeCode = $attributeData['attribute_code'];
                $this->_usedForSortBy[$attributeCode] = Mage::getSingleton('eav/config')
                    ->getAttribute($entityType, $attributeCode);
            }
        }
        return $this->_usedForSortBy;
    }

    /**
     * Retrieve Attributes Used for Sort by as array
     * key = code, value = name
     *
     * @return array
     */
    public function getAttributeUsedForSortByArray()
    {
        $options = array(
            'position'  => Mage::helper('catalog')->__('Position')
        );
        foreach ($this->getAttributesUsedForSortBy() as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute_Abstract */
            $options[$attribute->getAttributeCode()] = $attribute->getStoreLabel();
        }

        return $options;
    }

    /**
     * Retrieve Product List Default Sort By
     *
     * @param mixed $store
     * @return string
     */
    public function getProductListDefaultSortBy($store = null) {
        return Mage::getStoreConfig(self::XML_PATH_LIST_DEFAULT_SORT_BY, $store);
    }
}
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
 * Catalog Custom Category design Model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Design extends Mage_Core_Model_Abstract
{
    const APPLY_FOR_PRODUCT     = 1;
    const APPLY_FOR_CATEGORY    = 2;

    /**
     * @deprecated after 1.4.1.0
     * Category / Custom Design / Apply To constants
     */
    const CATEGORY_APPLY_CATEGORY_AND_PRODUCT_RECURSIVE = 1;
    const CATEGORY_APPLY_CATEGORY_ONLY                  = 2;
    const CATEGORY_APPLY_CATEGORY_AND_PRODUCT_ONLY      = 3;
    const CATEGORY_APPLY_CATEGORY_RECURSIVE             = 4;

    /**
     * Apply design from catalog object
     *
     * @deprecated after 1.4.2.0-beta1
     *
     * @param array|Mage_Catalog_Model_Category|Mage_Catalog_Model_Product $object
     * @param int $calledFrom
     * @return Mage_Catalog_Model_Design
     */
    public function applyDesign($object, $calledFrom = 0)
    {
        if ($calledFrom != self::APPLY_FOR_CATEGORY && $calledFrom != self::APPLY_FOR_PRODUCT) {
            return $this;
        }

        // If Flat Data enabled then use it but only on frontend
        if (Mage::helper('catalog/category_flat')->isAvailable() && !Mage::app()->getStore()->isAdmin()) {
            $this->_applyDesign($object, $calledFrom);
        } else {
            $this->_inheritDesign($object, $calledFrom);
        }

        return $this;
    }

    /**
     * Apply package and theme
     *
     * @param string $package
     * @param string $theme
     */
    protected function _apply($package, $theme)
    {
        Mage::getSingleton('core/design_package')
            ->setPackageName($package)
            ->setTheme($theme);
    }

    /**
     * Apply custom design
     *
     * @param string $design
     */
    public function applyCustomDesign($design)
    {
        $designInfo = explode('/', $design);
        if (count($designInfo) != 2) {
            return false;
        }
        $package = $designInfo[0];
        $theme   = $designInfo[1];
        $this->_apply($package, $theme);
    }

    /**
     * Check is allow apply for
     *
     * @deprecated after 1.4.1.0
     *
     * @param int $applyForObject
     * @param int $applyTo
     * @param int $pass
     * @return bool
     */
    protected function _isApplyFor($applyForObject, $applyTo, $pass = 0)
    {
        $hasError = false;

        if ($pass == 0) {
            switch ($applyForObject) {
                case self::APPLY_FOR_CATEGORY:
                    break;
                case self::APPLY_FOR_PRODUCT:
                    $validApplyTo = array(
                        self::CATEGORY_APPLY_CATEGORY_AND_PRODUCT_RECURSIVE,
                        self::CATEGORY_APPLY_CATEGORY_AND_PRODUCT_ONLY
                    );
                    if ($applyTo && !in_array($applyTo, $validApplyTo)) {
                        $hasError = true;
                    }
                    break;
                default:
                    $hasError = true;
                    break;
            }
        }
        else {
            switch ($applyForObject) {
                case self::APPLY_FOR_CATEGORY:
                    $validApplyTo = array(
                        self::CATEGORY_APPLY_CATEGORY_AND_PRODUCT_RECURSIVE,
                        self::CATEGORY_APPLY_CATEGORY_RECURSIVE
                    );
                    if ($applyTo && !in_array($applyTo, $validApplyTo)) {
                        $hasError = true;
                    }
                    break;
                case self::APPLY_FOR_PRODUCT:
                    $validApplyTo = array(
                        self::CATEGORY_APPLY_CATEGORY_AND_PRODUCT_RECURSIVE
                    );
                    if ($applyTo && !in_array($applyTo, $validApplyTo)) {
                        $hasError = true;
                    }
                    break;
                default:
                    $hasError = true;
                    break;
            }
        }

        return !$hasError;
    }

    /**
     * Check and apply design
     *
     * @deprecated after 1.4.2.0-beta1
     *
     * @param string $design
     * @param array $date
     */
    protected function _isApplyDesign($design, array $date)
    {
        if (!array_key_exists('from', $date) || !array_key_exists('to', $date)) {
            return false;
        }

        $designInfo = explode("/", $design);
        if (count($designInfo) != 2) {
            return false;
        }

        // define package and theme
        $package    = $designInfo[0];
        $theme      = $designInfo[1];

        // compare dates
        if (Mage::app()->getLocale()->isStoreDateInInterval(null, $date['from'], $date['to'])) {
            $this->_apply($package, $theme);
            return true;
        }

        return false;
    }

    /**
     * Recursively apply design
     *
     * @deprecated after 1.4.2.0-beta1
     *
     * @param Varien_Object $object
     * @param int $calledFrom
     *
     * @return Mage_Catalog_Model_Design
     */
    protected function _inheritDesign($object, $calledFrom = 0)
    {
        $useParentSettings = false;
        if ($object instanceof Mage_Catalog_Model_Product) {
            $category = $object->getCategory();

            if ($category && $category->getId()) {
                return $this->_inheritDesign($category, $calledFrom);
            }
        }
        elseif ($object instanceof Mage_Catalog_Model_Category) {
            $category = $object->getParentCategory();

            $useParentSettings = $object->getCustomUseParentSettings();
            if ($useParentSettings) {
                if ($category &&
                    $category->getId() &&
                    $category->getLevel() > 1 &&
                    $category->getId() != Mage_Catalog_Model_Category::TREE_ROOT_ID) {
                    return $this->_inheritDesign($category, $calledFrom);
                }
            }

            if ($calledFrom == self::APPLY_FOR_PRODUCT) {
                $applyToProducts = $object->getCustomApplyToProducts();
                if (!$applyToProducts) {
                    return $this;
                }
            }
        }

        if (!$useParentSettings) {
            $design = $object->getCustomDesign();
            $date   = $object->getCustomDesignDate();
            $this->_isApplyDesign($design, $date);
        }

        return $this;
    }

    /**
     * Apply design recursively (if using EAV)
     *
     * @deprecated after 1.4.1.0
     *
     * @param Varien_Object $object
     * @param int $calledFrom
     * @param int $pass
     *
     * @return Mage_Catalog_Model_Design
     */
    protected function _applyDesignRecursively($object, $calledFrom = 0, $pass = 0)
    {
        $design  = $object->getCustomDesign();
        $date    = $object->getCustomDesignDate();
        $applyTo = $object->getCustomDesignApply();

        $checkAndApply = $this->_isApplyFor($calledFrom, $applyTo, $pass)
            && $this->_isApplyDesign($design, $date);
        if ($checkAndApply) {
            return $this;
        }

        $pass ++;

        $category = null;
        if ($object instanceof Mage_Catalog_Model_Product) {
            $category = $object->getCategory();
            $pass --;
        }
        elseif ($object instanceof Mage_Catalog_Model_Category) {
            $category = $object->getParentCategory();
        }

        if ($category && $category->getId()) {
            $this->_applyDesignRecursively($category, $calledFrom, $pass);
        }

        return $this;
    }

    /**
     * @deprecated after 1.4.2.0-beta1
     */
    protected function _applyDesign($designUpdateData, $calledFrom = 0, $loaded = false, $pass = 0)
    {
        $objects = array();
        if (is_object($designUpdateData)) {
            $objects = array($designUpdateData);
        } elseif (is_array($designUpdateData)) {
            $objects = &$designUpdateData;
        }
        foreach ($objects as $object) {
            $design  = $object->getCustomDesign();
            $date    = $object->getCustomDesignDate();
            $applyTo = $object->getCustomDesignApply();

            $checkAndApply = $this->_isApplyFor($calledFrom, $applyTo, $pass)
                && $this->_isApplyDesign($design, $date);
            if ($checkAndApply) {
                return $this;
            }
        }

        $pass ++;

        if (false === $loaded && is_object($designUpdateData)) {
            $_designUpdateData = array();
            if ($designUpdateData instanceof Mage_Catalog_Model_Product) {
                $_category = $designUpdateData->getCategory();
                $_designUpdateData = array_merge(
                    $_designUpdateData, array($_category)
                );
                $pass --;
            } elseif ($designUpdateData instanceof Mage_Catalog_Model_Category) {
                $_category = &$designUpdateData;
            }
            if ($_category && $_category->getId()) {
                $_designUpdateData = array_merge(
                    $_designUpdateData,
                    $_category->getResource()->getDesignUpdateData($_category)
                );
                $this->_applyDesign($_designUpdateData, $calledFrom, true, $pass);
            }
        }
        return $this;
    }

    /**
     * Get custom layout settings
     *
     * @param Mage_Catalog_Model_Category|Mage_Catalog_Model_Product $object
     * @return Varien_Object
     */
    public function getDesignSettings($object)
    {
        if ($object instanceof Mage_Catalog_Model_Product) {
            $currentCategory = $object->getCategory();
        } else {
            $currentCategory = $object;
        }

        $category = null;
        if ($currentCategory) {
            $category = $currentCategory->getParentDesignCategory($currentCategory);
        }

        if ($object instanceof Mage_Catalog_Model_Product) {
            if ($category && $category->getCustomApplyToProducts()) {
                return $this->_mergeSettings($this->_extractSettings($category), $this->_extractSettings($object));
            } else {
                return $this->_extractSettings($object);
            }
        } else {
             return $this->_extractSettings($category);
        }
    }

    /**
     * Extract custom layout settings from category or product object
     *
     * @param Mage_Catalog_Model_Category|Mage_Catalog_Model_Product $object
     * @return Varien_Object
     */
    protected function _extractSettings($object)
    {
        $settings = new Varien_Object;
        if (!$object) {
            return $settings;
        }
        $date = $object->getCustomDesignDate();
        if (array_key_exists('from', $date) && array_key_exists('to', $date)
            && Mage::app()->getLocale()->isStoreDateInInterval(null, $date['from'], $date['to'])) {
                $settings->setCustomDesign($object->getCustomDesign())
                    ->setPageLayout($object->getPageLayout())
                    ->setLayoutUpdates((array)$object->getCustomLayoutUpdate());
        }
        return $settings;
    }

    /**
     * Merge custom design settings
     *
     * @param Varien_Object $categorySettings
     * @param Varien_Object $productSettings
     * @return Varien_Object
     */
    protected function _mergeSettings($categorySettings, $productSettings)
    {
        if ($productSettings->getCustomDesign()) {
            $categorySettings->setCustomDesign($productSettings->getCustomDesign());
        }
        if ($productSettings->getPageLayout()) {
            $categorySettings->setPageLayout($productSettings->getPageLayout());
        }
        if ($productSettings->getLayoutUpdates()) {
            $update = array_merge($categorySettings->getLayoutUpdates(), $productSettings->getLayoutUpdates());
            $categorySettings->setLayoutUpdates($update);
        }
        return $categorySettings;
    }
}
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
 * Catalog product model
 *
 * @method Mage_Catalog_Model_Resource_Product getResource()
 * @method Mage_Catalog_Model_Product setHasError(bool $value)
 * @method null|bool getHasError()
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product extends Mage_Catalog_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY                 = 'catalog_product';

    const CACHE_TAG              = 'catalog_product';
    protected $_cacheTag         = 'catalog_product';
    protected $_eventPrefix      = 'catalog_product';
    protected $_eventObject      = 'product';
    protected $_canAffectOptions = false;

    /**
     * Product type instance
     *
     * @var Mage_Catalog_Model_Product_Type_Abstract
     */
    protected $_typeInstance            = null;

    /**
     * Product type instance as singleton
     */
    protected $_typeInstanceSingleton   = null;

    /**
     * Product link instance
     *
     * @var Mage_Catalog_Model_Product_Link
     */
    protected $_linkInstance;

    /**
     * Product object customization (not stored in DB)
     *
     * @var array
     */
    protected $_customOptions = array();

    /**
     * Product Url Instance
     *
     * @var Mage_Catalog_Model_Product_Url
     */
    protected $_urlModel = null;

    protected static $_url;
    protected static $_urlRewrite;

    protected $_errors = array();

    protected $_optionInstance;

    protected $_options = array();

    /**
     * Product reserved attribute codes
     */
    protected $_reservedAttributes;

    /**
     * Flag for available duplicate function
     *
     * @var boolean
     */
    protected $_isDuplicable = true;

    /**
     * Flag for get Price function
     *
     * @var boolean
     */
    protected $_calculatePrice = true;

    /**
     * Initialize resources
     */
    protected function _construct()
    {
        $this->_init('catalog/product');
    }

    /**
     * Init mapping array of short fields to
     * its full names
     *
     * @return Varien_Object
     */
    protected function _initOldFieldsMap()
    {
        $this->_oldFieldsMap = Mage::helper('catalog')->getOldFieldMap();
        return $this;
    }

    /**
     * Retrieve Store Id
     *
     * @return int
     */
    public function getStoreId()
    {
        if ($this->hasData('store_id')) {
            return $this->getData('store_id');
        }
        return Mage::app()->getStore()->getId();
    }

    /**
     * Get collection instance
     *
     * @return object
     */
    public function getResourceCollection()
    {
        if (empty($this->_resourceCollectionName)) {
            Mage::throwException(Mage::helper('catalog')->__('The model collection resource name is not defined.'));
        }
        $collection = Mage::getResourceModel($this->_resourceCollectionName);
        $collection->setStoreId($this->getStoreId());
        return $collection;
    }

    /**
     * Get product url model
     *
     * @return Mage_Catalog_Model_Product_Url
     */
    public function getUrlModel()
    {
        if ($this->_urlModel === null) {
            $this->_urlModel = Mage::getSingleton('catalog/factory')->getProductUrlInstance();
        }
        return $this->_urlModel;
    }

    /**
     * Validate Product Data
     *
     * @todo implement full validation process with errors returning which are ignoring now
     *
     * @return Mage_Catalog_Model_Product
     */
    public function validate()
    {
//        $this->getAttributes();
//        Mage::dispatchEvent($this->_eventPrefix.'_validate_before', array($this->_eventObject=>$this));
//        $result = $this->_getResource()->validate($this);
//        Mage::dispatchEvent($this->_eventPrefix.'_validate_after', array($this->_eventObject=>$this));
//        return $result;
        Mage::dispatchEvent($this->_eventPrefix.'_validate_before', array($this->_eventObject=>$this));
        $this->_getResource()->validate($this);
        Mage::dispatchEvent($this->_eventPrefix.'_validate_after', array($this->_eventObject=>$this));
        return $this;
    }

    /**
     * Get product name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_getData('name');
    }

    /**
     * Get product price throught type instance
     *
     * @return unknown
     */
    public function getPrice()
    {
        if ($this->_calculatePrice || !$this->getData('price')) {
            return $this->getPriceModel()->getPrice($this);
        } else {
            return $this->getData('price');
        }
    }

    /**
     * Set Price calculation flag
     *
     * @param bool $calculate
     * @return void
     */
    public function setPriceCalculation($calculate = true)
    {
        $this->_calculatePrice = $calculate;
    }

    /**
     * Get product type identifier
     *
     * @return string
     */
    public function getTypeId()
    {
        return $this->_getData('type_id');
    }

    /**
     * Get product status
     *
     * @return int
     */
    public function getStatus()
    {
        if (is_null($this->_getData('status'))) {
            $this->setData('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        }
        return $this->_getData('status');
    }

    /**
     * Retrieve type instance
     *
     * Type instance implement type depended logic
     *
     * @param  bool $singleton
     * @return Mage_Catalog_Model_Product_Type_Abstract
     */
    public function getTypeInstance($singleton = false)
    {
        if ($singleton === true) {
            if (is_null($this->_typeInstanceSingleton)) {
                $this->_typeInstanceSingleton = Mage::getSingleton('catalog/product_type')
                    ->factory($this, true);
            }
            return $this->_typeInstanceSingleton;
        }

        if ($this->_typeInstance === null) {
            $this->_typeInstance = Mage::getSingleton('catalog/product_type')
                ->factory($this);
        }
        return $this->_typeInstance;
    }

    /**
     * Set type instance for external
     *
     * @param Mage_Catalog_Model_Product_Type_Abstract $instance  Product type instance
     * @param bool                                     $singleton Whether instance is singleton
     * @return Mage_Catalog_Model_Product
     */
    public function setTypeInstance($instance, $singleton = false)
    {
        if ($singleton === true) {
            $this->_typeInstanceSingleton = $instance;
        } else {
            $this->_typeInstance = $instance;
        }
        return $this;
    }

    /**
     * Retrieve link instance
     *
     * @return  Mage_Catalog_Model_Product_Link
     */
    public function getLinkInstance()
    {
        if (!$this->_linkInstance) {
            $this->_linkInstance = Mage::getSingleton('catalog/product_link');
        }
        return $this->_linkInstance;
    }

    /**
     * Retrive product id by sku
     *
     * @param   string $sku
     * @return  integer
     */
    public function getIdBySku($sku)
    {
        return $this->_getResource()->getIdBySku($sku);
    }

    /**
     * Retrieve product category id
     *
     * @return int
     */
    public function getCategoryId()
    {
        if ($category = Mage::registry('current_category')) {
            return $category->getId();
        }
        return false;
    }

    /**
     * Retrieve product category
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCategory()
    {
        $category = $this->getData('category');
        if (is_null($category) && $this->getCategoryId()) {
            $category = Mage::getModel('catalog/category')->load($this->getCategoryId());
            $this->setCategory($category);
        }
        return $category;
    }

    /**
     * Set assigned category IDs array to product
     *
     * @param array|string $ids
     * @return Mage_Catalog_Model_Product
     */
    public function setCategoryIds($ids)
    {
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        } elseif (!is_array($ids)) {
            Mage::throwException(Mage::helper('catalog')->__('Invalid category IDs.'));
        }
        foreach ($ids as $i => $v) {
            if (empty($v)) {
                unset($ids[$i]);
            }
        }

        $this->setData('category_ids', $ids);
        return $this;
    }

    /**
     * Retrieve assigned category Ids
     *
     * @return array
     */
    public function getCategoryIds()
    {
        if (! $this->hasData('category_ids')) {
            $wasLocked = false;
            if ($this->isLockedAttribute('category_ids')) {
                $wasLocked = true;
                $this->unlockAttribute('category_ids');
            }
            $ids = $this->_getResource()->getCategoryIds($this);
            $this->setData('category_ids', $ids);
            if ($wasLocked) {
                $this->lockAttribute('category_ids');
            }
        }

        return (array) $this->_getData('category_ids');
    }

    /**
     * Retrieve product categories
     *
     * @return Varien_Data_Collection
     */
    public function getCategoryCollection()
    {
        return $this->_getResource()->getCategoryCollection($this);
    }

    /**
     * Retrieve product websites identifiers
     *
     * @return array
     */
    public function getWebsiteIds()
    {
        if (!$this->hasWebsiteIds()) {
            $ids = $this->_getResource()->getWebsiteIds($this);
            $this->setWebsiteIds($ids);
        }
        return $this->getData('website_ids');
    }

    /**
     * Get all sore ids where product is presented
     *
     * @return array
     */
    public function getStoreIds()
    {
        if (!$this->hasStoreIds()) {
            $storeIds = array();
            if ($websiteIds = $this->getWebsiteIds()) {
                foreach ($websiteIds as $websiteId) {
                    $websiteStores = Mage::app()->getWebsite($websiteId)->getStoreIds();
                    $storeIds = array_merge($storeIds, $websiteStores);
                }
            }
            $this->setStoreIds($storeIds);
        }
        return $this->getData('store_ids');
    }

    /**
     * Retrieve product attributes
     * if $groupId is null - retrieve all product attributes
     *
     * @param int  $groupId   Retrieve attributes of the specified group
     * @param bool $skipSuper Not used
     * @return array
     */
    public function getAttributes($groupId = null, $skipSuper = false)
    {
        $productAttributes = $this->getTypeInstance(true)->getEditableAttributes($this);
        if ($groupId) {
            $attributes = array();
            foreach ($productAttributes as $attribute) {
                if ($attribute->isInGroup($this->getAttributeSetId(), $groupId)) {
                    $attributes[] = $attribute;
                }
            }
        } else {
            $attributes = $productAttributes;
        }

        return $attributes;
    }

    /**
     * Check product options and type options and save them, too
     */
    protected function _beforeSave()
    {
        $this->cleanCache();
        $this->setTypeHasOptions(false);
        $this->setTypeHasRequiredOptions(false);

        $this->getTypeInstance(true)->beforeSave($this);

        $hasOptions         = false;
        $hasRequiredOptions = false;

        /**
         * $this->_canAffectOptions - set by type instance only
         * $this->getCanSaveCustomOptions() - set either in controller when "Custom Options" ajax tab is loaded,
         * or in type instance as well
         */
        $this->canAffectOptions($this->_canAffectOptions && $this->getCanSaveCustomOptions());
        if ($this->getCanSaveCustomOptions()) {
            $options = $this->getProductOptions();
            if (is_array($options)) {
                $this->setIsCustomOptionChanged(true);
                foreach ($this->getProductOptions() as $option) {
                    $this->getOptionInstance()->addOption($option);
                    if ((!isset($option['is_delete'])) || $option['is_delete'] != '1') {
                        $hasOptions = true;
                    }
                }
                foreach ($this->getOptionInstance()->getOptions() as $option) {
                    if ($option['is_require'] == '1') {
                        $hasRequiredOptions = true;
                        break;
                    }
                }
            }
        }

        /**
         * Set true, if any
         * Set false, ONLY if options have been affected by Options tab and Type instance tab
         */
        if ($hasOptions || (bool)$this->getTypeHasOptions()) {
            $this->setHasOptions(true);
            if ($hasRequiredOptions || (bool)$this->getTypeHasRequiredOptions()) {
                $this->setRequiredOptions(true);
            } elseif ($this->canAffectOptions()) {
                $this->setRequiredOptions(false);
            }
        } elseif ($this->canAffectOptions()) {
            $this->setHasOptions(false);
            $this->setRequiredOptions(false);
        }
        parent::_beforeSave();
    }

    /**
     * Check/set if options can be affected when saving product
     * If value specified, it will be set.
     *
     * @param   bool $value
     * @return  bool
     */
    public function canAffectOptions($value = null)
    {
        if (null !== $value) {
            $this->_canAffectOptions = (bool)$value;
        }
        return $this->_canAffectOptions;
    }

    /**
     * Saving product type related data and init index
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _afterSave()
    {
        $this->getLinkInstance()->saveProductRelations($this);
        $this->getTypeInstance(true)->save($this);

        /**
         * Product Options
         */
        $this->getOptionInstance()->setProduct($this)
            ->saveOptions();

        return parent::_afterSave();
    }

    /**
     * Clear chache related with product and protect delete from not admin
     * Register indexing event before delete product
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        $this->cleanCache();

        return parent::_beforeDelete();
    }

    /**
     * Init indexing process after product delete commit
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _afterDeleteCommit()
    {
        parent::_afterDeleteCommit();

        /** @var \Mage_Index_Model_Indexer $indexer */
        $indexer = Mage::getSingleton('index/indexer');

        $indexer->processEntityAction($this, self::ENTITY, Mage_Index_Model_Event::TYPE_DELETE);
    }

    /**
     * Load product options if they exists
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        /**
         * Load product options
         */
        if ($this->getHasOptions()) {
            foreach ($this->getProductOptionsCollection() as $option) {
                $option->setProduct($this);
                $this->addOption($option);
            }
        }
        return $this;
    }

    /**
     * Retrieve resource instance wrapper
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Clear cache related with product id
     *
     * @return Mage_Catalog_Model_Product
     */
    public function cleanCache()
    {
        Mage::app()->cleanCache('catalog_product_'.$this->getId());
        return $this;
    }

    /**
     * Get product price model
     *
     * @return Mage_Catalog_Model_Product_Type_Price
     */
    public function getPriceModel()
    {
        return Mage::getSingleton('catalog/product_type')->priceFactory($this->getTypeId());
    }

    /**
     * Get product group price
     *
     * @return float
     */
    public function getGroupPrice()
    {
        return $this->getPriceModel()->getGroupPrice($this);
    }

    /**
     * Get product tier price by qty
     *
     * @param   double $qty
     * @return  double
     */
    public function getTierPrice($qty=null)
    {
        return $this->getPriceModel()->getTierPrice($qty, $this);
    }

    /**
     * Count how many tier prices we have for the product
     *
     * @return  int
     */
    public function getTierPriceCount()
    {
        return $this->getPriceModel()->getTierPriceCount($this);
    }

    /**
     * Get formated by currency tier price
     *
     * @param   double $qty
     * @return  array || double
     */
    public function getFormatedTierPrice($qty=null)
    {
        return $this->getPriceModel()->getFormatedTierPrice($qty, $this);
    }

    /**
     * Get formated by currency product price
     *
     * @return  array || double
     */
    public function getFormatedPrice()
    {
        return $this->getPriceModel()->getFormatedPrice($this);
    }

    /**
     * Sets final price of product
     *
     * This func is equal to magic 'setFinalPrice()', but added as a separate func, because in cart with bundle
     * products it's called very often in Item->getProduct(). So removing chain of magic with more cpu consuming
     * algorithms gives nice optimization boost.
     *
     * @param float $price Price amount
     * @return Mage_Catalog_Model_Product
     */
    public function setFinalPrice($price)
    {
        $this->_data['final_price'] = $price;
        return $this;
    }

    /**
     * Get product final price
     *
     * @param double $qty
     * @return double
     */
    public function getFinalPrice($qty=null)
    {
        $price = $this->_getData('final_price');
        if ($price !== null) {
            return $price;
        }
        return $this->getPriceModel()->getFinalPrice($qty, $this);
    }

    /**
     * Returns calculated final price
     *
     * @return float
     */
    public function getCalculatedFinalPrice()
    {
        return $this->_getData('calculated_final_price');
    }

    /**
     * Returns minimal price
     *
     * @return float
     */
    public function getMinimalPrice()
    {
        return max($this->_getData('minimal_price'), 0);
    }

    /**
     * Returns special price
     *
     * @return float
     */
    public function getSpecialPrice()
    {
        return $this->_getData('special_price');
    }

    /**
     * Returns starting date of the special price
     *
     * @return mixed
     */
    public function getSpecialFromDate()
    {
        return $this->_getData('special_from_date');
    }

    /**
     * Returns end date of the special price
     *
     * @return mixed
     */
    public function getSpecialToDate()
    {
        return $this->_getData('special_to_date');
    }


/*******************************************************************************
 ** Linked products API
 */
    /**
     * Retrieve array of related roducts
     *
     * @return array
     */
    public function getRelatedProducts()
    {
        if (!$this->hasRelatedProducts()) {
            $products = array();
            $collection = $this->getRelatedProductCollection();
            foreach ($collection as $product) {
                $products[] = $product;
            }
            $this->setRelatedProducts($products);
        }
        return $this->getData('related_products');
    }

    /**
     * Retrieve related products identifiers
     *
     * @return array
     */
    public function getRelatedProductIds()
    {
        if (!$this->hasRelatedProductIds()) {
            $ids = array();
            foreach ($this->getRelatedProducts() as $product) {
                $ids[] = $product->getId();
            }
            $this->setRelatedProductIds($ids);
        }
        return $this->getData('related_product_ids');
    }

    /**
     * Retrieve collection related product
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Product_Collection
     */
    public function getRelatedProductCollection()
    {
        $collection = $this->getLinkInstance()->useRelatedLinks()
            ->getProductCollection()
            ->setIsStrongMode();
        $collection->setProduct($this);
        return $collection;
    }

    /**
     * Retrieve collection related link
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Collection
     */
    public function getRelatedLinkCollection()
    {
        $collection = $this->getLinkInstance()->useRelatedLinks()
            ->getLinkCollection();
        $collection->setProduct($this);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();
        return $collection;
    }

    /**
     * Retrieve array of up sell products
     *
     * @return array
     */
    public function getUpSellProducts()
    {
        if (!$this->hasUpSellProducts()) {
            $products = array();
            foreach ($this->getUpSellProductCollection() as $product) {
                $products[] = $product;
            }
            $this->setUpSellProducts($products);
        }
        return $this->getData('up_sell_products');
    }

    /**
     * Retrieve up sell products identifiers
     *
     * @return array
     */
    public function getUpSellProductIds()
    {
        if (!$this->hasUpSellProductIds()) {
            $ids = array();
            foreach ($this->getUpSellProducts() as $product) {
                $ids[] = $product->getId();
            }
            $this->setUpSellProductIds($ids);
        }
        return $this->getData('up_sell_product_ids');
    }

    /**
     * Retrieve collection up sell product
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Product_Collection
     */
    public function getUpSellProductCollection()
    {
        $collection = $this->getLinkInstance()->useUpSellLinks()
            ->getProductCollection()
            ->setIsStrongMode();
        $collection->setProduct($this);
        return $collection;
    }

    /**
     * Retrieve collection up sell link
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Collection
     */
    public function getUpSellLinkCollection()
    {
        $collection = $this->getLinkInstance()->useUpSellLinks()
            ->getLinkCollection();
        $collection->setProduct($this);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();
        return $collection;
    }

    /**
     * Retrieve array of cross sell products
     *
     * @return array
     */
    public function getCrossSellProducts()
    {
        if (!$this->hasCrossSellProducts()) {
            $products = array();
            foreach ($this->getCrossSellProductCollection() as $product) {
                $products[] = $product;
            }
            $this->setCrossSellProducts($products);
        }
        return $this->getData('cross_sell_products');
    }

    /**
     * Retrieve cross sell products identifiers
     *
     * @return array
     */
    public function getCrossSellProductIds()
    {
        if (!$this->hasCrossSellProductIds()) {
            $ids = array();
            foreach ($this->getCrossSellProducts() as $product) {
                $ids[] = $product->getId();
            }
            $this->setCrossSellProductIds($ids);
        }
        return $this->getData('cross_sell_product_ids');
    }

    /**
     * Retrieve collection cross sell product
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Link_Product_Collection
     */
    public function getCrossSellProductCollection()
    {
        $collection = $this->getLinkInstance()->useCrossSellLinks()
            ->getProductCollection()
            ->setIsStrongMode();
        $collection->setProduct($this);
        return $collection;
    }

    /**
     * Retrieve collection cross sell link
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Collection
     */
    public function getCrossSellLinkCollection()
    {
        $collection = $this->getLinkInstance()->useCrossSellLinks()
            ->getLinkCollection();
        $collection->setProduct($this);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();
        return $collection;
    }

    /**
     * Retrieve collection grouped link
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Collection
     */
    public function getGroupedLinkCollection()
    {
        $collection = $this->getLinkInstance()->useGroupedLinks()
            ->getLinkCollection();
        $collection->setProduct($this);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();
        return $collection;
    }

/*******************************************************************************
 ** Media API
 */
    /**
     * Retrive attributes for media gallery
     *
     * @return array
     */
    public function getMediaAttributes()
    {
        if (!$this->hasMediaAttributes()) {
            $mediaAttributes = array();
            foreach ($this->getAttributes() as $attribute) {
                if($attribute->getFrontend()->getInputType() == 'media_image') {
                    $mediaAttributes[$attribute->getAttributeCode()] = $attribute;
                }
            }
            $this->setMediaAttributes($mediaAttributes);
        }
        return $this->getData('media_attributes');
    }

    /**
     * Retrive media gallery images
     *
     * @return Varien_Data_Collection
     */
    public function getMediaGalleryImages()
    {
        if(!$this->hasData('media_gallery_images') && is_array($this->getMediaGallery('images'))) {
            $images = new Varien_Data_Collection();
            foreach ($this->getMediaGallery('images') as $image) {
                if ($image['disabled']) {
                    continue;
                }
                $image['url'] = $this->getMediaConfig()->getMediaUrl($image['file']);
                $image['id'] = isset($image['value_id']) ? $image['value_id'] : null;
                $image['path'] = $this->getMediaConfig()->getMediaPath($image['file']);
                $images->addItem(new Varien_Object($image));
            }
            $this->setData('media_gallery_images', $images);
        }

        return $this->getData('media_gallery_images');
    }

    /**
     * Add image to media gallery
     *
     * @param string        $file              file path of image in file system
     * @param string|array  $mediaAttribute    code of attribute with type 'media_image',
     *                                          leave blank if image should be only in gallery
     * @param boolean       $move              if true, it will move source file
     * @param boolean       $exclude           mark image as disabled in product page view
     * @return Mage_Catalog_Model_Product
     */
    public function addImageToMediaGallery($file, $mediaAttribute=null, $move=false, $exclude=true)
    {
        $attributes = $this->getTypeInstance(true)->getSetAttributes($this);
        if (!isset($attributes['media_gallery'])) {
            return $this;
        }
        $mediaGalleryAttribute = $attributes['media_gallery'];
        /* @var $mediaGalleryAttribute Mage_Catalog_Model_Resource_Eav_Attribute */
        $mediaGalleryAttribute->getBackend()->addImage($this, $file, $mediaAttribute, $move, $exclude);
        return $this;
    }

    /**
     * Retrive product media config
     *
     * @return Mage_Catalog_Model_Product_Media_Config
     */
    public function getMediaConfig()
    {
        return Mage::getSingleton('catalog/product_media_config');
    }

    /**
     * Create duplicate
     *
     * @return Mage_Catalog_Model_Product
     */
    public function duplicate()
    {
        $this->getWebsiteIds();
        $this->getCategoryIds();

        /* @var $newProduct Mage_Catalog_Model_Product */
        $newProduct = Mage::getModel('catalog/product')->setData($this->getData())
            ->setIsDuplicate(true)
            ->setOriginalId($this->getId())
            ->setSku(null)
            ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_DISABLED)
            ->setCreatedAt(null)
            ->setUpdatedAt(null)
            ->setId(null)
            ->setStoreId(Mage::app()->getStore()->getId());

        Mage::dispatchEvent(
            'catalog_model_product_duplicate',
            array('current_product' => $this, 'new_product' => $newProduct)
        );

        /* Prepare Related*/
        $data = array();
        $this->getLinkInstance()->useRelatedLinks();
        $attributes = array();
        foreach ($this->getLinkInstance()->getAttributes() as $_attribute) {
            if (isset($_attribute['code'])) {
                $attributes[] = $_attribute['code'];
            }
        }
        foreach ($this->getRelatedLinkCollection() as $_link) {
            $data[$_link->getLinkedProductId()] = $_link->toArray($attributes);
        }
        $newProduct->setRelatedLinkData($data);

        /* Prepare UpSell*/
        $data = array();
        $this->getLinkInstance()->useUpSellLinks();
        $attributes = array();
        foreach ($this->getLinkInstance()->getAttributes() as $_attribute) {
            if (isset($_attribute['code'])) {
                $attributes[] = $_attribute['code'];
            }
        }
        foreach ($this->getUpSellLinkCollection() as $_link) {
            $data[$_link->getLinkedProductId()] = $_link->toArray($attributes);
        }
        $newProduct->setUpSellLinkData($data);

        /* Prepare Cross Sell */
        $data = array();
        $this->getLinkInstance()->useCrossSellLinks();
        $attributes = array();
        foreach ($this->getLinkInstance()->getAttributes() as $_attribute) {
            if (isset($_attribute['code'])) {
                $attributes[] = $_attribute['code'];
            }
        }
        foreach ($this->getCrossSellLinkCollection() as $_link) {
            $data[$_link->getLinkedProductId()] = $_link->toArray($attributes);
        }
        $newProduct->setCrossSellLinkData($data);

        /* Prepare Grouped */
        $data = array();
        $this->getLinkInstance()->useGroupedLinks();
        $attributes = array();
        foreach ($this->getLinkInstance()->getAttributes() as $_attribute) {
            if (isset($_attribute['code'])) {
                $attributes[] = $_attribute['code'];
            }
        }
        foreach ($this->getGroupedLinkCollection() as $_link) {
            $data[$_link->getLinkedProductId()] = $_link->toArray($attributes);
        }
        $newProduct->setGroupedLinkData($data);

        $newProduct->save();

        $this->getOptionInstance()->duplicate($this->getId(), $newProduct->getId());
        $this->getResource()->duplicate($this->getId(), $newProduct->getId());

        // TODO - duplicate product on all stores of the websites it is associated with
        /*if ($storeIds = $this->getWebsiteIds()) {
            foreach ($storeIds as $storeId) {
                $this->setStoreId($storeId)
                   ->load($this->getId());

                $newProduct->setData($this->getData())
                    ->setSku(null)
                    ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_DISABLED)
                    ->setId($newId)
                    ->save();
            }
        }*/
        return $newProduct;
    }

    /**
     * Is product grouped
     *
     * @return bool
     */
    public function isSuperGroup()
    {
        return $this->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_GROUPED;
    }

    /**
     * Alias for isConfigurable()
     *
     * @return bool
     */
    public function isSuperConfig()
    {
        return $this->isConfigurable();
    }
    /**
     * Check is product grouped
     *
     * @return bool
     */
    public function isGrouped()
    {
        return $this->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_GROUPED;
    }

    /**
     * Check is product configurable
     *
     * @return bool
     */
    public function isConfigurable()
    {
        return $this->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE;
    }

    /**
     * Whether product configurable or grouped
     *
     * @return bool
     */
    public function isSuper()
    {
        return $this->isConfigurable() || $this->isGrouped();
    }

    /**
     * Returns visible status IDs in catalog
     *
     * @return array
     */
    public function getVisibleInCatalogStatuses()
    {
        return Mage::getSingleton('catalog/product_status')->getVisibleStatusIds();
    }

    /**
     * Retrieve visible statuses
     *
     * @return array
     */
    public function getVisibleStatuses()
    {
        return Mage::getSingleton('catalog/product_status')->getVisibleStatusIds();
    }

    /**
     * Check Product visilbe in catalog
     *
     * @return bool
     */
    public function isVisibleInCatalog()
    {
        return in_array($this->getStatus(), $this->getVisibleInCatalogStatuses());
    }

    /**
     * Retrieve visible in site visibilities
     *
     * @return array
     */
    public function getVisibleInSiteVisibilities()
    {
        return Mage::getSingleton('catalog/product_visibility')->getVisibleInSiteIds();
    }

    /**
     * Check Product visible in site
     *
     * @return bool
     */
    public function isVisibleInSiteVisibility()
    {
        return in_array($this->getVisibility(), $this->getVisibleInSiteVisibilities());
    }

    /**
     * Checks product can be duplicated
     *
     * @return boolean
     */
    public function isDuplicable()
    {
        return $this->_isDuplicable;
    }

    /**
     * Set is duplicable flag
     *
     * @param boolean $value
     * @return Mage_Catalog_Model_Product
     */
    public function setIsDuplicable($value)
    {
        $this->_isDuplicable = (boolean) $value;
        return $this;
    }


    /**
     * Check is product available for sale
     *
     * @return bool
     */
    public function isSalable()
    {
        Mage::dispatchEvent('catalog_product_is_salable_before', array(
            'product'   => $this
        ));

        $salable = $this->isAvailable();

        $object = new Varien_Object(array(
            'product'    => $this,
            'is_salable' => $salable
        ));
        Mage::dispatchEvent('catalog_product_is_salable_after', array(
            'product'   => $this,
            'salable'   => $object
        ));
        return $object->getIsSalable();
    }

    /**
     * Check whether the product type or stock allows to purchase the product
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->getTypeInstance(true)->isSalable($this)
            || Mage::helper('catalog/product')->getSkipSaleableCheck();
    }

    /**
     * Is product salable detecting by product type
     *
     * @return bool
     */
    public function getIsSalable()
    {
        $productType = $this->getTypeInstance(true);
        if (method_exists($productType, 'getIsSalable')) {
            return $productType->getIsSalable($this);
        }
        if ($this->hasData('is_salable')) {
            return $this->getData('is_salable');
        }

        return $this->isSalable();
    }

    /**
     * Check is a virtual product
     * Data helper wrapper
     *
     * @return bool
     */
    public function isVirtual()
    {
        return $this->getIsVirtual();
    }

    /**
     * Whether the product is a recurring payment
     *
     * @return bool
     */
    public function isRecurring()
    {
        return $this->getIsRecurring() == '1';
    }

    /**
     * Alias for isSalable()
     *
     * @return bool
     */
    public function isSaleable()
    {
        return $this->isSalable();
    }

    /**
     * Whether product available in stock
     *
     * @return bool
     */
    public function isInStock()
    {
        return $this->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_ENABLED;
    }

    /**
     * Get attribute text by its code
     *
     * @param $attributeCode Code of the attribute
     * @return string
     */
    public function getAttributeText($attributeCode)
    {
        return $this->getResource()
            ->getAttribute($attributeCode)
                ->getSource()
                    ->getOptionText($this->getData($attributeCode));
    }

    /**
     * Returns array with dates for custom design
     *
     * @return array
     */
    public function getCustomDesignDate()
    {
        $result = array();
        $result['from'] = $this->getData('custom_design_from');
        $result['to'] = $this->getData('custom_design_to');

        return $result;
    }

    /**
     * Retrieve Product URL
     *
     * @param  bool $useSid
     * @return string
     */
    public function getProductUrl($useSid = null)
    {
        return $this->getUrlModel()->getProductUrl($this, $useSid);
    }

    /**
     * Retrieve URL in current store
     *
     * @param array $params the route params
     * @return string
     */
    public function getUrlInStore($params = array())
    {
        return $this->getUrlModel()->getUrlInStore($this, $params);
    }

    /**
     * Formats URL key
     *
     * @param $str URL
     * @return string
     */
    public function formatUrlKey($str)
    {
        return $this->getUrlModel()->formatUrlKey($str);
    }

    /**
     * Retrieve Product Url Path (include category)
     *
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    public function getUrlPath($category=null)
    {
        return $this->getUrlModel()->getUrlPath($this, $category);
    }

    /**
     * Save current attribute with code $code and assign new value
     *
     * @param string $code  Attribute code
     * @param mixed  $value New attribute value
     * @param int    $store Store ID
     * @return void
     */
    public function addAttributeUpdate($code, $value, $store)
    {
        $oldValue = $this->getData($code);
        $oldStore = $this->getStoreId();

        $this->setData($code, $value);
        $this->setStoreId($store);
        $this->getResource()->saveAttribute($this, $code);

        $this->setData($code, $oldValue);
        $this->setStoreId($oldStore);
    }

    /**
     * Renders the object to array
     *
     * @param array $arrAttributes Attribute array
     * @return array
     */
    public function toArray(array $arrAttributes=array())
    {
        $data = parent::toArray($arrAttributes);
        if ($stock = $this->getStockItem()) {
            $data['stock_item'] = $stock->toArray();
        }
        unset($data['stock_item']['product']);
        return $data;
    }

    /**
     * Same as setData(), but also initiates the stock item (if it is there)
     *
     * @param array $data Array to form the object from
     * @return Mage_Catalog_Model_Product
     */
    public function fromArray($data)
    {
        if (isset($data['stock_item'])) {
            if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
                $stockItem = Mage::getModel('cataloginventory/stock_item')
                    ->setData($data['stock_item'])
                    ->setProduct($this);
                $this->setStockItem($stockItem);
            }
            unset($data['stock_item']);
        }
        $this->setData($data);
        return $this;
    }

    /**
     * @deprecated after 1.4.2.0
     * @return Mage_Catalog_Model_Product
     */
    public function loadParentProductIds()
    {
        return $this->setParentProductIds(array());
    }

    /**
     * Delete product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function delete()
    {
        parent::delete();
        Mage::dispatchEvent($this->_eventPrefix.'_delete_after_done', array($this->_eventObject=>$this));
        return $this;
    }

    /**
     * Returns request path
     *
     * @return string
     */
    public function getRequestPath()
    {
        if (!$this->_getData('request_path')) {
            $this->getProductUrl();
        }
        return $this->_getData('request_path');
    }

    /**
     * Custom function for other modules
     * @return string
     */

    public function getGiftMessageAvailable()
    {
        return $this->_getData('gift_message_available');
    }

    /**
     * Returns rating summary
     *
     * @return mixed
     */
    public function getRatingSummary()
    {
        return $this->_getData('rating_summary');
    }

    /**
     * Check is product composite
     *
     * @return bool
     */
    public function isComposite()
    {
        return $this->getTypeInstance(true)->isComposite($this);
    }

    /**
     * Check if product can be configured
     *
     * @return bool
     */
    public function canConfigure()
    {
        $options = $this->getOptions();
        return !empty($options) || $this->getTypeInstance(true)->canConfigure($this);
    }

    /**
     * Retrieve sku through type instance
     *
     * @return string
     */
    public function getSku()
    {
        return $this->getTypeInstance(true)->getSku($this);
    }

    /**
     * Retrieve weight throught type instance
     *
     * @return unknown
     */
    public function getWeight()
    {
        return $this->getTypeInstance(true)->getWeight($this);
    }

    /**
     * Retrieve option instance
     *
     * @return Mage_Catalog_Model_Product_Option
     */
    public function getOptionInstance()
    {
        if (!$this->_optionInstance) {
            $this->_optionInstance = Mage::getSingleton('catalog/product_option');
        }
        return $this->_optionInstance;
    }

    /**
     * Retrieve options collection of product
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Option_Collection
     */
    public function getProductOptionsCollection()
    {
        $collection = $this->getOptionInstance()
            ->getProductOptionCollection($this);

        return $collection;
    }

    /**
     * Add option to array of product options
     *
     * @param Mage_Catalog_Model_Product_Option $option
     * @return Mage_Catalog_Model_Product
     */
    public function addOption(Mage_Catalog_Model_Product_Option $option)
    {
        $this->_options[$option->getId()] = $option;
        return $this;
    }

    /**
     * Get option from options array of product by given option id
     *
     * @param int $optionId
     * @return Mage_Catalog_Model_Product_Option | null
     */
    public function getOptionById($optionId)
    {
        if (isset($this->_options[$optionId])) {
            return $this->_options[$optionId];
        }

        return null;
    }

    /**
     * Get all options of product
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Retrieve is a virtual product
     *
     * @return bool
     */
    public function getIsVirtual()
    {
        return $this->getTypeInstance(true)->isVirtual($this);
    }

    /**
     * Add custom option information to product
     *
     * @param   string $code    Option code
     * @param   mixed  $value   Value of the option
     * @param   int    $product Product ID
     * @return  Mage_Catalog_Model_Product
     */
    public function addCustomOption($code, $value, $product=null)
    {
        $product = $product ? $product : $this;
        $option = Mage::getModel('catalog/product_configuration_item_option')
            ->addData(array(
                'product_id'=> $product->getId(),
                'product'   => $product,
                'code'      => $code,
                'value'     => $value,
            ));
        $this->_customOptions[$code] = $option;
        return $this;
    }

    /**
     * Sets custom options for the product
     *
     * @param array $options Array of options
     * @return void
     */
    public function setCustomOptions(array $options)
    {
        $this->_customOptions = $options;
    }

    /**
     * Get all custom options of the product
     *
     * @return array
     */
    public function getCustomOptions()
    {
        return $this->_customOptions;
    }

    /**
     * Get product custom option info
     *
     * @param   string $code
     * @return  array
     */
    public function getCustomOption($code)
    {
        if (isset($this->_customOptions[$code])) {
            return $this->_customOptions[$code];
        }
        return null;
    }

    /**
     * Checks if there custom option for this product
     *
     * @return bool
     */
    public function hasCustomOptions()
    {
        if (count($this->_customOptions)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check availability display product in category
     *
     * @param   int $categoryId
     * @return  bool
     */
    public function canBeShowInCategory($categoryId)
    {
        return $this->_getResource()->canBeShowInCategory($this, $categoryId);
    }

    /**
     * Retrieve category ids where product is available
     *
     * @return array
     */
    public function getAvailableInCategories()
    {
        return $this->_getResource()->getAvailableInCategories($this);
    }

    /**
     * Retrieve default attribute set id
     *
     * @return int
     */
    public function getDefaultAttributeSetId()
    {
        return $this->getResource()->getEntityType()->getDefaultAttributeSetId();
    }

    /**
     * Return Catalog Product Image helper instance
     *
     * @return Mage_Catalog_Helper_Image
     */
    protected function _getImageHelper()
    {
        return Mage::helper('catalog/image');
    }

    /**
     * Return re-sized image URL
     *
     * @deprecated since 1.1.5
     * @return string
     */
    public function getImageUrl()
    {
        return (string)$this->_getImageHelper()->init($this, 'image')->resize(265);
    }

    /**
     * Return re-sized small image URL
     *
     * @deprecated since 1.1.5
     * @param int $width
     * @param int $height
     * @return string
     */
    public function getSmallImageUrl($width = 88, $height = 77)
    {
        return (string)$this->_getImageHelper()->init($this, 'small_image')->resize($width, $height);
    }

    /**
     * Return re-sized thumbnail image URL
     *
     * @deprecated since 1.1.5
     * @param int $width
     * @param int $height
     * @return string
     */
    public function getThumbnailUrl($width = 75, $height = 75)
    {
        return (string)$this->_getImageHelper()->init($this, 'thumbnail')->resize($width, $height);
    }

    /**
     *  Returns system reserved attribute codes
     *
     *  @return array Reserved attribute names
     */
    public function getReservedAttributes()
    {
        if ($this->_reservedAttributes === null) {
            $_reserved = array('position');
            $methods = get_class_methods(__CLASS__);
            foreach ($methods as $method) {
                if (preg_match('/^get([A-Z]{1}.+)/', $method, $matches)) {
                    $method = $matches[1];
                    $tmp = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $method));
                    $_reserved[] = $tmp;
                }
            }
            $_allowed = array(
                'type_id','calculated_final_price','request_path','rating_summary'
            );
            $this->_reservedAttributes = array_diff($_reserved, $_allowed);
        }
        return $this->_reservedAttributes;
    }

    /**
     *  Check whether attribute reserved or not
     *
     *  @param Mage_Catalog_Model_Entity_Attribute $attribute Attribute model object
     *  @return boolean
     */
    public function isReservedAttribute ($attribute)
    {
        return $attribute->getIsUserDefined()
            && in_array($attribute->getAttributeCode(), $this->getReservedAttributes());
    }

    /**
     * Set original loaded data if needed
     *
     * @param string $key
     * @param mixed $data
     * @return Varien_Object
     */
    public function setOrigData($key=null, $data=null)
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return parent::setOrigData($key, $data);
        }

        return $this;
    }

    /**
     * Reset all model data
     *
     * @return Mage_Catalog_Model_Product
     */
    public function reset()
    {
        $this->unlockAttributes();
        $this->_clearData();
        return $this;
    }

    /**
     * Get cahce tags associated with object id
     *
     * @return array
     */
    public function getCacheIdTags()
    {
        $tags = parent::getCacheIdTags();
        $affectedCategoryIds = $this->getAffectedCategoryIds();
        if (!$affectedCategoryIds) {
            $affectedCategoryIds = $this->getCategoryIds();
        }
        foreach ($affectedCategoryIds as $categoryId) {
            $tags[] = Mage_Catalog_Model_Category::CACHE_TAG.'_'.$categoryId;
        }
        return $tags;
    }

    /**
     * Check for empty SKU on each product
     *
     * @param  array $productIds
     * @return boolean|null
     */
    public function isProductsHasSku(array $productIds)
    {
        $products = $this->_getResource()->getProductsSku($productIds);
        if (count($products)) {
            foreach ($products as $product) {
                if (!strlen($product['sku'])) {
                    return false;
                }
            }
            return true;
        }
        return null;
    }

    /**
     * Parse buyRequest into options values used by product
     *
     * @param  Varien_Object $buyRequest
     * @return Varien_Object
     */
    public function processBuyRequest(Varien_Object $buyRequest)
    {
        $options = new Varien_Object();

        /* add product custom options data */
        $customOptions = $buyRequest->getOptions();
        if (is_array($customOptions)) {
            foreach ($customOptions as $key => $value) {
                if ($value === '') {
                    unset($customOptions[$key]);
                }
            }
            $options->setOptions($customOptions);
        }

        /* add product type selected options data */
        $type = $this->getTypeInstance(true);
        $typeSpecificOptions = $type->processBuyRequest($this, $buyRequest);
        $options->addData($typeSpecificOptions);

        /* check correctness of product's options */
        $options->setErrors($type->checkProductConfiguration($this, $buyRequest));

        return $options;
    }

    /**
     * Get preconfigured values from product
     *
     * @return Varien_Object
     */
    public function getPreconfiguredValues()
    {
        $preconfiguredValues = $this->getData('preconfigured_values');
        if (!$preconfiguredValues) {
            $preconfiguredValues = new Varien_Object();
        }

        return $preconfiguredValues;
    }

    /**
     * Prepare product custom options.
     * To be sure that all product custom options does not has ID and has product instance
     *
     * @return Mage_Catalog_Model_Product
     */
    public function prepareCustomOptions()
    {
        foreach ($this->getCustomOptions() as $option) {
            if (!is_object($option->getProduct()) || $option->getId()) {
                $this->addCustomOption($option->getCode(), $option->getValue());
            }
        }

        return $this;
    }

    /**
     * Clearing references on product
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _clearReferences()
    {
        $this->_clearOptionReferences();
        return $this;
    }

    /**
     * Clearing product's data
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _clearData()
    {
        foreach ($this->_data as $data){
            if (is_object($data) && method_exists($data, 'reset')){
                $data->reset();
            }
        }

        $this->setData(array());
        $this->setOrigData();
        $this->_customOptions       = array();
        $this->_optionInstance      = null;
        $this->_options             = array();
        $this->_canAffectOptions    = false;
        $this->_errors              = array();

        return $this;
    }

    /**
     * Clearing references to product from product's options
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _clearOptionReferences()
    {
        /**
         * unload product options
         */
        if (!empty($this->_options)) {
            foreach ($this->_options as $key => $option) {
                $option->setProduct();
                $option->clearInstance();
            }
        }

        return $this;
    }

    /**
     * Retrieve product entities info as array
     *
     * @param string|array $columns One or several columns
     * @return array
     */
    public function getProductEntitiesInfo($columns = null)
    {
        return $this->_getResource()->getProductEntitiesInfo($columns);
    }

    /**
     * Checks whether product has disabled status
     *
     * @return bool
     */
    public function isDisabled()
    {
        return $this->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_DISABLED;
    }

    /**
     * Callback function which called after transaction commit in resource model
     *
     * @return Mage_Catalog_Model_Product
     */
    public function afterCommitCallback()
    {
        parent::afterCommitCallback();

        /** @var \Mage_Index_Model_Indexer $indexer */
        $indexer = Mage::getSingleton('index/indexer');
        $indexer->processEntityAction($this, self::ENTITY, Mage_Index_Model_Event::TYPE_SAVE);

        return $this;
    }
}
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
 * Product status functionality model
 *
 * @method Mage_Catalog_Model_Resource_Product_Status _getResource()
 * @method Mage_Catalog_Model_Resource_Product_Status getResource()
 * @method int getProductId()
 * @method Mage_Catalog_Model_Product_Status setProductId(int $value)
 * @method int getStoreId()
 * @method Mage_Catalog_Model_Product_Status setStoreId(int $value)
 * @method int getVisibility()
 * @method Mage_Catalog_Model_Product_Status setVisibility(int $value)
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Status extends Mage_Core_Model_Abstract
{
    const STATUS_ENABLED    = 1;
    const STATUS_DISABLED   = 2;

    /**
     * Reference to the attribute instance
     *
     * @var Mage_Catalog_Model_Resource_Eav_Attribute
     */
    protected $_attribute;

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/product_status');
    }

    /**
     * Retrieve resource model wrapper
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Status
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Retrieve Product Attribute by code
     *
     * @param string $attributeCode
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getProductAttribute($attributeCode)
    {
        return $this->_getResource()->getProductAttribute($attributeCode);
    }

    /**
     * Add visible filter to Product Collection
     *
     * @deprecated remove on new builds
     * @param Mage_Eav_Model_Entity_Collection_Abstract $collection
     * @return Mage_Catalog_Model_Product_Status
     */
    public function addVisibleFilterToCollection(Mage_Eav_Model_Entity_Collection_Abstract $collection)
    {
        //$collection->addAttributeToFilter('status', array('in'=>$this->getVisibleStatusIds()));
        return $this;
    }

    /**
     * Add saleable filter to Product Collection
     *
     * @deprecated remove on new builds
     * @param Mage_Eav_Model_Entity_Collection_Abstract $collection
     * @return Mage_Catalog_Model_Product_Status
     */
    public function addSaleableFilterToCollection(Mage_Eav_Model_Entity_Collection_Abstract $collection)
    {
        //$collection->addAttributeToFilter('status', array('in'=>$this->getSaleableStatusIds()));
        return $this;
    }

    /**
     * Retrieve Visible Status Ids
     *
     * @return array
     */
    public function getVisibleStatusIds()
    {
        return array(self::STATUS_ENABLED);
    }

    /**
     * Retrieve Saleable Status Ids
     * Default Product Enable status
     *
     * @return array
     */
    public function getSaleableStatusIds()
    {
        return array(self::STATUS_ENABLED);
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('catalog')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('catalog')->__('Disabled')
        );
    }

    /**
     * Retrieve option array with empty value
     *
     * @return array
     */
    static public function getAllOption()
    {
        $options = self::getOptionArray();
        array_unshift($options, array('value'=>'', 'label'=>''));
        return $options;
    }

    /**
     * Retrieve option array with empty value
     *
     * @return array
     */
    static public function getAllOptions()
    {
        $res = array(
            array(
                'value' => '',
                'label' => Mage::helper('catalog')->__('-- Please Select --')
            )
        );
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = array(
               'value' => $index,
               'label' => $value
            );
        }
        return $res;
    }

    /**
     * Retrieve option text by option value
     *
     * @param string $optionId
     * @return string
     */
    static public function getOptionText($optionId)
    {
        $options = self::getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }

    /**
     * Update status value for product
     *
     * @param   int $productId
     * @param   int $storeId
     * @param   int $value
     * @return  Mage_Catalog_Model_Product_Status
     */
    public function updateProductStatus($productId, $storeId, $value)
    {
        Mage::getSingleton('catalog/product_action')
            ->updateAttributes(array($productId), array('status' => $value), $storeId);

        // add back compatibility event
        $status = $this->_getResource()->getProductAttribute('status');
        if ($status->isScopeWebsite()) {
            $website = Mage::app()->getStore($storeId)->getWebsite();
            $stores  = $website->getStoreIds();
        } else if ($status->isScopeStore()) {
            $stores = array($storeId);
        } else {
            $stores = array_keys(Mage::app()->getStores());
        }

        foreach ($stores as $storeId) {
            Mage::dispatchEvent('catalog_product_status_update', array(
                'product_id'    => $productId,
                'store_id'      => $storeId,
                'status'        => $value
            ));
        }

        return $this;
    }

    /**
     * Retrieve Product(s) status for store
     * Return array where key is product, value - status
     *
     * @param int|array $productIds
     * @param int $storeId
     * @return array
     */
    public function getProductStatus($productIds, $storeId = null)
    {
        return $this->getResource()->getProductStatus($productIds, $storeId);
    }

    /**
     * ---------------- Eav Source methods for Flat data -----------------------
     */

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColums()
    {
        return array();
    }

    /**
     * Retrieve Indexes for Flat
     *
     * @return array
     */
    public function getFlatIndexes()
    {
        return array();
    }

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param int $store
     * @return Varien_Db_Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return null;
    }

    /**
     * Set attribute instance
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
     */
    public function setAttribute($attribute)
    {
        $this->_attribute = $attribute;
        return $this;
    }

    /**
     * Get attribute instance
     *
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    public function getAttribute()
    {
        return $this->_attribute;
    }

    /**
     * Add Value Sort To Collection Select
     *
     * @param Mage_Eav_Model_Entity_Collection_Abstract $collection
     * @param string $dir direction
     * @return Mage_Eav_Model_Entity_Attribute_Source_Abstract
     */
    public function addValueSortToCollection($collection, $dir = 'asc')
    {
        $attributeCode  = $this->getAttribute()->getAttributeCode();
        $attributeId    = $this->getAttribute()->getId();
        $attributeTable = $this->getAttribute()->getBackend()->getTable();

        if ($this->getAttribute()->isScopeGlobal()) {
            $tableName = $attributeCode . '_t';
            $collection->getSelect()
                ->joinLeft(
                    array($tableName => $attributeTable),
                    "e.entity_id={$tableName}.entity_id"
                        . " AND {$tableName}.attribute_id='{$attributeId}'"
                        . " AND {$tableName}.store_id='0'",
                    array());
            $valueExpr = $tableName . '.value';
        }
        else {
            $valueTable1 = $attributeCode . '_t1';
            $valueTable2 = $attributeCode . '_t2';
            $collection->getSelect()
                ->joinLeft(
                    array($valueTable1 => $attributeTable),
                    "e.entity_id={$valueTable1}.entity_id"
                        . " AND {$valueTable1}.attribute_id='{$attributeId}'"
                        . " AND {$valueTable1}.store_id='0'",
                    array())
                ->joinLeft(
                    array($valueTable2 => $attributeTable),
                    "e.entity_id={$valueTable2}.entity_id"
                        . " AND {$valueTable2}.attribute_id='{$attributeId}'"
                        . " AND {$valueTable2}.store_id='{$collection->getStoreId()}'",
                    array()
                );

                $valueExpr = $collection->getConnection()->getCheckSql(
                    $valueTable2 . '.value_id > 0',
                    $valueTable2 . '.value',
                    $valueTable1 . '.value'
                );
        }

        $collection->getSelect()->order($valueExpr . ' ' . $dir);
        return $this;
    }
}
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
 * Catalog Product visibilite model and attribute source model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Visibility extends Varien_Object
{
    const VISIBILITY_NOT_VISIBLE    = 1;
    const VISIBILITY_IN_CATALOG     = 2;
    const VISIBILITY_IN_SEARCH      = 3;
    const VISIBILITY_BOTH           = 4;

    /**
     * Reference to the attribute instance
     *
     * @var Mage_Catalog_Model_Resource_Eav_Attribute
     */
    protected $_attribute;

    /**
     * Initialize object
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setIdFieldName('visibility_id');
    }

    /**
     * Add visible in catalog filter to collection
     *
     * @deprecated
     * @param Mage_Eav_Model_Entity_Collection_Abstract $collection
     * @return Mage_Catalog_Model_Product_Visibility
     */
    public function addVisibleInCatalogFilterToCollection(Mage_Eav_Model_Entity_Collection_Abstract $collection)
    {
        $collection->setVisibility($this->getVisibleInCatalogIds());
//        $collection->addAttributeToFilter('visibility', array('in'=>$this->getVisibleInCatalogIds()));
        return $this;
    }

    /**
     * Add visibility in searchfilter to collection
     *
     * @deprecated
     * @param Mage_Eav_Model_Entity_Collection_Abstract $collection
     * @return Mage_Catalog_Model_Product_Visibility
     */
    public function addVisibleInSearchFilterToCollection(Mage_Eav_Model_Entity_Collection_Abstract $collection)
    {
        $collection->setVisibility($this->getVisibleInSearchIds());
        //$collection->addAttributeToFilter('visibility', array('in'=>$this->getVisibleInSearchIds()));
        return $this;
    }

    /**
     * Add visibility in site filter to collection
     *
     * @deprecated
     * @param Mage_Eav_Model_Entity_Collection_Abstract $collection
     * @return Mage_Catalog_Model_Product_Visibility
     */
    public function addVisibleInSiteFilterToCollection(Mage_Eav_Model_Entity_Collection_Abstract $collection)
    {
        $collection->setVisibility($this->getVisibleInSiteIds());
        //$collection->addAttributeToFilter('visibility', array('in'=>$this->getVisibleInSiteIds()));
        return $this;
    }

    /**
     * Retrieve visible in catalog ids array
     *
     * @return array
     */
    public function getVisibleInCatalogIds()
    {
        return array(self::VISIBILITY_IN_CATALOG, self::VISIBILITY_BOTH);
    }

    /**
     * Retrieve visible in search ids array
     *
     * @return array
     */
    public function getVisibleInSearchIds()
    {
        return array(self::VISIBILITY_IN_SEARCH, self::VISIBILITY_BOTH);
    }

    /**
     * Retrieve visible in site ids array
     *
     * @return array
     */
    public function getVisibleInSiteIds()
    {
        return array(self::VISIBILITY_IN_SEARCH, self::VISIBILITY_IN_CATALOG, self::VISIBILITY_BOTH);
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        return array(
            self::VISIBILITY_NOT_VISIBLE=> Mage::helper('catalog')->__('Not Visible Individually'),
            self::VISIBILITY_IN_CATALOG => Mage::helper('catalog')->__('Catalog'),
            self::VISIBILITY_IN_SEARCH  => Mage::helper('catalog')->__('Search'),
            self::VISIBILITY_BOTH       => Mage::helper('catalog')->__('Catalog, Search')
        );
    }

    /**
     * Retrieve all options
     *
     * @return array
     */
    static public function getAllOption()
    {
        $options = self::getOptionArray();
        array_unshift($options, array('value'=>'', 'label'=>''));
        return $options;
    }

    /**
     * Retireve all options
     *
     * @return array
     */
    static public function getAllOptions()
    {
        $res = array();
        $res[] = array('value'=>'', 'label'=> Mage::helper('catalog')->__('-- Please Select --'));
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = array(
               'value' => $index,
               'label' => $value
            );
        }
        return $res;
    }

    /**
     * Retrieve option text
     *
     * @param int $optionId
     * @return string
     */
    static public function getOptionText($optionId)
    {
        $options = self::getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColums()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $column = array(
            'unsigned'  => true,
            'default'   => null,
            'extra'     => null
        );

        if (Mage::helper('core')->useDbCompatibleMode()) {
            $column['type']     = 'tinyint';
            $column['is_null']  = true;
        } else {
            $column['type']     = Varien_Db_Ddl_Table::TYPE_SMALLINT;
            $column['nullable'] = true;
            $column['comment']  = 'Catalog Product Visibility ' . $attributeCode . ' column';
        }

        return array($attributeCode => $column);
    }

    /**
     * Retrieve Indexes for Flat
     *
     * @return array
     */
    public function getFlatIndexes()
    {
        return array();
    }

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param int $store
     * @return Varien_Db_Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceSingleton('eav/entity_attribute')
            ->getFlatUpdateSelect($this->getAttribute(), $store);
    }

    /**
     * Set attribute instance
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
     */
    public function setAttribute($attribute)
    {
        $this->_attribute = $attribute;
        return $this;
    }

    /**
     * Get attribute instance
     *
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    public function getAttribute()
    {
        return $this->_attribute;
    }

    /**
     * Add Value Sort To Collection Select
     *
     * @param Mage_Eav_Model_Entity_Collection_Abstract $collection
     * @param string $dir direction
     * @return Mage_Eav_Model_Entity_Attribute_Source_Abstract
     */
    public function addValueSortToCollection($collection, $dir = 'asc')
    {
        $attributeCode  = $this->getAttribute()->getAttributeCode();
        $attributeId    = $this->getAttribute()->getId();
        $attributeTable = $this->getAttribute()->getBackend()->getTable();

        if ($this->getAttribute()->isScopeGlobal()) {
            $tableName = $attributeCode . '_t';
            $collection->getSelect()
                ->joinLeft(
                    array($tableName => $attributeTable),
                    "e.entity_id={$tableName}.entity_id"
                        . " AND {$tableName}.attribute_id='{$attributeId}'"
                        . " AND {$tableName}.store_id='0'",
                    array());
            $valueExpr = $tableName . '.value';
        }
        else {
            $valueTable1 = $attributeCode . '_t1';
            $valueTable2 = $attributeCode . '_t2';
            $collection->getSelect()
                ->joinLeft(
                    array($valueTable1 => $attributeTable),
                    "e.entity_id={$valueTable1}.entity_id"
                        . " AND {$valueTable1}.attribute_id='{$attributeId}'"
                        . " AND {$valueTable1}.store_id='0'",
                    array())
                ->joinLeft(
                    array($valueTable2 => $attributeTable),
                    "e.entity_id={$valueTable2}.entity_id"
                        . " AND {$valueTable2}.attribute_id='{$attributeId}'"
                        . " AND {$valueTable2}.store_id='{$collection->getStoreId()}'",
                    array()
                );
                $valueExpr = $collection->getConnection()->getCheckSql(
                    $valueTable2 . '.value_id > 0',
                    $valueTable2 . '.value',
                    $valueTable1 . '.value'
                );
        }

        $collection->getSelect()->order($valueExpr . ' ' . $dir);
        return $this;
    }
}
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
 * Catalog entity abstract model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Catalog_Model_Resource_Abstract extends Mage_Eav_Model_Entity_Abstract
{
    /**
     * Store firstly set attributes to filter selected attributes when used specific store_id
     *
     * @var array
     */
    protected $_attributes   = array();

    /**
     * Redeclare attribute model
     *
     * @return string
     */
    protected function _getDefaultAttributeModel()
    {
        return 'catalog/resource_eav_attribute';
    }

    /**
     * Returns default Store ID
     *
     * @return int
     */
    public function getDefaultStoreId()
    {
        return Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
    }

    /**
     * Check whether the attribute is Applicable to the object
     *
     * @param Varien_Object $object
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return boolean
     */
    protected function _isApplicableAttribute($object, $attribute)
    {
        $applyTo = $attribute->getApplyTo();
        return count($applyTo) == 0 || in_array($object->getTypeId(), $applyTo);
    }

    /**
     * Check whether attribute instance (attribute, backend, frontend or source) has method and applicable
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract|Mage_Eav_Model_Entity_Attribute_Backend_Abstract|Mage_Eav_Model_Entity_Attribute_Frontend_Abstract|Mage_Eav_Model_Entity_Attribute_Source_Abstract $instance
     * @param string $method
     * @param array $args array of arguments
     * @return boolean
     */
    protected function _isCallableAttributeInstance($instance, $method, $args)
    {
        if ($instance instanceof Mage_Eav_Model_Entity_Attribute_Backend_Abstract
            && ($method == 'beforeSave' || $method = 'afterSave')
        ) {
            $attributeCode = $instance->getAttribute()->getAttributeCode();
            if (isset($args[0]) && $args[0] instanceof Varien_Object && $args[0]->getData($attributeCode) === false) {
                return false;
            }
        }

        return parent::_isCallableAttributeInstance($instance, $method, $args);
    }



    /**
     * Retrieve select object for loading entity attributes values
     * Join attribute store value
     *
     * @param Varien_Object $object
     * @param string $table
     * @return Varien_Db_Select
     */
    protected function _getLoadAttributesSelect($object, $table)
    {
        /**
         * This condition is applicable for all cases when we was work in not single
         * store mode, customize some value per specific store view and than back
         * to single store mode. We should load correct values
         */
        if (Mage::app()->isSingleStoreMode()) {
            $storeId = (int)Mage::app()->getStore(true)->getId();
        } else {
            $storeId = (int)$object->getStoreId();
        }

        $setId  = $object->getAttributeSetId();
        $storeIds = array($this->getDefaultStoreId());
        if ($storeId != $this->getDefaultStoreId()) {
            $storeIds[] = $storeId;
        }

        $select = $this->_getReadAdapter()->select()
            ->from(array('attr_table' => $table), array())
            ->where("attr_table.{$this->getEntityIdField()} = ?", $object->getId())
            ->where('attr_table.store_id IN (?)', $storeIds);
        if ($setId) {
            $select->join(
                array('set_table' => $this->getTable('eav/entity_attribute')),
                $this->_getReadAdapter()->quoteInto('attr_table.attribute_id = set_table.attribute_id' .
                ' AND set_table.attribute_set_id = ?', $setId),
                array()
            );
        }
        return $select;
    }

    /**
     * Adds Columns prepared for union
     *
     * @param Varien_Db_Select $select
     * @param string $table
     * @param string $type
     * @return Varien_Db_Select
     */
    protected function _addLoadAttributesSelectFields($select, $table, $type)
    {
        $select->columns(
            Mage::getResourceHelper('catalog')->attributeSelectFields('attr_table', $type)
        );
        return $select;
    }

    /**
     * Prepare select object for loading entity attributes values
     *
     * @param array $selects
     * @return Varien_Db_Select
     */
    protected function _prepareLoadSelect(array $selects)
    {
        $select = parent::_prepareLoadSelect($selects);
        $select->order('store_id');
        return $select;
    }

    /**
     * Initialize attribute value for object
     *
     * @param Mage_Catalog_Model_Abstract $object
     * @param array $valueRow
     * @return Mage_Catalog_Model_Resource_Abstract
     */
    protected function _setAttributeValue($object, $valueRow)
    {
        $attribute = $this->getAttribute($valueRow['attribute_id']);
        if ($attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $isDefaultStore = $valueRow['store_id'] == $this->getDefaultStoreId();
            if (isset($this->_attributes[$valueRow['attribute_id']])) {
                if ($isDefaultStore) {
                    $object->setAttributeDefaultValue($attributeCode, $valueRow['value']);
                } else {
                    $object->setAttributeDefaultValue(
                        $attributeCode,
                        $this->_attributes[$valueRow['attribute_id']]['value']
                    );
                }
            } else {
                $this->_attributes[$valueRow['attribute_id']] = $valueRow;
            }

            $value   = $valueRow['value'];
            $valueId = $valueRow['value_id'];

            $object->setData($attributeCode, $value);
            if (!$isDefaultStore) {
                $object->setExistsStoreValueFlag($attributeCode);
            }
            $attribute->getBackend()->setEntityValueId($object, $valueId);
        }

        return $this;
    }

    /**
     * Insert or Update attribute data
     *
     * @param Mage_Catalog_Model_Abstract $object
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param mixed $value
     * @return Mage_Catalog_Model_Resource_Abstract
     */
    protected function _saveAttributeValue($object, $attribute, $value)
    {
        $write   = $this->_getWriteAdapter();
        $storeId = (int)Mage::app()->getStore($object->getStoreId())->getId();
        $table   = $attribute->getBackend()->getTable();

        /**
         * If we work in single store mode all values should be saved just
         * for default store id
         * In this case we clear all not default values
         */
        if (Mage::app()->isSingleStoreMode()) {
            $storeId = $this->getDefaultStoreId();
            $write->delete($table, array(
                'attribute_id = ?' => $attribute->getAttributeId(),
                'entity_id = ?'    => $object->getEntityId(),
                'store_id <> ?'    => $storeId
            ));
        }

        $data = new Varien_Object(array(
            'entity_type_id'    => $attribute->getEntityTypeId(),
            'attribute_id'      => $attribute->getAttributeId(),
            'store_id'          => $storeId,
            'entity_id'         => $object->getEntityId(),
            'value'             => $this->_prepareValueForSave($value, $attribute)
        ));
        $bind = $this->_prepareDataForTable($data, $table);

        if ($attribute->isScopeStore()) {
            /**
             * Update attribute value for store
             */
            $this->_attributeValuesToSave[$table][] = $bind;
        } else if ($attribute->isScopeWebsite() && $storeId != $this->getDefaultStoreId()) {
            /**
             * Update attribute value for website
             */
            $storeIds = Mage::app()->getStore($storeId)->getWebsite()->getStoreIds(true);
            foreach ($storeIds as $storeId) {
                $bind['store_id'] = (int)$storeId;
                $this->_attributeValuesToSave[$table][] = $bind;
            }
        } else {
            /**
             * Update global attribute value
             */
            $bind['store_id'] = $this->getDefaultStoreId();
            $this->_attributeValuesToSave[$table][] = $bind;
        }

        return $this;
    }

    /**
     * Insert entity attribute value
     *
     * @param Varien_Object $object
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param mixed $value
     * @return Mage_Catalog_Model_Resource_Abstract
     */
    protected function _insertAttribute($object, $attribute, $value)
    {
        /**
         * save required attributes in global scope every time if store id different from default
         */
        $storeId = (int)Mage::app()->getStore($object->getStoreId())->getId();
        if ($attribute->getIsRequired() && $this->getDefaultStoreId() != $storeId) {
            $table = $attribute->getBackend()->getTable();

            $select = $this->_getReadAdapter()->select()
                ->from($table)
                ->where('entity_type_id = ?', $attribute->getEntityTypeId())
                ->where('attribute_id = ?', $attribute->getAttributeId())
                ->where('store_id = ?', $this->getDefaultStoreId())
                ->where('entity_id = ?',  $object->getEntityId());
            $row = $this->_getReadAdapter()->fetchOne($select);

            if (!$row) {
                $data  = new Varien_Object(array(
                    'entity_type_id'    => $attribute->getEntityTypeId(),
                    'attribute_id'      => $attribute->getAttributeId(),
                    'store_id'          => $this->getDefaultStoreId(),
                    'entity_id'         => $object->getEntityId(),
                    'value'             => $this->_prepareValueForSave($value, $attribute)
                ));
                $bind  = $this->_prepareDataForTable($data, $table);
                $this->_getWriteAdapter()->insertOnDuplicate($table, $bind, array('value'));
            }
        }

        return $this->_saveAttributeValue($object, $attribute, $value);
    }

    /**
     * Update entity attribute value
     *
     * @deprecated after 1.5.1.0
     * @see Mage_Catalog_Model_Resource_Abstract::_saveAttributeValue()
     *
     * @param Varien_Object $object
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param mixed $valueId
     * @param mixed $value
     * @return Mage_Catalog_Model_Resource_Abstract
     */
    protected function _updateAttribute($object, $attribute, $valueId, $value)
    {
        return $this->_saveAttributeValue($object, $attribute, $value);
    }

    /**
     * Update attribute value for specific store
     *
     * @param Mage_Catalog_Model_Abstract $object
     * @param object $attribute
     * @param mixed $value
     * @param int $storeId
     * @return Mage_Catalog_Model_Resource_Abstract
     */
    protected function _updateAttributeForStore($object, $attribute, $value, $storeId)
    {
        $adapter = $this->_getWriteAdapter();
        $table   = $attribute->getBackend()->getTable();
        $entityIdField = $attribute->getBackend()->getEntityIdField();
        $select  = $adapter->select()
            ->from($table, 'value_id')
            ->where('entity_type_id = :entity_type_id')
            ->where("$entityIdField = :entity_field_id")
            ->where('store_id = :store_id')
            ->where('attribute_id = :attribute_id');
        $bind = array(
            'entity_type_id'  => $object->getEntityTypeId(),
            'entity_field_id' => $object->getId(),
            'store_id'        => $storeId,
            'attribute_id'    => $attribute->getId()
        );
        $valueId = $adapter->fetchOne($select, $bind);
        /**
         * When value for store exist
         */
        if ($valueId) {
            $bind  = array('value' => $this->_prepareValueForSave($value, $attribute));
            $where = array('value_id = ?' => (int)$valueId);

            $adapter->update($table, $bind, $where);
        } else {
            $bind  = array(
                $idField            => (int)$object->getId(),
                'entity_type_id'    => (int)$object->getEntityTypeId(),
                'attribute_id'      => (int)$attribute->getId(),
                'value'             => $this->_prepareValueForSave($value, $attribute),
                'store_id'          => (int)$storeId
            );

            $adapter->insert($table, $bind);
        }

        return $this;
    }

    /**
     * Delete entity attribute values
     *
     * @param Varien_Object $object
     * @param string $table
     * @param array $info
     * @return Mage_Catalog_Model_Resource_Abstract
     */
    protected function _deleteAttributes($object, $table, $info)
    {
        $adapter            = $this->_getWriteAdapter();
        $entityIdField      = $this->getEntityIdField();
        $globalValues       = array();
        $websiteAttributes  = array();
        $storeAttributes    = array();

        /**
         * Separate attributes by scope
         */
        foreach ($info as $itemData) {
            $attribute = $this->getAttribute($itemData['attribute_id']);
            if ($attribute->isScopeStore()) {
                $storeAttributes[] = (int)$itemData['attribute_id'];
            } elseif ($attribute->isScopeWebsite()) {
                $websiteAttributes[] = (int)$itemData['attribute_id'];
            } else {
                $globalValues[] = (int)$itemData['value_id'];
            }
        }

        /**
         * Delete global scope attributes
         */
        if (!empty($globalValues)) {
            $adapter->delete($table, array('value_id IN (?)' => $globalValues));
        }

        $condition = array(
            $entityIdField . ' = ?' => $object->getId(),
            'entity_type_id = ?'  => $object->getEntityTypeId()
        );

        /**
         * Delete website scope attributes
         */
        if (!empty($websiteAttributes)) {
            $storeIds = $object->getWebsiteStoreIds();
            if (!empty($storeIds)) {
                $delCondition = $condition;
                $delCondition['attribute_id IN(?)'] = $websiteAttributes;
                $delCondition['store_id IN(?)'] = $storeIds;

                $adapter->delete($table, $delCondition);
            }
        }

        /**
         * Delete store scope attributes
         */
        if (!empty($storeAttributes)) {
            $delCondition = $condition;
            $delCondition['attribute_id IN(?)'] = $storeAttributes;
            $delCondition['store_id = ?']       = (int)$object->getStoreId();

            $adapter->delete($table, $delCondition);
        }

        return $this;
    }

    /**
     * Retrieve Object instance with original data
     *
     * @param Varien_Object $object
     * @return Varien_Object
     */
    protected function _getOrigObject($object)
    {
        $className  = get_class($object);
        $origObject = new $className();
        $origObject->setData(array());
        $origObject->setStoreId($object->getStoreId());
        $this->load($origObject, $object->getData($this->getEntityIdField()));

        return $origObject;
    }

    /**
     * Collect original data
     *
     * @deprecated after 1.5.1.0
     *
     * @param Varien_Object $object
     * @return array
     */
    protected function _collectOrigData($object)
    {
        $this->loadAllAttributes($object);

        if ($this->getUseDataSharing()) {
            $storeId = $object->getStoreId();
        } else {
            $storeId = $this->getStoreId();
        }

        $data = array();
        foreach ($this->getAttributesByTable() as $table=>$attributes) {
            $select = $this->_getReadAdapter()->select()
                ->from($table)
                ->where($this->getEntityIdField() . '=?', $object->getId());

            $where = $this->_getReadAdapter()->quoteInto('store_id=?', $storeId);

            $globalAttributeIds = array();
            foreach ($attributes as $attr) {
                if ($attr->getIsGlobal()) {
                    $globalAttributeIds[] = $attr->getId();
                }
            }
            if (!empty($globalAttributeIds)) {
                $where .= ' or '.$this->_getReadAdapter()->quoteInto('attribute_id in (?)', $globalAttributeIds);
            }
            $select->where($where);

            $values = $this->_getReadAdapter()->fetchAll($select);

            if (empty($values)) {
                continue;
            }

            foreach ($values as $row) {
                $data[$this->getAttribute($row['attribute_id'])->getName()][$row['store_id']] = $row;
            }
        }
        return $data;
    }

    /**
     * Check is attribute value empty
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param mixed $value
     * @return bool
     */
    protected function _isAttributeValueEmpty(Mage_Eav_Model_Entity_Attribute_Abstract $attribute, $value)
    {
        return $value === false;
    }

    /**
     * Return if attribute exists in original data array.
     * Checks also attribute's store scope:
     * We should insert on duplicate key update values if we unchecked 'STORE VIEW' checkbox in store view.
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param mixed $value New value of the attribute.
     * @param array $origData
     * @return bool
     */
    protected function _canUpdateAttribute(
        Mage_Eav_Model_Entity_Attribute_Abstract $attribute,
        $value,
        array &$origData)
    {
        $result = parent::_canUpdateAttribute($attribute, $value, $origData);
        if ($result &&
            ($attribute->isScopeStore() || $attribute->isScopeWebsite()) &&
            !$this->_isAttributeValueEmpty($attribute, $value) &&
            $value == $origData[$attribute->getAttributeCode()] &&
            isset($origData['store_id']) && $origData['store_id'] != $this->getDefaultStoreId()
        ) {
            return false;
        }

        return $result;
    }

    /**
     * Prepare value for save
     *
     * @param mixed $value
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return mixed
     */
    protected function _prepareValueForSave($value, Mage_Eav_Model_Entity_Attribute_Abstract $attribute)
    {
        $type = $attribute->getBackendType();
        if (($type == 'int' || $type == 'decimal' || $type == 'datetime') && $value === '') {
            $value = null;
        }

        return parent::_prepareValueForSave($value, $attribute);
    }

    /**
     * Retrieve attribute's raw value from DB.
     *
     * @param int $entityId
     * @param int|string|array $attribute atrribute's ids or codes
     * @param int|Mage_Core_Model_Store $store
     * @return bool|string|array
     */
    public function getAttributeRawValue($entityId, $attribute, $store)
    {
        if (!$entityId || empty($attribute)) {
            return false;
        }
        if (!is_array($attribute)) {
            $attribute = array($attribute);
        }

        $attributesData     = array();
        $staticAttributes   = array();
        $typedAttributes    = array();
        $staticTable        = null;
        $adapter            = $this->_getReadAdapter();

        foreach ($attribute as $_attribute) {
            /* @var $attribute Mage_Catalog_Model_Entity_Attribute */
            $_attribute = $this->getAttribute($_attribute);
            if (!$_attribute) {
                continue;
            }
            $attributeCode = $_attribute->getAttributeCode();
            $attrTable     = $_attribute->getBackend()->getTable();
            $isStatic      = $_attribute->getBackend()->isStatic();

            if ($isStatic) {
                $staticAttributes[] = $attributeCode;
                $staticTable = $attrTable;
            } else {
                /**
                 * That structure needed to avoid farther sql joins for getting attribute's code by id
                 */
                $typedAttributes[$attrTable][$_attribute->getId()] = $attributeCode;
            }
        }

        /**
         * Collecting static attributes
         */
        if ($staticAttributes) {
            $select = $adapter->select()->from($staticTable, $staticAttributes)
                ->where($this->getEntityIdField() . ' = :entity_id');
            $attributesData = $adapter->fetchRow($select, array('entity_id' => $entityId));
        }

        /**
         * Collecting typed attributes, performing separate SQL query for each attribute type table
         */
        if ($store instanceof Mage_Core_Model_Store) {
            $store = $store->getId();
        }

        $store = (int)$store;
        if ($typedAttributes) {
            foreach ($typedAttributes as $table => $_attributes) {
                $select = $adapter->select()
                    ->from(array('default_value' => $table), array('attribute_id'))
                    ->where('default_value.attribute_id IN (?)', array_keys($_attributes))
                    ->where('default_value.entity_type_id = :entity_type_id')
                    ->where('default_value.entity_id = :entity_id')
                    ->where('default_value.store_id = ?', 0);
                $bind = array(
                    'entity_type_id' => $this->getTypeId(),
                    'entity_id'      => $entityId,
                );

                if ($store != $this->getDefaultStoreId()) {
                    $valueExpr = $adapter->getCheckSql('store_value.value IS NULL',
                        'default_value.value', 'store_value.value');
                    $joinCondition = array(
                        $adapter->quoteInto('store_value.attribute_id IN (?)', array_keys($_attributes)),
                        'store_value.entity_type_id = :entity_type_id',
                        'store_value.entity_id = :entity_id',
                        'store_value.store_id = :store_id',
                    );

                    $select->joinLeft(
                        array('store_value' => $table),
                        implode(' AND ', $joinCondition),
                        array('attr_value' => $valueExpr)
                    );

                    $bind['store_id'] = $store;

                } else {
                    $select->columns(array('attr_value' => 'value'), 'default_value');
                }

                $result = $adapter->fetchPairs($select, $bind);
                foreach ($result as $attrId => $value) {
                    $attrCode = $typedAttributes[$table][$attrId];
                    $attributesData[$attrCode] = $value;
                }
            }
        }

        if (sizeof($attributesData) == 1) {
            $_data = each($attributesData);
            $attributesData = $_data[1];
        }

        return $attributesData ? $attributesData : false;
    }

    /**
     * Reset firstly loaded attributes
     *
     * @param Varien_Object $object
     * @param integer $entityId
     * @param array|null $attributes
     * @return Mage_Catalog_Model_Resource_Abstract
     */
    public function load($object, $entityId, $attributes = array())
    {
        $this->_attributes = array();
        return parent::load($object, $entityId, $attributes);
    }
}
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
 * @package     Mage_Index
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract resource model. Can be used as base for indexer resources
 *
 * @category    Mage
 * @package     Mage_Index
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Index_Model_Resource_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    const IDX_SUFFIX= '_idx';
    const TMP_SUFFIX= '_tmp';

    /**
     * Flag that defines if need to use "_idx" index table suffix instead of "_tmp"
     *
     * @var bool
     */
    protected $_isNeedUseIdxTable = false;

    /**
     * Flag that defines if need to disable keys during data inserting
     *
     * @var bool
     */
    protected $_isDisableKeys = true;

    /**
     * Whether table changes are allowed
     *
     * @deprecated after 1.6.1.0
     * @var bool
     */
    protected $_allowTableChanges = true;

    /**
     * Reindex all
     *
     * @return Mage_Index_Model_Resource_Abstract
     */
    public function reindexAll()
    {
        $this->useIdxTable(true);
        return $this;
    }

    /**
     * Get DB adapter for index data processing
     *
     * @return Varien_Db_Adapter_Interface
     */
    protected function _getIndexAdapter()
    {
        return $this->_getWriteAdapter();
    }

    /**
     * Get index table name with additional suffix
     *
     * @param string $table
     * @return string
     */
    public function getIdxTable($table = null)
    {
        $suffix = self::TMP_SUFFIX;
        if ($this->_isNeedUseIdxTable) {
            $suffix = self::IDX_SUFFIX;
        }
        if ($table) {
            return $table . $suffix;
        }
        return $this->getMainTable() . $suffix;
    }

    /**
     * Synchronize data between index storage and original storage
     *
     * @return Mage_Index_Model_Resource_Abstract
     */
    public function syncData()
    {
        $this->beginTransaction();
        try {
            /**
             * Can't use truncate because of transaction
             */
            $this->_getWriteAdapter()->delete($this->getMainTable());
            $this->insertFromTable($this->getIdxTable(), $this->getMainTable(), false);
            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Create temporary table for index data pregeneration
     *
     * @deprecated since 1.5.0.0
     * @param bool $asOriginal
     * @return Mage_Index_Model_Resource_Abstract
     */
    public function cloneIndexTable($asOriginal = false)
    {
        return $this;
    }

    /**
     * Copy data from source table of read adapter to destination table of index adapter
     *
     * @param string $sourceTable
     * @param string $destTable
     * @param bool $readToIndex data migration direction (true - read=>index, false - index=>read)
     * @return Mage_Index_Model_Resource_Abstract
     */
    public function insertFromTable($sourceTable, $destTable, $readToIndex = true)
    {
        if ($readToIndex) {
            $sourceColumns = array_keys($this->_getWriteAdapter()->describeTable($sourceTable));
            $targetColumns = array_keys($this->_getWriteAdapter()->describeTable($destTable));
        } else {
            $sourceColumns = array_keys($this->_getIndexAdapter()->describeTable($sourceTable));
            $targetColumns = array_keys($this->_getWriteAdapter()->describeTable($destTable));
        }
        $select = $this->_getIndexAdapter()->select()->from($sourceTable, $sourceColumns);

        Mage::getResourceHelper('index')->insertData($this, $select, $destTable, $targetColumns, $readToIndex);
        return $this;
    }

    /**
     * Insert data from select statement of read adapter to
     * destination table related with index adapter
     *
     * @param Varien_Db_Select $select
     * @param string $destTable
     * @param array $columns
     * @param bool $readToIndex data migration direction (true - read=>index, false - index=>read)
     * @return Mage_Index_Model_Resource_Abstract
     */
    public function insertFromSelect($select, $destTable, array $columns, $readToIndex = true)
    {
        if ($readToIndex) {
            $from   = $this->_getWriteAdapter();
            $to     = $this->_getIndexAdapter();
        } else {
            $from   = $this->_getIndexAdapter();
            $to     = $this->_getWriteAdapter();
        }

        if ($from === $to) {
            $query = $select->insertFromSelect($destTable, $columns);
            $to->query($query);
        } else {
            $stmt = $from->query($select);
            $data = array();
            $counter = 0;
            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                $data[] = $row;
                $counter++;
                if ($counter>2000) {
                    $to->insertArray($destTable, $columns, $data);
                    $data = array();
                    $counter = 0;
                }
            }
            if (!empty($data)) {
                $to->insertArray($destTable, $columns, $data);
            }
        }

        return $this;
    }

    /**
     * Set or get what either "_idx" or "_tmp" suffixed temporary index table need to use
     *
     * @param bool $value
     * @return bool
     */
    public function useIdxTable($value = null)
    {
        if (!is_null($value)) {
            $this->_isNeedUseIdxTable = (bool)$value;
        }
        return $this->_isNeedUseIdxTable;
    }

    /**
     * Set or get flag that defines if need to disable keys during data inserting
     *
     * @param bool $value
     * @return bool
     */
    public function useDisableKeys($value = null)
    {
        if (!is_null($value)) {
            $this->_isDisableKeys = (bool)$value;
        }
        return $this->_isDisableKeys;
    }

    /**
     * Clean up temporary index table
     *
     */
    public function clearTemporaryIndexTable()
    {
        $this->_getWriteAdapter()->delete($this->getIdxTable());
    }

    /**
     * Set whether table changes are allowed
     *
     * @deprecated after 1.6.1.0
     * @param bool $value
     * @return Mage_Index_Model_Resource_Abstract
     */
    public function setAllowTableChanges($value = true)
    {
        $this->_allowTableChanges = $value;
        return $this;
    }

    /**
     * Disable Main Table keys
     *
     * @return Mage_Index_Model_Resource_Abstract
     */
    public function disableTableKeys()
    {
        if ($this->useDisableKeys()) {
            $this->_getWriteAdapter()->disableTableKeys($this->getMainTable());
        }
        return $this;
    }

    /**
     * Enable Main Table keys
     *
     * @return Mage_Index_Model_Resource_Abstract
     */
    public function enableTableKeys()
    {
        if ($this->useDisableKeys()) {
            $this->_getWriteAdapter()->enableTableKeys($this->getMainTable());
        }
        return $this;
    }
}
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
 * Category flat model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Category_Flat extends Mage_Index_Model_Resource_Abstract
{
    /**
     * Amount of categories to be processed in batch
     */
    const CATEGORY_BATCH = 500;

    /**
     * Store id
     *
     * @var int
     */
    protected $_storeId                  = null;

    /**
     * Loaded
     *
     * @var boolean
     */
    protected $_loaded                   = false;

    /**
     * Nodes
     *
     * @var array
     */
    protected $_nodes                    = array();

    /**
     * Columns
     *
     * @var array
     */
    protected $_columns                  = null;

    /**
     * Columns sql
     *
     * @var array
     */
    protected $_columnsSql               = null;

    /**
     * Attribute codes
     *
     * @var array
     */
    protected $_attributeCodes           = null;

    /**
     * Inactive categories ids
     *
     * @var array
     */
    protected $_inactiveCategoryIds      = null;

    /**
     * Store flag which defines if Catalog Category Flat Data has been initialized
     *
     * @var array
     */
    protected $_isBuilt                  = array();

    /**
     * Store flag which defines if Catalog Category Flat Data has been initialized
     *
     * @deprecated after 1.7.0.0 use $this->_isBuilt instead
     *
     * @var bool|null
     */
    protected $_isRebuilt                = null;

    /**
     * array with root category id per store
     *
     * @var array
     */
    protected $_storesRootCategories;

    /**
     * Whether table changes are allowed
     *
     * @var bool
     */
    protected $_allowTableChanges        = true;

    /**
     * Factory instance
     *
     * @var Mage_Catalog_Model_Factory
     */
    protected $_factory;

    /**
     * Initialize factory instance
     *
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        $this->_factory = !empty($args['factory']) ? $args['factory'] : Mage::getSingleton('catalog/factory');
        parent::__construct();
    }

    /**
     * Resource initializations
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/category_flat', 'entity_id');
    }

    /**
     * Set store id
     *
     * @param integer $storeId
     * @return Mage_Catalog_Model_Resource_Category_Flat
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = (int)$storeId;
        return $this;
    }

    /**
     * Return store id
     *
     * @return integer
     */
    public function getStoreId()
    {
        if (is_null($this->_storeId)) {
            return (int)Mage::app()->getStore()->getId();
        }
        return $this->_storeId;
    }

    /**
     * Get main table name
     *
     * @return string
     */
    public function getMainTable()
    {
        return $this->getMainStoreTable($this->getStoreId());
    }

    /**
     * Return name of table for given $storeId.
     *
     * @param integer $storeId
     * @return string
     */
    public function getMainStoreTable($storeId = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID)
    {
        if (is_string($storeId)) {
            $storeId = intval($storeId);
        }
        if ($this->getUseStoreTables() && $storeId) {
            $suffix = sprintf('store_%d', $storeId);
            $table = $this->getTable(array('catalog/category_flat', $suffix));
        } else {
            $table = parent::getMainTable();
        }

        return $table;
    }

    /**
     * Return true if need use for each store different table of flat categories data.
     *
     * @return boolean
     */
    public function getUseStoreTables()
    {
        return true;
    }

    /**
     * Add inactive categories ids
     *
     * @param array $ids
     * @return Mage_Catalog_Model_Resource_Category_Flat
     */
    public function addInactiveCategoryIds($ids)
    {
        if (!is_array($this->_inactiveCategoryIds)) {
            $this->_initInactiveCategoryIds();
        }
        $this->_inactiveCategoryIds = array_merge($ids, $this->_inactiveCategoryIds);
        return $this;
    }

    /**
     * Retreive inactive categories ids
     *
     * @return Mage_Catalog_Model_Resource_Category_Flat
     */
    protected function _initInactiveCategoryIds()
    {
        $this->_inactiveCategoryIds = array();
        Mage::dispatchEvent('catalog_category_tree_init_inactive_category_ids', array('tree' => $this));
        return $this;
    }

    /**
     * Retreive inactive categories ids
     *
     * @return array
     */
    public function getInactiveCategoryIds()
    {
        if (!is_array($this->_inactiveCategoryIds)) {
            $this->_initInactiveCategoryIds();
        }

        return $this->_inactiveCategoryIds;
    }

    /**
     * Load nodes by parent id
     *
     * @param Mage_Catalog_Model_Category|int $parentNode
     * @param integer $recursionLevel
     * @param integer $storeId
     * @param bool $onlyActive
     * @return Mage_Catalog_Model_Resource_Category_Flat
     */
    protected function _loadNodes($parentNode = null, $recursionLevel = 0, $storeId = 0, $onlyActive = true)
    {
        $_conn = $this->_getReadAdapter();
        $startLevel = 1;
        $parentPath = '';
        if ($parentNode instanceof Mage_Catalog_Model_Category) {
            $parentPath = $parentNode->getPath();
            $startLevel = $parentNode->getLevel();
        } elseif (is_numeric($parentNode)) {
            $selectParent = $_conn->select()
                ->from($this->getMainStoreTable($storeId))
                ->where('entity_id = ?', $parentNode)
                ->where('store_id = ?', $storeId);
            $parentNode = $_conn->fetchRow($selectParent);
            if ($parentNode) {
                $parentPath = $parentNode['path'];
                $startLevel = $parentNode['level'];
            }
        }
        $select = $_conn->select()
            ->from(
                array('main_table' => $this->getMainStoreTable($storeId)),
                array('entity_id',
                    new Zend_Db_Expr('main_table.' . $_conn->quoteIdentifier('name')),
                    new Zend_Db_Expr('main_table.' . $_conn->quoteIdentifier('path')),
                    'is_active',
                    'is_anchor'))

            ->where('main_table.include_in_menu = ?', '1')
            ->order('main_table.position');

        if ($onlyActive) {
            $select->where('main_table.is_active = ?', '1');
        }

        /** @var $urlRewrite Mage_Catalog_Helper_Category_Url_Rewrite_Interface */
        $urlRewrite = $this->_factory->getCategoryUrlRewriteHelper();
        $urlRewrite->joinTableToSelect($select, $storeId);

        if ($parentPath) {
            $select->where($_conn->quoteInto("main_table.path like ?", "$parentPath/%"));
        }
        if ($recursionLevel != 0) {
            $levelField = $_conn->quoteIdentifier('level');
            $select->where($levelField . ' <= ?', $startLevel + $recursionLevel);
        }

        $inactiveCategories = $this->getInactiveCategoryIds();

        if (!empty($inactiveCategories)) {
            $select->where('main_table.entity_id NOT IN (?)', $inactiveCategories);
        }

        // Allow extensions to modify select (e.g. add custom category attributes to select)
        Mage::dispatchEvent('catalog_category_flat_loadnodes_before', array('select' => $select));

        $arrNodes = $_conn->fetchAll($select);
        $nodes = array();
        foreach ($arrNodes as $node) {
            $node['id'] = $node['entity_id'];
            $nodes[$node['id']] = Mage::getModel('catalog/category')->setData($node);
        }

        return $nodes;
    }

    /**
     * Creating sorted array of nodes
     *
     * @param array $children
     * @param string $path
     * @param Varien_Object $parent
     */
    public function addChildNodes($children, $path, $parent)
    {
        if (isset($children[$path])) {
            foreach ($children[$path] as $child) {
                $childrenNodes = $parent->getChildrenNodes();
                if ($childrenNodes && isset($childrenNodes[$child->getId()])) {
                    $childrenNodes[$child['entity_id']]->setChildrenNodes(array($child->getId()=>$child));
                } else {
                    if ($childrenNodes) {
                        $childrenNodes[$child->getId()] = $child;
                    } else {
                        $childrenNodes = array($child->getId()=>$child);
                    }
                    $parent->setChildrenNodes($childrenNodes);
                }

                if ($path) {
                    $childrenPath = explode('/', $path);
                } else {
                    $childrenPath = array();
                }
                $childrenPath[] = $child->getId();
                $childrenPath = implode('/', $childrenPath);
                $this->addChildNodes($children, $childrenPath, $child);
            }
        }
    }

    /**
     * Return sorted array of nodes
     *
     * @param integer|null $parentId
     * @param integer $recursionLevel
     * @param integer $storeId
     * @return array
     */
    public function getNodes($parentId, $recursionLevel = 0, $storeId = 0)
    {
        if (!$this->_loaded) {
            $selectParent = $this->_getReadAdapter()->select()
                ->from($this->getMainStoreTable($storeId))
                ->where('entity_id = ?', $parentId);
            if ($parentNode = $this->_getReadAdapter()->fetchRow($selectParent)) {
                $parentNode['id'] = $parentNode['entity_id'];
                $parentNode = Mage::getModel('catalog/category')->setData($parentNode);
                $this->_nodes[$parentNode->getId()] = $parentNode;
                $nodes = $this->_loadNodes($parentNode, $recursionLevel, $storeId);
                $childrenItems = array();
                foreach ($nodes as $node) {
                    $pathToParent = explode('/', $node->getPath());
                    array_pop($pathToParent);
                    $pathToParent = implode('/', $pathToParent);
                    $childrenItems[$pathToParent][] = $node;
                }
                $this->addChildNodes($childrenItems, $parentNode->getPath(), $parentNode);
                $childrenNodes = $this->_nodes[$parentNode->getId()];
                if ($childrenNodes->getChildrenNodes()) {
                    $this->_nodes = $childrenNodes->getChildrenNodes();
                }
                else {
                    $this->_nodes = array();
                }
                $this->_loaded = true;
            }
        }
        return $this->_nodes;
    }

    /**
     * Return array or collection of categories
     *
     * @param integer $parent
     * @param integer $recursionLevel
     * @param boolean|string $sorted
     * @param boolean $asCollection
     * @param boolean $toLoad
     * @return array|Varien_Data_Collection
     */
    public function getCategories($parent, $recursionLevel = 0, $sorted = false, $asCollection = false, $toLoad = true)
    {
        if ($asCollection) {
            $select = $this->_getReadAdapter()->select()
                ->from(array('mt' => $this->getMainStoreTable($this->getStoreId())), array('path'))
                ->where('mt.entity_id = ?', $parent);
            $parentPath = $this->_getReadAdapter()->fetchOne($select);

            $collection = Mage::getModel('catalog/category')->getCollection()
                ->addNameToResult()
                ->addUrlRewriteToResult()
                ->addParentPathFilter($parentPath)
                ->addStoreFilter()
                ->addIsActiveFilter()
                ->addAttributeToFilter('include_in_menu', 1)
                ->addSortedField($sorted);
            if ($toLoad) {
                return $collection->load();
            }
            return $collection;
        }
        return $this->getNodes($parent, $recursionLevel, Mage::app()->getStore()->getId());
    }

    /**
     * Return node with id $nodeId
     *
     * @param integer $nodeId
     * @param array $nodes
     * @return Varien_Object
     */
    public function getNodeById($nodeId, $nodes = null)
    {
        if (is_null($nodes)) {
            $nodes = $this->getNodes($nodeId);
        }
        if (isset($nodes[$nodeId])) {
            return $nodes[$nodeId];
        }
        foreach ($nodes as $node) {
            if ($node->getChildrenNodes()) {
                return $this->getNodeById($nodeId, $node->getChildrenNodes());
            }
        }
        return array();
    }

    /**
     * Check if Catalog Category Flat Data has been initialized
     *
     * @param bool|int|\Mage_Core_Model_Store|null $storeView Store(id) for which the value is checked
     * @return bool
     */
    public function isBuilt($storeView = null)
    {
        $storeView = is_null($storeView) ? Mage::app()->getDefaultStoreView() : Mage::app()->getStore($storeView);
        if ($storeView === null) {
            $storeId = Mage_Core_Model_App::ADMIN_STORE_ID;
        } else {
            $storeId = $storeView->getId();
        }
        if (!isset($this->_isBuilt[$storeId])) {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getMainStoreTable($storeId), 'entity_id')
                ->limit(1);
            try {
                $this->_isBuilt[$storeId] = (bool)$this->_getReadAdapter()->fetchOne($select);
            } catch (Exception $e) {
                $this->_isBuilt[$storeId] = false;
            }
        }
        return $this->_isBuilt[$storeId];
    }

    /**
     * Rebuild flat data from eav
     *
     * @param array|null $stores
     * @return Mage_Catalog_Model_Resource_Category_Flat
     */
    public function rebuild($stores = null)
    {
        if ($stores === null) {
            $stores = Mage::app()->getStores();
        }

        if (!is_array($stores)) {
            $stores = array($stores);
        }

        $rootId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
        $categories = array();
        $categoriesIds = array();
        /* @var $store Mage_Core_Model_Store */
        foreach ($stores as $store) {
            if ($this->_allowTableChanges) {
                $this->_createTable($store->getId());
            }

            if (!isset($categories[$store->getRootCategoryId()])) {
                $select = $this->_getWriteAdapter()->select()
                    ->from($this->getTable('catalog/category'))
                    ->where('path = ?', (string)$rootId)
                    ->orWhere('path = ?', "{$rootId}/{$store->getRootCategoryId()}")
                    ->orWhere('path LIKE ?', "{$rootId}/{$store->getRootCategoryId()}/%");
                $categories[$store->getRootCategoryId()] = $this->_getWriteAdapter()->fetchAll($select);
                $categoriesIds[$store->getRootCategoryId()] = array();
                foreach ($categories[$store->getRootCategoryId()] as $category) {
                    $categoriesIds[$store->getRootCategoryId()][] = $category['entity_id'];
                }
            }
            $categoriesIdsChunks = array_chunk($categoriesIds[$store->getRootCategoryId()], self::CATEGORY_BATCH);
            foreach ($categoriesIdsChunks as $categoriesIdsChunk) {
                $attributesData = $this->_getAttributeValues($categoriesIdsChunk, $store->getId());
                $data = array();
                foreach ($categories[$store->getRootCategoryId()] as $category) {
                    if (!isset($attributesData[$category['entity_id']])) {
                        continue;
                    }
                    $category['store_id'] = $store->getId();
                    $data[] = $this->_prepareValuesToInsert(
                        array_merge($category, $attributesData[$category['entity_id']])
                    );
                }
                $this->_getWriteAdapter()->insertMultiple($this->getMainStoreTable($store->getId()), $data);
            }
        }
        return $this;
    }

    /**
     * Prepare array of column and columnValue pairs
     *
     * @param array $data
     * @return array
     */
    protected function _prepareValuesToInsert($data)
    {
        $values = array();
        foreach (array_keys($this->_columns) as $key => $column) {
            if (isset($data[$column])) {
                $values[$column] = $data[$column];
            } else {
                $values[$column] = null;
            }
        }
        return $values;
    }

    /**
     * Create Flate Table(s)
     *
     * @param array|int $stores
     * @return Mage_Catalog_Model_Resource_Category_Flat
     */
    public function createTable($stores)
    {
        return $this->_createTable($stores);
    }

    /**
     * Creating table and adding attributes as fields to table
     *
     * @param array|integer $store
     * @return Mage_Catalog_Model_Resource_Category_Flat
     */
    protected function _createTable($store)
    {
        $tableName = $this->getMainStoreTable($store);
        $_writeAdapter = $this->_getWriteAdapter();
        $_writeAdapter->dropTable($tableName);
        $table = $this->_getWriteAdapter()
            ->newTable($tableName)
            ->setComment(sprintf('Catalog Category Flat (Store %d)', $store));

        //Adding columns
        if ($this->_columnsSql === null) {
            $this->_columns = array_merge($this->_getStaticColumns(), $this->_getEavColumns());
            foreach ($this->_columns as $fieldName => $fieldProp) {
                $default = $fieldProp['default'];
                if ($fieldProp['type'][0] == Varien_Db_Ddl_Table::TYPE_TIMESTAMP
                    && $default == 'CURRENT_TIMESTAMP') {
                    $default = Varien_Db_Ddl_Table::TIMESTAMP_INIT;
                }
                $table->addColumn($fieldName, $fieldProp['type'][0], $fieldProp['type'][1], array(
                    'nullable' => $fieldProp['nullable'],
                    'unsigned' => $fieldProp['unsigned'],
                    'default'  => $default,
                    'primary'  => isset($fieldProp['primary']) ? $fieldProp['primary'] : false,
                ), ($fieldProp['comment'] != '') ?
                    $fieldProp['comment'] :
                    ucwords(str_replace('_', ' ', $fieldName))
                );
            }
        }

        // Adding indexes
        $table->addIndex(
            $_writeAdapter->getIndexName($tableName, array('entity_id')),
            array('entity_id'),
            array('type' => 'primary')
        );
        $table->addIndex(
            $_writeAdapter->getIndexName($tableName, array('store_id')), array('store_id'), array('type' => 'index')
        );
        $table->addIndex(
            $_writeAdapter->getIndexName($tableName, array('path')), array('path'), array('type' => 'index')
        );
        $table->addIndex(
            $_writeAdapter->getIndexName($tableName, array('level')), array('level'), array('type' => 'index')
        );

        // Adding foreign keys
        $table->addForeignKey(
            $_writeAdapter->getForeignKeyName(
                $tableName, 'entity_id', $this->getTable('catalog/category'), 'entity_id'
            ),
            'entity_id', $this->getTable('catalog/category'), 'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);
        $table->addForeignKey(
            $_writeAdapter->getForeignKeyName($tableName, 'store_id', $this->getTable('core/store'), 'store_id'),
            'store_id', $this->getTable('core/store'), 'store_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);
        $_writeAdapter->createTable($table);
        return $this;
    }

    /**
     * Return array of static columns
     *
     * @return array
     */
    protected function _getStaticColumns()
    {
        $helper = Mage::getResourceHelper('catalog');
        $columns = array();
        $columnsToSkip = array('entity_type_id', 'attribute_set_id');
        $describe = $this->_getWriteAdapter()->describeTable($this->getTable('catalog/category'));

        foreach ($describe as $column) {
            if (in_array($column['COLUMN_NAME'], $columnsToSkip)) {
                continue;
            }
            $_is_unsigned = '';
            $ddlType = $helper->getDdlTypeByColumnType($column['DATA_TYPE']);
            $column['DEFAULT'] = trim($column['DEFAULT'],"' ");
            switch ($ddlType) {
                case Varien_Db_Ddl_Table::TYPE_SMALLINT:
                case Varien_Db_Ddl_Table::TYPE_INTEGER:
                case Varien_Db_Ddl_Table::TYPE_BIGINT:
                    $_is_unsigned = (bool)$column['UNSIGNED'];
                    if ($column['DEFAULT'] === '') {
                        $column['DEFAULT'] = null;
                    }

                    $options = null;
                    if ($column['SCALE'] > 0) {
                        $ddlType = Varien_Db_Ddl_Table::TYPE_DECIMAL;
                    } else {
                        break;
                    }
                case Varien_Db_Ddl_Table::TYPE_DECIMAL:
                    $options = $column['PRECISION'] . ',' . $column['SCALE'];
                    $_is_unsigned = null;
                    if ($column['DEFAULT'] === '') {
                        $column['DEFAULT'] = null;
                    }
                    break;
                case Varien_Db_Ddl_Table::TYPE_TEXT:
                    $options = $column['LENGTH'];
                    $_is_unsigned = null;
                    break;
                case Varien_Db_Ddl_Table::TYPE_TIMESTAMP:
                    $options = null;
                    $_is_unsigned = null;
                    break;
                case Varien_Db_Ddl_Table::TYPE_DATETIME:
                    $_is_unsigned = null;
                    break;

            }
            $columns[$column['COLUMN_NAME']] = array(
                'type' => array($ddlType, $options),
                'unsigned' => $_is_unsigned,
                'nullable' => $column['NULLABLE'],
                'default' => ($column['DEFAULT'] === null ? false : $column['DEFAULT']),
                'comment' => $column['COLUMN_NAME']
            );
        }
        $columns['store_id'] = array(
            'type' => array(Varien_Db_Ddl_Table::TYPE_SMALLINT, 5),
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
            'comment' => 'Store Id'
        );
        return $columns;
    }

    /**
     * Return array of eav columns, skip attribute with static type
     *
     * @return array
     */
    protected function _getEavColumns()
    {
        $columns = array();
        $attributes = $this->_getAttributes();
        foreach ($attributes as $attribute) {
            if ($attribute['backend_type'] == 'static') {
                continue;
            }
            $columns[$attribute['attribute_code']] = array();
            switch ($attribute['backend_type']) {
                case 'varchar':
                    $columns[$attribute['attribute_code']] = array(
                        'type' => array(Varien_Db_Ddl_Table::TYPE_TEXT, 255),
                        'unsigned' => null,
                        'nullable' => true,
                        'default' => null,
                        'comment' => (string)$attribute['frontend_label']
                    );
                    break;
                case 'int':
                    $columns[$attribute['attribute_code']] = array(
                        'type' => array(Varien_Db_Ddl_Table::TYPE_INTEGER, null),
                        'unsigned' => null,
                        'nullable' => true,
                        'default' => null,
                        'comment' => (string)$attribute['frontend_label']
                    );
                    break;
                case 'text':
                    $columns[$attribute['attribute_code']] = array(
                        'type' => array(Varien_Db_Ddl_Table::TYPE_TEXT, '64k'),
                        'unsigned' => null,
                        'nullable' => true,
                        'default' => null,
                        'comment' => (string)$attribute['frontend_label']
                    );
                    break;
                case 'datetime':
                    $columns[$attribute['attribute_code']] = array(
                        'type' => array(Varien_Db_Ddl_Table::TYPE_DATETIME, null),
                        'unsigned' => null,
                        'nullable' => true,
                        'default' => null,
                        'comment' => (string)$attribute['frontend_label']
                    );
                    break;
                case 'decimal':
                    $columns[$attribute['attribute_code']] = array(
                        'type' => array(Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4'),
                        'unsigned' => null,
                        'nullable' => true,
                        'default' => null,
                        'comment' => (string)$attribute['frontend_label']
                    );
                    break;
            }
        }
        return $columns;
    }

    /**
     * Return array of attribute codes for entity type 'catalog_category'
     *
     * @return array
     */
    protected function _getAttributes()
    {
        if ($this->_attributeCodes === null) {
            $select = $this->_getWriteAdapter()->select()
                ->from($this->getTable('eav/entity_type'), array())
                ->join(
                    $this->getTable('eav/attribute'),
                    $this->getTable('eav/attribute')
                        . '.entity_type_id = ' . $this->getTable('eav/entity_type') . '.entity_type_id',
                    $this->getTable('eav/attribute').'.*'
                )
                ->where(
                    $this->getTable('eav/entity_type') . '.entity_type_code = ?', Mage_Catalog_Model_Category::ENTITY
                );
            $this->_attributeCodes = array();
            foreach ($this->_getWriteAdapter()->fetchAll($select) as $attribute) {
                $this->_attributeCodes[$attribute['attribute_id']] = $attribute;
            }
        }
        return $this->_attributeCodes;
    }

    /**
     * Return attribute values for given entities and store
     *
     * @param array $entityIds
     * @param integer $store_id
     * @return array
     */
    protected function _getAttributeValues($entityIds, $store_id)
    {
        if (!is_array($entityIds)) {
            $entityIds = array($entityIds);
        }
        $values = array();

        foreach ($entityIds as $entityId) {
            $values[$entityId] = array();
        }
        $attributes = $this->_getAttributes();
        $attributesType = array(
            'varchar',
            'int',
            'decimal',
            'text',
            'datetime'
        );
        foreach ($attributesType as $type) {
            foreach ($this->_getAttributeTypeValues($type, $entityIds, $store_id) as $row) {
                $values[$row['entity_id']][$attributes[$row['attribute_id']]['attribute_code']] = $row['value'];
            }
        }
        return $values;
    }

    /**
     * Return attribute values for given entities and store of specific attribute type
     *
     * @param string $type
     * @param array $entityIds
     * @param integer $storeId
     * @return array
     */
    protected function _getAttributeTypeValues($type, $entityIds, $storeId)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from(
                array('def' => $this->getTable(array('catalog/category', $type))),
                array('entity_id', 'attribute_id')
            )
            ->joinLeft(
                array('store' => $this->getTable(array('catalog/category', $type))),
                'store.entity_id = def.entity_id AND store.attribute_id = def.attribute_id '
                    . 'AND store.store_id = ' . $storeId,
                array('value' => $this->_getWriteAdapter()->getCheckSql(
                    'store.value_id > 0',
                    $this->_getWriteAdapter()->quoteIdentifier('store.value'),
                    $this->_getWriteAdapter()->quoteIdentifier('def.value')
                ))
            )
            ->where('def.entity_id IN (?)', $entityIds)
            ->where('def.store_id IN (?)', array(Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID, $storeId));
        return $this->_getWriteAdapter()->fetchAll($select);
    }

    /**
     * Delete store table(s) of given stores;
     *
     * @param array|integer $stores
     * @return Mage_Catalog_Model_Resource_Category_Flat
     */
    public function deleteStores($stores)
    {
        $this->_deleteTable($stores);
        return $this;
    }

    /**
     * Delete table(s) of given stores.
     *
     * @param array|integer $stores
     * @return Mage_Catalog_Model_Resource_Category_Flat
     */
    protected function _deleteTable($stores)
    {
        if (!is_array($stores)) {
            $stores = array($stores);
        }
        foreach ($stores as $store) {
            $this->_getWriteAdapter()->dropTable($this->getMainStoreTable($store));
        }
        return $this;
    }

    /**
     * Synchronize flat data with eav model for category
     *
     * @param Varien_Object $category
     * @return Mage_Catalog_Model_Resource_Category_Flat
     */
    protected function _synchronize($category)
    {
        $table = $this->getMainStoreTable($category->getStoreId());
        $data  = $this->_prepareDataForAllFields($category);
        $this->_getWriteAdapter()->insertOnDuplicate($table, $data);
        return $this;
    }

    /**
     * Synchronize flat data with eav model.
     *
     * @param Mage_Catalog_Model_Category|int $category
     * @param array $storeIds
     * @return Mage_Catalog_Model_Resource_Category_Flat
     */
    public function synchronize($category = null, $storeIds = array())
    {
        if (is_null($category)) {
            if (empty($storeIds)) {
                $storeIds = null;
            }
            $stores = $this->getStoresRootCategories($storeIds);

            $storesObjects = array();
            foreach ($stores as $storeId => $rootCategoryId) {
                $_store = new Varien_Object(array(
                    'store_id'          => $storeId,
                    'root_category_id'  => $rootCategoryId
                ));
                $_store->setIdFieldName('store_id');
                $storesObjects[] = $_store;
            }

            $this->rebuild($storesObjects);
        } else if ($category instanceof Mage_Catalog_Model_Category) {
            $categoryId = $category->getId();
            foreach ($category->getStoreIds() as $storeId) {
                if ($storeId == Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID) {
                    continue;
                }

                $attributeValues = $this->_getAttributeValues($categoryId, $storeId);
                $data = new Varien_Object($category->getData());
                $data->addData($attributeValues[$categoryId])
                    ->setStoreId($storeId);
                $this->_synchronize($data);
            }
        } else if (is_numeric($category)) {
            $write  = $this->_getWriteAdapter();
            $select = $write->select()
                ->from($this->getTable('catalog/category'))
                ->where('entity_id=?', $category);
            $row    = $write->fetchRow($select);
            if (!$row) {
                return $this;
            }

            $stores = $this->getStoresRootCategories();
            $path   = explode('/', $row['path']);
            foreach ($stores as $storeId => $rootCategoryId) {
                if (in_array($rootCategoryId, $path)) {
                    $attributeValues = $this->_getAttributeValues($category, $storeId);
                    $data = new Varien_Object($row);
                    $data->addData($attributeValues[$category])
                        ->setStoreId($storeId);
                    $this->_synchronize($data);
                } else {
                    $where = $write->quoteInto('entity_id = ?', $category);
                    $write->delete($this->getMainStoreTable($storeId), $where);
                }
            }
        }

        return $this;
    }

    /**
     * Remove table of given stores
     *
     * @param int|array $stores
     * @return Mage_Catalog_Model_Resource_Category_Flat
     */
    public function removeStores($stores)
    {
        $this->_deleteTable($stores);
        return $this;
    }

    /**
     * Synchronize flat category data after move by affected category ids
     *
     * @param array $affectedCategoryIds
     * @return Mage_Catalog_Model_Resource_Category_Flat
     */
    public function move(array $affectedCategoryIds)
    {
        $write  = $this->_getWriteAdapter();
        $select = $write->select()
            ->from($this->getTable('catalog/category'), array('entity_id', 'path'))
            ->where('entity_id IN(?)', $affectedCategoryIds);
        $pairs  = $write->fetchPairs($select);

        $pathCond  = array($write->quoteInto('entity_id IN(?)', $affectedCategoryIds));
        $parentIds = array();

        foreach ($pairs as $path) {
            $pathCond[] = $write->quoteInto('path LIKE ?', $path . '/%');
            $parentIds  = array_merge($parentIds, explode('/', $path));
        }

        $stores = $this->getStoresRootCategories();
        $where  = join(' OR ', $pathCond);
        $lastId = 0;
        while (true) {
            $select = $write->select()
                ->from($this->getTable('catalog/category'))
                ->where('entity_id>?', $lastId)
                ->where($where)
                ->order('entity_id')
                ->limit(500);
            $rowSet = $write->fetchAll($select);

            if (!$rowSet) {
                break;
            }

            $addStores = array();
            $remStores = array();

            foreach ($rowSet as &$row) {
                $lastId = $row['entity_id'];
                $path = explode('/', $row['path']);
                foreach ($stores as $storeId => $rootCategoryId) {
                    if (in_array($rootCategoryId, $path)) {
                        $addStores[$storeId][$row['entity_id']] = $row;
                    } else {
                        $remStores[$storeId][] = $row['entity_id'];
                    }
                }
            }

            // remove
            foreach ($remStores as $storeId => $categoryIds) {
                $where = $write->quoteInto('entity_id IN(?)', $categoryIds);
                $write->delete($this->getMainStoreTable($storeId), $where);
            }

            // add/update
            foreach ($addStores as $storeId => $storeCategoryIds) {
                $attributeValues = $this->_getAttributeValues(array_keys($storeCategoryIds), $storeId);
                foreach ($storeCategoryIds as $row) {
                    $data = new Varien_Object($row);
                    $data->addData($attributeValues[$row['entity_id']])
                        ->setStoreId($storeId);
                    $this->_synchronize($data);
                }
            }
        }

        return $this;
    }

    /**
     * Synchronize flat data with eav after moving category
     *
     * @param integer $categoryId
     * @param integer $prevParentId
     * @param integer $parentId
     * @return Mage_Catalog_Model_Resource_Category_Flat
     */
    public function moveold($categoryId, $prevParentId, $parentId)
    {
        $catalogCategoryTable = $this->getTable('catalog/category');
        $_staticFields = array(
            'parent_id',
            'path',
            'level',
            'position',
            'children_count',
            'updated_at'
        );
        $prevParent = Mage::getModel('catalog/category')->load($prevParentId);
        $parent = Mage::getModel('catalog/category')->load($parentId);
        if ($prevParent->getStore()->getWebsiteId() != $parent->getStore()->getWebsiteId()) {
            foreach ($prevParent->getStoreIds() as $storeId) {
                $this->_getWriteAdapter()->delete(
                    $this->getMainStoreTable($storeId),
                    $this->_getWriteAdapter()->quoteInto('entity_id = ?', $categoryId)
                );
            }
            $select = $this->_getReadAdapter()->select()
                ->from($catalogCategoryTable, 'path')
                ->where('entity_id = ?', $categoryId);

            $categoryPath = $this->_getWriteAdapter()->fetchOne($select);

            $select = $this->_getWriteAdapter()->select()
                ->from($catalogCategoryTable, 'entity_id')
                ->where('path LIKE ?', "$categoryPath/%")
                ->orWhere('path = ?', $categoryPath);
            $_categories = $this->_getWriteAdapter()->fetchAll($select);
            foreach ($_categories as $_category) {
                foreach ($parent->getStoreIds() as $storeId) {
                    $_tmpCategory = Mage::getModel('catalog/category')
                        ->setStoreId($storeId)
                        ->load($_category['entity_id']);
                    $this->_synchronize($_tmpCategory);
                }
            }
        } else {
            foreach ($parent->getStoreIds() as $store) {
                $mainStoreTable = $this->getMainStoreTable($store);

                $update = "UPDATE {$mainStoreTable}, {$catalogCategoryTable} SET";
                foreach ($_staticFields as $field) {
                    $update .= " {$mainStoreTable}.".$field."={$catalogCategoryTable}.".$field.",";
                }
                $update = substr($update, 0, -1);
                $update .= " WHERE {$mainStoreTable}.entity_id = {$catalogCategoryTable}.entity_id AND " .
                    "($catalogCategoryTable}.path like '{$parent->getPath()}/%' OR " .
                    "{$catalogCategoryTable}.path like '{$prevParent->getPath()}/%')";
                $this->_getWriteAdapter()->query($update);
            }
        }
        $prevParent   = null;
        $parent       = null;
        $_tmpCategory = null;
//        $this->_move($categoryId, $prevParentPath, $parentPath);
        return $this;
    }

    /**
     * Prepare array of category data to insert or update.
     * array(
     *  'field_name' => 'value'
     * )
     *
     * @param Varien_Object $category
     * @param array $replaceFields
     * @return array
     */
    protected function _prepareDataForAllFields($category, $replaceFields = array())
    {
        $table = $this->getMainStoreTable($category->getStoreId());
        $this->_getWriteAdapter()->resetDdlCache($table);
        $table = $this->_getWriteAdapter()->describeTable($table);
        $data = array();
        $idFieldName = Mage::getSingleton('catalog/category')->getIdFieldName();
        foreach ($table as $column => $columnData) {
            if ($column != $idFieldName || null !== $category->getData($column)) {
                if (key_exists($column, $replaceFields)) {
                    $value = $category->getData($replaceFields[$column]);
                } else {
                    $value = $category->getData($column);
                }
                if (is_array($value)) {
                    $value = implode(',', $value);
                }
                $data[$column] = $value;
            }
        }
        return $data;
    }

    /**
     * Retrieve attribute instance
     * Special for non static flat table
     *
     * @param mixed $attribute
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getAttribute($attribute)
    {
        return Mage::getSingleton('catalog/config')
            ->getAttribute(Mage_Catalog_Model_Category::ENTITY, $attribute);
    }

    /**
     * Get count of active/not active children categories
     *
     * @param Mage_Catalog_Model_Category $category
     * @param bool $isActiveFlag
     * @return integer
     */
    public function getChildrenAmount($category, $isActiveFlag = true)
    {
        $_table = $this->getMainStoreTable($category->getStoreId());
        $select = $this->_getReadAdapter()->select()
            ->from($_table, "COUNT({$_table}.entity_id)")
            ->where("{$_table}.path LIKE ?", $category->getPath() . '/%')
            ->where("{$_table}.is_active = ?", (int) $isActiveFlag);
        return (int) $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Get products count in category
     *
     * @param Mage_Catalog_Model_Category $category
     * @return integer
     */
    public function getProductCount($category)
    {
        $select =  $this->_getReadAdapter()->select()
            ->from(
                $this->getTable('catalog/category_product'),
                "COUNT({$this->getTable('catalog/category_product')}.product_id)"
            )
            ->where("{$this->getTable('catalog/category_product')}.category_id = ?", $category->getId())
            ->group("{$this->getTable('catalog/category_product')}.category_id");
        return (int) $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Return parent categories of category
     *
     * @param Mage_Catalog_Model_Category $category
     * @param bool $isActive
     * @return array
     */
    public function getParentCategories($category, $isActive = true)
    {
        $categories = array();
        $select = $this->_getReadAdapter()->select()
            ->from(
                array('main_table' => $this->getMainStoreTable($category->getStoreId())),
                array('main_table.entity_id', 'main_table.name')
            )
            ->where('main_table.entity_id IN (?)', array_reverse(explode(',', $category->getPathInStore())));
        if ($isActive) {
            $select->where('main_table.is_active = ?', '1');
        }
        $select->order('main_table.path ASC');

        $urlRewrite = $this->_factory->getCategoryUrlRewriteHelper();
        $urlRewrite->joinTableToSelect($select, $category->getStoreId());

        $result = $this->_getReadAdapter()->fetchAll($select);
        foreach ($result as $row) {
            $row['id'] = $row['entity_id'];
            $categories[$row['entity_id']] = Mage::getModel('catalog/category')->setData($row);
        }
        return $categories;
    }

    /**
     * Return parent category of current category with own custom design settings
     *
     * @param Mage_Catalog_Model_Category $category
     * @return Mage_Catalog_Model_Category
     */
    public function getParentDesignCategory($category)
    {
        $adapter    = $this->_getReadAdapter();
        $levelField = $adapter->quoteIdentifier('level');
        $pathIds    = array_reverse($category->getPathIds());
        $select = $adapter->select()
            ->from(array('main_table' => $this->getMainStoreTable($category->getStoreId())), '*')
            ->where('entity_id IN (?)', $pathIds)
            ->where('custom_use_parent_settings = ?', 0)
            ->where($levelField . ' != ?', 0)
            ->order('level ' . Varien_Db_Select::SQL_DESC);
        $result = $adapter->fetchRow($select);
        return Mage::getModel('catalog/category')->setData($result);
    }

    /**
     * Return children categories of category
     *
     * @param Mage_Catalog_Model_Category $category
     * @return array
     */
    public function getChildrenCategories($category)
    {
        $categories = $this->_loadNodes($category, 1, $category->getStoreId());
        return $categories;
    }

    /**
     * Return children categories of category with inactive
     *
     * @param Mage_Catalog_Model_Category $category
     * @return array
     */
    public function getChildrenCategoriesWithInactive($category)
    {
        return $this->_loadNodes($category, 1, $category->getStoreId(), false);
    }

    /**
     * Check is category in list of store categories
     *
     * @param Mage_Catalog_Model_Category $category
     * @return boolean
     */
    public function isInRootCategoryList($category)
    {
        $pathIds = $category->getParentIds();
        return in_array(Mage::app()->getStore()->getRootCategoryId(), $pathIds);
    }

    /**
     * Return children ids of category
     *
     * @param Mage_Catalog_Model_Category $category
     * @param bool $recursive
     * @param bool $isActive
     * @return array
     */
    public function getChildren($category, $recursive = true, $isActive = true)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainStoreTable($category->getStoreId()), 'entity_id')
            ->where('path LIKE ?', "{$category->getPath()}/%");
        if (!$recursive) {
            $select->where('level <= ?', $category->getLevel() + 1);
        }
        if ($isActive) {
            $select->where('is_active = ?', '1');
        }
        $_categories = $this->_getReadAdapter()->fetchAll($select);
        $categoriesIds = array();
        foreach ($_categories as $_category) {
            $categoriesIds[] = $_category['entity_id'];
        }
        return $categoriesIds;
    }

    /**
     * Return all children ids of category (with category id)
     *
     * @param Mage_Catalog_Model_Category $category
     * @return array
     */
    public function getAllChildren($category)
    {
        $categoriesIds = $this->getChildren($category);
        $myId = array($category->getId());
        $categoriesIds = array_merge($myId, $categoriesIds);

        return $categoriesIds;
    }

    /**
     * Check if category id exist
     *
     * @param int $id
     * @return bool
     */
    public function checkId($id)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainStoreTable($this->getStoreId()), 'entity_id')
            ->where('entity_id=?', $id);
        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Get design update data of parent categories
     *
     * @param Mage_Catalog_Model_Category $category
     * @return array
     */
    public function getDesignUpdateData($category)
    {
        $categories = array();
        $pathIds = array();
        foreach (array_reverse($category->getParentIds()) as $pathId) {
            if ($pathId == Mage::app()->getStore()->getRootCategoryId()) {
                $pathIds[] = $pathId;
                break;
            }
            $pathIds[] = $pathId;
        }
        $select = $this->_getReadAdapter()->select()
            ->from(
                array('main_table' => $this->getMainStoreTable($category->getStoreId())),
                array(
                    'main_table.entity_id',
                    'main_table.custom_design',
                    'main_table.custom_design_apply',
                    'main_table.custom_design_from',
                    'main_table.custom_design_to',
                )
            )
            ->where('main_table.entity_id IN (?)', $pathIds)
            ->where('main_table.is_active = ?', '1')
            ->order('main_table.path ' . Varien_Db_Select::SQL_DESC);
        $result = $this->_getReadAdapter()->fetchAll($select);
        foreach ($result as $row) {
            $row['id'] = $row['entity_id'];
            $categories[$row['entity_id']] = Mage::getModel('catalog/category')->setData($row);
        }
        return $categories;
    }

    /**
     * Retrieve anchors above
     *
     * @param array $filterIds
     * @param int $storeId
     * @return array
     */
    public function getAnchorsAbove(array $filterIds, $storeId = 0)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('e' => $this->getMainStoreTable($storeId)), 'entity_id')
            ->where('is_anchor = ?', 1)
            ->where('entity_id IN (?)', $filterIds);

        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Retrieve array with root category id per store
     *
     * @param int|array $storeIds   result limitation
     * @return array
     */
    public function getStoresRootCategories($storeIds = null)
    {
        if (is_null($this->_storesRootCategories)) {
            $select = $this->_getWriteAdapter()->select()
                ->from(array('cs' => $this->getTable('core/store')), array('store_id'))
                ->join(
                    array('csg' => $this->getTable('core/store_group')),
                    'csg.group_id = cs.group_id',
                    array('root_category_id'))
                ->where('cs.store_id <> ?', Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID);
            $this->_storesRootCategories = $this->_getWriteAdapter()->fetchPairs($select);
        }

        if (!is_null($storeIds)) {
            if (!is_array($storeIds)) {
                $storeIds = array($storeIds);
            }

            $stores = array();
            foreach ($this->_storesRootCategories as $storeId => $rootId) {
                if (in_array($storeId, $storeIds)) {
                    $stores[$storeId] = $rootId;
                }
            }
            return $stores;
        }

        return $this->_storesRootCategories;
    }

    /**
     * Creating table and adding attributes as fields to table for all stores
     *
     * @return Mage_Catalog_Model_Resource_Category_Flat
     */
    protected function _createTables()
    {
        if ($this->_allowTableChanges) {
            foreach (Mage::app()->getStores() as $store) {
                $this->_createTable($store->getId());
            }
        }
        return $this;
    }

    /**
     * Transactional rebuild flat data from eav
     *
     * @throws Exception
     * @return Mage_Catalog_Model_Resource_Category_Flat
     */
    public function reindexAll()
    {
        $this->_createTables();
        $allowTableChanges = $this->_allowTableChanges;
        if ($allowTableChanges) {
            $this->_allowTableChanges = false;
        }
        $this->beginTransaction();
        try {
            $this->rebuild();
            $this->commit();
            if ($allowTableChanges) {
                $this->_allowTableChanges = true;
            }
        } catch (Exception $e) {
            $this->rollBack();
            if ($allowTableChanges) {
                $this->_allowTableChanges = true;
            }
            throw $e;
        }
        return $this;
    }

    /**
     * Check if Catalog Category Flat Data has been initialized
     *
     * @deprecated use Mage_Catalog_Model_Resource_Category_Flat::isBuilt() instead
     *
     * @return bool
     */
    public function isRebuilt()
    {
        return $this->isBuilt();
    }
}
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
 * @package     Mage_Eav
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Entity/Attribute/Model - collection abstract
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Eav_Model_Entity_Collection_Abstract extends Varien_Data_Collection_Db
{
    /**
     * Array of items with item id key
     *
     * @var array
     */
    protected $_itemsById                  = array();

    /**
     * Entity static fields
     *
     * @var array
     */
    protected $_staticFields               = array();

    /**
     * Entity object to define collection's attributes
     *
     * @var Mage_Eav_Model_Entity_Abstract
     */
    protected $_entity;

    /**
     * Entity types to be fetched for objects in collection
     *
     * @var array
     */
    protected $_selectEntityTypes         = array();

    /**
     * Attributes to be fetched for objects in collection
     *
     * @var array
     */
    protected $_selectAttributes          = array();

    /**
     * Attributes to be filtered order sorted by
     *
     * @var array
     */
    protected $_filterAttributes          = array();

    /**
     * Joined entities
     *
     * @var array
     */
    protected $_joinEntities              = array();

    /**
     * Joined attributes
     *
     * @var array
     */
    protected $_joinAttributes            = array();

    /**
     * Joined fields data
     *
     * @var array
     */
    protected $_joinFields                = array();

    /**
     * Use analytic function flag
     * If true - allows to prepare final select with analytic functions
     *
     * @var bool
     */
    protected $_useAnalyticFunction         = false;

    /**
     * Cast map for attribute order
     *
     * @var array
     */
    protected $_castToIntMap = array(
        'validate-digits'
    );

    /**
     * Collection constructor
     *
     * @param Mage_Core_Model_Resource_Abstract $resource
     */
    public function __construct($resource = null)
    {
        parent::__construct();
        $this->_construct();
        $this->setConnection($this->getEntity()->getReadConnection());
        $this->_prepareStaticFields();
        $this->_initSelect();
    }

    /**
     * Initialize collection
     */
    protected function _construct()
    {

    }

    /**
     * Retreive table name
     *
     * @param string $table
     * @return string
     */
    public function getTable($table)
    {
        return $this->getResource()->getTable($table);
    }

    /**
     * Prepare static entity fields
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _prepareStaticFields()
    {
        foreach ($this->getEntity()->getDefaultAttributes() as $field) {
            $this->_staticFields[$field] = $field;
        }
        return $this;
    }

    /**
     * Init select
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(array('e' => $this->getEntity()->getEntityTable()));
        if ($this->getEntity()->getTypeId()) {
            $this->addAttributeToFilter('entity_type_id', $this->getEntity()->getTypeId());
        }
        return $this;
    }

    /**
     * Standard resource collection initalization
     *
     * @param string $model
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    protected function _init($model, $entityModel = null)
    {
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName($model));
        if ($entityModel === null) {
            $entityModel = $model;
        }
        $entity = Mage::getResourceSingleton($entityModel);
        $this->setEntity($entity);

        return $this;
    }

    /**
     * Set entity to use for attributes
     *
     * @param Mage_Eav_Model_Entity_Abstract $entity
     * @throws Mage_Eav_Exception
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function setEntity($entity)
    {
        if ($entity instanceof Mage_Eav_Model_Entity_Abstract) {
            $this->_entity = $entity;
        } elseif (is_string($entity) || $entity instanceof Mage_Core_Model_Config_Element) {
            $this->_entity = Mage::getModel('eav/entity')->setType($entity);
        } else {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid entity supplied: %s', print_r($entity, 1)));
        }
        return $this;
    }

    /**
     * Get collection's entity object
     *
     * @return Mage_Eav_Model_Entity_Abstract
     */
    public function getEntity()
    {
        if (empty($this->_entity)) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Entity is not initialized'));
        }
        return $this->_entity;
    }

    /**
     * Get resource instance
     *
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    public function getResource()
    {
        return $this->getEntity();
    }

    /**
     * Set template object for the collection
     *
     * @param   Varien_Object $object
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function setObject($object=null)
    {
        if (is_object($object)) {
            $this->setItemObjectClass(get_class($object));
        } else {
            $this->setItemObjectClass($object);
        }

        return $this;
    }


    /**
     * Add an object to the collection
     *
     * @param Varien_Object $object
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addItem(Varien_Object $object)
    {
        if (get_class($object) !== $this->_itemObjectClass) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Attempt to add an invalid object'));
        }
        return parent::addItem($object);
    }

    /**
     * Retrieve entity attribute
     *
     * @param   string $attributeCode
     * @return  Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getAttribute($attributeCode)
    {
        if (isset($this->_joinAttributes[$attributeCode])) {
            return $this->_joinAttributes[$attributeCode]['attribute'];
        }

        return $this->getEntity()->getAttribute($attributeCode);
    }

    /**
     * Add attribute filter to collection
     *
     * If $attribute is an array will add OR condition with following format:
     * array(
     *     array('attribute'=>'firstname', 'like'=>'test%'),
     *     array('attribute'=>'lastname', 'like'=>'test%'),
     * )
     *
     * @see self::_getConditionSql for $condition
     * @param Mage_Eav_Model_Entity_Attribute_Interface|integer|string|array $attribute
     * @param null|string|array $condition
     * @param string $operator
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addAttributeToFilter($attribute, $condition = null, $joinType = 'inner')
    {
        if ($attribute === null) {
            $this->getSelect();
            return $this;
        }

        if (is_numeric($attribute)) {
            $attribute = $this->getEntity()->getAttribute($attribute)->getAttributeCode();
        } else if ($attribute instanceof Mage_Eav_Model_Entity_Attribute_Interface) {
            $attribute = $attribute->getAttributeCode();
        }

        if (is_array($attribute)) {
            $sqlArr = array();
            foreach ($attribute as $condition) {
                $sqlArr[] = $this->_getAttributeConditionSql($condition['attribute'], $condition, $joinType);
            }
            $conditionSql = '('.implode(') OR (', $sqlArr).')';
        } else if (is_string($attribute)) {
            if ($condition === null) {
                $condition = '';
            }
            $conditionSql = $this->_getAttributeConditionSql($attribute, $condition, $joinType);
        }

        if (!empty($conditionSql)) {
            $this->getSelect()->where($conditionSql, null, Varien_Db_Select::TYPE_CONDITION);
        } else {
            Mage::throwException('Invalid attribute identifier for filter ('.get_class($attribute).')');
        }

        return $this;
    }

    /**
     * Wrapper for compatibility with Varien_Data_Collection_Db
     *
     * @param mixed $attribute
     * @param mixed $condition
     */
    public function addFieldToFilter($attribute, $condition = null)
    {
        return $this->addAttributeToFilter($attribute, $condition);
    }

    /**
     * Add attribute to sort order
     *
     * @param string $attribute
     * @param string $dir
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addAttributeToSort($attribute, $dir = self::SORT_ORDER_ASC)
    {
        if (isset($this->_joinFields[$attribute])) {
            $this->getSelect()->order($this->_getAttributeFieldName($attribute).' '.$dir);
            return $this;
        }
        if (isset($this->_staticFields[$attribute])) {
            $this->getSelect()->order("e.{$attribute} {$dir}");
            return $this;
        }
        if (isset($this->_joinAttributes[$attribute])) {
            $attrInstance = $this->_joinAttributes[$attribute]['attribute'];
            $entityField = $this->_getAttributeTableAlias($attribute) . '.' . $attrInstance->getAttributeCode();
        } else {
            $attrInstance = $this->getEntity()->getAttribute($attribute);
            $entityField = 'e.' . $attribute;
        }

        if ($attrInstance) {
            if ($attrInstance->getBackend()->isStatic()) {
                $orderExpr = $entityField;
            } else {
                $this->_addAttributeJoin($attribute, 'left');
                if (isset($this->_joinAttributes[$attribute])||isset($this->_joinFields[$attribute])) {
                    $orderExpr = $attribute;
                } else {
                    $orderExpr = $this->_getAttributeTableAlias($attribute).'.value';
                }
            }

            if (in_array($attrInstance->getFrontendClass(), $this->_castToIntMap)) {
                $orderExpr = Mage::getResourceHelper('eav')->getCastToIntExpression(
                    $this->_prepareOrderExpression($orderExpr)
                );
            }

            $orderExpr .= ' ' . $dir;
            $this->getSelect()->order($orderExpr);
        }
        return $this;
    }

    /**
     * Retrieve attribute expression by specified column
     *
     * @param string $field
     * @return string|Zend_Db_Expr
     */
    protected function _prepareOrderExpression($field)
    {
        foreach ($this->getSelect()->getPart(Zend_Db_Select::COLUMNS) as $columnEntry) {
            if ($columnEntry[2] != $field) {
                continue;
            }
            if ($columnEntry[1] instanceof Zend_Db_Expr) {
                return $columnEntry[1];
            }
        }
        return $field;
    }

    /**
     * Add attribute to entities in collection
     *
     * If $attribute=='*' select all attributes
     *
     * @param   array|string|integer|Mage_Core_Model_Config_Element $attribute
     * @param   false|string $joinType flag for joining attribute
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addAttributeToSelect($attribute, $joinType = false)
    {
        if (is_array($attribute)) {
            Mage::getSingleton('eav/config')->loadCollectionAttributes($this->getEntity()->getType(), $attribute);
            foreach ($attribute as $a) {
                $this->addAttributeToSelect($a, $joinType);
            }
            return $this;
        }
        if ($joinType !== false && !$this->getEntity()->getAttribute($attribute)->isStatic()) {
            $this->_addAttributeJoin($attribute, $joinType);
        } elseif ('*' === $attribute) {
            $entity = clone $this->getEntity();
            $attributes = $entity
                ->loadAllAttributes()
                ->getAttributesByCode();
            foreach ($attributes as $attrCode=>$attr) {
                $this->_selectAttributes[$attrCode] = $attr->getId();
            }
        } else {
            if (isset($this->_joinAttributes[$attribute])) {
                $attrInstance = $this->_joinAttributes[$attribute]['attribute'];
            } else {
                $attrInstance = Mage::getSingleton('eav/config')
                    ->getCollectionAttribute($this->getEntity()->getType(), $attribute);
            }
            if (empty($attrInstance)) {
                throw Mage::exception(
                    'Mage_Eav',
                    Mage::helper('eav')->__('Invalid attribute requested: %s', (string)$attribute)
                );
            }
            $this->_selectAttributes[$attrInstance->getAttributeCode()] = $attrInstance->getId();
        }
        return $this;
    }

    public function addEntityTypeToSelect($entityType, $prefix)
    {
        $this->_selectEntityTypes[$entityType] = array(
            'prefix' => $prefix,
        );
        return $this;
    }

    /**
     * Add field to static
     *
     * @param string $field
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addStaticField($field)
    {
        if (!isset($this->_staticFields[$field])) {
            $this->_staticFields[$field] = $field;
        }
        return $this;
    }

    /**
     * Add attribute expression (SUM, COUNT, etc)
     *
     * Example: ('sub_total', 'SUM({{attribute}})', 'revenue')
     * Example: ('sub_total', 'SUM({{revenue}})', 'revenue')
     *
     * For some functions like SUM use groupByAttribute.
     *
     * @param string $alias
     * @param string $expression
     * @param string $attribute
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addExpressionAttributeToSelect($alias, $expression, $attribute)
    {
        // validate alias
        if (isset($this->_joinFields[$alias])) {
            throw Mage::exception(
                'Mage_Eav',
                Mage::helper('eav')->__('Joint field or attribute expression with this alias is already declared')
            );
        }
        if (!is_array($attribute)) {
            $attribute = array($attribute);
        }

        $fullExpression = $expression;
        // Replacing multiple attributes
        foreach ($attribute as $attributeItem) {
            if (isset($this->_staticFields[$attributeItem])) {
                $attrField = sprintf('e.%s', $attributeItem);
            } else {
                $attributeInstance = $this->getAttribute($attributeItem);

                if ($attributeInstance->getBackend()->isStatic()) {
                    $attrField = 'e.' . $attributeItem;
                } else {
                    $this->_addAttributeJoin($attributeItem, 'left');
                    $attrField = $this->_getAttributeFieldName($attributeItem);
                }
            }

            $fullExpression = str_replace('{{attribute}}', $attrField, $fullExpression);
            $fullExpression = str_replace('{{' . $attributeItem . '}}', $attrField, $fullExpression);
        }

        $this->getSelect()->columns(array($alias => $fullExpression));

        $this->_joinFields[$alias] = array(
            'table' => false,
            'field' => $fullExpression
        );

        return $this;
    }


    /**
     * Groups results by specified attribute
     *
     * @param string|array $attribute
     */
    public function groupByAttribute($attribute)
    {
        if (is_array($attribute)) {
            foreach ($attribute as $attributeItem) {
                $this->groupByAttribute($attributeItem);
            }
        } else {
            if (isset($this->_joinFields[$attribute])) {
                $this->getSelect()->group($this->_getAttributeFieldName($attribute));
                return $this;
            }

            if (isset($this->_staticFields[$attribute])) {
                $this->getSelect()->group(sprintf('e.%s', $attribute));
                return $this;
            }

            if (isset($this->_joinAttributes[$attribute])) {
                $attrInstance = $this->_joinAttributes[$attribute]['attribute'];
                $entityField = $this->_getAttributeTableAlias($attribute) . '.' . $attrInstance->getAttributeCode();
            } else {
                $attrInstance = $this->getEntity()->getAttribute($attribute);
                $entityField = 'e.' . $attribute;
            }

            if ($attrInstance->getBackend()->isStatic()) {
                $this->getSelect()->group($entityField);
            } else {
                $this->_addAttributeJoin($attribute);
                $this->getSelect()->group($this->_getAttributeTableAlias($attribute).'.value');
            }
        }

        return $this;
    }

    /**
     * Add attribute from joined entity to select
     *
     * Examples:
     * ('billing_firstname', 'customer_address/firstname', 'default_billing')
     * ('billing_lastname', 'customer_address/lastname', 'default_billing')
     * ('shipping_lastname', 'customer_address/lastname', 'default_billing')
     * ('shipping_postalcode', 'customer_address/postalcode', 'default_shipping')
     * ('shipping_city', $cityAttribute, 'default_shipping')
     *
     * Developer is encouraged to use existing instances of attributes and entities
     * After first use of string entity name it will be cached in the collection
     *
     * @todo connect between joined attributes of same entity
     * @param string $alias alias for the joined attribute
     * @param string|Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param string $bind attribute of the main entity to link with joined $filter
     * @param string $filter primary key for the joined entity (entity_id default)
     * @param string $joinType inner|left
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function joinAttribute($alias, $attribute, $bind, $filter=null, $joinType='inner', $storeId=null)
    {
        // validate alias
        if (isset($this->_joinAttributes[$alias])) {
            throw Mage::exception(
                'Mage_Eav',
                Mage::helper('eav')->__('Invalid alias, already exists in joint attributes')
            );
        }

        // validate bind attribute
        if (is_string($bind)) {
            $bindAttribute = $this->getAttribute($bind);
        }

        if (!$bindAttribute || (!$bindAttribute->isStatic() && !$bindAttribute->getId())) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid foreign key'));
        }

        // try to explode combined entity/attribute if supplied
        if (is_string($attribute)) {
            $attrArr = explode('/', $attribute);
            if (isset($attrArr[1])) {
                $entity    = $attrArr[0];
                $attribute = $attrArr[1];
            }
        }

        // validate entity
        if (empty($entity) && $attribute instanceof Mage_Eav_Model_Entity_Attribute_Abstract) {
            $entity = $attribute->getEntity();
        } elseif (is_string($entity)) {
            // retrieve cached entity if possible
            if (isset($this->_joinEntities[$entity])) {
                $entity = $this->_joinEntities[$entity];
            } else {
                $entity = Mage::getModel('eav/entity')->setType($attrArr[0]);
            }
        }
        if (!$entity || !$entity->getTypeId()) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid entity type'));
        }

        // cache entity
        if (!isset($this->_joinEntities[$entity->getType()])) {
            $this->_joinEntities[$entity->getType()] = $entity;
        }

        // validate attribute
        if (is_string($attribute)) {
            $attribute = $entity->getAttribute($attribute);
        }
        if (!$attribute) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid attribute type'));
        }

        if (empty($filter)) {
            $filter = $entity->getEntityIdField();
        }

        // add joined attribute
        $this->_joinAttributes[$alias] = array(
            'bind'          => $bind,
            'bindAttribute' => $bindAttribute,
            'attribute'     => $attribute,
            'filter'        => $filter,
            'store_id'      => $storeId,
        );

        $this->_addAttributeJoin($alias, $joinType);

        return $this;
    }

    /**
     * Join regular table field and use an attribute as fk
     *
     * Examples:
     * ('country_name', 'directory/country_name', 'name', 'country_id=shipping_country',
     *      "{{table}}.language_code='en'", 'left')
     *
     * @param string $alias 'country_name'
     * @param string $table 'directory/country_name'
     * @param string $field 'name'
     * @param string $bind 'PK(country_id)=FK(shipping_country_id)'
     * @param string|array $cond "{{table}}.language_code='en'" OR array('language_code'=>'en')
     * @param string $joinType 'left'
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function joinField($alias, $table, $field, $bind, $cond=null, $joinType='inner')
    {
        // validate alias
        if (isset($this->_joinFields[$alias])) {
            throw Mage::exception(
                'Mage_Eav',
                Mage::helper('eav')->__('Joined field with this alias is already declared')
            );
        }

        // validate table
        if (strpos($table, '/')!==false) {
            $table = Mage::getSingleton('core/resource')->getTableName($table);
        }
        $tableAlias = $this->_getAttributeTableAlias($alias);

        // validate bind
        list($pk, $fk) = explode('=', $bind);
        $pk = $this->getSelect()->getAdapter()->quoteColumnAs(trim($pk), null);
        $bindCond = $tableAlias . '.' . trim($pk) . '=' . $this->_getAttributeFieldName(trim($fk));

        // process join type
        switch ($joinType) {
            case 'left':
                $joinMethod = 'joinLeft';
                break;

            default:
                $joinMethod = 'join';
        }
        $condArr = array($bindCond);

        // add where condition if needed
        if ($cond !== null) {
            if (is_array($cond)) {
                foreach ($cond as $k=>$v) {
                    $condArr[] = $this->_getConditionSql($tableAlias.'.'.$k, $v);
                }
            } else {
                $condArr[] = str_replace('{{table}}', $tableAlias, $cond);
            }
        }
        $cond = '(' . implode(') AND (', $condArr) . ')';

        // join table
        $this->getSelect()
            ->$joinMethod(array($tableAlias => $table), $cond, ($field ? array($alias=>$field) : array()));

        // save joined attribute
        $this->_joinFields[$alias] = array(
            'table' => $tableAlias,
            'field' => $field,
        );

        return $this;
    }

    /**
     * Join a table
     *
     * @param string|array $table
     * @param string $bind
     * @param string|array $fields
     * @param null|array $cond
     * @param string $joinType
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function joinTable($table, $bind, $fields = null, $cond = null, $joinType = 'inner')
    {
        $tableAlias = null;
        if (is_array($table)) {
            list($tableAlias, $tableName) = each($table);
        } else {
            $tableName = $table;
        }

        // validate table
        if (strpos($tableName, '/') !== false) {
            $tableName = Mage::getSingleton('core/resource')->getTableName($tableName);
        }
        if (empty($tableAlias)) {
            $tableAlias = $tableName;
        }

        // validate fields and aliases
        if (!$fields) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid joint fields'));
        }
        foreach ($fields as $alias=>$field) {
            if (isset($this->_joinFields[$alias])) {
                throw Mage::exception(
                    'Mage_Eav',
                    Mage::helper('eav')->__('A joint field with this alias (%s) is already declared', $alias)
                );
            }
            $this->_joinFields[$alias] = array(
                'table' => $tableAlias,
                'field' => $field,
            );
        }

        // validate bind
        list($pk, $fk) = explode('=', $bind);
        $bindCond = $tableAlias . '.' . $pk . '=' . $this->_getAttributeFieldName($fk);

        // process join type
        switch ($joinType) {
            case 'left':
                $joinMethod = 'joinLeft';
                break;

            default:
                $joinMethod = 'join';
        }
        $condArr = array($bindCond);

        // add where condition if needed
        if ($cond !== null) {
            if (is_array($cond)) {
                foreach ($cond as $k => $v) {
                    $condArr[] = $this->_getConditionSql($tableAlias.'.'.$k, $v);
                }
            } else {
                $condArr[] = str_replace('{{table}}', $tableAlias, $cond);
            }
        }
        $cond = '('.implode(') AND (', $condArr).')';

// join table
        $this->getSelect()->$joinMethod(array($tableAlias => $tableName), $cond, $fields);

        return $this;
    }

    /**
     * Remove an attribute from selection list
     *
     * @param string $attribute
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function removeAttributeToSelect($attribute = null)
    {
        if ($attribute === null) {
            $this->_selectAttributes = array();
        } else {
            unset($this->_selectAttributes[$attribute]);
        }
        return $this;
    }

    /**
     * Set collection page start and records to show
     *
     * @param integer $pageNum
     * @param integer $pageSize
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function setPage($pageNum, $pageSize)
    {
        $this->setCurPage($pageNum)
            ->setPageSize($pageSize);
        return $this;
    }

    /**
     * Load collection data into object items
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        Varien_Profiler::start('__EAV_COLLECTION_BEFORE_LOAD__');
        Mage::dispatchEvent('eav_collection_abstract_load_before', array('collection' => $this));
        $this->_beforeLoad();
        Varien_Profiler::stop('__EAV_COLLECTION_BEFORE_LOAD__');

        $this->_renderFilters();
        $this->_renderOrders();

        Varien_Profiler::start('__EAV_COLLECTION_LOAD_ENT__');
        $this->_loadEntities($printQuery, $logQuery);
        Varien_Profiler::stop('__EAV_COLLECTION_LOAD_ENT__');
        Varien_Profiler::start('__EAV_COLLECTION_LOAD_ATTR__');
        $this->_loadAttributes($printQuery, $logQuery);
        Varien_Profiler::stop('__EAV_COLLECTION_LOAD_ATTR__');

        Varien_Profiler::start('__EAV_COLLECTION_ORIG_DATA__');
        foreach ($this->_items as $item) {
            $item->setOrigData();
        }
        Varien_Profiler::stop('__EAV_COLLECTION_ORIG_DATA__');

        $this->_setIsLoaded();
        Varien_Profiler::start('__EAV_COLLECTION_AFTER_LOAD__');
        $this->_afterLoad();
        Varien_Profiler::stop('__EAV_COLLECTION_AFTER_LOAD__');
        return $this;
    }

    /**
     * Clone and reset collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getAllIdsSelect($limit = null, $offset = null)
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->columns('e.' . $this->getEntity()->getIdFieldName());
        $idsSelect->limit($limit, $offset);

        return $idsSelect;
    }

    /**
     * Retrive all ids for collection
     *
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
        return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limit, $offset), $this->_bindParams);
    }

    /**
     * Retrive all ids sql
     *
     * @return array
     */
    public function getAllIdsSql()
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->reset(Zend_Db_Select::GROUP);
        $idsSelect->columns('e.'.$this->getEntity()->getIdFieldName());

        return $idsSelect;
    }

    /**
     * Save all the entities in the collection
     *
     * @todo make batch save directly from collection
     */
    public function save()
    {
        foreach ($this->getItems() as $item) {
            $item->save();
        }
        return $this;
    }


    /**
     * Delete all the entities in the collection
     *
     * @todo make batch delete directly from collection
     */
    public function delete()
    {
        foreach ($this->getItems() as $k=>$item) {
            $this->getEntity()->delete($item);
            unset($this->_items[$k]);
        }
        return $this;
    }

    /**
     * Import 2D array into collection as objects
     *
     * If the imported items already exist, update the data for existing objects
     *
     * @param array $arr
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function importFromArray($arr)
    {
        $entityIdField = $this->getEntity()->getEntityIdField();
        foreach ($arr as $row) {
            $entityId = $row[$entityIdField];
            if (!isset($this->_items[$entityId])) {
                $this->_items[$entityId] = $this->getNewEmptyItem();
                $this->_items[$entityId]->setData($row);
            } else {
                $this->_items[$entityId]->addData($row);
            }
        }
        return $this;
    }

    /**
     * Get collection data as a 2D array
     *
     * @return array
     */
    public function exportToArray()
    {
        $result = array();
        $entityIdField = $this->getEntity()->getEntityIdField();
        foreach ($this->getItems() as $item) {
            $result[$item->getData($entityIdField)] = $item->getData();
        }
        return $result;
    }

    /**
     * Retreive row id field name
     *
     * @return string
     */
    public function getRowIdFieldName()
    {
        if ($this->_idFieldName === null) {
            $this->_setIdFieldName($this->getEntity()->getIdFieldName());
        }
        return $this->getIdFieldName();
    }

    /**
     * Set row id field name
     * @param string $fieldName
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function setRowIdFieldName($fieldName)
    {
        return $this->_setIdFieldName($fieldName);
    }

    /**
     * Load entities records into items
     *
     * @throws Exception
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function _loadEntities($printQuery = false, $logQuery = false)
    {
        if ($this->_pageSize) {
            $this->getSelect()->limitPage($this->getCurPage(), $this->_pageSize);
        }

        $this->printLogQuery($printQuery, $logQuery);

        try {
            /**
             * Prepare select query
             * @var string $query
             */
            $query = $this->_prepareSelect($this->getSelect());
            $rows = $this->_fetchAll($query);
        } catch (Exception $e) {
            Mage::printException($e, $query);
            $this->printLogQuery(true, true, $query);
            throw $e;
        }

        foreach ($rows as $v) {
            $object = $this->getNewEmptyItem()
                ->setData($v);
            $this->addItem($object);
            if (isset($this->_itemsById[$object->getId()])) {
                $this->_itemsById[$object->getId()][] = $object;
            } else {
                $this->_itemsById[$object->getId()] = array($object);
            }
        }

        return $this;
    }

    /**
     * Load attributes into loaded entities
     *
     * @throws Exception
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function _loadAttributes($printQuery = false, $logQuery = false)
    {
        if (empty($this->_items) || empty($this->_itemsById) || empty($this->_selectAttributes)) {
            return $this;
        }

        $entity = $this->getEntity();

        $tableAttributes = array();
        $attributeTypes  = array();
        foreach ($this->_selectAttributes as $attributeCode => $attributeId) {
            if (!$attributeId) {
                continue;
            }
            $attribute = Mage::getSingleton('eav/config')->getCollectionAttribute($entity->getType(), $attributeCode);
            if ($attribute && !$attribute->isStatic()) {
                $tableAttributes[$attribute->getBackendTable()][] = $attributeId;
                if (!isset($attributeTypes[$attribute->getBackendTable()])) {
                    $attributeTypes[$attribute->getBackendTable()] = $attribute->getBackendType();
                }
            }
        }

        $selects = array();
        foreach ($tableAttributes as $table=>$attributes) {
            $select = $this->_getLoadAttributesSelect($table, $attributes);
            $selects[$attributeTypes[$table]][] = $this->_addLoadAttributesSelectValues(
                $select,
                $table,
                $attributeTypes[$table]
            );
        }
        $selectGroups = Mage::getResourceHelper('eav')->getLoadAttributesSelectGroups($selects);
        foreach ($selectGroups as $selects) {
            if (!empty($selects)) {
                try {
                    $select = implode(' UNION ALL ', $selects);
                    $values = $this->getConnection()->fetchAll($select);
                } catch (Exception $e) {
                    Mage::printException($e, $select);
                    $this->printLogQuery(true, true, $select);
                    throw $e;
                }

                foreach ($values as $value) {
                    $this->_setItemAttributeValue($value);
                }
            }
        }

        return $this;
    }

    /**
     * Retrieve attributes load select
     *
     * @param   string $table
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getLoadAttributesSelect($table, $attributeIds = array())
    {
        if (empty($attributeIds)) {
            $attributeIds = $this->_selectAttributes;
        }
        $helper = Mage::getResourceHelper('eav');
        $entityIdField = $this->getEntity()->getEntityIdField();
        $select = $this->getConnection()->select()
            ->from($table, array($entityIdField, 'attribute_id'))
            ->where('entity_type_id =?', $this->getEntity()->getTypeId())
            ->where("$entityIdField IN (?)", array_keys($this->_itemsById))
            ->where('attribute_id IN (?)', $attributeIds);
        return $select;
    }

    /**
     * @param Varien_Db_Select $select
     * @param string $table
     * @param string $type
     * @return Varien_Db_Select
     */
    protected function _addLoadAttributesSelectValues($select, $table, $type)
    {
        $helper = Mage::getResourceHelper('eav');
        $select->columns(array(
            'value' => $helper->prepareEavAttributeValue($table. '.value', $type),
        ));

        return $select;
    }

    /**
     * Initialize entity ubject property value
     *
     * $valueInfo is _getLoadAttributesSelect fetch result row
     *
     * @param   array $valueInfo
     * @throws Mage_Eav_Exception
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _setItemAttributeValue($valueInfo)
    {
        $entityIdField  = $this->getEntity()->getEntityIdField();
        $entityId       = $valueInfo[$entityIdField];
        if (!isset($this->_itemsById[$entityId])) {
            throw Mage::exception('Mage_Eav',
                Mage::helper('eav')->__('Data integrity: No header row found for attribute')
            );
        }
        $attributeCode = array_search($valueInfo['attribute_id'], $this->_selectAttributes);
        if (!$attributeCode) {
            $attribute = Mage::getSingleton('eav/config')->getCollectionAttribute(
                $this->getEntity()->getType(),
                $valueInfo['attribute_id']
            );
            $attributeCode = $attribute->getAttributeCode();
        }

        foreach ($this->_itemsById[$entityId] as $object) {
            $object->setData($attributeCode, $valueInfo['value']);
        }

        return $this;
    }

    /**
     * Get alias for attribute value table
     *
     * @param string $attributeCode
     * @return string
     */
    protected function _getAttributeTableAlias($attributeCode)
    {
        return 'at_' . $attributeCode;
    }

    /**
     * Retreive attribute field name by attribute code
     *
     * @param string $attributeCode
     * @return string
     */
    protected function _getAttributeFieldName($attributeCode)
    {
        $attributeCode = trim($attributeCode);
        if (isset($this->_joinAttributes[$attributeCode]['condition_alias'])) {
            return $this->_joinAttributes[$attributeCode]['condition_alias'];
        }
        if (isset($this->_staticFields[$attributeCode])) {
            return sprintf('e.%s', $attributeCode);
        }
        if (isset($this->_joinFields[$attributeCode])) {
            $attr = $this->_joinFields[$attributeCode];
            return $attr['table'] ? $attr['table'] . '.' . $attr['field'] : $attr['field'];
        }

        $attribute = $this->getAttribute($attributeCode);
        if (!$attribute) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid attribute name: %s', $attributeCode));
        }

        if ($attribute->isStatic()) {
            if (isset($this->_joinAttributes[$attributeCode])) {
                $fieldName = $this->_getAttributeTableAlias($attributeCode) . '.' . $attributeCode;
            } else {
                $fieldName = 'e.' . $attributeCode;
            }
        } else {
            $fieldName = $this->_getAttributeTableAlias($attributeCode) . '.value';
        }

        return $fieldName;
    }

    /**
     * Add attribute value table to the join if it wasn't added previously
     *
     * @param   string $attributeCode
     * @param   string $joinType inner|left
     * @throws  Mage_Eav_Exception
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _addAttributeJoin($attributeCode, $joinType = 'inner')
    {
        if (!empty($this->_filterAttributes[$attributeCode])) {
            return $this;
        }

        $adapter = $this->getConnection();

        $attrTable = $this->_getAttributeTableAlias($attributeCode);
        if (isset($this->_joinAttributes[$attributeCode])) {
            $attribute      = $this->_joinAttributes[$attributeCode]['attribute'];
            $entity         = $attribute->getEntity();
            $entityIdField  = $entity->getEntityIdField();
            $fkName         = $this->_joinAttributes[$attributeCode]['bind'];
            $fkAttribute    = $this->_joinAttributes[$attributeCode]['bindAttribute'];
            $fkTable        = $this->_getAttributeTableAlias($fkName);

            if ($fkAttribute->getBackend()->isStatic()) {
                if (isset($this->_joinAttributes[$fkName])) {
                    $fk = $fkTable . '.' . $fkAttribute->getAttributeCode();
                } else {
                    $fk = 'e.' . $fkAttribute->getAttributeCode();
                }
            } else {
                $this->_addAttributeJoin($fkAttribute->getAttributeCode(), $joinType);
                $fk = $fkTable . '.value';
            }
            $pk = $attrTable . '.' . $this->_joinAttributes[$attributeCode]['filter'];
        } else {
            $entity         = $this->getEntity();
            $entityIdField  = $entity->getEntityIdField();
            $attribute      = $entity->getAttribute($attributeCode);
            $fk             = 'e.' . $entityIdField;
            $pk             = $attrTable . '.' . $entityIdField;
        }

        if (!$attribute) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid attribute name: %s', $attributeCode));
        }

        if ($attribute->getBackend()->isStatic()) {
            $attrFieldName = $attrTable . '.' . $attribute->getAttributeCode();
        } else {
            $attrFieldName = $attrTable . '.value';
        }

        $fk = $adapter->quoteColumnAs($fk, null);
        $pk = $adapter->quoteColumnAs($pk, null);

        $condArr = array("$pk = $fk");
        if (!$attribute->getBackend()->isStatic()) {
            $condArr[] = $this->getConnection()->quoteInto(
                $adapter->quoteColumnAs("$attrTable.attribute_id", null) . ' = ?', $attribute->getId());
        }

        /**
         * process join type
         */
        $joinMethod = ($joinType == 'left') ? 'joinLeft' : 'join';

        $this->_joinAttributeToSelect($joinMethod, $attribute, $attrTable, $condArr, $attributeCode, $attrFieldName);

        $this->removeAttributeToSelect($attributeCode);
        $this->_filterAttributes[$attributeCode] = $attribute->getId();

        /**
         * Fix double join for using same as filter
         */
        $this->_joinFields[$attributeCode] = array(
            'table' => '',
            'field' => $attrFieldName,
        );

        return $this;
    }

    /**
     * Adding join statement to collection select instance
     *
     * @param   string $method
     * @param   object $attribute
     * @param   string $tableAlias
     * @param   array $condition
     * @param   string $fieldCode
     * @param   string $fieldAlias
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _joinAttributeToSelect($method, $attribute, $tableAlias, $condition, $fieldCode, $fieldAlias)
    {
        $this->getSelect()->$method(
            array($tableAlias => $attribute->getBackend()->getTable()),
            '('.implode(') AND (', $condition).')',
            array($fieldCode => $fieldAlias)
        );
        return $this;
    }

    /**
     * Get condition sql for the attribute
     *
     * @see self::_getConditionSql
     * @param string $attribute
     * @param mixed $condition
     * @param string $joinType
     * @return string
     */
    protected function _getAttributeConditionSql($attribute, $condition, $joinType = 'inner')
    {
        if (isset($this->_joinFields[$attribute])) {

            return $this->_getConditionSql($this->_getAttributeFieldName($attribute), $condition);
        }
        if (isset($this->_staticFields[$attribute])) {
            return $this->_getConditionSql($this->getConnection()->quoteIdentifier('e.' . $attribute), $condition);
        }
        // process linked attribute
        if (isset($this->_joinAttributes[$attribute])) {
            $entity      = $this->getAttribute($attribute)->getEntity();
            $entityTable = $entity->getEntityTable();
        } else {
            $entity      = $this->getEntity();
            $entityTable = 'e';
        }

        if ($entity->isAttributeStatic($attribute)) {
            $conditionSql = $this->_getConditionSql(
                $this->getConnection()->quoteIdentifier('e.' . $attribute),
                $condition
            );
        } else {
            $this->_addAttributeJoin($attribute, $joinType);
            if (isset($this->_joinAttributes[$attribute]['condition_alias'])) {
                $field = $this->_joinAttributes[$attribute]['condition_alias'];
            } else {
                $field = $this->_getAttributeTableAlias($attribute) . '.value';

            }

            $conditionSql = $this->_getConditionSql($field, $condition);
        }

        return $conditionSql;
    }

    /**
     * Set sorting order
     *
     * $attribute can also be an array of attributes
     *
     * @param string|array $attribute
     * @param string $dir
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function setOrder($attribute, $dir = self::SORT_ORDER_ASC)
    {
        if (is_array($attribute)) {
            foreach ($attribute as $attr) {
                parent::setOrder($attr, $dir);
            }
        }
        return parent::setOrder($attribute, $dir);
    }

    /**
     * Retreive array of attributes
     *
     * @param array $arrAttributes
     * @return array
     */
    public function toArray($arrAttributes = array())
    {
        $arr = array();
        foreach ($this->_items as $k => $item) {
            $arr[$k] = $item->toArray($arrAttributes);
        }
        return $arr;
    }

    /**
     * Treat "order by" items as attributes to sort
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _renderOrders()
    {
        if (!$this->_isOrdersRendered) {
            foreach ($this->_orders as $attribute => $direction) {
                $this->addAttributeToSort($attribute, $direction);
            }
            $this->_isOrdersRendered = true;
        }
        return $this;
    }

    /**
     * After load method
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _afterLoad()
    {
        return $this;
    }

    /**
     * Reset collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _reset()
    {
        parent::_reset();

        $this->_selectEntityTypes = array();
        $this->_selectAttributes  = array();
        $this->_filterAttributes  = array();
        $this->_joinEntities      = array();
        $this->_joinAttributes    = array();
        $this->_joinFields        = array();

        return $this;
    }

    /**
     * Returns already loaded element ids
     *
     * return array
     */
    public function getLoadedIds()
    {
        return array_keys($this->_items);
    }

    /**
     * Prepare select for load
     *
     * @param Varien_Db_Select $select OPTIONAL
     * @return string
     */
    public function _prepareSelect(Varien_Db_Select $select)
    {
        if ($this->_useAnalyticFunction) {
            $helper = Mage::getResourceHelper('core');
            return $helper->getQueryUsingAnalyticFunction($select);
        }

        return (string)$select;
    }
}
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
 * Catalog EAV collection resource abstract model
 * Implement using diferent stores for retrieve attribute values
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Collection_Abstract extends Mage_Eav_Model_Entity_Collection_Abstract
{
    /**
     * Current scope (store Id)
     *
     * @var int
     */
    protected $_storeId;

    /**
     * Set store scope
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return Mage_Catalog_Model_Resource_Collection_Abstract
     */
    public function setStore($store)
    {
        $this->setStoreId(Mage::app()->getStore($store)->getId());
        return $this;
    }

    /**
     * Set store scope
     *
     * @param int|string|Mage_Core_Model_Store $storeId
     * @return Mage_Catalog_Model_Resource_Collection_Abstract
     */
    public function setStoreId($storeId)
    {
        if ($storeId instanceof Mage_Core_Model_Store) {
            $storeId = $storeId->getId();
        }
        $this->_storeId = (int)$storeId;
        return $this;
    }

    /**
     * Return current store id
     *
     * @return int
     */
    public function getStoreId()
    {
        if (is_null($this->_storeId)) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }
        return $this->_storeId;
    }

    /**
     * Retrieve default store id
     *
     * @return int
     */
    public function getDefaultStoreId()
    {
        return Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
    }

    /**
     * Retrieve attributes load select
     *
     * @param string $table
     * @param array|int $attributeIds
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getLoadAttributesSelect($table, $attributeIds = array())
    {
        if (empty($attributeIds)) {
            $attributeIds = $this->_selectAttributes;
        }
        $storeId = $this->getStoreId();

        if ($storeId) {

            $adapter        = $this->getConnection();
            $entityIdField  = $this->getEntity()->getEntityIdField();
            $joinCondition  = array(
                't_s.attribute_id = t_d.attribute_id',
                't_s.entity_id = t_d.entity_id',
                $adapter->quoteInto('t_s.store_id = ?', $storeId)
            );
            $select = $adapter->select()
                ->from(array('t_d' => $table), array($entityIdField, 'attribute_id'))
                ->joinLeft(
                    array('t_s' => $table),
                    implode(' AND ', $joinCondition),
                    array())
                ->where('t_d.entity_type_id = ?', $this->getEntity()->getTypeId())
                ->where("t_d.{$entityIdField} IN (?)", array_keys($this->_itemsById))
                ->where('t_d.attribute_id IN (?)', $attributeIds)
                ->where('t_d.store_id = ?', 0);
        } else {
            $select = parent::_getLoadAttributesSelect($table)
                ->where('store_id = ?', $this->getDefaultStoreId());
        }

        return $select;
    }

    /**
     * @param Varien_Db_Select $select
     * @param string $table
     * @param string $type
     * @return Varien_Db_Select
     */
    protected function _addLoadAttributesSelectValues($select, $table, $type)
    {
        $storeId = $this->getStoreId();
        if ($storeId) {
            $helper = Mage::getResourceHelper('eav');
            $adapter        = $this->getConnection();
            $valueExpr      = $adapter->getCheckSql(
                't_s.value_id IS NULL',
                $helper->prepareEavAttributeValue('t_d.value', $type),
                $helper->prepareEavAttributeValue('t_s.value', $type)
            );

            $select->columns(array(
                'default_value' => $helper->prepareEavAttributeValue('t_d.value', $type),
                'store_value'   => $helper->prepareEavAttributeValue('t_s.value', $type),
                'value'         => $valueExpr
            ));
        } else {
            $select = parent::_addLoadAttributesSelectValues($select, $table, $type);
        }
        return $select;
    }

    /**
     * Adding join statement to collection select instance
     *
     * @param string $method
     * @param object $attribute
     * @param string $tableAlias
     * @param array $condition
     * @param string $fieldCode
     * @param string $fieldAlias
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _joinAttributeToSelect($method, $attribute, $tableAlias, $condition, $fieldCode, $fieldAlias)
    {
        if (isset($this->_joinAttributes[$fieldCode]['store_id'])) {
            $store_id = $this->_joinAttributes[$fieldCode]['store_id'];
        } else {
            $store_id = $this->getStoreId();
        }

        $adapter = $this->getConnection();

        if ($store_id != $this->getDefaultStoreId() && !$attribute->isScopeGlobal()) {
            /**
             * Add joining default value for not default store
             * if value for store is null - we use default value
             */
            $defCondition = '('.implode(') AND (', $condition).')';
            $defAlias     = $tableAlias . '_default';
            $defAlias     = $this->getConnection()->getTableName($defAlias);
            $defFieldAlias= str_replace($tableAlias, $defAlias, $fieldAlias);
            $tableAlias   = $this->getConnection()->getTableName($tableAlias);

            $defCondition = str_replace($tableAlias, $defAlias, $defCondition);
            $defCondition.= $adapter->quoteInto(
                " AND " . $adapter->quoteColumnAs("$defAlias.store_id", null) . " = ?",
                $this->getDefaultStoreId());

            $this->getSelect()->$method(
                array($defAlias => $attribute->getBackend()->getTable()),
                $defCondition,
                array()
            );

            $method = 'joinLeft';
            $fieldAlias = $this->getConnection()->getCheckSql("{$tableAlias}.value_id > 0",
                $fieldAlias, $defFieldAlias);
            $this->_joinAttributes[$fieldCode]['condition_alias'] = $fieldAlias;
            $this->_joinAttributes[$fieldCode]['attribute']       = $attribute;
        } else {
            $store_id = $this->getDefaultStoreId();
        }
        $condition[] = $adapter->quoteInto(
            $adapter->quoteColumnAs("$tableAlias.store_id", null) . ' = ?', $store_id);
        return parent::_joinAttributeToSelect($method, $attribute, $tableAlias, $condition, $fieldCode, $fieldAlias);
    }
}
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
 * Catalog Config Resource Model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Config extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * catalog_product entity type id
     *
     * @var int
     */
    protected $_entityTypeId;

    /**
     * Store id
     *
     * @var int
     */
    protected $_storeId          = null;

    /**
     * Initialize connection
     *
     */
    protected function _construct()
    {
        $this->_init('eav/attribute', 'attribute_id');
    }

    /**
     * Set store id
     *
     * @param integer $storeId
     * @return Mage_Catalog_Model_Resource_Config
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = (int)$storeId;
        return $this;
    }

    /**
     * Return store id.
     * If is not set return current app store
     *
     * @return integer
     */
    public function getStoreId()
    {
        if ($this->_storeId === null) {
            return Mage::app()->getStore()->getId();
        }
        return $this->_storeId;
    }

    /**
     * Retrieve catalog_product entity type id
     *
     * @return int
     */
    public function getEntityTypeId()
    {
        if ($this->_entityTypeId === null) {
            $this->_entityTypeId = Mage::getSingleton('eav/config')->getEntityType(Mage_Catalog_Model_Product::ENTITY)->getId();
        }
        return $this->_entityTypeId;
    }

    /**
     * Retrieve Product Attributes Used in Catalog Product listing
     *
     * @return array
     */
    public function getAttributesUsedInListing()
    {
        $adapter = $this->_getReadAdapter();
        $storeLabelExpr = $adapter->getCheckSql('al.value IS NOT NULL', 'al.value', 'main_table.frontend_label');

        $select  = $adapter->select()
            ->from(array('main_table' => $this->getTable('eav/attribute')))
            ->join(
                array('additional_table' => $this->getTable('catalog/eav_attribute')),
                'main_table.attribute_id = additional_table.attribute_id'
            )
            ->joinLeft(
                array('al' => $this->getTable('eav/attribute_label')),
                'al.attribute_id = main_table.attribute_id AND al.store_id = ' . (int)$this->getStoreId(),
                array('store_label' => $storeLabelExpr)
            )
            ->where('main_table.entity_type_id = ?', (int)$this->getEntityTypeId())
            ->where('additional_table.used_in_product_listing = ?', 1);

        return $adapter->fetchAll($select);
    }

    /**
     * Retrieve Used Product Attributes for Catalog Product Listing Sort By
     *
     * @return array
     */
    public function getAttributesUsedForSortBy()
    {
        $adapter = $this->_getReadAdapter();
        $storeLabelExpr = $adapter->getCheckSql('al.value IS NULL', 'main_table.frontend_label','al.value');
        $select = $adapter->select()
            ->from(array('main_table' => $this->getTable('eav/attribute')))
            ->join(
                array('additional_table' => $this->getTable('catalog/eav_attribute')),
                'main_table.attribute_id = additional_table.attribute_id',
                array()
            )
            ->joinLeft(
                array('al' => $this->getTable('eav/attribute_label')),
                'al.attribute_id = main_table.attribute_id AND al.store_id = ' . (int)$this->getStoreId(),
                array('store_label' => $storeLabelExpr)
            )
            ->where('main_table.entity_type_id = ?', (int)$this->getEntityTypeId())
            ->where('additional_table.used_for_sort_by = ?', 1);

        return $adapter->fetchAll($select);
    }
}
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
 * Catalog attribute model
 *
 * @method Mage_Catalog_Model_Resource_Attribute _getResource()
 * @method Mage_Catalog_Model_Resource_Attribute getResource()
 * @method Mage_Catalog_Model_Resource_Eav_Attribute getFrontendInputRenderer()
 * @method string setFrontendInputRenderer(string $value)
 * @method int setIsGlobal(int $value)
 * @method Mage_Catalog_Model_Resource_Eav_Attribute getIsVisible()
 * @method int setIsVisible(int $value)
 * @method Mage_Catalog_Model_Resource_Eav_Attribute getIsSearchable()
 * @method int setIsSearchable(int $value)
 * @method Mage_Catalog_Model_Resource_Eav_Attribute getSearchWeight()
 * @method int setSearchWeight(int $value)
 * @method Mage_Catalog_Model_Resource_Eav_Attribute getIsFilterable()
 * @method int setIsFilterable(int $value)
 * @method Mage_Catalog_Model_Resource_Eav_Attribute getIsComparable()
 * @method int setIsComparable(int $value)
 * @method int setIsVisibleOnFront(int $value)
 * @method Mage_Catalog_Model_Resource_Eav_Attribute getIsHtmlAllowedOnFront()
 * @method int setIsHtmlAllowedOnFront(int $value)
 * @method Mage_Catalog_Model_Resource_Eav_Attribute getIsUsedForPriceRules()
 * @method int setIsUsedForPriceRules(int $value)
 * @method Mage_Catalog_Model_Resource_Eav_Attribute getIsFilterableInSearch()
 * @method int setIsFilterableInSearch(int $value)
 * @method Mage_Catalog_Model_Resource_Eav_Attribute getUsedInProductListing()
 * @method int setUsedInProductListing(int $value)
 * @method Mage_Catalog_Model_Resource_Eav_Attribute getUsedForSortBy()
 * @method int setUsedForSortBy(int $value)
 * @method Mage_Catalog_Model_Resource_Eav_Attribute getIsConfigurable()
 * @method int setIsConfigurable(int $value)
 * @method string setApplyTo(string $value)
 * @method Mage_Catalog_Model_Resource_Eav_Attribute getIsVisibleInAdvancedSearch()
 * @method int setIsVisibleInAdvancedSearch(int $value)
 * @method Mage_Catalog_Model_Resource_Eav_Attribute getPosition()
 * @method int setPosition(int $value)
 * @method Mage_Catalog_Model_Resource_Eav_Attribute getIsWysiwygEnabled()
 * @method int setIsWysiwygEnabled(int $value)
 * @method Mage_Catalog_Model_Resource_Eav_Attribute getIsUsedForPromoRules()
 * @method int setIsUsedForPromoRules(int $value)
 * @method Mage_Catalog_Model_Resource_Eav_Attribute getIsUsedForCustomerSegment()
 * @method int setIsUsedForCustomerSegment(int $value)
 * @method Mage_Catalog_Model_Resource_Eav_Attribute getIsUsedForTargetRules()
 * @method int setIsUsedForTargetRules(int $value)
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Eav_Attribute extends Mage_Eav_Model_Entity_Attribute
{
    const SCOPE_STORE                           = 0;
    const SCOPE_GLOBAL                          = 1;
    const SCOPE_WEBSITE                         = 2;

    const MODULE_NAME                           = 'Mage_Catalog';
    const ENTITY                                = 'catalog_eav_attribute';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix                     = 'catalog_entity_attribute';
    /**
     * Event object name
     *
     * @var string
     */
    protected $_eventObject                     = 'attribute';

    /**
     * Array with labels
     *
     * @var array
     */
    static protected $_labels                   = null;

    protected function _construct()
    {
        $this->_init('catalog/attribute');
    }

    /**
     * Processing object before save data
     *
     * @throws Mage_Core_Exception
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $this->setData('modulePrefix', self::MODULE_NAME);
        if (isset($this->_origData['is_global'])) {
            if (!isset($this->_data['is_global'])) {
                $this->_data['is_global'] = self::SCOPE_GLOBAL;
            }
            if (($this->_data['is_global'] != $this->_origData['is_global'])
                && $this->_getResource()->isUsedBySuperProducts($this)) {
                Mage::throwException(Mage::helper('catalog')->__('Scope must not be changed, because the attribute is used in configurable products.'));
            }
        }
        if ($this->getFrontendInput() == 'price') {
            if (!$this->getBackendModel()) {
                $this->setBackendModel('catalog/product_attribute_backend_price');
            }
        }
        if ($this->getFrontendInput() == 'textarea') {
            if ($this->getIsWysiwygEnabled()) {
                $this->setIsHtmlAllowedOnFront(1);
            }
        }
        return parent::_beforeSave();
    }

    /**
     * Processing object after save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterSave()
    {
        /**
         * Fix saving attribute in admin
         */
        Mage::getSingleton('eav/config')->clear();

        return parent::_afterSave();
    }

    /**
     * Register indexing event before delete catalog eav attribute
     *
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    protected function _beforeDelete()
    {
        if ($this->_getResource()->isUsedBySuperProducts($this)) {
            Mage::throwException(Mage::helper('catalog')->__('This attribute is used in configurable products.'));
        }
        Mage::getSingleton('index/indexer')->logEvent(
            $this, self::ENTITY, Mage_Index_Model_Event::TYPE_DELETE
        );
        return parent::_beforeDelete();
    }

    /**
     * Init indexing process after catalog eav attribute delete commit
     *
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    protected function _afterDeleteCommit()
    {
        parent::_afterDeleteCommit();
        Mage::getSingleton('index/indexer')->indexEvents(
            self::ENTITY, Mage_Index_Model_Event::TYPE_DELETE
        );
        return $this;
    }

    /**
     * Return is attribute global
     *
     * @return integer
     */
    public function getIsGlobal()
    {
        return $this->_getData('is_global');
    }

    /**
     * Retrieve attribute is global scope flag
     *
     * @return bool
     */
    public function isScopeGlobal()
    {
        return $this->getIsGlobal() == self::SCOPE_GLOBAL;
    }

    /**
     * Retrieve attribute is website scope website
     *
     * @return bool
     */
    public function isScopeWebsite()
    {
        return $this->getIsGlobal() == self::SCOPE_WEBSITE;
    }

    /**
     * Retrieve attribute is store scope flag
     *
     * @return bool
     */
    public function isScopeStore()
    {
        return !$this->isScopeGlobal() && !$this->isScopeWebsite();
    }

    /**
     * Retrieve store id
     *
     * @return int
     */
    public function getStoreId()
    {
        $dataObject = $this->getDataObject();
        if ($dataObject) {
            return $dataObject->getStoreId();
        }
        return $this->getData('store_id');
    }

    /**
     * Retrieve apply to products array
     * Return empty array if applied to all products
     *
     * @return array
     */
    public function getApplyTo()
    {
        if ($this->getData('apply_to')) {
            if (is_array($this->getData('apply_to'))) {
                return $this->getData('apply_to');
            }
            return explode(',', $this->getData('apply_to'));
        } else {
            return array();
        }
    }

    /**
     * Retrieve source model
     *
     * @return Mage_Eav_Model_Entity_Attribute_Source_Abstract
     */
    public function getSourceModel()
    {
        $model = $this->getData('source_model');
        if (empty($model)) {
            if ($this->getBackendType() == 'int' && $this->getFrontendInput() == 'select') {
                return $this->_getDefaultSourceModel();
            }
        }
        return $model;
    }

    /**
     * Check is allow for rule condition
     *
     * @return bool
     */
    public function isAllowedForRuleCondition()
    {
        $allowedInputTypes = array('text', 'multiselect', 'textarea', 'date', 'datetime', 'select', 'boolean', 'price');
        return $this->getIsVisible() && in_array($this->getFrontendInput(), $allowedInputTypes);
    }

    /**
     * Retrieve don't translated frontend label
     *
     * @return string
     */
    public function getFrontendLabel()
    {
        return $this->_getData('frontend_label');
    }

    /**
     * Get Attribute translated label for store
     *
     * @deprecated
     * @return string
     */
    protected function _getLabelForStore()
    {
        return $this->getFrontendLabel();
    }

    /**
     * Initialize store Labels for attributes
     *
     * @deprecated
     * @param int $storeId
     */
    public static function initLabels($storeId = null)
    {
        if (is_null(self::$_labels)) {
            if (is_null($storeId)) {
                $storeId = Mage::app()->getStore()->getId();
            }
            $attributeLabels = array();
            $attributes = Mage::getResourceSingleton('catalog/product')->getAttributesByCode();
            foreach ($attributes as $attribute) {
                if (strlen($attribute->getData('frontend_label')) > 0) {
                    $attributeLabels[] = $attribute->getData('frontend_label');
                }
            }

            self::$_labels = Mage::app()->getTranslator()->getResource()
                ->getTranslationArrayByStrings($attributeLabels, $storeId);
        }
    }

    /**
     * Get default attribute source model
     *
     * @return string
     */
    public function _getDefaultSourceModel()
    {
        return 'eav/entity_attribute_source_table';
    }

    /**
     * Check is an attribute used in EAV index
     *
     * @return bool
     */
    public function isIndexable()
    {
        // exclude price attribute
        if ($this->getAttributeCode() == 'price') {
            return false;
        }

        if (!$this->getIsFilterableInSearch() && !$this->getIsVisibleInAdvancedSearch() && !$this->getIsFilterable()) {
            return false;
        }

        $backendType    = $this->getBackendType();
        $frontendInput  = $this->getFrontendInput();

        if ($backendType == 'int' && $frontendInput == 'select') {
            return true;
        } else if ($backendType == 'varchar' && $frontendInput == 'multiselect') {
            return true;
        } else if ($backendType == 'decimal') {
            return true;
        }

        return false;
    }

    /**
     * Retrieve index type for indexable attribute
     *
     * @return string|false
     */
    public function getIndexType()
    {
        if (!$this->isIndexable()) {
            return false;
        }
        if ($this->getBackendType() == 'decimal') {
            return 'decimal';
        }

        return 'source';
    }

    /**
     * Callback function which called after transaction commit in resource model
     *
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    public function afterCommitCallback()
    {
        parent::afterCommitCallback();

        /** @var \Mage_Index_Model_Indexer $indexer */
        $indexer = Mage::getSingleton('index/indexer');
        $indexer->processEntityAction($this, self::ENTITY, Mage_Index_Model_Event::TYPE_SAVE);

        return $this;
    }
}
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
 * Product entity resource model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Product extends Mage_Catalog_Model_Resource_Abstract
{
    /**
     * Product to website linkage table
     *
     * @var string
     */
    protected $_productWebsiteTable;

    /**
     * Product to category linkage table
     *
     * @var string
     */
    protected $_productCategoryTable;

    /**
     * Initialize resource
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType(Mage_Catalog_Model_Product::ENTITY)
             ->setConnection('catalog_read', 'catalog_write');
        $this->_productWebsiteTable  = $this->getTable('catalog/product_website');
        $this->_productCategoryTable = $this->getTable('catalog/category_product');
    }

    /**
     * Default product attributes
     *
     * @return array
     */
    protected function _getDefaultAttributes()
    {
        return array('entity_id', 'entity_type_id', 'attribute_set_id', 'type_id', 'created_at', 'updated_at');
    }

    /**
     * Retrieve product website identifiers
     *
     * @param Mage_Catalog_Model_Product|int $product
     * @return array
     */
    public function getWebsiteIds($product)
    {
        $adapter = $this->_getReadAdapter();

        if ($product instanceof Mage_Catalog_Model_Product) {
            $productId = $product->getId();
        } else {
            $productId = $product;
        }

        $select = $adapter->select()
            ->from($this->_productWebsiteTable, 'website_id')
            ->where('product_id = ?', (int)$productId);

        return $adapter->fetchCol($select);
    }

    /**
     * Retrieve product website identifiers by product identifiers
     *
     * @param   array $productIds
     * @return  array
     */
    public function getWebsiteIdsByProductIds($productIds)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from($this->_productWebsiteTable, array('product_id', 'website_id'))
            ->where('product_id IN (?)', $productIds);
        $productsWebsites = array();
        foreach ($this->_getWriteAdapter()->fetchAll($select) as $productInfo) {
            $productId = $productInfo['product_id'];
            if (!isset($productsWebsites[$productId])) {
                $productsWebsites[$productId] = array();
            }
            $productsWebsites[$productId][] = $productInfo['website_id'];

        }

        return $productsWebsites;
    }

    /**
     * Retrieve product category identifiers
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getCategoryIds($product)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from($this->_productCategoryTable, 'category_id')
            ->where('product_id = ?', (int)$product->getId());

        return $adapter->fetchCol($select);
    }

    /**
     * Get product identifier by sku
     *
     * @param string $sku
     * @return int|false
     */
    public function getIdBySku($sku)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from($this->getEntityTable(), 'entity_id')
            ->where('sku = :sku');

        $bind = array(':sku' => (string)$sku);

        return $adapter->fetchOne($select, $bind);
    }

    /**
     * Process product data before save
     *
     * @param Varien_Object $object
     * @return Mage_Catalog_Model_Resource_Product
     */
    protected function _beforeSave(Varien_Object $object)
    {
        /**
         * Try detect product id by sku if id is not declared
         */
        if (!$object->getId() && $object->getSku()) {
            $object->setId($this->getIdBySku($object->getSku()));
        }

        /**
         * Check if declared category ids in object data.
         */
        if ($object->hasCategoryIds()) {
            $categoryIds = Mage::getResourceSingleton('catalog/category')->verifyIds(
                $object->getCategoryIds()
            );
            $object->setCategoryIds($categoryIds);
        }

        return parent::_beforeSave($object);
    }

    /**
     * Save data related with product
     *
     * @param Varien_Object $product
     * @return Mage_Catalog_Model_Resource_Product
     */
    protected function _afterSave(Varien_Object $product)
    {
        $this->_saveWebsiteIds($product)
            ->_saveCategories($product);

        return parent::_afterSave($product);
    }

    /**
     * Save product website relations
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Resource_Product
     */
    protected function _saveWebsiteIds($product)
    {
        $websiteIds = $product->getWebsiteIds();
        $oldWebsiteIds = array();

        $product->setIsChangedWebsites(false);

        $adapter = $this->_getWriteAdapter();

        $oldWebsiteIds = $this->getWebsiteIds($product);

        $insert = array_diff($websiteIds, $oldWebsiteIds);
        $delete = array_diff($oldWebsiteIds, $websiteIds);

        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $websiteId) {
                $data[] = array(
                    'product_id' => (int)$product->getId(),
                    'website_id' => (int)$websiteId
                );
            }
            $adapter->insertMultiple($this->_productWebsiteTable, $data);
        }

        if (!empty($delete)) {
            foreach ($delete as $websiteId) {
                $condition = array(
                    'product_id = ?' => (int)$product->getId(),
                    'website_id = ?' => (int)$websiteId,
                );

                $adapter->delete($this->_productWebsiteTable, $condition);
            }
        }

        if (!empty($insert) || !empty($delete)) {
            $product->setIsChangedWebsites(true);
        }

        return $this;
    }

    /**
     * Save product category relations
     *
     * @param Varien_Object $object
     * @return Mage_Catalog_Model_Resource_Product
     */
    protected function _saveCategories(Varien_Object $object)
    {
        /**
         * If category ids data is not declared we haven't do manipulations
         */
        if (!$object->hasCategoryIds()) {
            return $this;
        }
        $categoryIds = $object->getCategoryIds();
        $oldCategoryIds = $this->getCategoryIds($object);

        $object->setIsChangedCategories(false);

        $insert = array_diff($categoryIds, $oldCategoryIds);
        $delete = array_diff($oldCategoryIds, $categoryIds);

        $write = $this->_getWriteAdapter();
        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $categoryId) {
                if (empty($categoryId)) {
                    continue;
                }
                $data[] = array(
                    'category_id' => (int)$categoryId,
                    'product_id'  => (int)$object->getId(),
                    'position'    => 1
                );
            }
            if ($data) {
                $write->insertMultiple($this->_productCategoryTable, $data);
            }
        }

        if (!empty($delete)) {
            foreach ($delete as $categoryId) {
                $where = array(
                    'product_id = ?'  => (int)$object->getId(),
                    'category_id = ?' => (int)$categoryId,
                );

                $write->delete($this->_productCategoryTable, $where);
            }
        }

        if (!empty($insert) || !empty($delete)) {
            $object->setAffectedCategoryIds(array_merge($insert, $delete));
            $object->setIsChangedCategories(true);
        }

        return $this;
    }

    /**
     * Refresh Product Enabled Index
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Resource_Product
     */
    public function refreshIndex($product)
    {
        $writeAdapter = $this->_getWriteAdapter();

        /**
         * Ids of all categories where product is assigned (not related with store)
         */
        $categoryIds = $product->getCategoryIds();

        /**
         * Clear previous index data related with product
         */
        $condition = array('product_id = ?' => (int)$product->getId());
        $writeAdapter->delete($this->getTable('catalog/category_product_index'), $condition);

        /** @var $categoryObject Mage_Catalog_Model_Resource_Category */
        $categoryObject = Mage::getResourceSingleton('catalog/category');
        if (!empty($categoryIds)) {
            $categoriesSelect = $writeAdapter->select()
                ->from($this->getTable('catalog/category'))
                ->where('entity_id IN (?)', $categoryIds);

            $categoriesInfo = $writeAdapter->fetchAll($categoriesSelect);

            $indexCategoryIds = array();
            foreach ($categoriesInfo as $categoryInfo) {
                $ids = explode('/', $categoryInfo['path']);
                $ids[] = $categoryInfo['entity_id'];
                $indexCategoryIds = array_merge($indexCategoryIds, $ids);
            }

            $indexCategoryIds   = array_unique($indexCategoryIds);
            $indexProductIds    = array($product->getId());

           $categoryObject->refreshProductIndex($indexCategoryIds, $indexProductIds);
        } else {
            $websites = $product->getWebsiteIds();

            if ($websites) {
                $storeIds = array();

                foreach ($websites as $websiteId) {
                    $website  = Mage::app()->getWebsite($websiteId);
                    $storeIds = array_merge($storeIds, $website->getStoreIds());
                }

                $categoryObject->refreshProductIndex(array(), array($product->getId()), $storeIds);
            }
        }

        /**
         * Refresh enabled products index (visibility state)
         */
        $this->refreshEnabledIndex(null, $product);

        return $this;
    }

    /**
     * Refresh index for visibility of enabled product in store
     * if store parameter is null - index will refreshed for all stores
     * if product parameter is null - idex will be refreshed for all products
     *
     * @param Mage_Core_Model_Store $store
     * @param Mage_Catalog_Model_Product $product
     * @throws Mage_Core_Exception
     * @return Mage_Catalog_Model_Resource_Product
     */
    public function refreshEnabledIndex($store = null, $product = null)
    {
        $statusAttribute        = $this->getAttribute('status');
        $visibilityAttribute    = $this->getAttribute('visibility');
        $statusAttributeId      = $statusAttribute->getId();
        $visibilityAttributeId  = $visibilityAttribute->getId();
        $statusTable            = $statusAttribute->getBackend()->getTable();
        $visibilityTable        = $visibilityAttribute->getBackend()->getTable();

        $adapter = $this->_getWriteAdapter();

        $select = $adapter->select();
        $condition = array();

        $indexTable = $this->getTable('catalog/product_enabled_index');
        if (is_null($store) && is_null($product)) {
            Mage::throwException(
                Mage::helper('catalog')->__('To reindex the enabled product(s), the store or product must be specified')
            );
        } elseif (is_null($product) || is_array($product)) {
            $storeId    = $store->getId();
            $websiteId  = $store->getWebsiteId();

            if (is_array($product) && !empty($product)) {
                $condition[] = $adapter->quoteInto('product_id IN (?)', $product);
            }

            $condition[] = $adapter->quoteInto('store_id = ?', $storeId);

            $selectFields = array(
                't_v_default.entity_id',
                new Zend_Db_Expr($storeId),
                $adapter->getCheckSql('t_v.value_id > 0', 't_v.value', 't_v_default.value'),
            );

            $select->joinInner(
                array('w' => $this->getTable('catalog/product_website')),
                $adapter->quoteInto(
                    'w.product_id = t_v_default.entity_id AND w.website_id = ?', $websiteId
                ),
                array()
            );
        } elseif ($store === null) {
            foreach ($product->getStoreIds() as $storeId) {
                $store = Mage::app()->getStore($storeId);
                $this->refreshEnabledIndex($store, $product);
            }
            return $this;
        } else {
            $productId = is_numeric($product) ? $product : $product->getId();
            $storeId   = is_numeric($store) ? $store : $store->getId();

            $condition = array(
                'product_id = ?' => (int)$productId,
                'store_id   = ?' => (int)$storeId,
            );

            $selectFields = array(
                new Zend_Db_Expr($productId),
                new Zend_Db_Expr($storeId),
                $adapter->getCheckSql('t_v.value_id > 0', 't_v.value', 't_v_default.value')
            );

            $select->where('t_v_default.entity_id = ?', $productId);
        }

        $adapter->delete($indexTable, $condition);

        $select->from(array('t_v_default' => $visibilityTable), $selectFields);

        $visibilityTableJoinCond = array(
            't_v.entity_id = t_v_default.entity_id',
            $adapter->quoteInto('t_v.attribute_id = ?', $visibilityAttributeId),
            $adapter->quoteInto('t_v.store_id     = ?', $storeId),
        );

        $select->joinLeft(
            array('t_v' => $visibilityTable),
            implode(' AND ', $visibilityTableJoinCond),
            array()
        );

        $defaultStatusJoinCond = array(
            't_s_default.entity_id = t_v_default.entity_id',
            't_s_default.store_id = 0',
            $adapter->quoteInto('t_s_default.attribute_id = ?', $statusAttributeId),
        );

        $select->joinInner(
            array('t_s_default' => $statusTable),
            implode(' AND ', $defaultStatusJoinCond),
            array()
        );


        $statusJoinCond = array(
            't_s.entity_id = t_v_default.entity_id',
            $adapter->quoteInto('t_s.store_id     = ?', $storeId),
            $adapter->quoteInto('t_s.attribute_id = ?', $statusAttributeId),
        );

        $select->joinLeft(
            array('t_s' => $statusTable),
            implode(' AND ', $statusJoinCond),
            array()
        );

        $valueCondition = $adapter->getCheckSql('t_s.value_id > 0', 't_s.value', 't_s_default.value');

        $select->where('t_v_default.attribute_id = ?', $visibilityAttributeId)
            ->where('t_v_default.store_id = ?', 0)
            ->where(sprintf('%s = ?', $valueCondition), Mage_Catalog_Model_Product_Status::STATUS_ENABLED);

        if (is_array($product) && !empty($product)) {
            $select->where('t_v_default.entity_id IN (?)', $product);
        }

        $adapter->query($adapter->insertFromSelect($select, $indexTable));


        return $this;
    }

    /**
     * Get collection of product categories
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    public function getCategoryCollection($product)
    {
        $collection = Mage::getResourceModel('catalog/category_collection')
            ->joinField('product_id',
                'catalog/category_product',
                'product_id',
                'category_id = entity_id',
                null)
            ->addFieldToFilter('product_id', (int)$product->getId());
        return $collection;
    }

    /**
     * Retrieve category ids where product is available
     *
     * @param Mage_Catalog_Model_Product $object
     * @return array
     */
    public function getAvailableInCategories($object)
    {
        // is_parent=1 ensures that we'll get only category IDs those are direct parents of the product, instead of
        // fetching all parent IDs, including those are higher on the tree
        $select = $this->_getReadAdapter()->select()->distinct()
            ->from($this->getTable('catalog/category_product_index'), array('category_id'))
            ->where('product_id = ? AND is_parent = 1', (int)$object->getEntityId());

        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Get default attribute source model
     *
     * @return string
     */
    public function getDefaultAttributeSourceModel()
    {
        return 'eav/entity_attribute_source_table';
    }

    /**
     * Check availability display product in category
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int $categoryId
     * @return string
     */
    public function canBeShowInCategory($product, $categoryId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('catalog/category_product_index'), 'product_id')
            ->where('product_id = ?', (int)$product->getId())
            ->where('category_id = ?', (int)$categoryId);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Duplicate product store values
     *
     * @param int $oldId
     * @param int $newId
     * @return Mage_Catalog_Model_Resource_Product
     */
    public function duplicate($oldId, $newId)
    {
        $adapter = $this->_getWriteAdapter();
        $eavTables = array('datetime', 'decimal', 'int', 'text', 'varchar');

        $adapter = $this->_getWriteAdapter();

        // duplicate EAV store values
        foreach ($eavTables as $suffix) {
            $tableName = $this->getTable(array('catalog/product', $suffix));

            $select = $adapter->select()
                ->from($tableName, array(
                    'entity_type_id',
                    'attribute_id',
                    'store_id',
                    'entity_id' => new Zend_Db_Expr($adapter->quote($newId)),
                    'value'
                ))
                ->where('entity_id = ?', $oldId)
                ->where('store_id > ?', 0);

            $adapter->query($adapter->insertFromSelect(
                $select,
                $tableName,
                array(
                    'entity_type_id',
                    'attribute_id',
                    'store_id',
                    'entity_id',
                    'value'
                ),
                Varien_Db_Adapter_Interface::INSERT_ON_DUPLICATE
            ));
        }

        // set status as disabled
        $statusAttribute      = $this->getAttribute('status');
        $statusAttributeId    = $statusAttribute->getAttributeId();
        $statusAttributeTable = $statusAttribute->getBackend()->getTable();
        $updateCond[]         = 'store_id > 0';
        $updateCond[]         = $adapter->quoteInto('entity_id = ?', $newId);
        $updateCond[]         = $adapter->quoteInto('attribute_id = ?', $statusAttributeId);
        $adapter->update(
            $statusAttributeTable,
            array('value' => Mage_Catalog_Model_Product_Status::STATUS_DISABLED),
            $updateCond
        );

        return $this;
    }

    /**
     * Get SKU through product identifiers
     *
     * @param  array $productIds
     * @return array
     */
    public function getProductsSku(array $productIds)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('catalog/product'), array('entity_id', 'sku'))
            ->where('entity_id IN (?)', $productIds);
        return $this->_getReadAdapter()->fetchAll($select);
    }

    /**
     * @deprecated after 1.4.2.0
     * @param  $object Mage_Catalog_Model_Product
     * @return array
     */
    public function getParentProductIds($object)
    {
        return array();
    }

    /**
     * Retrieve product entities info
     *
     * @param  array|string|null $columns
     * @return array
     */
    public function getProductEntitiesInfo($columns = null)
    {
        if (!empty($columns) && is_string($columns)) {
            $columns = array($columns);
        }
        if (empty($columns) || !is_array($columns)) {
            $columns = $this->_getDefaultAttributes();
        }

        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getTable('catalog/product'), $columns);

        return $adapter->fetchAll($select);
    }

    /**
     * Return assigned images for specific stores
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int|array $storeIds
     * @return array
     *
     */
    public function getAssignedImages($product, $storeIds)
    {
        if (!is_array($storeIds)) {
            $storeIds = array($storeIds);
        }

        $mainTable = $product->getResource()->getAttribute('image')
            ->getBackend()
            ->getTable();
        $read      = $this->_getReadAdapter();
        $select    = $read->select()
            ->from(
                array('images' => $mainTable),
                array('value as filepath', 'store_id')
            )
            ->joinLeft(
                array('attr' => $this->getTable('eav/attribute')),
                'images.attribute_id = attr.attribute_id',
                array('attribute_code')
            )
            ->where('entity_id = ?', $product->getId())
            ->where('store_id IN (?)', $storeIds)
            ->where('attribute_code IN (?)', array('small_image', 'thumbnail', 'image'));

        $images = $read->fetchAll($select);
        return $images;
    }
}
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
 * Product collection
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Product_Collection extends Mage_Catalog_Model_Resource_Collection_Abstract
{
    /**
     * Alias for index table
     */
    const INDEX_TABLE_ALIAS = 'price_index';

    /**
     * Alias for main table
     */
    const MAIN_TABLE_ALIAS = 'e';

    /**
     * Catalog Product Flat is enabled cache per store
     *
     * @var array
     */
    protected $_flatEnabled                  = array();

    /**
     * Product websites table name
     *
     * @var string
     */
    protected $_productWebsiteTable;

    /**
     * Product categories table name
     *
     * @var string
     */
    protected $_productCategoryTable;

    /**
     * Is add URL rewrites to collection flag
     *
     * @var bool
     */
    protected $_addUrlRewrite                = false;

    /**
     * Add URL rewrite for category
     *
     * @var int
     */
    protected $_urlRewriteCategory           = '';

    /**
     * Is add minimal price to product collection flag
     *
     * @var bool
     */
    protected $_addMinimalPrice              = false;

    /**
     * Is add final price to product collection flag
     *
     * @var unknown_type
     */
    protected $_addFinalPrice                = false;

    /**
     * Cache for all ids
     *
     * @var array
     */
    protected $_allIdsCache                  = null;

    /**
     * Is add tax percents to product collection flag
     *
     * @var bool
     */
    protected $_addTaxPercents               = false;

    /**
     * Product limitation filters
     * Allowed filters
     *  store_id                int;
     *  category_id             int;
     *  category_is_anchor      int;
     *  visibility              array|int;
     *  website_ids             array|int;
     *  store_table             string;
     *  use_price_index         bool;   join price index table flag
     *  customer_group_id       int;    required for price; customer group limitation for price
     *  website_id              int;    required for price; website limitation for price
     *
     * @var array
     */
    protected $_productLimitationFilters     = array();

    /**
     * Category product count select
     *
     * @var Zend_Db_Select
     */
    protected $_productCountSelect           = null;

    /**
     * Enter description here ...
     *
     * @var bool
     */
    protected $_isWebsiteFilter              = false;

    /**
     * Additional field filters, applied in _productLimitationJoinPrice()
     *
     * @var array
     */
    protected $_priceDataFieldFilters = array();

    /**
     * Map of price fields
     *
     * @var array
     */
    protected $_map = array('fields' => array(
        'price'         => 'price_index.price',
        'final_price'   => 'price_index.final_price',
        'min_price'     => 'price_index.min_price',
        'max_price'     => 'price_index.max_price',
        'tier_price'    => 'price_index.tier_price',
        'special_price' => 'price_index.special_price',
    ));

    /**
     * Price expression sql
     *
     * @var string|null
     */
    protected $_priceExpression;

    /**
     * Additional price expression sql part
     *
     * @var string|null
     */
    protected $_additionalPriceExpression;

    /**
     * Max prise (statistics data)
     *
     * @var float
     */
    protected $_maxPrice;

    /**
     * Min prise (statistics data)
     *
     * @var float
     */
    protected $_minPrice;

    /**
     * Prise standard deviation (statistics data)
     *
     * @var float
     */
    protected $_priceStandardDeviation;

    /**
     * Prises count (statistics data)
     *
     * @var int
     */
    protected $_pricesCount = null;

    /**
     * Cloned Select after dispatching 'catalog_prepare_price_select' event
     *
     * @var Varien_Db_Select
     */
    protected $_catalogPreparePriceSelect = null;

    /**
     * Catalog factory instance
     *
     * @var Mage_Catalog_Model_Factory
     */
    protected $_factory;

    /**
     * Initialize factory
     *
     * @param Mage_Core_Model_Resource_Abstract $resource
     * @param array $args
     */
    public function __construct($resource = null, array $args = array())
    {
        parent::__construct($resource);
        $this->_factory = !empty($args['factory']) ? $args['factory'] : Mage::getSingleton('catalog/factory');
    }

    /**
     * Get cloned Select after dispatching 'catalog_prepare_price_select' event
     *
     * @return Varien_Db_Select
     */
    public function getCatalogPreparedSelect()
    {
        return $this->_catalogPreparePriceSelect;
    }

    /**
     * Prepare additional price expression sql part
     *
     * @param Varien_Db_Select $select
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _preparePriceExpressionParameters($select)
    {
        // prepare response object for event
        $response = new Varien_Object();
        $response->setAdditionalCalculations(array());
        $tableAliases = array_keys($select->getPart(Zend_Db_Select::FROM));
        if (in_array(self::INDEX_TABLE_ALIAS, $tableAliases)) {
            $table = self::INDEX_TABLE_ALIAS;
        } else {
            $table = reset($tableAliases);
        }

        // prepare event arguments
        $eventArgs = array(
            'select'          => $select,
            'table'           => $table,
            'store_id'        => $this->getStoreId(),
            'response_object' => $response
        );

        Mage::dispatchEvent('catalog_prepare_price_select', $eventArgs);

        $additional   = join('', $response->getAdditionalCalculations());
        $this->_priceExpression = $table . '.min_price';
        $this->_additionalPriceExpression = $additional;
        $this->_catalogPreparePriceSelect = clone $select;

        return $this;
    }

    /**
     * Get price expression sql part
     *
     * @param Varien_Db_Select $select
     * @return string
     */
    public function getPriceExpression($select)
    {
        if (is_null($this->_priceExpression)) {
            $this->_preparePriceExpressionParameters($select);
        }
        return $this->_priceExpression;
    }

    /**
     * Get additional price expression sql part
     *
     * @param Varien_Db_Select $select
     * @return string
     */
    public function getAdditionalPriceExpression($select)
    {
        if (is_null($this->_additionalPriceExpression)) {
            $this->_preparePriceExpressionParameters($select);
        }
        return $this->_additionalPriceExpression;
    }

    /**
     * Get currency rate
     *
     * @return float
     */
    public function getCurrencyRate()
    {
        return Mage::app()->getStore($this->getStoreId())->getCurrentCurrencyRate();
    }

    /**
     * Retrieve Catalog Product Flat Helper object
     *
     * @return Mage_Catalog_Helper_Product_Flat
     */
    public function getFlatHelper()
    {
        return Mage::helper('catalog/product_flat');
    }

    /**
     * Retrieve is flat enabled flag
     * Return always false if magento run admin
     *
     * @return bool
     */
    public function isEnabledFlat()
    {
        // Flat Data can be used only on frontend
        if (Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        $storeId = $this->getStoreId();
        if (!isset($this->_flatEnabled[$storeId])) {
            $flatHelper = $this->getFlatHelper();
            $this->_flatEnabled[$storeId] = $flatHelper->isAvailable() && $flatHelper->isBuilt($storeId);
        }
        return $this->_flatEnabled[$storeId];
    }

    /**
     * Initialize resources
     *
     */
    protected function _construct()
    {
        if ($this->isEnabledFlat()) {
            $this->_init('catalog/product', 'catalog/product_flat');
        }
        else {
            $this->_init('catalog/product');
        }
        $this->_initTables();
    }

    /**
     * Define product website and category product tables
     *
     */
    protected function _initTables()
    {
        $this->_productWebsiteTable = $this->getResource()->getTable('catalog/product_website');
        $this->_productCategoryTable= $this->getResource()->getTable('catalog/category_product');
    }

    /**
     * Standard resource collection initalization
     *
     * @param string $model
     * @param unknown_type $entityModel
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _init($model, $entityModel = null)
    {
        if ($this->isEnabledFlat()) {
            $entityModel = 'catalog/product_flat';
        }

        return parent::_init($model, $entityModel);
    }

    /**
     * Prepare static entity fields
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _prepareStaticFields()
    {
        if ($this->isEnabledFlat()) {
            return $this;
        }
        return parent::_prepareStaticFields();
    }

    /**
     * Retrieve collection empty item
     * Redeclared for specifying id field name without getting resource model inside model
     *
     * @return Varien_Object
     */
    public function getNewEmptyItem()
    {
        $object = parent::getNewEmptyItem();
        if ($this->isEnabledFlat()) {
            $object->setIdFieldName($this->getEntity()->getIdFieldName());
        }
        return $object;
    }

    /**
     * Set entity to use for attributes
     *
     * @param Mage_Eav_Model_Entity_Abstract $entity
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function setEntity($entity)
    {
        if ($this->isEnabledFlat() && ($entity instanceof Mage_Core_Model_Resource_Db_Abstract)) {
            $this->_entity = $entity;
            return $this;
        }
        return parent::setEntity($entity);
    }

    /**
     * Set Store scope for collection
     *
     * @param mixed $store
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function setStore($store)
    {
        parent::setStore($store);
        if ($this->isEnabledFlat()) {
            $this->getEntity()->setStoreId($this->getStoreId());
        }
        return $this;
    }

    /**
     * Initialize collection select
     * Redeclared for remove entity_type_id condition
     * in catalog_product_entity we store just products
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _initSelect()
    {
        if ($this->isEnabledFlat()) {
            $this->getSelect()
                ->from(array(self::MAIN_TABLE_ALIAS => $this->getEntity()->getFlatTableName()), null)
                ->columns(array('status' => new Zend_Db_Expr(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)));
            $this->addAttributeToSelect(array('entity_id', 'type_id', 'attribute_set_id'));
            if ($this->getFlatHelper()->isAddChildData()) {
                $this->getSelect()
                    ->where('e.is_child=?', 0);
                $this->addAttributeToSelect(array('child_id', 'is_child'));
            }
        } else {
            $this->getSelect()->from(array(self::MAIN_TABLE_ALIAS => $this->getEntity()->getEntityTable()));
        }
        return $this;
    }

    /**
     * Load attributes into loaded entities
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function _loadAttributes($printQuery = false, $logQuery = false)
    {
        if ($this->isEnabledFlat()) {
            return $this;
        }
        return parent::_loadAttributes($printQuery, $logQuery);
    }

    /**
     * Add attribute to entities in collection
     * If $attribute=='*' select all attributes
     *
     * @param array|string|integer|Mage_Core_Model_Config_Element $attribute
     * @param false|string $joinType
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function addAttributeToSelect($attribute, $joinType = false)
    {
        if ($this->isEnabledFlat()) {
            if (!is_array($attribute)) {
                $attribute = array($attribute);
            }
            foreach ($attribute as $attributeCode) {
                if ($attributeCode == '*') {
                    foreach ($this->getEntity()->getAllTableColumns() as $column) {
                        $this->getSelect()->columns('e.' . $column);
                        $this->_selectAttributes[$column] = $column;
                        $this->_staticFields[$column]     = $column;
                    }
                } else {
                    $columns = $this->getEntity()->getAttributeForSelect($attributeCode);
                    if ($columns) {
                        foreach ($columns as $alias => $column) {
                            $this->getSelect()->columns(array($alias => 'e.' . $column));
                            $this->_selectAttributes[$column] = $column;
                            $this->_staticFields[$column]     = $column;
                        }
                    }
                }
            }
            return $this;
        }
        return parent::addAttributeToSelect($attribute, $joinType);
    }

    /**
     * Add tax class id attribute to select and join price rules data if needed
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _beforeLoad()
    {
        Mage::dispatchEvent('catalog_product_collection_load_before', array('collection' => $this));

        return parent::_beforeLoad();
    }

    /**
     * Processing collection items after loading
     * Adding url rewrites, minimal prices, final prices, tax percents
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _afterLoad()
    {
        if ($this->_addUrlRewrite) {
           $this->_addUrlRewrite($this->_urlRewriteCategory);
        }

        if (count($this) > 0) {
            Mage::dispatchEvent('catalog_product_collection_load_after', array('collection' => $this));
        }

        foreach ($this as $product) {
            if ($product->isRecurring() && $profile = $product->getRecurringProfile()) {
                $product->setRecurringProfile(unserialize($profile));
            }
        }

        return $this;
    }

    /**
     * Prepare Url Data object
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     * @deprecated after 1.7.0.2
     */
    protected function _prepareUrlDataObject()
    {
        $objects = array();
        /** @var $item Mage_Catalog_Model_Product */
        foreach ($this->_items as $item) {
            if ($this->getFlag('do_not_use_category_id')) {
                $item->setDoNotUseCategoryId(true);
            }
            if (!$item->isVisibleInSiteVisibility() && $item->getItemStoreId()) {
                $objects[$item->getEntityId()] = $item->getItemStoreId();
            }
        }

        if ($objects && $this->hasFlag('url_data_object')) {
            $objects = Mage::getResourceSingleton('catalog/url')
                ->getRewriteByProductStore($objects);
            foreach ($this->_items as $item) {
                if (isset($objects[$item->getEntityId()])) {
                    $object = new Varien_Object($objects[$item->getEntityId()]);
                    $item->setUrlDataObject($object);
                }
            }
        }

        return $this;
    }

    /**
     * Add collection filters by identifiers
     *
     * @param mixed $productId
     * @param boolean $exclude
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function addIdFilter($productId, $exclude = false)
    {
        if (empty($productId)) {
            $this->_setIsLoaded(true);
            return $this;
        }
        if (is_array($productId)) {
            if (!empty($productId)) {
                if ($exclude) {
                    $condition = array('nin' => $productId);
                } else {
                    $condition = array('in' => $productId);
                }
            } else {
                $condition = '';
            }
        } else {
            if ($exclude) {
                $condition = array('neq' => $productId);
            } else {
                $condition = $productId;
            }
        }
        $this->addFieldToFilter('entity_id', $condition);
        return $this;
    }

    /**
     * Adding product website names to result collection
     * Add for each product websites information
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function addWebsiteNamesToResult()
    {
        $productWebsites = array();
        foreach ($this as $product) {
            $productWebsites[$product->getId()] = array();
        }

        if (!empty($productWebsites)) {
            $select = $this->getConnection()->select()
                ->from(array('product_website' => $this->_productWebsiteTable))
                ->join(
                    array('website' => $this->getResource()->getTable('core/website')),
                    'website.website_id = product_website.website_id',
                    array('name'))
                ->where('product_website.product_id IN (?)', array_keys($productWebsites))
                ->where('website.website_id > ?', 0);

            $data = $this->getConnection()->fetchAll($select);
            foreach ($data as $row) {
                $productWebsites[$row['product_id']][] = $row['website_id'];
            }
        }

        foreach ($this as $product) {
            if (isset($productWebsites[$product->getId()])) {
                $product->setData('websites', $productWebsites[$product->getId()]);
            }
        }
        return $this;
    }

    /**
     * Add store availability filter. Include availability product
     * for store website
     *
     * @param mixed $store
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function addStoreFilter($store = null)
    {
        if ($store === null) {
            $store = $this->getStoreId();
        }
        $store = Mage::app()->getStore($store);

        if (!$store->isAdmin()) {
            $this->_productLimitationFilters['store_id'] = $store->getId();
            $this->_applyProductLimitations();
        }

        return $this;
    }

    /**
     * Add website filter to collection
     *
     * @param unknown_type $websites
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function addWebsiteFilter($websites = null)
    {
        if (!is_array($websites)) {
            $websites = array(Mage::app()->getWebsite($websites)->getId());
        }

        $this->_productLimitationFilters['website_ids'] = $websites;
        $this->_applyProductLimitations();

        return $this;
    }

    /**
     * Get filters applied to collection
     *
     * @return array
     */
    public function getLimitationFilters()
    {
        return $this->_productLimitationFilters;
    }

    /**
     * Specify category filter for product collection
     *
     * @param Mage_Catalog_Model_Category $category
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function addCategoryFilter(Mage_Catalog_Model_Category $category)
    {
        $this->_productLimitationFilters['category_id'] = $category->getId();
        if ($category->getIsAnchor()) {
            unset($this->_productLimitationFilters['category_is_anchor']);
        } else {
            $this->_productLimitationFilters['category_is_anchor'] = 1;
        }

        if ($this->getStoreId() == Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID) {
            $this->_applyZeroStoreProductLimitations();
        } else {
            $this->_applyProductLimitations();
        }

        return $this;
    }

    /**
     * Join minimal price attribute to result
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function joinMinimalPrice()
    {
        $this->addAttributeToSelect('price')
             ->addAttributeToSelect('minimal_price');
        return $this;
    }

    /**
     * Retrieve max value by attribute
     *
     * @param string $attribute
     * @return mixed
     */
    public function getMaxAttributeValue($attribute)
    {
        $select        = clone $this->getSelect();
        $attribute     = $this->getEntity()->getAttribute($attribute);
        $attributeCode = $attribute->getAttributeCode();
        $tableAlias    = $attributeCode . '_max_value';
        $fieldAlias    = 'max_' . $attributeCode;
        $condition  = 'e.entity_id = ' . $tableAlias . '.entity_id
            AND '.$this->_getConditionSql($tableAlias . '.attribute_id', $attribute->getId());

        $select->join(
                array($tableAlias => $attribute->getBackend()->getTable()),
                $condition,
                array($fieldAlias => new Zend_Db_Expr('MAX('.$tableAlias.'.value)'))
            )
            ->group('e.entity_type_id');

        $data = $this->getConnection()->fetchRow($select);
        if (isset($data[$fieldAlias])) {
            return $data[$fieldAlias];
        }

        return null;
    }

    /**
     * Retrieve ranging product count for arrtibute range
     *
     * @param string $attribute
     * @param int $range
     * @return array
     */
    public function getAttributeValueCountByRange($attribute, $range)
    {
        $select        = clone $this->getSelect();
        $attribute     = $this->getEntity()->getAttribute($attribute);
        $attributeCode = $attribute->getAttributeCode();
        $tableAlias    = $attributeCode . '_range_count_value';

        $condition  = 'e.entity_id = ' . $tableAlias . '.entity_id
            AND ' . $this->_getConditionSql($tableAlias . '.attribute_id', $attribute->getId());

        $select->reset(Zend_Db_Select::GROUP);
        $select->join(
                array($tableAlias => $attribute->getBackend()->getTable()),
                $condition,
                array(
                    'count_' . $attributeCode => new Zend_Db_Expr('COUNT(DISTINCT e.entity_id)'),
                    'range_' . $attributeCode => new Zend_Db_Expr(
                        'CEIL((' . $tableAlias . '.value+0.01)/' . $range . ')')
                 )
            )
            ->group('range_' . $attributeCode);

        $data   = $this->getConnection()->fetchAll($select);
        $res    = array();

        foreach ($data as $row) {
            $res[$row['range_' . $attributeCode]] = $row['count_' . $attributeCode];
        }
        return $res;
    }

    /**
     * Retrieve product count by some value of attribute
     *
     * @param string $attribute
     * @return array($value=>$count)
     */
    public function getAttributeValueCount($attribute)
    {
        $select        = clone $this->getSelect();
        $attribute     = $this->getEntity()->getAttribute($attribute);
        $attributeCode = $attribute->getAttributeCode();
        $tableAlias    = $attributeCode . '_value_count';

        $select->reset(Zend_Db_Select::GROUP);
        $condition  = 'e.entity_id=' . $tableAlias . '.entity_id
            AND '.$this->_getConditionSql($tableAlias . '.attribute_id', $attribute->getId());

        $select->join(
                array($tableAlias => $attribute->getBackend()->getTable()),
                $condition,
                array(
                    'count_' . $attributeCode => new Zend_Db_Expr('COUNT(DISTINCT e.entity_id)'),
                    'value_' . $attributeCode => new Zend_Db_Expr($tableAlias . '.value')
                 )
            )
            ->group('value_' . $attributeCode);

        $data   = $this->getConnection()->fetchAll($select);
        $res    = array();

        foreach ($data as $row) {
            $res[$row['value_' . $attributeCode]] = $row['count_' . $attributeCode];
        }
        return $res;
    }

    /**
     * Return all attribute values as array in form:
     * array(
     *   [entity_id_1] => array(
     *          [store_id_1] => store_value_1,
     *          [store_id_2] => store_value_2,
     *          ...
     *          [store_id_n] => store_value_n
     *   ),
     *   ...
     * )
     *
     * @param string $attribute attribute code
     * @return array
     */
    public function getAllAttributeValues($attribute)
    {
        /** @var $select Varien_Db_Select */
        $select    = clone $this->getSelect();
        $attribute = $this->getEntity()->getAttribute($attribute);

        $select->reset()
            ->from($attribute->getBackend()->getTable(), array('entity_id', 'store_id', 'value'))
            ->where('attribute_id = ?', (int)$attribute->getId());

        $data = $this->getConnection()->fetchAll($select);
        $res  = array();

        foreach ($data as $row) {
            $res[$row['entity_id']][$row['store_id']] = $row['value'];
        }

        return $res;
    }

    /**
     * Get SQL for get record count without left JOINs
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        return $this->_getSelectCountSql();
    }

    /**
     * Get SQL for get record count
     *
     * @param bool $resetLeftJoins
     * @return Varien_Db_Select
     */
    protected function _getSelectCountSql($select = null, $resetLeftJoins = true)
    {
        $this->_renderFilters();
        $countSelect = (is_null($select)) ?
            $this->_getClearSelect() :
            $this->_buildClearSelect($select);
        // Clear GROUP condition for count method
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->columns('COUNT(DISTINCT e.entity_id)');
        if ($resetLeftJoins) {
            $countSelect->resetJoinLeft();
        }
        return $countSelect;
    }

    /**
     * Prepare statistics data
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _prepareStatisticsData()
    {
        $select = clone $this->getSelect();
        $priceExpression = $this->getPriceExpression($select) . ' ' . $this->getAdditionalPriceExpression($select);
        $sqlEndPart = ') * ' . $this->getCurrencyRate() . ', 2)';
        $select = $this->_getSelectCountSql($select, false);
        $select->columns(array(
            'max' => 'ROUND(MAX(' . $priceExpression . $sqlEndPart,
            'min' => 'ROUND(MIN(' . $priceExpression . $sqlEndPart,
            'std' => $this->getConnection()->getStandardDeviationSql('ROUND((' . $priceExpression . $sqlEndPart)
        ));
        $select->where($this->getPriceExpression($select) . ' IS NOT NULL');
        $row = $this->getConnection()->fetchRow($select, $this->_bindParams, Zend_Db::FETCH_NUM);
        $this->_pricesCount = (int)$row[0];
        $this->_maxPrice = (float)$row[1];
        $this->_minPrice = (float)$row[2];
        $this->_priceStandardDeviation = (float)$row[3];

        return $this;
    }

    /**
     * Retreive clear select
     *
     * @return Varien_Db_Select
     */
    protected function _getClearSelect()
    {
        return $this->_buildClearSelect();
    }

    /**
     * Build clear select
     *
     * @param Varien_Db_Select $select
     * @return Varien_Db_Select
     */
    protected function _buildClearSelect($select = null)
    {
        if (is_null($select)) {
            $select = clone $this->getSelect();
        }
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);
        $select->reset(Zend_Db_Select::COLUMNS);

        return $select;
    }

    /**
     * Retrive all ids for collection
     *
     * @param unknown_type $limit
     * @param unknown_type $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
        $idsSelect = $this->_getClearSelect();
        $idsSelect->columns('e.' . $this->getEntity()->getIdFieldName());
        $idsSelect->limit($limit, $offset);
        $idsSelect->resetJoinLeft();

        return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
    }

    /**
     * Retreive product count select for categories
     *
     * @return Varien_Db_Select
     */
    public function getProductCountSelect()
    {
        if ($this->_productCountSelect === null) {
            $this->_productCountSelect = clone $this->getSelect();
            $this->_productCountSelect->reset(Zend_Db_Select::COLUMNS)
                ->reset(Zend_Db_Select::GROUP)
                ->reset(Zend_Db_Select::ORDER)
                ->distinct(false)
                ->join(array('count_table' => $this->getTable('catalog/category_product_index')),
                    'count_table.product_id = e.entity_id',
                    array(
                        'count_table.category_id',
                        'product_count' => new Zend_Db_Expr('COUNT(DISTINCT count_table.product_id)')
                    )
                )
                ->where('count_table.store_id = ?', $this->getStoreId())
                ->group('count_table.category_id');
        }

        return $this->_productCountSelect;
    }

    /**
     * Destruct product count select
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function unsProductCountSelect()
    {
        $this->_productCountSelect = null;
        return $this;
    }

    /**
     * Adding product count to categories collection
     *
     * @param Mage_Eav_Model_Entity_Collection_Abstract $categoryCollection
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function addCountToCategories($categoryCollection)
    {
        $isAnchor    = array();
        $isNotAnchor = array();
        foreach ($categoryCollection as $category) {
            if ($category->getIsAnchor()) {
                $isAnchor[]    = $category->getId();
            } else {
                $isNotAnchor[] = $category->getId();
            }
        }
        $productCounts = array();
        if ($isAnchor || $isNotAnchor) {
            $select = $this->getProductCountSelect();

            Mage::dispatchEvent(
                'catalog_product_collection_before_add_count_to_categories',
                array('collection' => $this)
            );

            if ($isAnchor) {
                $anchorStmt = clone $select;
                $anchorStmt->limit(); //reset limits
                $anchorStmt->where('count_table.category_id IN (?)', $isAnchor);
                $productCounts += $this->getConnection()->fetchPairs($anchorStmt);
                $anchorStmt = null;
            }
            if ($isNotAnchor) {
                $notAnchorStmt = clone $select;
                $notAnchorStmt->limit(); //reset limits
                $notAnchorStmt->where('count_table.category_id IN (?)', $isNotAnchor);
                $notAnchorStmt->where('count_table.is_parent = 1');
                $productCounts += $this->getConnection()->fetchPairs($notAnchorStmt);
                $notAnchorStmt = null;
            }
            $select = null;
            $this->unsProductCountSelect();
        }

        foreach ($categoryCollection as $category) {
            $_count = 0;
            if (isset($productCounts[$category->getId()])) {
                $_count = $productCounts[$category->getId()];
            }
            $category->setProductCount($_count);
        }

        return $this;
    }

    /**
     * Retrieve unique attribute set ids in collection
     *
     * @return array
     */
    public function getSetIds()
    {
        $select = clone $this->getSelect();
        /** @var $select Varien_Db_Select */
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->distinct(true);
        $select->columns('attribute_set_id');
        return $this->getConnection()->fetchCol($select);
    }

    /**
     * Return array of unique product type ids in collection
     *
     * @return array
     */
    public function getProductTypeIds()
    {
        $select = clone $this->getSelect();
        /** @var $select Varien_Db_Select */
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->distinct(true);
        $select->columns('type_id');
        return $this->getConnection()->fetchCol($select);
    }

    /**
     * Joins url rewrite rules to collection
     *
     * @deprecated after 1.7.0.2. Method is not used anywhere in the code.
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function joinUrlRewrite()
    {
        $this->joinTable(
            'core/url_rewrite',
            'entity_id=entity_id',
            array('request_path'),
            '{{table}}.type = ' . Mage_Core_Model_Url_Rewrite::TYPE_PRODUCT,
            'left'
        );

        return $this;
    }

    /**
     * Add URL rewrites data to product
     * If collection loadded - run processing else set flag
     *
     * @param int|string $categoryId
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function addUrlRewrite($categoryId = '')
    {
        $this->_addUrlRewrite = true;
        if (Mage::getStoreConfig(Mage_Catalog_Helper_Product::XML_PATH_PRODUCT_URL_USE_CATEGORY, $this->getStoreId())) {
            $this->_urlRewriteCategory = $categoryId;
        } else {
            $this->_urlRewriteCategory = 0;
        }

        if ($this->isLoaded()) {
            $this->_addUrlRewrite();
        }

        return $this;
    }

    /**
     * Add URL rewrites to collection
     *
     */
    protected function _addUrlRewrite()
    {
        $urlRewrites = null;
        if ($this->_cacheConf) {
            if (!($urlRewrites = Mage::app()->loadCache($this->_cacheConf['prefix'] . 'urlrewrite'))) {
                $urlRewrites = null;
            } else {
                $urlRewrites = unserialize($urlRewrites);
            }
        }

        if (!$urlRewrites) {
            $productIds = array();
            foreach($this->getItems() as $item) {
                $productIds[] = $item->getEntityId();
            }
            if (!count($productIds)) {
                return;
            }

            $select = $this->_factory->getProductUrlRewriteHelper()
                ->getTableSelect($productIds, $this->_urlRewriteCategory, Mage::app()->getStore()->getId());

            $urlRewrites = array();
            foreach ($this->getConnection()->fetchAll($select) as $row) {
                if (!isset($urlRewrites[$row['product_id']])) {
                    $urlRewrites[$row['product_id']] = $row['request_path'];
                }
            }

            if ($this->_cacheConf) {
                Mage::app()->saveCache(
                    serialize($urlRewrites),
                    $this->_cacheConf['prefix'] . 'urlrewrite',
                    array_merge($this->_cacheConf['tags'], array(Mage_Catalog_Model_Product_Url::CACHE_TAG)),
                    $this->_cacheLifetime
                );
            }
        }

        foreach($this->getItems() as $item) {
            if (empty($this->_urlRewriteCategory)) {
                $item->setDoNotUseCategoryId(true);
            }
            if (isset($urlRewrites[$item->getEntityId()])) {
                $item->setData('request_path', $urlRewrites[$item->getEntityId()]);
            } else {
                $item->setData('request_path', false);
            }
        }
    }

    /**
     * Add minimal price data to result
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function addMinimalPrice()
    {
        return $this->addPriceData();
    }

    /**
     * Add minimal price to product collection
     *
     * @deprecated sinse 1.3.2.2
     * @see Mage_Catalog_Model_Resource_Product_Collection::addPriceData
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _addMinimalPrice()
    {
        return $this;
    }

    /**
     * Add price data for calculate final price
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function addFinalPrice()
    {
        return $this->addPriceData();
    }

    /**
     * Join prices from price rules to products collection
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _joinPriceRules()
    {
        if ($this->isEnabledFlat()) {
            $customerGroup = Mage::getSingleton('customer/session')->getCustomerGroupId();
            $priceColumn   = 'e.display_price_group_' . $customerGroup;
            $this->getSelect()->columns(array('_rule_price' => $priceColumn));

            return $this;
        }
        if (!Mage::helper('catalog')->isModuleEnabled('Mage_CatalogRule')) {
            return $this;
        }
        $wId = Mage::app()->getWebsite()->getId();
        $gId = Mage::getSingleton('customer/session')->getCustomerGroupId();

        $storeDate = Mage::app()->getLocale()->storeTimeStamp($this->getStoreId());
        $conditions  = 'price_rule.product_id = e.entity_id AND ';
        $conditions .= "price_rule.rule_date = '".$this->getResource()->formatDate($storeDate, false)."' AND ";
        $conditions .= $this->getConnection()->quoteInto('price_rule.website_id = ? AND', $wId);
        $conditions .= $this->getConnection()->quoteInto('price_rule.customer_group_id = ?', $gId);

        $this->getSelect()->joinLeft(
            array('price_rule' => $this->getTable('catalogrule/rule_product_price')),
            $conditions,
            array('rule_price' => 'rule_price')
        );
        return $this;
    }

    /**
     * Add final price to the product
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _addFinalPrice()
    {
        foreach ($this->_items as $product) {
            $basePrice = $product->getPrice();
            $specialPrice = $product->getSpecialPrice();
            $specialPriceFrom = $product->getSpecialFromDate();
            $specialPriceTo = $product->getSpecialToDate();
            if ($this->isEnabledFlat()) {
                $rulePrice = null;
                if ($product->getData('_rule_price') != $basePrice) {
                    $rulePrice = $product->getData('_rule_price');
                }
            } else {
                $rulePrice = $product->getData('_rule_price');
            }

            $finalPrice = $product->getPriceModel()->calculatePrice(
                $basePrice,
                $specialPrice,
                $specialPriceFrom,
                $specialPriceTo,
                $rulePrice,
                null,
                null,
                $product->getId()
            );

            $product->setCalculatedFinalPrice($finalPrice);
        }

        return $this;
    }

    /**
     * Retreive all ids
     *
     * @param boolean $resetCache
     * @return array
     */
    public function getAllIdsCache($resetCache = false)
    {
        $ids = null;
        if (!$resetCache) {
            $ids = $this->_allIdsCache;
        }

        if (is_null($ids)) {
            $ids = $this->getAllIds();
            $this->setAllIdsCache($ids);
        }

        return $ids;
    }

    /**
     * Set all ids
     *
     * @param array $value
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function setAllIdsCache($value)
    {
        $this->_allIdsCache = $value;
        return $this;
    }

    /**
     * Add Price Data to result
     *
     * @param int $customerGroupId
     * @param int $websiteId
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function addPriceData($customerGroupId = null, $websiteId = null)
    {
        $this->_productLimitationFilters['use_price_index'] = true;

        if (!isset($this->_productLimitationFilters['customer_group_id']) && is_null($customerGroupId)) {
            $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        }
        if (!isset($this->_productLimitationFilters['website_id']) && is_null($websiteId)) {
            $websiteId       = Mage::app()->getStore($this->getStoreId())->getWebsiteId();
        }

        if (!is_null($customerGroupId)) {
            $this->_productLimitationFilters['customer_group_id'] = $customerGroupId;
        }
        if (!is_null($websiteId)) {
            $this->_productLimitationFilters['website_id'] = $websiteId;
        }

        $this->_applyProductLimitations();

        return $this;
    }

    /**
     * Add attribute to filter
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract|string $attribute
     * @param array $condition
     * @param string $joinType
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function addAttributeToFilter($attribute, $condition = null, $joinType = 'inner')
    {
        if ($this->isEnabledFlat()) {
            if ($attribute instanceof Mage_Eav_Model_Entity_Attribute_Abstract) {
                $attribute = $attribute->getAttributeCode();
            }

            if (is_array($attribute)) {
                $sqlArr = array();
                foreach ($attribute as $condition) {
                    $sqlArr[] = $this->_getAttributeConditionSql($condition['attribute'], $condition, $joinType);
                }
                $conditionSql = '('.join(') OR (', $sqlArr).')';
                $this->getSelect()->where($conditionSql);
                return $this;
            }

            if (!isset($this->_selectAttributes[$attribute])) {
                $this->addAttributeToSelect($attribute);
            }

            if (isset($this->_selectAttributes[$attribute])) {
                $this->getSelect()->where($this->_getConditionSql('e.' . $attribute, $condition));
            }

            return $this;
        }

        $this->_allIdsCache = null;

        if (is_string($attribute) && $attribute == 'is_saleable') {
            $columns = $this->getSelect()->getPart(Zend_Db_Select::COLUMNS);
            foreach ($columns as $columnEntry) {
                list($correlationName, $column, $alias) = $columnEntry;
                if ($alias == 'is_saleable') {
                    if ($column instanceof Zend_Db_Expr) {
                        $field = $column;
                    } else {
                        $adapter = $this->getSelect()->getAdapter();
                        if (empty($correlationName)) {
                            $field = $adapter->quoteColumnAs($column, $alias, true);
                        } else {
                            $field = $adapter->quoteColumnAs(array($correlationName, $column), $alias, true);
                        }
                    }
                    $this->getSelect()->where("{$field} = ?", $condition);
                    break;
                }
            }

            return $this;
        } else {
            return parent::addAttributeToFilter($attribute, $condition, $joinType);
        }
    }

    /**
     * Add requere tax percent flag for product collection
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function addTaxPercents()
    {
        $this->_addTaxPercents = true;
        return $this;
    }

    /**
     * Get require tax percent flag value
     *
     * @return bool
     */
    public function requireTaxPercent()
    {
        return $this->_addTaxPercents;
    }

    /**
     * Enter description here ...
     *
     * @deprecated from 1.3.0
     *
     */
    protected function _addTaxPercents()
    {
        $classToRate = array();
        $request = Mage::getSingleton('tax/calculation')->getRateRequest();
        foreach ($this as &$item) {
            if (null === $item->getTaxClassId()) {
                $item->setTaxClassId($item->getMinimalTaxClassId());
            }
            if (!isset($classToRate[$item->getTaxClassId()])) {
                $request->setProductClassId($item->getTaxClassId());
                $classToRate[$item->getTaxClassId()] = Mage::getSingleton('tax/calculation')->getRate($request);
            }
            $item->setTaxPercent($classToRate[$item->getTaxClassId()]);
        }
    }

    /**
     * Adding product custom options to result collection
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function addOptionsToResult()
    {
        $productIds = array();
        foreach ($this as $product) {
            $productIds[] = $product->getId();
        }
        if (!empty($productIds)) {
            $options = Mage::getModel('catalog/product_option')
                ->getCollection()
                ->addTitleToResult(Mage::app()->getStore()->getId())
                ->addPriceToResult(Mage::app()->getStore()->getId())
                ->addProductToFilter($productIds)
                ->addValuesToResult();

            foreach ($options as $option) {
                if($this->getItemById($option->getProductId())) {
                    $this->getItemById($option->getProductId())->addOption($option);
                }
            }
        }

        return $this;
    }

    /**
     * Filter products with required options
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function addFilterByRequiredOptions()
    {
        $this->addAttributeToFilter('required_options', array(array('neq' => '1'), array('null' => true)), 'left');
        return $this;
    }

    /**
     * Set product visibility filter for enabled products
     *
     * @param array $visibility
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function setVisibility($visibility)
    {
        $this->_productLimitationFilters['visibility'] = $visibility;
        $this->_applyProductLimitations();

        return $this;
    }

    /**
     * Add attribute to sort order
     *
     * @param string $attribute
     * @param string $dir
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function addAttributeToSort($attribute, $dir = self::SORT_ORDER_ASC)
    {
        if ($attribute == 'position') {
            if (isset($this->_joinFields[$attribute])) {
                $this->getSelect()->order($this->_getAttributeFieldName($attribute) . ' ' . $dir);
                return $this;
            }
            if ($this->isEnabledFlat()) {
                $this->getSelect()->order("cat_index_position {$dir}");
            }
            // optimize if using cat index
            $filters = $this->_productLimitationFilters;
            if (isset($filters['category_id']) || isset($filters['visibility'])) {
                $this->getSelect()->order('cat_index.position ' . $dir);
            } else {
                $this->getSelect()->order('e.entity_id ' . $dir);
            }

            return $this;
        } elseif($attribute == 'is_saleable'){
            $this->getSelect()->order("is_saleable " . $dir);
            return $this;
        }

        $storeId = $this->getStoreId();
        if ($attribute == 'price' && $storeId != 0) {
            $this->addPriceData();
            $this->getSelect()->order("price_index.min_price {$dir}");

            return $this;
        }

        if ($this->isEnabledFlat()) {
            $column = $this->getEntity()->getAttributeSortColumn($attribute);

            if ($column) {
                $this->getSelect()->order("e.{$column} {$dir}");
            }
            else if (isset($this->_joinFields[$attribute])) {
                $this->getSelect()->order($this->_getAttributeFieldName($attribute) . ' ' . $dir);
            }

            return $this;
        } else {
            $attrInstance = $this->getEntity()->getAttribute($attribute);
            if ($attrInstance && $attrInstance->usesSource()) {
                $attrInstance->getSource()
                    ->addValueSortToCollection($this, $dir);
                return $this;
            }
        }

        return parent::addAttributeToSort($attribute, $dir);
    }

    /**
     * Prepare limitation filters
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _prepareProductLimitationFilters()
    {
        if (isset($this->_productLimitationFilters['visibility'])
            && !isset($this->_productLimitationFilters['store_id'])
        ) {
            $this->_productLimitationFilters['store_id'] = $this->getStoreId();
        }
        if (isset($this->_productLimitationFilters['category_id'])
            && !isset($this->_productLimitationFilters['store_id'])
        ) {
            $this->_productLimitationFilters['store_id'] = $this->getStoreId();
        }
        if (isset($this->_productLimitationFilters['store_id'])
            && isset($this->_productLimitationFilters['visibility'])
            && !isset($this->_productLimitationFilters['category_id'])
        ) {
            $this->_productLimitationFilters['category_id'] = Mage::app()
                ->getStore($this->_productLimitationFilters['store_id'])
                ->getRootCategoryId();
        }

        return $this;
    }

    /**
     * Join website product limitation
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _productLimitationJoinWebsite()
    {
        $joinWebsite = false;
        $filters     = $this->_productLimitationFilters;
        $conditions  = array('product_website.product_id = e.entity_id');

        if (isset($filters['website_ids'])) {
            $joinWebsite = true;
            if (count($filters['website_ids']) > 1) {
                $this->getSelect()->distinct(true);
            }
            $conditions[] = $this->getConnection()
                ->quoteInto('product_website.website_id IN(?)', $filters['website_ids']);
        } elseif (isset($filters['store_id'])
            && (!isset($filters['visibility']) && !isset($filters['category_id']))
            && !$this->isEnabledFlat()
        ) {
            $joinWebsite = true;
            $websiteId = Mage::app()->getStore($filters['store_id'])->getWebsiteId();
            $conditions[] = $this->getConnection()
                ->quoteInto('product_website.website_id = ?', $websiteId);
        }

        $fromPart = $this->getSelect()->getPart(Zend_Db_Select::FROM);
        if (isset($fromPart['product_website'])) {
            if (!$joinWebsite) {
                unset($fromPart['product_website']);
            } else {
                $fromPart['product_website']['joinCondition'] = join(' AND ', $conditions);
            }
            $this->getSelect()->setPart(Zend_Db_Select::FROM, $fromPart);
        } elseif ($joinWebsite) {
            $this->getSelect()->join(
                array('product_website' => $this->getTable('catalog/product_website')),
                join(' AND ', $conditions),
                array()
            );
        }

        return $this;
    }

    /**
     * Join additional (alternative) store visibility filter
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _productLimitationJoinStore()
    {
        $filters = $this->_productLimitationFilters;
        if (!isset($filters['store_table'])) {
            return $this;
        }

        $hasColumn = false;
        foreach ($this->getSelect()->getPart(Zend_Db_Select::COLUMNS) as $columnEntry) {
            list(,,$alias) = $columnEntry;
            if ($alias == 'visibility') {
                $hasColumn = true;
            }
        }
        if (!$hasColumn) {
            $this->getSelect()->columns('visibility', 'cat_index');
        }

        $fromPart = $this->getSelect()->getPart(Zend_Db_Select::FROM);
        if (!isset($fromPart['store_index'])) {
            $this->getSelect()->joinLeft(
                array('store_index' => $this->getTable('core/store')),
                'store_index.store_id = ' . $filters['store_table'] . '.store_id',
                array()
            );
        }
        if (!isset($fromPart['store_group_index'])) {
            $this->getSelect()->joinLeft(
                array('store_group_index' => $this->getTable('core/store_group')),
                'store_index.group_id = store_group_index.group_id',
                array()
            );
        }
        if (!isset($fromPart['store_cat_index'])) {
            $this->getSelect()->joinLeft(
                array('store_cat_index' => $this->getTable('catalog/category_product_index')),
                join(' AND ', array(
                    'store_cat_index.product_id = e.entity_id',
                    'store_cat_index.store_id = ' . $filters['store_table'] . '.store_id',
                    'store_cat_index.category_id=store_group_index.root_category_id'
                )),
                array('store_visibility' => 'visibility')
            );
        }
        // Avoid column duplication problems
        Mage::getResourceHelper('core')->prepareColumnsList($this->getSelect());

        $whereCond = join(' OR ', array(
            $this->getConnection()->quoteInto('cat_index.visibility IN(?)', $filters['visibility']),
            $this->getConnection()->quoteInto('store_cat_index.visibility IN(?)', $filters['visibility'])
        ));

        $wherePart = $this->getSelect()->getPart(Zend_Db_Select::WHERE);
        $hasCond   = false;
        foreach ($wherePart as $cond) {
            if ($cond == '(' . $whereCond . ')') {
                $hasCond = true;
            }
        }

        if (!$hasCond) {
            $this->getSelect()->where($whereCond);
        }

        return $this;
    }

    /**
     * Join Product Price Table
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _productLimitationJoinPrice()
    {
        return $this->_productLimitationPrice();
    }

    /**
     * Join Product Price Table with left-join possibility
     *
     * @see Mage_Catalog_Model_Resource_Product_Collection::_productLimitationJoinPrice()
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _productLimitationPrice($joinLeft = false)
    {
        $filters = $this->_productLimitationFilters;
        if (empty($filters['use_price_index'])) {
            return $this;
        }

        $helper     = Mage::getResourceHelper('core');
        $connection = $this->getConnection();
        $select     = $this->getSelect();
        $joinCond   = join(' AND ', array(
            'price_index.entity_id = e.entity_id',
            $connection->quoteInto('price_index.website_id = ?', $filters['website_id']),
            $connection->quoteInto('price_index.customer_group_id = ?', $filters['customer_group_id'])
        ));

        $fromPart = $select->getPart(Zend_Db_Select::FROM);
        if (!isset($fromPart['price_index'])) {
            $least       = $connection->getLeastSql(array('price_index.min_price', 'price_index.tier_price'));
            $minimalExpr = $connection->getCheckSql('price_index.tier_price IS NOT NULL',
                $least, 'price_index.min_price');
            $colls       = array('price', 'tax_class_id', 'final_price',
                'minimal_price' => $minimalExpr , 'min_price', 'max_price', 'tier_price');
            $tableName = array('price_index' => $this->getTable('catalog/product_index_price'));
            if ($joinLeft) {
                $select->joinLeft($tableName, $joinCond, $colls);
            } else {
                $select->join($tableName, $joinCond, $colls);
            }
            // Set additional field filters
            foreach ($this->_priceDataFieldFilters as $filterData) {
                $select->where(call_user_func_array('sprintf', $filterData));
            }
        } else {
            $fromPart['price_index']['joinCondition'] = $joinCond;
            $select->setPart(Zend_Db_Select::FROM, $fromPart);
        }
        //Clean duplicated fields
        $helper->prepareColumnsList($select);


        return $this;
    }

    /**
     * Apply front-end price limitation filters to the collection
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function applyFrontendPriceLimitations()
    {
        $this->_productLimitationFilters['use_price_index'] = true;
        if (!isset($this->_productLimitationFilters['customer_group_id'])) {
            $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
            $this->_productLimitationFilters['customer_group_id'] = $customerGroupId;
        }
        if (!isset($this->_productLimitationFilters['website_id'])) {
            $websiteId = Mage::app()->getStore($this->getStoreId())->getWebsiteId();
            $this->_productLimitationFilters['website_id'] = $websiteId;
        }
        $this->_applyProductLimitations();
        return $this;
    }

    /**
     * Apply limitation filters to collection
     * Method allows using one time category product index table (or product website table)
     * for different combinations of store_id/category_id/visibility filter states
     * Method supports multiple changes in one collection object for this parameters
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _applyProductLimitations()
    {
        Mage::dispatchEvent('catalog_product_collection_apply_limitations_before', array(
            'collection'  => $this,
            'category_id' => isset($this->_productLimitationFilters['category_id'])
                ? $this->_productLimitationFilters['category_id']
                : null,
        ));
        $this->_prepareProductLimitationFilters();
        $this->_productLimitationJoinWebsite();
        $this->_productLimitationJoinPrice();
        $filters = $this->_productLimitationFilters;

        if (!isset($filters['category_id']) && !isset($filters['visibility'])) {
            return $this;
        }

        $conditions = array(
            'cat_index.product_id=e.entity_id',
            $this->getConnection()->quoteInto('cat_index.store_id=?', $filters['store_id'])
        );
        if (isset($filters['visibility']) && !isset($filters['store_table'])) {
            $conditions[] = $this->getConnection()
                ->quoteInto('cat_index.visibility IN(?)', $filters['visibility']);
        }

        if (!$this->getFlag('disable_root_category_filter')) {
            $conditions[] = $this->getConnection()->quoteInto('cat_index.category_id = ?', $filters['category_id']);
        }

        if (isset($filters['category_is_anchor'])) {
            $conditions[] = $this->getConnection()
                ->quoteInto('cat_index.is_parent=?', $filters['category_is_anchor']);
        }

        $joinCond = join(' AND ', $conditions);
        $fromPart = $this->getSelect()->getPart(Zend_Db_Select::FROM);
        if (isset($fromPart['cat_index'])) {
            $fromPart['cat_index']['joinCondition'] = $joinCond;
            $this->getSelect()->setPart(Zend_Db_Select::FROM, $fromPart);
        }
        else {
            $this->getSelect()->join(
                array('cat_index' => $this->getTable('catalog/category_product_index')),
                $joinCond,
                array('cat_index_position' => 'position')
            );
        }

        $this->_productLimitationJoinStore();

        Mage::dispatchEvent('catalog_product_collection_apply_limitations_after', array(
            'collection' => $this
        ));

        return $this;
    }

    /**
     * Apply limitation filters to collection base on API
     * Method allows using one time category product table
     * for combinations of category_id filter states
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _applyZeroStoreProductLimitations()
    {
        $filters = $this->_productLimitationFilters;

        $conditions = array(
            'cat_pro.product_id=e.entity_id',
            $this->getConnection()->quoteInto('cat_pro.category_id=?', $filters['category_id'])
        );
        $joinCond = join(' AND ', $conditions);

        $fromPart = $this->getSelect()->getPart(Zend_Db_Select::FROM);
        if (isset($fromPart['cat_pro'])) {
            $fromPart['cat_pro']['joinCondition'] = $joinCond;
            $this->getSelect()->setPart(Zend_Db_Select::FROM, $fromPart);
        }
        else {
            $this->getSelect()->join(
                array('cat_pro' => $this->getTable('catalog/category_product')),
                $joinCond,
                array('cat_index_position' => 'position')
            );
        }
        $this->_joinFields['position'] = array(
            'table' => 'cat_pro',
            'field' => 'position',
        );

        return $this;
    }

    /**
     * Add category ids to loaded items
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function addCategoryIds()
    {
        if ($this->getFlag('category_ids_added')) {
            return $this;
        }
        $ids = array_keys($this->_items);
        if (empty($ids)) {
            return $this;
        }

        $select = $this->getConnection()->select();

        $select->from($this->_productCategoryTable, array('product_id', 'category_id'));
        $select->where('product_id IN (?)', $ids);

        $data = $this->getConnection()->fetchAll($select);

        $categoryIds = array();
        foreach ($data as $info) {
            if (isset($categoryIds[$info['product_id']])) {
                $categoryIds[$info['product_id']][] = $info['category_id'];
            } else {
                $categoryIds[$info['product_id']] = array($info['category_id']);
            }
        }


        foreach ($this->getItems() as $item) {
            $productId = $item->getId();
            if (isset($categoryIds[$productId])) {
                $item->setCategoryIds($categoryIds[$productId]);
            } else {
                $item->setCategoryIds(array());
            }
        }

        $this->setFlag('category_ids_added', true);
        return $this;
    }

    /**
     * Add tier price data to loaded items
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function addTierPriceData()
    {
        if ($this->getFlag('tier_price_added')) {
            return $this;
        }

        $tierPrices = array();
        $productIds = array();
        foreach ($this->getItems() as $item) {
            $productIds[] = $item->getId();
            $tierPrices[$item->getId()] = array();
        }
        if (!$productIds) {
            return $this;
        }

        /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        $attribute = $this->getAttribute('tier_price');
        if ($attribute->isScopeGlobal()) {
            $websiteId = 0;
        } else if ($this->getStoreId()) {
            $websiteId = Mage::app()->getStore($this->getStoreId())->getWebsiteId();
        }

        $adapter   = $this->getConnection();
        $columns   = array(
            'price_id'      => 'value_id',
            'website_id'    => 'website_id',
            'all_groups'    => 'all_groups',
            'cust_group'    => 'customer_group_id',
            'price_qty'     => 'qty',
            'price'         => 'value',
            'product_id'    => 'entity_id'
        );
        $select  = $adapter->select()
            ->from($this->getTable('catalog/product_attribute_tier_price'), $columns)
            ->where('entity_id IN(?)', $productIds)
            ->order(array('entity_id','qty'));

        if ($websiteId == '0') {
            $select->where('website_id = ?', $websiteId);
        } else {
            $select->where('website_id IN(?)', array('0', $websiteId));
        }

        foreach ($adapter->fetchAll($select) as $row) {
            $tierPrices[$row['product_id']][] = array(
                'website_id'    => $row['website_id'],
                'cust_group'    => $row['all_groups'] ? Mage_Customer_Model_Group::CUST_GROUP_ALL : $row['cust_group'],
                'price_qty'     => $row['price_qty'],
                'price'         => $row['price'],
                'website_price' => $row['price'],

            );
        }

        /* @var $backend Mage_Catalog_Model_Product_Attribute_Backend_Tierprice */
        $backend = $attribute->getBackend();

        foreach ($this->getItems() as $item) {
            $data = $tierPrices[$item->getId()];
            if (!empty($data) && $websiteId) {
                $data = $backend->preparePriceData($data, $item->getTypeId(), $websiteId);
            }
            $item->setData('tier_price', $data);
        }

        $this->setFlag('tier_price_added', true);
        return $this;
    }

    /**
     * Add field comparison expression
     *
     * @param string $comparisonFormat - expression for sprintf()
     * @param array $fields - list of fields
     * @return Mage_Catalog_Model_Resource_Product_Collection
     * @throws Exception
     */
    public function addPriceDataFieldFilter($comparisonFormat, $fields)
    {
        if (!preg_match('/^%s( (<|>|=|<=|>=|<>) %s)*$/', $comparisonFormat)) {
            throw new Exception('Invalid comparison format.');
        }

        if (!is_array($fields)) {
            $fields = array($fields);
        }
        foreach ($fields as $key => $field) {
            $fields[$key] = $this->_getMappedField($field);
        }

        $this->_priceDataFieldFilters[] = array_merge(array($comparisonFormat), $fields);
        return $this;
    }

    /**
     * Clear collection
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function clear()
    {
        foreach ($this->_items as $i => $item) {
            if ($item->hasStockItem()) {
                $item->unsStockItem();
            }
            $item = $this->_items[$i] = null;
        }

        foreach ($this->_itemsById as $i => $item) {
            $item = $this->_itemsById[$i] = null;
        }

        unset($this->_items, $this->_data, $this->_itemsById);
        $this->_data = array();
        $this->_itemsById = array();
        return parent::clear();
    }

    /**
     * Set Order field
     *
     * @param string $attribute
     * @param string $dir
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function setOrder($attribute, $dir = 'desc')
    {
        if ($attribute == 'price') {
            $this->addAttributeToSort($attribute, $dir);
        } else {
            parent::setOrder($attribute, $dir);
        }
        return $this;
    }

    /**
     * Get products max price
     *
     * @return float
     */
    public function getMaxPrice()
    {
        if (is_null($this->_maxPrice)) {
            $this->_prepareStatisticsData();
        }

        return $this->_maxPrice;
    }

    /**
     * Get products min price
     *
     * @return float
     */
    public function getMinPrice()
    {
        if (is_null($this->_minPrice)) {
            $this->_prepareStatisticsData();
        }

        return $this->_minPrice;
    }

    /**
     * Get standard deviation of products price
     *
     * @return float
     */
    public function getPriceStandardDeviation()
    {
        if (is_null($this->_priceStandardDeviation)) {
            $this->_prepareStatisticsData();
        }

        return $this->_priceStandardDeviation;
    }


    /**
     * Get count of product prices
     *
     * @return int
     */
    public function getPricesCount()
    {
        if (is_null($this->_pricesCount)) {
            $this->_prepareStatisticsData();
        }

        return $this->_pricesCount;
    }
}