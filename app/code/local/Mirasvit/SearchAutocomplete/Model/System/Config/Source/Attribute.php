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
 * @category Mirasvit
 */
class Mirasvit_SearchAutocomplete_Model_System_Config_Source_Attribute
{
    public function toOptionArray()
    {
        if (Mage::helper('core')->isModuleEnabled('Mirasvit_SearchIndex')) {
            $productIndex = Mage::helper('searchindex/index')->getIndex('mage_catalog_product');
            $attributes = array_keys($productIndex->getAttributes());

            $productAttributeCollection = Mage::getResourceModel('catalog/product_attribute_collection');
            if (is_array($attributes) && count($attributes)) {
                $productAttributeCollection->addFieldToFilter('attribute_code', $attributes);
            } else {
                return array();
            }

            $values = array();
            $values['---'] = array(
                'value' => '',
                'label' => '',
            );

            foreach ($productAttributeCollection as $attribute) {
                $values[$attribute->getAttributeCode()] = array(
                    'value' => $attribute->getAttributeCode(),
                    'label' => $attribute->getFrontendLabel(),
                );
            }

            return $values;
        }

        return array();
    }
}
