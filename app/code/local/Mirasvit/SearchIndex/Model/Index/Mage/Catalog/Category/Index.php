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



class Mirasvit_SearchIndex_Model_Index_Mage_Catalog_Category_Index extends Mirasvit_SearchIndex_Model_Index
{
    public function getBaseGroup()
    {
        return 'Magento';
    }

    public function getBaseTitle()
    {
        return 'Catalog Categories';
    }

    public function getPrimaryKey()
    {
        return 'entity_id';
    }

    public function getAvailableAttributes()
    {
        $result = array(
            'name' => Mage::helper('searchindex')->__('Name'),
            'meta_title' => Mage::helper('searchindex')->__('Meta Title'),
            'meta_keywords' => Mage::helper('searchindex')->__('Meta Keywords'),
            'meta_description' => Mage::helper('searchindex')->__('Meta Description'),
            'description' => Mage::helper('searchindex')->__('Description'),
        );

        return $result;
    }

    public function getCollection()
    {
        $collection = Mage::getModel('catalog/category')->getCollection()
            ->addNameToResult();
        $collection->addFieldToFilter('is_active', 1);

        $mainTable = 'e';

        $select = $collection->getSelect()->__toString();
        if (strpos($select, 'main_table') !== false) {
            $mainTable = 'main_table';
        }

        $this->joinMatched($collection, $mainTable.'.entity_id');

        return $collection;
    }
}
