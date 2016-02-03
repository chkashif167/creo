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


class Mirasvit_SearchIndex_Model_Catalogsearch_Resource_Fulltext_Collection extends Mage_CatalogSearch_Model_Resource_Fulltext_Collection
{
    public function addSearchFilter($query)
    {
        $catalogIndex = Mage::helper('searchindex/index')->getIndex('mage_catalog_product');
        $catalogIndex->joinMatched($this);

        $this->_addStockOrder($this);

        if (Mage::getSingleton('catalog/session')->getSortOrder() == ''
            && Mage::getSingleton('catalog/session')->getSortDirection() == '') {
            $this->setOrder('relevance');
        }

        return $this;
    }

    protected function _addStockOrder($collection)
    {
        $index = Mage::helper('searchindex/index')->getIndex('mage_catalog_product');

        if ($index->getProperty('out_of_stock_to_end')) {
            $resource = Mage::getSingleton('core/resource');
            $select = $collection->getSelect();

            $select->joinLeft(
                array('ss_inventory_table' => $resource->getTableName('cataloginventory_stock_item')),
                'ss_inventory_table.product_id = e.entity_id',
                array('is_in_stock', 'manage_stock')
            );

            $select->order(new Zend_Db_Expr('(CASE WHEN (((ss_inventory_table.use_config_manage_stock = 1)
                AND (ss_inventory_table.is_in_stock = 1)) OR  ((ss_inventory_table.use_config_manage_stock = 0)
                AND (1 - ss_inventory_table.manage_stock + ss_inventory_table.is_in_stock >= 1)))
                THEN 1 ELSE 0 END) DESC'));
        }

        return $this;
    }
}
