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



class Mirasvit_SearchIndex_Model_Index_Mage_Cms_Page_Index extends Mirasvit_SearchIndex_Model_Index
{
    public function getBaseGroup()
    {
        return 'Magento';
    }

    public function getBaseTitle()
    {
        return 'Cms Pages';
    }

    public function getPrimaryKey()
    {
        return 'page_id';
    }

    public function getFieldsets()
    {
        return array(
            'Mage_Cms_Page_Additional',
        );
    }

    public function getAvailableAttributes()
    {
        $result = array(
            'title' => Mage::helper('searchindex')->__('Title'),
            'meta_keywords' => Mage::helper('searchindex')->__('Meta Keywords'),
            'meta_description' => Mage::helper('searchindex')->__('Meta Description'),
            'content_heading' => Mage::helper('searchindex')->__('Content Heading'),
            'content' => Mage::helper('searchindex')->__('Content'),
        );

        return $result;
    }

    public function getCollection()
    {
        $collection = Mage::getModel('cms/page')->getCollection();
        $collection->addFieldToFilter('is_active', 1)
            ->addStoreFilter(Mage::app()->getStore()->getId());

        $this->joinMatched($collection, 'main_table.page_id');

        return $collection;
    }
}
