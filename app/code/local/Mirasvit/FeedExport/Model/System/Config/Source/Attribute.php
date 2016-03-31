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


class Mirasvit_FeedExport_Model_System_Config_Source_Attribute
{
    protected $_additional = array(
        'entity_id'            => 'Product Id',
        'is_in_stock'          => 'Is In Stock',
        'qty'                  => 'Qty',
        'parent_qty'           => 'Parent Qty',
        'image'                => 'Image',
        'url'                  => 'Product Url',
        'category'             => 'Category Name',
        'category_id'          => 'Category Id',
        'final_price'          => 'Final Price',
        'store_price'          => 'Store Price',
        'category_path'        => 'Category Path (Category > Sub Category)',
        'category_paths'       => 'Category Paths (Category > Sub Category, Category > Sub Category, Category > Sub Category)',
        'image1'               => 'Image 1',
        'image2'               => 'Image 2',
        'image3'               => 'Image 3',
        'image4'               => 'Image 4',
        'image5'               => 'Image 5',
        'attribute_set'        => 'Attribute Set',
        'rating_summary'       => 'Rating Summary',
        'reviews_count'        => 'Number of Reviews',
        'type_id'              => 'Product Type',
    );

    public function toOptionArray()
    {
        $result = array();

        foreach ($this->getAttributeCollection() as $attribute) {
            if ($attribute->getFrontendLabel()) {
                $label = $attribute->getFrontendLabel();
                $code  = $attribute->getAttributeCode();
                $result[$label.$code] = array(
                    'label' => $label.' ['.$code.']',
                    'value' => $code,
                );
            }
        }

        foreach ($this->_additional as $code => $label) {
            $result[$label.$code] = array(
                'label' => $label.' ['.$code.']',
                'value' => $code,
            );
        }

        $customCollection = Mage::getModel('feedexport/dynamic_attribute')->getCollection();

        foreach ($customCollection as $attribute) {
            $label = $attribute->getName();
            $result[$label] = array(
                'label' => $label.' ['.$attribute->getCode().']',
                'value' => 'custom:'.$attribute->getCode(),
            );
        }

        $mappingCollection = Mage::getModel('feedexport/dynamic_category')->getCollection();

        foreach ($mappingCollection as $mapping) {
            $label = $mapping->getName();
            $result[$label] = array(
                'label' => __('Category Mapping').': '.$label,
                'value' => 'mapping:'.$mapping->getId(),
            );
        }

        if ((string)Mage::getConfig()->getNode('modules/Amasty_Meta/active') == 'true') {
            $tags = array(
                'title'             => __('Title'),
                'description'       => __('Description'),
                'keywords'          => __('Keywords'),
                'short_description' => __('Short Description'),
                'full_description'  => __('Full Description'),
            );

            foreach ($tags as $code => $label) {
                $result['ammeta:'.$code] = array(
                    'label' => $label.' ['.$code.']',
                    'value' => 'ammeta:'.$code,
                );
            }
        }

        ksort($result);

        return $result;
    }

    public function getAttributeCollection()
    {
        return Mage::getResourceModel('eav/entity_attribute_collection')
            ->setItemObjectClass('catalog/resource_eav_attribute')
            ->setEntityTypeFilter(Mage::getResourceModel('catalog/product')->getTypeId())
            ->addFieldToFilter('attribute_code', array('nin' => array('gallery', 'media_gallery')));
    }
}