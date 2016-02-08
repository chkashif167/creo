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



class Mirasvit_SearchIndex_Model_Index_Mage_Catalogsearch_Query_Index extends Mirasvit_SearchIndex_Model_Index
{
    public function getBaseGroup()
    {
        return 'Magento';
    }

    public function getBaseTitle()
    {
        return 'Catalog Search Queries';
    }

    public function getPrimaryKey()
    {
        return 'query_id';
    }

    public function getAvailableAttributes()
    {
        $result = array(
            'query_text' => Mage::helper('searchindex')->__('Query Text'),
        );

        return $result;
    }

    public function getCollection()
    {
        $collection = Mage::getResourceModel('catalogsearch/query_collection')
            ->setPopularQueryFilter(Mage::app()->getStore()->getId())
            ->addFieldToFilter('query_text', array('neq' => $this->getQuery()->getQueryText()));

        $this->joinMatched($collection, 'main_table.query_id');

        return $collection;
    }
}
