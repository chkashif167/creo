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



/**
 * @category Mirasvit
 */
class Mirasvit_Misspell_Model_Indexer extends Varien_Object
{
    protected $_likeTables = array(
        'catalogsearch_fulltext',
        'm_searchindex_',
        'catalog_product_entity_text',
        'catalog_product_entity_varchar',
        'catalog_category_entity_text',
        'catalog_category_entity_varchar',
    );

    protected $_dislikeTables = array(
        'm_searchindex_mage_catalogsearch_query',
    );

    public function reindexAll()
    {
        $helper = Mage::helper('misspell/string');
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_write');
        $tables = $connection->listTables();
        $preresults = array();

        $obj = new Varien_Object();

        Mage::dispatchEvent('misspell_indexer_prepare', array('obj' => $obj));

        foreach ($obj->getData() as $key => $string) {
            $this->_split($string, $preresults, 0);
        }

        foreach ($tables as $table) {
            $like = false;

            foreach ($this->_likeTables as $likeTable) {
                if (strpos($table, $likeTable) !== false) {
                    $like = true;
                }
            }

            foreach ($this->_dislikeTables as $dislikeTable) {
                if (strpos($table, $dislikeTable) !== false) {
                    $like = false;
                }
            }

            if (!$like) {
                continue;
            }

            $columns = $this->_getTextColumns($table);
            if (!count($columns)) {
                continue;
            }

            foreach ($columns as $idx => $col) {
                $columns[$idx] = '`'.$col.'`';
            }

            $select = $connection->select();
            $fromColumns = new Zend_Db_Expr('CONCAT('.implode(",' ',", $columns).') as data_index');
            $select->from($table, $fromColumns);

            $result = $connection->query($select);
            while ($row = $result->fetch()) {
                $dataindex = $row['data_index'];

                $this->_split($dataindex, $preresults);
            }
        }

        $tableName = Mage::getSingleton('core/resource')->getTableName('misspell/misspell');
        $connection->delete($tableName);

        foreach ($preresults as $word => $freq) {
            $rows[] = array(
                'keyword' => $word,
                'trigram' => $helper->getTrigram($word),
                'freq' => $freq / count($preresults),
            );

            if (count($rows) > 1000) {
                $connection->insertArray($tableName, array('keyword', 'trigram', 'freq'), $rows);
                $rows = array();
            }
        }

        if (count($rows) > 0) {
            $connection->insertArray($tableName, array('keyword', 'trigram', 'freq'), $rows);
        }

        $connection->delete(Mage::getSingleton('core/resource')->getTableName('misspell/misspell_suggest'));

        return count($preresults);
    }

    protected function _split($string, &$results, $increment = 1)
    {
        $helper = Mage::helper('misspell/string');
        $string = $helper->cleanString($string);
        $words = $helper->splitWords($string, false, 0);

        foreach ($words as $word) {
            if ($helper->strlen($word) >= $helper->getGram()
                && !is_numeric($word)) {
                $word = $helper->strtolower($word);
                if (!isset($results[$word])) {
                    $results[$word] = $increment;
                } else {
                    $results[$word] += $increment;
                }
            }
        }
    }

    protected function _getTextColumns($table)
    {
        $result = array();
        $types = array('text', 'varchar', 'mediumtext', 'longtext');
        $columns = Mage::getSingleton('core/resource')->getConnection('core_write')->describeTable($table);
        foreach ($columns as $column => $info) {
            if (in_array($info['DATA_TYPE'], $types)) {
                $result[] = $column;
            }
        }

        return $result;
    }
}
