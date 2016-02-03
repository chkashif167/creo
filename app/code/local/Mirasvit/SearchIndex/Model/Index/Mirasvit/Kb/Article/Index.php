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



class Mirasvit_SearchIndex_Model_Index_Mirasvit_Kb_Article_Index extends Mirasvit_SearchIndex_Model_Index
{
    public function getBaseGroup()
    {
        return 'Mirasvit';
    }

    public function getBaseTitle()
    {
        return 'Kb';
    }

    public function canUse()
    {
        return Mage::getConfig()->getModuleConfig('Mirasvit_Kb')->is('active', 'true');
    }

    public function getPrimaryKey()
    {
        return 'article_id';
    }

    public function getAvailableAttributes()
    {
        $result = array(
            'name' => Mage::helper('searchindex')->__('Name'),
            'text' => Mage::helper('searchindex')->__('Text'),
            'meta_title' => Mage::helper('searchindex')->__('Meta Title'),
            'meta_keywords' => Mage::helper('searchindex')->__('Meta Keywords'),
            'meta_description' => Mage::helper('searchindex')->__('Meta Description'),
        );

        return $result;
    }

    public function getCollection()
    {
        $collection = Mage::getModel('kb/article')->getCollection();
        $collection->addFieldToFilter('main_table.is_active', 1);

        $this->joinMatched($collection, 'main_table.article_id');

        return $collection;
    }
}
