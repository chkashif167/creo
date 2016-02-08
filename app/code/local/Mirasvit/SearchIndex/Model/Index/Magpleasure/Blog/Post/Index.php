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



class Mirasvit_SearchIndex_Model_Index_Magpleasure_Blog_Post_Index extends Mirasvit_SearchIndex_Model_Index
{
    public function getBaseGroup()
    {
        return 'Magpleasure';
    }

    public function getBaseTitle()
    {
        return 'Blog';
    }

    public function canUse()
    {
        return Mage::getConfig()->getModuleConfig('Magpleasure_Blog')->is('active', 'true');
    }

    public function getPrimaryKey()
    {
        return 'post_id';
    }

    public function getAvailableAttributes()
    {
        $result = array(
            'title' => Mage::helper('searchindex')->__('Title'),
            'short_content' => Mage::helper('searchindex')->__('Short Content'),
            'full_content' => Mage::helper('searchindex')->__('Full Content'),
            'meta_title' => Mage::helper('searchindex')->__('Meta Title'),
            'meta_tags' => Mage::helper('searchindex')->__('Meta Tags'),
            'meta_description' => Mage::helper('searchindex')->__('Meta Description'),
        );

        return $result;
    }

    public function getCollection()
    {
        $collection = Mage::getModel('mpblog/post')->getCollection();
        $collection->addFieldToFilter('status', Magpleasure_Blog_Model_Post::STATUS_ENABLED)
            ->addStoreFilter(Mage::app()->getStore()->getId());

        $this->joinMatched($collection, 'main_table.post_id');

        return $collection;
    }
}
