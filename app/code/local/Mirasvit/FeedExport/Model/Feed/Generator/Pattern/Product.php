<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced Product Feeds
 * @version   1.1.4
 * @build     702
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_FeedExport_Model_Feed_Generator_Pattern_Product
    extends Mirasvit_FeedExport_Model_Feed_Generator_Pattern
{
    private static $_parentProductsCache = array();
    private $_dynamicCategory = array();

    public function getValue($pattern, $product)
    {
        $value = null;
        $pattern = $this->parsePattern($pattern);

        $this->evalValue($pattern, $value, $product);

        if ($pattern['type'] == 'parent') {
            $product = $this->getParentProduct($product);
        } elseif ($pattern['type'] == 'only_parent') {
            $product = $this->getParentProduct($product, true);
        }

        if (in_array($pattern['type'], array('grouped', 'salable_grouped'))) {
            $products = $this->_getChildProducts($product, ($pattern['type'] == 'salable_grouped'));
            $values = array();
            $childPattern = $pattern;
            $childPattern['type'] = null;
            foreach ($products as $child) {
                $child = $child->load($child->getId());
                $value = $this->getValue($childPattern, $child);
                if ($value) {
                    $values[] = $value;
                }
            }

            $value = implode(',', $values);

            return $value;
        }

        switch ($pattern['key']) {
            case 'url':
                $value = Mage::helper('feedexport')->getProductUrl($product, $this->getFeed()->getStoreId());

                if ($product->getConfigOptions() && 0) { //enable if nessesary
                    $value .= strpos($value, '?') !== false ? '&' : '?';
                    $value .= $product->getConfigOptions();
                }

                if ($this->getFeed()) {
                    $getParams = array();

                    if ($this->getFeed()->getReportEnabled()) {
                        $getParams['fee'] = $this->getFeed()->getId();
                        $getParams['fep'] = $product->getId();
                    }

                    $patternModel = Mage::getSingleton('feedexport/feed_generator_pattern');
                    if ($this->getFeed()->getGaSource()) {
                        $getParams['utm_source'] = $patternModel->getPatternValue($this->getFeed()->getGaSource(), 'product', $product);
                    }
                    if ($this->getFeed()->getGaMedium()) {
                        $getParams['utm_medium'] = $patternModel->getPatternValue($this->getFeed()->getGaMedium(), 'product', $product);
                    }
                    if ($this->getFeed()->getGaName()) {
                        $getParams['utm_campaign'] = $patternModel->getPatternValue($this->getFeed()->getGaName(), 'product', $product);
                    }
                    if ($this->getFeed()->getGaTerm()) {
                        $getParams['utm_term'] = $patternModel->getPatternValue($this->getFeed()->getGaTerm(), 'product', $product);
                    }
                    if ($this->getFeed()->getGaContent()) {
                        $getParams['utm_content'] = $patternModel->getPatternValue($this->getFeed()->getGaContent(), 'product', $product);
                    }

                    if (count($getParams)) {
                        $value .= strpos($value, '?') !== false ? '&' : '?';
                        $value .= http_build_query($getParams);
                    }
                }

                break;

            case 'image':
            case 'thumbnail':
            case 'small_image':
                $this->imageValue($pattern, $value, $product);
                break;

            case 'image1':
            case 'image2':
            case 'image3':
            case 'image4':
            case 'image5':
            case 'image6':
            case 'image7':
            case 'image8':
            case 'image9':
            case 'image10':
            case 'image11':
            case 'image12':
            case 'image13':
            case 'image14':
            case 'image15':
                $this->imageGalleryValue($pattern, $value, $product);

                break;

            case 'qty':
                $stockItem = $product->getStockItem();
                if (!($stockItem && $stockItem->getData('item_id'))) {
                    $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
                }
                if ($stockItem && $stockItem->getData('item_id')) {
                    $product->setStockItem($stockItem);
                    $value = ceil($stockItem->getQty());
                } else {
                    $value = 0;
                }
                $value = intval($value);

                break;

            case 'parent_qty':
                $value = 0;
                if ($product->getTypeId() == 'configurable') {
                    $childIds = Mage::getModel('catalog/product_type_configurable')
                        ->getChildrenIds($product->getId());
                    if (is_array($childIds) && isset($childIds[0])) {
                        $childCollection = Mage::getModel('catalog/product')->getCollection()
                            ->addFieldToFilter('entity_id', array('in' => $childIds[0]))
                            ->joinField(
                                'qty',
                                'cataloginventory/stock_item',
                                'qty',
                                'product_id = entity_id',
                                '{{table}}.stock_id = 1',
                                'left'
                            );
                        foreach ($childCollection as $child) {
                            if ($child->getIsSalable() == 1) {
                                $value += $child->getQty();
                            }
                        }
                    }
                }
                break;

            case 'is_in_stock':
                $stockItem = $product->getStockItem();
                if (!($stockItem && $stockItem->getData('item_id'))) {
                    $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
                }
                if ($stockItem) {
                    $value = $stockItem->getIsInStock();
                } else {
                    $value = 0;
                }
                break;

            case 'category_id':
                $this->_prepareProductCategory($product);
                $value = $product->getData('category_id');
                break;

            case 'category_ids':
                $value = implode(', ', $product->getCategoryIds());
                break;

            case 'category':
                $this->_prepareProductCategory($product);
                $value = $product->getCategory();
                break;

            case 'category_url':
                $this->_prepareProductCategory($product);
                if ($product->getCategoryModel()) {
                    $value = $product->getCategoryModel()->getUrl();
                }
                break;

            case 'category_path':
                $this->_prepareProductCategory($product);
                $value = $product->getCategoryPath();
                break;

            case 'category_paths':
                $this->_prepareProductCategories($product);
                $value = $product->getCategoryPaths();
                break;

            case 'price':
                $value = Mage::helper('tax')->getPrice($product, $product->getPrice());
                break;

            case 'final_price':
                if ($product->getTypeId() == 'bundle') {
                    $bundle = Mage::getModel('bundle/product_price');
                    $prices = $bundle->getTotalPrices($product);
                    if (isset($prices[0])) {
                        $value = $prices[0];
                        break;
                    }
                } else {
                    $value = Mage::helper('tax')->getPrice($product, $product->getFinalPrice());
                }

                break;

            case 'store_price':
                $value = $this->getStore()->convertPrice($product->getFinalPrice(), false, false);
                break;

            case 'base_price':
                $value = $product->getPrice();
                break;

            case 'tier_price':
                $tierPrice = $product->getTierPrice();
                if (count($tierPrice)) {
                    $value = $tierPrice[0]['price'];
                }
                break;

            case 'min_price':
                if ($product->getTypeId() == 'bundle') {
                    $bundle = Mage::getModel('bundle/product_price');
                    $prices = $bundle->getTotalPrices($product);
                    if (isset($prices[0])) {
                        $value = $prices[0];
                        break;
                    }
                } else {
                    $value = Mage::helper('tax')->getPrice($product, $product->getFinalPrice());
                }

                $tierPrice = $product->getTierPrice();
                if (count($tierPrice)) {
                    foreach ($tierPrice as $key => $it) {
                        if ($value > $it['price'] && $it['price'] > 0) {
                            $value = $it['price'];
                        }
                    }
                }
            break;

            case 'group_price':
                $groupPrice = $product->getData('group_price');
                if (count($groupPrice)) {
                    $value = $groupPrice[0]['price'];
                }
                break;

            case 'attribute_set':
                $attributeSetModel = Mage::getModel('eav/entity_attribute_set');
                $attributeSetModel->load($product->getAttributeSetId());

                $value = $attributeSetModel->getAttributeSetName();
                break;

            case 'weight':
                if ($product->getTypeId() == 'bundle') {
                    $selectionCollection = $product->getTypeInstance(true)->getSelectionsCollection(
                        $product->getTypeInstance(true)->getOptionsIds($product), $product
                    );
                    $productIds = array(0);
                    $productQts = array();
                    foreach ($selectionCollection as $option) {
                        $productIds[] = $option->product_id;
                        $productQts[$option->product_id] = $option->getSelectionQty();
                    }
                    $collection = Mage::getModel('catalog/product')->getCollection()
                        ->addAttributeToSelect('weight')
                        ->addFieldToFilter('entity_id', array('in' => $productIds));
                    $value = 0;
                    foreach ($collection as $subProduct) {
                        $weight = $subProduct->getWeight();
                        $qty = $productQts[$subProduct->getEntityId()];
                        intval($qty > 0) ? intval($qty) : 1;
                        $value += $weight * $qty;
                    }
                } else {
                    $value = $product->getData('weight');
                }
                break;

            case 'rating_summary':
                $summaryData = Mage::getModel('review/review_summary')->load($product->getId());
                $value = $summaryData->getRatingSummary() * 0.05;
                break;

            case 'reviews_count':
                $summaryData = Mage::getModel('review/review_summary')->load($product->getId());
                $value = $summaryData->getReviewsCount();
                break;

            default:
                if (substr($pattern['key'], 0, strlen('group_price')) == 'group_price') {
                    $custId = substr($pattern['key'], strlen('group_price'));
                    $groupPrice = $product->getData('group_price');
                    if (is_array($groupPrice)) {
                        foreach ($groupPrice as $key => $price) {
                            if ($price['cust_group'] == $custId) {
                                $value = $price['price'];
                            }
                        }
                    }
                    break;
                }

                $attribute = $this->_getProductAttribute($pattern['key']);
                if ($attribute) {
                    if ($attribute->getFrontendInput() == 'select' || $attribute->getFrontendInput() == 'multiselect') {
                        $value = $product->getResource()
                            ->getAttribute($pattern['key'])
                                ->getSource()
                                ->getOptionText($product->getData($pattern['key']));
                        $value = implode(', ', (array) $value);
                    } else {
                        $value = $product->getData($pattern['key']);
                    }
                } else {
                    if ($product->hasData($pattern['key'])) {
                        $value = $product->getData($pattern['key']);
                    }
                }
        }

        $this->dynamicAttributeValue($pattern, $value, $product);
        $this->dynamicCategoryValue($pattern, $value, $product);
        $this->amastyMetaValue($pattern, $value, $product);

        if (!$value || $value == '') {
            if ($pattern['type'] == 'parent_if_empty') {
                $parent = $this->getParentProduct($product, true);
                $pattern['type'] = '';
                $value = $this->getValue($pattern, $parent);
            }
        }

        $value = $this->applyFormatters($pattern, $value);

        return $value;
    }

    public function evalValue($arPattern, &$value, $obj)
    {
        if ($arPattern['type'] === 'parent') {
            $obj = $this->getParentProduct($obj);
        }
        if (substr($arPattern['key'], 0, 1) == '(') {
            // extract base product variables
            extract($obj->getData());
            // extract variables from pattern
            $matches = array();
            preg_match_all('/\$[a-zA-Z0-9_]*/', $arPattern['key'], $matches);
            if (isset($matches[0])) {
                foreach ($matches[0] as $variable) {
                    $variable = substr($variable, 1);
                    $$variable = $this->getValue('{'.$variable.'}', $obj);
                }
            }

            // execute expression
            $eval = substr($arPattern['key'], 1, strlen($arPattern['key']) - 2);
            $value = eval($eval);
        }
    }

    public function imageValue($arPattern, &$value, $obj)
    {
        $mediaUrl = Mage::getBaseUrl('media');
        $image = $obj->getData($arPattern['key']);
        if (!$image || $image == 'no_selection') {
            $value = '';
        } else {
            if ($arPattern['additional']) {
                $size = explode('x', $arPattern['additional']);
                $w = intval($size[0]) ? intval($size[0]) : null;
                $h = intval($size[1]) ? intval($size[1]) : null;

                $value = Mage::helper('mstcore/image')->init($obj, $arPattern['key'], 'catalog/product')->resize($w, $h)->__toString();
            } else {
                $parsedUrl = parse_url($image);
                if (isset($parsedUrl['host'])) {
                    $value = $image;
                } else {
                    $value = $mediaUrl.'catalog/product'.$image;
                }
            }
        }
    }

    public function imageGalleryValue($arPattern, &$value, $obj)
    {
        $mediaUrl = Mage::getBaseUrl('media');
        if (!$obj->hasData('media_gallery_images')) {
            $tmpProduct = Mage::getModel('catalog/product')->load($obj->getId());
            $obj->setData('media_gallery_images', $tmpProduct->getMediaGalleryImages());
        }
        $i = 1;
        foreach ($obj->getMediaGalleryImages() as $image) {
            if ('image'.$i == $arPattern['key']) {
                $value = $image['url'];
            }
            ++$i;
        }
    }

    public function dynamicAttributeValue($arPattern, &$value, $obj)
    {
        if ($arPattern['key'] == 'custom') {
            $customAttribute = Mage::getModel('feedexport/dynamic_attribute')->getCollection()
                ->addFieldToFilter('code', $arPattern['additional'])
                ->getFirstItem();
            $customAttribute = $customAttribute->load($customAttribute->getId());
            if ($customAttribute->getId()) {
                $value = $customAttribute->getValue($obj);
            }
        }
    }

    public function dynamicCategoryValue($arPattern, &$value, $obj)
    {
        if ($arPattern['key'] == 'mapping') {
            $this->_prepareProductCategory($obj);

            $mappingId = $arPattern['additional'];
            if (!isset($this->_dynamicCategory[$mappingId])) {
                $this->_dynamicCategory[$mappingId] = Mage::getModel('feedexport/dynamic_category')->load($mappingId);
            }

            if ($this->_dynamicCategory[$mappingId]->getId()) {
                $mappingCategory = $this->_dynamicCategory[$mappingId];

                $value = $mappingCategory->getMappingValue($obj->getData('category_id'));
                if (null == $value) {
                    foreach (array_reverse($obj->getCategoryIds()) as $category) {
                        $value = $mappingCategory->getMappingValue($category);
                        if (null != $value) {
                            break;
                        }
                    }
                }
            }
        }

        if ($arPattern['key'] == 'mappings') {
            $this->_prepareProductCategory($obj);

            $mappingId = $arPattern['additional'];
            if (!isset($this->_dynamicCategory[$mappingId])) {
                $this->_dynamicCategory[$mappingId] = Mage::getModel('feedexport/dynamic_category')->load($mappingId);
            }

            if ($this->_dynamicCategory[$mappingId]->getId()) {
                $maps = array();
                $mappingCategory = $this->_dynamicCategory[$mappingId];

                $this->_prepareProductCategories($obj);
                $ids = $obj->getCategoryIds();
                foreach ($ids as $id) {
                    $map = $mappingCategory->getMappingValue($id);
                    if (null == $map) {
                        foreach (array_reverse($obj->getCategoryIds()) as $category) {
                            $map = $mappingCategory->getMappingValue($category);
                            if (null != $value) {
                                break;
                            }
                        }
                    }

                    if ($map != null) {
                        $maps[] = $map;
                    }
                }

                $value = implode(', ', array_unique($maps));
            }
        }
    }

    public function amastyMetaValue($arPattern, &$value, $obj)
    {
        if ($arPattern['key'] == 'ammeta') {
            $amHelper = Mage::helper('ammeta');
            $attributeCode = $arPattern['additional'];
            $arPattern = Mage::getStoreConfig('ammeta/product/'.$attributeCode);

            if ($arPattern) {
                $value = $amHelper->parse($obj, $arPattern);

                $max = (int) Mage::getStoreConfig('ammeta/general/max_'.$attributeCode);

                if ($max) {
                    $value = substr($value, 0, $max);
                }
            }
        }
    }

    public function getParentProduct(Varien_Object $product, $isOnlyParent = false)
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('read');
        $table = Mage::getSingleton('core/resource')->getTableName('catalog_product_relation');

        $parentId = $connection->fetchOne(
            'SELECT `parent_id` FROM '.$table.' WHERE `child_id` = '.intval($product->getEntityId())
        );

        if ($parentId > 0) {
            if (!isset(self::$_parentProductsCache[$parentId])) {
                $parent = Mage::getModel('catalog/product')->load($parentId);
                if ($this->getFeed()) {
                    $parent->setStoreId($this->getFeed()->getStoreId());
                }

                if ($parent->isConfigurable()) {
                    $childProducts = Mage::getModel('catalog/product_type_configurable')
                        ->getUsedProducts(null, $parent);
                    $attrs = $parent->getTypeInstance(true)->getConfigurableAttributesAsArray($parent);

                    foreach ($attrs as $attr) {
                        foreach ($childProducts as $cp) {
                            if ($cp->getData($attr['attribute_code'])) {
                                foreach ($attr['values'] as $value) {
                                    if ($value['value_index'] == $cp->getData($attr['attribute_code'])) {
                                        $configOptions = $cp->getData('config_options');
                                        if (!is_array($configOptions)) {
                                            $configOptions = array();
                                        }
                                        $configOptions[] = $attr['attribute_id'].'='.$value['value_index'];

                                        $cp->setData('config_options', $configOptions);
                                    }
                                }
                            }
                        }
                    }

                    foreach ($childProducts as $cp) {
                        if ($cp->getId() == $product->getId()) {
                            $configOptions = $cp->getData('config_options');

                            if (!is_array($configOptions)) {
                                $configOptions = array();
                            }

                            $parent->setConfigOptions(implode('&', $configOptions));
                        }
                    }
                }
                self::$_parentProductsCache[$parentId] = $parent;
            }
        }

        if (!$parentId || !self::$_parentProductsCache[$parentId]->getId()) {
            if ($isOnlyParent) {
                $copy = clone $product;
                $copy->setData(array('entity_id' => $copy->getId()));

                return $copy;
            } else {
                return $product;
            }
        }

        return self::$_parentProductsCache[$parentId];
    }

    protected function _getChildProducts($product, $isOnlySalable = false)
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('read');
        $table = Mage::getSingleton('core/resource')->getTableName('catalog_product_relation');
        $childIds = array(0);

        $rows = $connection->fetchAll(
            'SELECT `child_id` FROM '.$table.' WHERE `parent_id` = '.intval($product->getEntityId())
        );

        foreach ($rows as $row) {
            $childIds[] = $row['child_id'];
        }

        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addFieldToFilter('entity_id', array('in' => $childIds));
        if ($isOnlySalable) {
            $collection->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                ->joinField(
                    'qty',
                    'cataloginventory/stock_item',
                    'qty',
                    'product_id = entity_id',
                    '{{table}}.is_in_stock = 1 AND {{table}}.qty > 0'
                );
        }

        return $collection;
    }

    protected function _prepareProductCategory(&$product)
    {
        $category = null;
        $currentPosition = null;

        $collection = Mage::getModel('catalog/category')->getCollection();
        $collection->getSelect()
            ->joinInner(
                array('category_product' => $collection->getTable('catalog/category_product')),
                'category_product.category_id = entity_id AND category_product.product_id = '.$product->getId(),
                array('product_position' => 'position')
            )
            ->order(new Zend_Db_Expr('`category_product`.`position` asc'));

        foreach ($collection as $cat) {
            if ((is_null($category) || $cat->getLevel() > $category->getLevel()) &&
                (is_null($currentPosition) || $cat->getProductPosition() <= $currentPosition)
            ) {
                $category = $cat;
                $currentPosition = $category->getProductPosition();
            }
        }

        if ($category && $category = $this->getCategory($category->getId())) {
            $categoryPath = array($category->getName());
            $parentId = $category->getParentId();

            if ($category->getLevel() > $this->getRootCategory()->getLevel()) {
                $i = 0;
                while ($_category = $this->getCategory($parentId)) {
                    if ($_category->getLevel() <= $this->getRootCategory()->getLevel()) {
                        break;
                    }
                    $categoryPath[] = $_category->getName();
                    $parentId = $_category->getParentId();

                    ++$i;
                    if ($i > 10 || $parentId == 0) {
                        break;
                    }
                }
            }

            $product->setCategory($category->getName());
            $product->setCategoryModel($category);
            $product->setCategoryId($category->getEntityId());
            $product->setCategoryPath(implode(' > ', array_reverse($categoryPath)));
        } else {
            $product->setCategory('');
            $product->setCategorySubcategory('');
        }
    }

    protected function _prepareProductCategories(&$product)
    {
        $paths = array();
        $ids = array();

        $collection = Mage::getModel('catalog/category')->getCollection();
        $collection->getSelect()
            ->joinInner(
                array('category_product' => $collection->getTable('catalog/category_product')),
                'category_product.category_id = entity_id AND category_product.product_id = '.$product->getId(),
                array('product_position' => 'position')
            )
            ->order(new Zend_Db_Expr('`category_product`.`position` asc'));
        foreach ($collection as $category) {
            if ($category && $category = $this->getCategory($category->getId())) {
                $categoryPath = array($category->getName());
                $parentId = $category->getParentId();
                if ($category->getLevel() > $this->getRootCategory()->getLevel()) {
                    $i = 0;
                    while ($_category = $this->getCategory($parentId)) {
                        if ($_category->getLevel() <= $this->getRootCategory()->getLevel()) {
                            break;
                        }
                        $categoryPath[] = $_category->getName();
                        $parentId = $_category->getParentId();

                        ++$i;
                        if ($i > 10 || $parentId == 0) {
                            break;
                        }
                    }
                }

                $ids[] = $category->getId();
                $paths[] = implode(' > ', array_reverse($categoryPath));
            }
        }

        $product->setCategoryPaths(implode(',', $paths));
        $product->setCategoryIds($ids);

        return $this;
    }
}
