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



class Mirasvit_SearchIndex_Model_Index_Tm_Knowledgebase_Faq_Index extends Mirasvit_SearchIndex_Model_Index
{
    public function getBaseGroup()
    {
        return 'TM';
    }

    public function getBaseTitle()
    {
        return 'KnowledgeBase';
    }

    public function canUse()
    {
        return Mage::getConfig()->getModuleConfig('TM_KnowledgeBase')->is('active', 'true');
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getAvailableAttributes()
    {
        $result = array(
            'title' => Mage::helper('searchindex')->__('Title'),
            'content' => Mage::helper('searchindex')->__('Content'),
        );

        return $result;
    }

    public function getCollection()
    {
        $collection = Mage::getModel('knowledgebase/faq')->getCollection();
        $collection->addFieldToFilter('status', 1);

        $this->joinMatched($collection, 'main_table.id');

        return $collection;
    }
}
