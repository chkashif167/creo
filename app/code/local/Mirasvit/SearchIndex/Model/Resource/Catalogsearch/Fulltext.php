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



class Mirasvit_SearchIndex_Model_Resource_Catalogsearch_Fulltext extends Mage_CatalogSearch_Model_Mysql4_Fulltext
{
    protected $_columns = null;

    protected function _construct()
    {
        $this->_init('catalogsearch/fulltext', 'product_id');
        $this->_engine = Mage::getResourceSingleton('searchindex/catalogsearch_fulltext_engine');
    }

    public function rebuildTable()
    {
        set_time_limit(0);
        $uid = Mage::helper('mstcore/debug')->start();

        $tableName = $this->getMainTable();
        $adapter = $this->_getWriteAdapter();

        $adapter->resetDdlCache($tableName);

        $describe = $adapter->describeTable($tableName);
        $columns = $this->getColumns();

        $addColumns = array_diff_key($columns, $describe);
        $dropColumns = array_diff_key($describe, $columns);

        // Drop columns
        foreach (array_keys($dropColumns) as $columnName) {
            if (!in_array($columnName, array(
                    'product_id',
                    'store_id',
                    'data_index',
                    'fulltext_id',
                    'updated',
                    'searchindex_weight', )
                )) {
                $adapter->dropColumn($tableName, $columnName);
            }
        }

        // Add columns
        foreach ($addColumns as $columnName => $columnProp) {
            $adapter->addColumn($tableName, $columnName, $columnProp);
        }

        Mage::helper('mstcore/debug')->end($uid);

        return $this;
    }

    protected function getColumns()
    {
        $uid = Mage::helper('mstcore/debug')->start();

        if ($this->_columns === null) {
            $this->_columns = array();
            $this->_columns['updated'] = "int(1) NOT NULL default '1'";
            $this->_columns['searchindex_weight'] = "int(11) NOT NULL default '0'";

            $columns = array();

            $attributes = $this->getIndexModel()->getIndexInstance()->getAttributes();

            foreach ($this->_getSearchableAttributes() as $attribute) {
                $cols = $attribute->getFlatColumns();

                if (!count($cols)) {
                    continue;
                }

                $attributeCode = $attribute->getAttributeCode();

                if (isset($cols[$attributeCode.'_value'])) {
                    $columns[$attributeCode] = $cols[$attributeCode.'_value']['type'].' NULL';
                } else {
                    $columns[$attributeCode] = $cols[$attributeCode]['type'].' NULL';
                }
            }

            foreach ($attributes as $attributeCode => $weight) {
                if (isset($columns[$attributeCode])) {
                    $this->_columns[$attributeCode] = $columns[$attributeCode];
                } else {
                    $this->_columns[$attributeCode] = 'text NULL';
                }
            }
        }

        Mage::helper('mstcore/debug')->end($uid, $this->_columns);

        return $this->_columns;
    }

    protected function _getProductChildIds($productId, $typeId)
    {
        if (!$this->getIndexModel()->getIndexInstance()->getProperty('include_bundled')) {
            return;
        }

        $typeInstance = $this->_getProductTypeInstance($typeId);
        $relation = $typeInstance->isComposite()
            ? $typeInstance->getRelationInfo()
            : false;

        if ($relation && $relation->getTable() && $relation->getParentFieldName() && $relation->getChildFieldName()) {
            $select = $this->_getReadAdapter()->select()
                ->from(
                    array('main' => $this->getTable($relation->getTable())),
                    array($relation->getChildFieldName()))
                ->where("{$relation->getParentFieldName()}=?", $productId);
            if (!is_null($relation->getWhere())) {
                $select->where($relation->getWhere());
            }

            return $this->_getReadAdapter()->fetchCol($select);
        }

        return;
    }

    protected function _saveProductIndexes($storeId, $productIndexes)
    {
        $uid = Mage::helper('mstcore/debug')->start();

        if ($this->_engine) {
            $this->_addRelatedData($productIndexes, $storeId);
            $this->_engine->saveEntityIndexes($storeId, $productIndexes);
        }

        Mage::helper('mstcore/debug')->end($uid);

        return $this;
    }

    protected function _addRelatedData(&$index, $storeId)
    {
        $staticFields = array();
        foreach ($this->_getSearchableAttributes('static') as $attribute) {
            $staticFields[] = $attribute->getAttributeCode();
        }

        foreach ($index as $entityId => $data) {
            $productChildren = array();

            $arGrouped = $this->_getProductChildIds($entityId, 'grouped');
            if (is_array($arGrouped) && count($arGrouped)) {
                $productChildren = array_merge($productChildren, $arGrouped);
            }

            $arConfigurable = $this->_getProductChildIds($entityId, 'configurable');
            if (is_array($arConfigurable) && count($arConfigurable)) {
                $productChildren = array_merge($productChildren, $arConfigurable);
            }

            if (count($productChildren)) {
                $relatedProducts = $this->_getSearchableProducts($storeId, $staticFields, $productChildren, 0);

                foreach ($relatedProducts as $pr) {
                    foreach ($pr as $attr => $value) {
                        if (isset($index[$entityId][$attr])) {
                            $index[$entityId][$attr] .= ' '.$value;
                            $index[$entityId]['data_index'] .= ' '.$value;
                        }
                    }
                }
            }
        }

        return $this;
    }

    public function getIndexModel()
    {
        return Mage::helper('searchindex/index')->getIndex('mage_catalog_product');
    }

    /**
     * Reset search results
     *
     * @return $this
     */
    public function resetSearchResults()
    {
        Mage::dispatchEvent('catalogsearch_reset_search_result');

        return $this;
    }
}
