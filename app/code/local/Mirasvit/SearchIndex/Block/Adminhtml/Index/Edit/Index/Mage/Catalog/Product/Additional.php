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



class Mirasvit_SearchIndex_Block_Adminhtml_Index_Edit_Index_Mage_Catalog_Product_Additional extends Varien_Data_Form_Element_Fieldset
{
    public function toHtml()
    {
        $model = $this->getModel();

        parent::__construct(array('legend' => __('Additional Search Index Configuration')));

        $this->addField('include_category', 'select', array(
            'name' => 'properties[include_category]',
            'label' => __('Search by parent categories names'),
            'required' => false,
            'value' => $model->getProperty('include_category'),
            'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
            'note' => Mage::helper('searchindex/help')->field('include_category'),
        ));

        $this->addField('include_bundled', 'select', array(
            'name' => 'properties[include_bundled]',
            'label' => __('Search by child products attributes (for bundle and configurable products)'),
            'required' => false,
            'value' => $model->getProperty('include_bundled'),
            'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
            'note' => Mage::helper('searchindex/help')->field('include_bundled'),
        ));

        $this->addField('include_tag', 'select', array(
            'name' => 'properties[include_tag]',
            'label' => __('Search by product tags'),
            'required' => false,
            'value' => $model->getProperty('include_tag'),
            'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
            'note' => Mage::helper('searchindex/help')->field('include_tag'),
        ));

        $this->addField('include_id', 'select', array(
            'name' => 'properties[include_id]',
            'label' => __('Search by product id'),
            'required' => false,
            'value' => $model->getProperty('include_id'),
            'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
            'note' => Mage::helper('searchindex/help')->field('include_id'),
        ));

        $this->addField('include_custom_options', 'select', array(
            'name' => 'properties[include_custom_options]',
            'label' => __('Search by custom options'),
            'required' => false,
            'value' => $model->getProperty('include_custom_options'),
            'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
            'note' => Mage::helper('searchindex/help')->field('include_custom_options'),
        ));

        $this->addField('out_of_stock_to_end', 'select', array(
            'name' => 'properties[out_of_stock_to_end]',
            'label' => __('Push "out of stock" products to the end'),
            'required' => false,
            'value' => $model->getProperty('out_of_stock_to_end'),
            'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
            'note' => Mage::helper('searchindex/help')->field('out_of_stock_to_end'),
        ));

        return parent::toHtml();
    }
}
