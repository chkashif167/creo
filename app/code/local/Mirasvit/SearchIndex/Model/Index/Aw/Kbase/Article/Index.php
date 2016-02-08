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



class Mirasvit_SearchIndex_Model_Index_Aw_Kbase_Article_Index extends Mirasvit_SearchIndex_Model_Index
{
    public function getBaseGroup()
    {
        return 'AheadWorks';
    }

    public function getBaseTitle()
    {
        return 'Knowledge Base';
    }

    public function canUse()
    {
        return Mage::getConfig()->getModuleConfig('AW_Kbase')->is('active', 'true');
    }

    public function getPrimaryKey()
    {
        return 'article_id';
    }

    public function getAvailableAttributes()
    {
        $result = array(
            'article_title' => __('Title'),
            'article_text' => __('Article'),
            'meta_title' => __('Meta Title'),
            'meta_description' => __('Meta Description'),
            'meta_keywords' => __('Meta Keywords'),
        );

        return $result;
    }

    public function getCollection()
    {
        $collection = Mage::getModel('kbase/article')->getCollection();
        $collection->addFieldToFilter('article_status', 1)
            ->addStoreFilter(Mage::app()->getStore()->getId());
        $collection->getSelect()->distinct(true);

        $this->joinMatched($collection, 'main_table.article_id');

        return $collection;
    }
}
