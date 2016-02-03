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



class Mirasvit_SearchIndex_Model_Catalogsearch_Indexer_Fulltext extends Mage_CatalogSearch_Model_Indexer_Fulltext
{
    public function getName()
    {
        return Mage::helper('searchindex')->__('Search Index');
    }

    public function getDescription()
    {
        $labels = array();
        foreach ($this->getIndexes() as $index) {
            $labels[] = $index->getTitle();
        }

        return Mage::helper('searchindex')->__('Rebuild search index (%s)', implode(', ', $labels));
    }

    protected function _getIndexer()
    {
        return Mage::getSingleton('searchindex/catalogsearch_fulltext');
    }

    public function getIndexes()
    {
        return Mage::helper('searchindex/index')->getIndexes();
    }

    public function reindexAll()
    {
        $uid = Mage::helper('mstcore/debug')->start();

        foreach ($this->getIndexes() as $index) {
            $indexer = $index->getIndexer();
            $indexer->reindexAll();
        }

        Mage::helper('mstcore/debug')->end($uid);
    }
}
