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



class Mirasvit_SearchIndex_Model_Index_Mage_Catalog_Product_Indexer extends Mage_CatalogSearch_Model_Indexer_Fulltext
{
    public function getIndexModel()
    {
        return Mage::helper('searchindex/index')->getIndexModel('catalog');
    }

    public function getTableName()
    {
        $tablePrefix = Mage::getConfig()->getNode('global/resources/db/table_prefix');
        $table = $tablePrefix.'catalogsearch_fulltext';

        return $table;
    }

    public function getPrimaryKey()
    {
        return 'product_id';
    }

    protected function _getIndexer()
    {
        return Mage::getSingleton('searchindex/catalogsearch_fulltext');
    }

    public function registerEvent(Mage_Index_Model_Event $event)
    {
        return parent::_registerEvent($event);
    }

    public function processEvent(Mage_Index_Model_Event $event)
    {
        return parent::_processEvent($event);
    }

    public function reindexAll()
    {
        Mage::getResourceSingleton('searchindex/catalogsearch_fulltext')->rebuildTable();
        parent::reindexAll();
    }
}
