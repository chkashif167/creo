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



class Mirasvit_SearchIndex_Model_Index extends Mage_Core_Model_Abstract
{
    protected $_matchedIds = array();
    protected $_useNativeTables = false;
    protected $_tmpTableCreated = false;
    protected static $_instances = array();

    protected function _construct()
    {
        $this->_init('searchindex/index');
    }

    public function addData(array $data)
    {
        if (isset($data['title']) && strpos($data['title'], 'a:') !== 0) {
            $this->setTitle($data['title']);
            unset($data['title']);
        }

        return parent::addData($data);
    }

    public function getTitle()
    {
        return Mage::helper('searchindex/storeview')->getStoreViewValue($this, 'title');
    }

    public function setTitle($value)
    {
        Mage::helper('searchindex/storeview')->setStoreViewValue($this, 'title', $value);

        return $this;
    }

    public function getBaseGroup()
    {
        return 'Others';
    }

    public function getBaseTitle()
    {
        return get_class($this);
    }

    public function getCode()
    {
        $arr = explode('_', get_class($this));

        return strtolower($arr[4].'_'.$arr[5].'_'.$arr[6]);
    }

    public function isAllowMultiInstance()
    {
        return false;
    }

    public function isAllowedInFrontend()
    {
        return true;
    }

    public function getSearchTabs()
    {
        return array($this->getIndexCode() => $this);
    }

    public function getIndexInstance()
    {
        if (!isset(self::$_instances[$this->getIndexCode().$this->getId()])) {
            $model = Mage::helper('searchindex/index')->getIndexModel($this->getIndexCode());
            if ($model) {
                $model->load($this->getId());
                self::$_instances[$this->getIndexCode().$this->getId()] = $model;
            } else {
                Mage::throwException("Can't find index instance for code ".$this->getIndexCode());
            }
        }

        return self::$_instances[$this->getIndexCode().$this->getId()];
    }

    public function getFieldsets()
    {
        return array();
    }

    public function canUse()
    {
        return true;
    }

    public function isLocked()
    {
        return false;
    }

    public function getIndexer()
    {
        $indexer = Mage::getSingleton('searchindex/index_'.$this->getCode().'_indexer');
        $indexer->setIndexModel($this);

        return $indexer;
    }

    public function reset()
    {
        $this->_tmpTableCreated = false;

        return $this;
    }

    public function getAttributes()
    {
        if (!$this->hasData('attributes')) {
            $attributes = unserialize($this->getAttributesSerialized());
            if (!is_array($attributes)) {
                $attributes = array();
            }

            $this->setData('attributes', $attributes);
        }

        return $this->getData('attributes');
    }

    public function getProperty($code)
    {
        if (!$this->hasData('properties')) {
            $properties = unserialize($this->getPropertiesSerialized());
            if (!is_array($properties)) {
                $properties = array();
            }

            $this->setData('properties', $properties);
        }

        return $this->getData('properties', $code);
    }

    public function reindexAll()
    {
        if (!Mage::helper('mstcore/code')->getStatus()) {
            return $this;
        }

        $uid = Mage::helper('mstcore/debug')->start();

        $this->getIndexInstance()->getIndexer()->reindexAll();
        $this->setStatus(1)
            ->save();

        Mage::helper('mstcore/debug')->end($uid);
    }

    public function getMatchedIds($queryText = null, $storeId = null)
    {
        if (!Mage::helper('mstcore/code')->getStatus()) {
            return array();
        }

        if ($queryText == null) {
            $query = $this->getQuery();
            $queryText = $query->getQueryText();

            if ($query->getSynonymFor()) {
                $queryText = $query->getSynonymFor();
            }
        }

        if ($storeId == null) {
            $storeId = Mage::app()->getStore()->getId();
        }

        if (!isset($this->_matchedIds[$queryText])) {
            $this->_processSearch($queryText, $storeId);
        }

        return $this->_matchedIds[$queryText];
    }

    public function setMatchedIds($queryText, $ids)
    {
        if (!is_array($ids)) {
            $ids = array();
        }

        $this->_matchedIds[$queryText] = $ids;

        return $this;
    }

    public function getQuery()
    {
        if (!$this->query) {
            $queryHelper = new Mage_CatalogSearch_Helper_Data();
            $this->query = $queryHelper->getQuery();

            $this->query
                ->save();
        }

        return $this->query;
    }

    /**
     * List of searchable attributes (search ONLY by these attributes).
     *
     * @return array
     */
    public function getSearchableAttributes()
    {
        if (Mage::getSingleton('core/app')->getRequest()->getParam('attr')) {
            $attr = Mage::getSingleton('core/app')->getRequest()->getParam('attr');
            $indexAttributes = $this->getAttributes();
            if (isset($indexAttributes[$attr])) {
                return array($attr);
            }
        }

        return array('data_index');
    }

    protected function _processSearch($queryText, $storeId)
    {
        $ts = microtime(true);

        $engine = Mage::helper('searchindex')->getSearchEngine();

        try {
            $result = $engine->query($queryText, $storeId, $this);
            $this->setMatchedIds($queryText, $result);

            $numberOfResults = count($result);
            $time = round(microtime(true) - $ts, 4);
            Mage::helper('mstcore/logger')->log($this, "Query: '$queryText', Store: $storeId", "Number of results: $numberOfResults, Time: $time sec.");
        } catch (Exception $e) {
            Mage::helper('mstcore/logger')->logException($this, $e, $e);

            // alternative engine (fulltext)
            try {
                $engine = Mage::getModel('searchsphinx/engine_fulltext');
                $result = $engine->query($queryText, $storeId, $this);
                $this->setMatchedIds($queryText, $result);
            } catch (Exception $e) {
                Mage::helper('mstcore/logger')->logException($this, $e, $e);
                $this->setMatchedIds($queryText, array());
            }
        }

        return $this;
    }

    public function getCountResults()
    {
        return $this->getCollection()->getSize();
    }

    public function joinMatched($collection, $mainTableKeyField = 'e.entity_id')
    {
        $matchedIds = $this->getMatchedIds(null, $this->getStoreId());
        $this->_createTemporaryTable($matchedIds);

        $collection->getSelect()->joinLeft(
            array('tmp_table' => $this->_getTemporaryTableName()),
            '(tmp_table.product_id='.$mainTableKeyField.')',
            array('relevance' => 'tmp_table.relevance')
        );

        if ($this->_useNativeTables) {
            $collection->getSelect()->where('tmp_table.query_id = '.$this->getQuery()->getId());
        }
        if (!$this->_useNativeTables) {
            $collection->getSelect()->where('tmp_table.id IS NOT NULL');
        }

        return $this;
    }

    public function getConnection()
    {
        return Mage::getSingleton('core/resource')->getConnection('core_read');
    }

    protected function _createTemporaryTable($matchedIds)
    {
        if ($this->_tmpTableCreated && Mage::app()->getRequest()->getRequestedControllerName() !== 'adminhtml_report') {
            return $this;
        }

        $values = array();
        $queryId = $this->getQuery()->getId();

        if (!$queryId) {
            $queryId = 0;
        }

        foreach ($matchedIds as $id => $relevance) {
            $values[] = '('.$queryId.','.$id.','.$relevance.')';
        }

        $connection = $this->getConnection();

        $query = '';
        if ($this->_useNativeTables) {
            $query .= 'CREATE TABLE IF NOT EXISTS `'.$this->_getTemporaryTableName().'` (';
        } else {
            $query .= 'CREATE TEMPORARY TABLE IF NOT EXISTS `'.$this->_getTemporaryTableName().'` (';
        }
        $query .= '
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `query_id` int(11) unsigned NOT NULL,
                `product_id` int(11) unsigned NOT NULL,
                `relevance` int(11) unsigned NOT NULL,
                PRIMARY KEY (`id`),
                INDEX `product_id` (`product_id`)';
        if ($this->_useNativeTables) {
            $query .= ')ENGINE=MEMORY DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
            $query .= 'DELETE FROM `'.$this->_getTemporaryTableName().'` WHERE `query_id`='.$queryId.';';
        } else {
            $query .= ')ENGINE=MYISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
            $query .= 'DELETE FROM `'.$this->_getTemporaryTableName().'`;';
        }
        if (count($values)) {
            $query .= 'INSERT INTO `'.$this->_getTemporaryTableName().'` (`query_id`, `product_id`, `relevance`)'.
                'VALUES '.implode(',', $values).';';
        }

        $connection->raw_query($query);
        $this->_tmpTableCreated = true;

        return $this;
    }

    protected function _getTemporaryTableName()
    {
        $tableName = '';
        if ($this->getCode() === 'mage_catalog_product' && $this->_useNativeTables) {
            $tableName = Mage::getSingleton('core/resource')->getTableName('catalogsearch/result');
        } else {
            $tableName = 'searchindex_result_'.$this->getCode();
        }

        return $tableName;
    }

    public function validate()
    {
        if ($this->getId() && count($this->getAttributes()) == 0) {
            Mage::throwException(Mage::helper('searchindex')->__("Search index should contains at least one configured attribute. Go to Search / Manage Search Indexes, open index {$this->getTitle()} and add least one attribute with weight at left bottom corner of page."));
        }

        return true;
    }
}
