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


class Mirasvit_FeedExport_Model_Rule_Condition_Product extends Mage_Rule_Model_Condition_Abstract
{
    protected $_entityAttributeValues = null;

    public function getAttributeObject()
    {
        try {
            $obj = Mage::getSingleton('eav/config')
                ->getAttribute('catalog_product', $this->getAttribute());
        }
        catch (Exception $e) {
            $obj = new Varien_Object();
            $obj->setEntity(Mage::getResourceSingleton('catalog/product'))
                ->setFrontendInput('text');
        }
        return $obj;
    }

    protected function _addSpecialAttributes(array &$attributes)
    {
        $attributes = array_merge($attributes, array(
            'attribute_set_id' => __('Attribute Set'),
            'category_ids'     => __('Category'),
            'qty'              => __('Quantity'),
            'type_id'          => __('Product Type'),
            'image'            => __('Base Image'),
            'thumbnail'        => __('Thumbnail'),
            'small_image'      => __('Small Image'),
            'image_size'       => __('Base Image Size (bytes)'),
            'thumbnail_size'   => __('Thumbnail Size (bytes)'),
            'small_image_size' => __('Small Image Size (bytes)'),
            'php'              => __('PHP Condition'),
            'is_in_stock'      => __('Stock Availability'),
            'manage_stock'     => __('Manage Stock'),
            'status_parent'    => Mage::helper('feedexport')->__('Status(Parent Product)'),
            'is_salable'       => __('Is Salable'),
        ));
    }

    public function loadAttributeOptions()
    {
        $productAttributes = Mage::getResourceSingleton('catalog/product')
            ->loadAllAttributes()
            ->getAttributesByCode();

        $attributes = array();
        foreach ($productAttributes as $attribute) {
            if (!$attribute->isAllowedForRuleCondition()) {
                continue;
            }
            $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }

        $this->_addSpecialAttributes($attributes);

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    protected function _prepareValueOptions()
    {
        // Check that both keys exist. Maybe somehow only one was set not in this routine, but externally.
        $selectReady = $this->getData('value_select_options');
        $hashedReady = $this->getData('value_option');
        if ($selectReady && $hashedReady) {
            return $this;
        }

        // Get array of select options. It will be used as source for hashed options
        $selectOptions = null;
        if ($this->getAttribute() === 'attribute_set_id') {
            $entityTypeId = Mage::getSingleton('eav/config')
                ->getEntityType('catalog_product')->getId();
            $selectOptions = Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter($entityTypeId)
                ->load()
                ->toOptionArray();
        } elseif ($this->getAttribute() === 'is_in_stock') {
            $selectOptions = array();
            $options = Mage::getSingleton('cataloginventory/source_stock')->toOptionArray();
            foreach ($options as $option) {
                $selectOptions[$option['value']] = $option;
            }
        } elseif ($this->getAttribute() === 'is_salable') {
            $selectOptions = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();
        } elseif ($this->getAttribute() === 'type_id') {
            $selectOptions = Mage::getSingleton('catalog/product_type')->getAllOptions();
        } elseif ($this->getAttribute() === 'status_parent') {
            $selectOptions = Mage_Catalog_Model_Product_Status::getAllOptions();
        } elseif (is_object($this->getAttributeObject())) {
            $attributeObject = $this->getAttributeObject();
            if ($attributeObject->usesSource()) {
                if ($attributeObject->getFrontendInput() == 'multiselect') {
                    $addEmptyOption = false;
                } else {
                    $addEmptyOption = true;
                }
                $selectOptions = $attributeObject->getSource()->getAllOptions($addEmptyOption);
            }
        }

        // Set new values only if we really got them
        if ($selectOptions !== null) {
            // Overwrite only not already existing values
            if (!$selectReady) {
                $this->setData('value_select_options', $selectOptions);
            }
            if (!$hashedReady) {
                $hashedOptions = array();
                foreach ($selectOptions as $o) {
                    if (is_array($o)) {
                        if (is_array($o['value'])) {
                            continue; // We cannot use array as index
                        }
                        $hashedOptions[$o['value']] = $o['label'];
                    }
                }
                $this->setData('value_option', $hashedOptions);
            }
        }

        return $this;
    }

    /**
     * Retrieve value by option
     *
     * @param mixed $option
     * @return string
     */
    public function getValueOption($option=null)
    {
        $this->_prepareValueOptions();
        return $this->getData('value_option'.(!is_null($option) ? '/'.$option : ''));
    }

    /**
     * Retrieve select option values
     *
     * @return array
     */
    public function getValueSelectOptions()
    {
        $this->_prepareValueOptions();
        return $this->getData('value_select_options');
    }

    /**
     * Retrieve after element HTML
     *
     * @return string
     */
    public function getValueAfterElementHtml()
    {
        $html = '';

        switch ($this->getAttribute()) {
            case 'sku': case 'category_ids':
                $image = Mage::getDesign()->getSkinUrl('images/rule_chooser_trigger.gif');
                break;
        }

        if (!empty($image)) {
            $html = '<a href="javascript:void(0)" class="rule-chooser-trigger">
                <img src="'.$image.'" alt="" class="v-middle rule-chooser-trigger" title="'
                .Mage::helper('rule')->__('Open Chooser') . '" /></a>';
        }
        return $html;
    }

    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    public function collectValidatedAttributes($productCollection)
    {
        $attribute = $this->getAttribute();

        if (!in_array($attribute, array('category_ids', 'qty', 'php', 'is_in_stock', 'manage_stock', 'status_parent', 'is_salable'))) {

            if ($attribute == 'image_size'
                || $attribute == 'small_image_size'
                || $attribute == 'thumbnail_size') {
                $attribute = str_replace('_size', '', $attribute);
            }

            $attributes = $this->getRule()->getCollectedAttributes();
            $attributes[$attribute] = true;
            $this->getRule()->setCollectedAttributes($attributes);
            $productCollection->addAttributeToSelect($attribute, 'left');
        }

        return $this;
    }

    public function getInputType()
    {
        if ($this->getAttribute() === 'attribute_set_id' ||
            $this->getAttribute() === 'type_id' ||
            $this->getAttribute() === 'is_in_stock' ||
            $this->getAttribute() === 'status_parent' ||
            $this->getAttribute() === 'is_salable'
        ) {
            return 'select';
        }
        if ($this->getAttribute() === 'manage_stock') {
            return 'boolean';
        }
        if (!is_object($this->getAttributeObject())) {
            return 'string';
        }
        switch ($this->getAttributeObject()->getFrontendInput()) {
            case 'select':
                return 'select';

            case 'multiselect':
                return 'multiselect';

            case 'date':
                return 'date';

            case 'boolean':
                return 'boolean';

            default:
                return 'string';
        }
    }

    public function getValueElementType()
    {
        if ($this->getAttribute() === 'attribute_set_id' ||
            $this->getAttribute() === 'type_id' ||
            $this->getAttribute() === 'is_in_stock' ||
            $this->getAttribute() === 'status_parent' ||
            $this->getAttribute() === 'is_salable'
        ) {
            return 'select';
        }
        if (!is_object($this->getAttributeObject())) {
            return 'text';
        }
        switch ($this->getAttributeObject()->getFrontendInput()) {
            case 'select':
            case 'boolean':
                return 'select';

            case 'multiselect':
                return 'multiselect';

            case 'date':
                return 'date';

            default:
                return 'text';
        }
    }

    public function getValueElement()
    {
        $element = parent::getValueElement();
        if (is_object($this->getAttributeObject())) {
            switch ($this->getAttributeObject()->getFrontendInput()) {
                case 'date':
                    $element->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'));
                    break;
            }
        }

        return $element;
    }

    public function getValueElementChooserUrl()
    {
        $url = false;
        switch ($this->getAttribute()) {
            case 'sku':
            case 'category_ids':
                $url = 'adminhtml/promo_widget/chooser'
                    .'/attribute/'.$this->getAttribute();
                if ($this->getJsFormObject()) {
                    $url .= '/form/'.$this->getJsFormObject();
                }
                break;
        }
        return $url!==false ? Mage::helper('adminhtml')->getUrl($url) : '';
    }

    public function getExplicitApply()
    {
        switch ($this->getAttribute()) {
            case 'sku': case 'category_ids': case 'php':
                return true;
        }
        if (is_object($this->getAttributeObject())) {
            switch ($this->getAttributeObject()->getFrontendInput()) {
                case 'date':
                    return true;
            }
        }
        return false;
    }

    public function loadArray($arr)
    {
        $this->setAttribute(isset($arr['attribute']) ? $arr['attribute'] : false);
        $attribute = $this->getAttributeObject();

        if ($attribute && $attribute->getBackendType() == 'decimal') {
            if (isset($arr['value'])) {
                if (!empty($arr['operator'])
                    && in_array($arr['operator'], array('!()', '()'))
                    && false !== strpos($arr['value'], ',')) {

                    $tmp = array();
                    foreach (explode(',', $arr['value']) as $value) {
                        $tmp[] = Mage::app()->getLocale()->getNumber($value);
                    }
                    $arr['value'] =  implode(',', $tmp);
                } else {
                    $arr['value'] =  Mage::app()->getLocale()->getNumber($arr['value']);
                }
            } else {
                $arr['value'] = false;
            }
            $arr['is_value_parsed'] = isset($arr['is_value_parsed'])
                ? Mage::app()->getLocale()->getNumber($arr['is_value_parsed']) : false;
        }

        return parent::loadArray($arr);
    }

    /**
     * Validate product attrbute value for condition
     *
     * @param Varien_Object $object
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        $attrCode = $this->getAttribute();

        switch ($attrCode) {
            case 'is_salable':
                $value = $object->isSalable();
                $object->setIsSalable($value);

                return $this->validateAttribute($value);
                break;

            case 'status_parent':
                $parentProduct = $this->getProductPattern()->getParentProduct($object);
                $value = $parentProduct->getStatus();
                
                return $this->validateAttribute($value);
                break;
                
            case 'image':
            case 'small_image':
            case 'thumbnail':
                $value = $object->getData($attrCode);
                if ('' === $value || 'no_selection' === $value) {
                    $value = null;
                }

                return $this->validateAttribute($value);
                break;
                
            case 'category_ids':
                return $this->validateAttribute($object->getAvailableInCategories());
                break;

            case 'qty':
                if ($object->getTypeId() == 'configurable') {
                    $totalQty = 0;
                    $childs = $object->getTypeInstance()->getChildrenIds($object->getId());
                    $childs = Mage::getModel('catalog/product')->getCollection()
                        ->addFieldToFilter('entity_id', array('in' => $childs[0]))
                        ->joinField(
                            'qty',
                            'cataloginventory/stock_item',
                            'qty',
                            'product_id = entity_id',
                            '{{table}}.stock_id = 1',
                            'left'
                        );

                    foreach ($childs as $child) {
                        # if product enabled
                        if ($child->getStatus() == 1) {
                            $totalQty += $child->getQty();
                        }
                    }

                    return $this->validateAttribute($totalQty);
                } else {
                    $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($object->getId());
                    return $this->validateAttribute($stockItem->getQty());
                }

                break;

            case 'is_in_stock':
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($object->getId());
                return $this->validateAttribute($stockItem->getIsInStock());
                break;

            case 'manage_stock':
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($object->getId());
                $m = 0;
                if ($stockItem->getManageStock()) {
                    $m = 1;
                }
                return $this->validateAttribute($m);
                break;

            case 'image_size':
            case 'small_image_size':
            case 'thumbnail_size':
                $imageCode = str_replace('_size', '', $attrCode);

                $imagePath = $object->getData($imageCode);
                $path      = Mage::getBaseDir('media').DS.'catalog/product'.$imagePath;

                $size = 0;
                if (file_exists($path) && is_file($path)) {
                    $size = filesize($path);
                }

                return $this->validateAttribute($size);
                break;

            case 'php':
                $object = $object->load($object->getId());
                extract($object->getData());
                $expr = 'return '.$this->getValue().';';
                $value = eval($expr);

                if ($this->getOperator() == '==') {
                    return $value;
                } else {
                    return !$value;
                }

                break;

            default:
                if (!isset($this->_entityAttributeValues[$object->getId()])) {
                    $attr = $object->getResource()->getAttribute($attrCode);

                    if ($attr && $attr->getBackendType() == 'datetime' && !is_int($this->getValue())) {
                        $this->setValue(strtotime($this->getValue()));
                        $value = strtotime($object->getData($attrCode));
                        return $this->validateAttribute($value);
                    }

                    if ($attr && $attr->getFrontendInput() == 'multiselect') {
                        $value = $object->getData($attrCode);
                        $value = strlen($value) ? explode(',', $value) : array();
                        return $this->validateAttribute($value);
                    }

                    return parent::validate($object);
                } else {
                    $result = false; // any valid value will set it to TRUE
                    $oldAttrValue = $object->hasData($attrCode) ? $object->getData($attrCode) : null;
                    foreach ($this->_entityAttributeValues[$object->getId()] as $storeId => $value) {
                        $attr = $object->getResource()->getAttribute($attrCode);
                        if ($attr && $attr->getBackendType() == 'datetime') {
                            $value = strtotime($value);
                        } else if ($attr && $attr->getFrontendInput() == 'multiselect') {
                            $value = strlen($value) ? explode(',', $value) : array();
                        }

                        $object->setData($attrCode, $value);
                        $result |= parent::validate($object);

                        if ($result) {
                            break;
                        }
                    }

                    if (is_null($oldAttrValue)) {
                        $object->unsetData($attrCode);
                    } else {
                        $object->setData($attrCode, $oldAttrValue);
                    }

                    return (bool) $result;
                }
                break;
        }
    }

    public function getJsFormObject()
    {
        return 'rule_conditions_fieldset';
    }

    /**
     * @return Mirasvit_FeedExport_Model_Feed_Generator_Pattern_Product
     */
    public function getProductPattern()
    {
        return Mage::getSingleton('feedexport/feed_generator_pattern_product')->setFeed($this->getRule()->getFeed());
    }
}
