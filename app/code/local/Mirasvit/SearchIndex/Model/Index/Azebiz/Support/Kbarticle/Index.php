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



class Mirasvit_SearchIndex_Model_Index_Azebiz_Support_Kbarticle_Index extends Mirasvit_SearchIndex_Model_Index
{
    public function getBaseGroup()
    {
        return 'MageBuzz';
    }

    public function getBaseTitle()
    {
        return 'Mageticket / Knowledge Base';
    }

    public function canUse()
    {
        return Mage::getConfig()->getModuleConfig('Azebiz_Support')->is('active', 'true');
    }

    public function getPrimaryKey()
    {
        return 'kb_article_id';
    }

    public function getAvailableAttributes()
    {
        $result = array(
            'title' => __('Title'),
            'kb_article_content' => __('Content'),
        );

        return $result;
    }

    public function getCollection()
    {
        $collection = Mage::getModel('support/kbarticle')->getCollection();
        $collection->addFieldToFilter('status', 1);

        $this->joinMatched($collection, 'main_table.kb_article_id');

        return $collection;
    }
}
