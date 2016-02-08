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



class Mirasvit_SearchIndex_Model_Index_Mage_Catalog_Attribute_Index extends Mirasvit_SearchIndex_Model_Index
{
    public function getBaseGroup()
    {
        return 'Magento';
    }

    public function getBaseTitle()
    {
        return 'Catalog Attribute';
    }

    public function getPrimaryKey()
    {
        return 'value';
    }

    public function getFieldsets()
    {
        return array(
            'Mage_Catalog_Attribute_Additional',
        );
    }

    public function getAvailableAttributes()
    {
        $result = array(
            'label' => Mage::helper('searchindex')->__('Option Label'),
        );

        return $result;
    }

    public function getCollection()
    {
        $matchedIds = $this->getMatchedIds();

        $attributeCode = $this->getProperty('attribute');
        $attribute = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
        $options = $attribute->getSource()->getAllOptions(false);

        $collection = new Varien_Data_Collection();
        foreach ($options as $option) {
            if (isset($matchedIds[$option['value']])) {
                $obj = new Varien_Object();
                $obj->addData($option);
                $collection->addItem($obj);
            }
        }

        return $collection;
    }

    public function getUrl($item)
    {
        $url = $this->getProperty('url_template');
        foreach ($item->getData() as $key => $value) {
            $key = strtolower($key);
            $url = str_replace('{'.$key.'}', $value, $url);
        }

        return $url;
    }
}
