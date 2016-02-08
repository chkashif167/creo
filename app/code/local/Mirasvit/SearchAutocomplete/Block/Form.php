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
 * @package   Sphinx Search Ultimate
 * @version   2.3.2
 * @build     1290
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



/**
 * Search form block.
 *
 * @category Mirasvit
 */
class Mirasvit_SearchAutocomplete_Block_Form extends Mage_Core_Block_Template
{
    public function getAjaxUrl()
    {
        $url = Mage::getUrl('searchautocomplete/ajax/get');

        $url = str_replace('http:', '', $url);
        $url = str_replace('https:', '', $url);

        return $url;
    }

    public function getCategories()
    {
        $rootId = Mage::app()->getStore()->getRootCategoryId();
        $root = Mage::getModel('catalog/category')->load($rootId);

        $collection = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('name');

        if ($this->getUserCategories()) {
            $collection->addFieldToFilter('entity_id', $this->getUserCategories());
        } else {
            $collection->addPathsFilter($root->getPath().DS)
                ->addFieldToFilter('level', 2)
                ->addFieldToFilter('is_active', 1)
                ->addFieldToFilter('include_in_menu', 1)
                ->setOrder('position', 'asc');
        }

        return $collection;
    }

    public function getIndexes()
    {
        $indexes = Mage::helper('searchautocomplete')->getIndexes(false);

        return $indexes;
    }

    public function getAttributes()
    {
        $result = array();

        $attributes = Mage::getSingleton('searchautocomplete/system_config_source_attribute')->toOptionArray();
        $allowedAttributes = Mage::getStoreConfig('searchautocomplete/general/attributes');

        if ($allowedAttributes == '') {
            $allowedAttributes = array();
        } else {
            $allowedAttributes = explode(',', $allowedAttributes);
        }

        foreach ($attributes as $attribute) {
            if (count($allowedAttributes) == 0 || in_array($attribute['value'], $allowedAttributes)) {
                if ($attribute['value']) {
                    $result[$attribute['value']] = $attribute['label'];
                }
            }
        }

        return $result;
    }

    protected function getUserCategories()
    {
        $categories = explode(',', Mage::getStoreConfig('searchautocomplete/general/categories'));
        if (count($categories) == 1 && $categories[0] == '') {
            return false;
        }

        return $categories;
    }

    public function getFilterType()
    {
        $filterType = Mage::getStoreConfig('searchautocomplete/general/filter_type');
        if (!$filterType) {
            $filterType = 'category';
        }

        return $filterType;
    }

    public function getFiltertOptions()
    {
    }
}
