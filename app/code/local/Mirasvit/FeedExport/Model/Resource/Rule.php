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
 * @package   Advanced Product Feeds
 * @version   1.1.4
 * @build     702
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_FeedExport_Model_Resource_Rule extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('feedexport/rule', 'rule_id');
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getIsMassDelete()) {
            $this->loadFeeds($object);
        }

        return parent::_afterLoad($object);
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getIsMassStatus()) {
            $this->saveFeeds($object);
        }

        return parent::_afterSave($object);
    }

    public function loadFeeds(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('feedexport/rule_feed'))
            ->where('rule_id = ?', $object->getId());

        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $array = array();
            foreach ($data as $row) {
                $array[] = $row['feed_id'];
            }
            $object->setData('feed_ids', $array);
        }
        return $object;
    }

    protected function saveFeeds($object)
    {
        $table     = $this->getTable('feedexport/rule_feed');
        $condition = $this->_getWriteAdapter()->quoteInto('rule_id = ?', $object->getId());

        $this->_getWriteAdapter()->delete($table, $condition);

        foreach ((array)$object->getData('feed_ids') as $feedId => $value) {
            $insertArray = array(
                'rule_id' => $object->getId(),
                'feed_id' => $feedId
            );
            $this->_getWriteAdapter()->insert($table, $insertArray);
        }
    }

    public function clearProductIds($ruleId)
    {
        $write  = $this->_getWriteAdapter();
        $write->delete($this->getTable('feedexport/rule_product'), $write->quoteInto('rule_id = ?', $ruleId));

        return $this;
    }

    public function saveProductIds($ruleId, $productIds)
    {
        $write = $this->_getWriteAdapter();
        $rows  = array();

        $queryStart = 'INSERT INTO '.$this->getTable('feedexport/rule_product').' (
                rule_id, product_id) values ';
        $queryEnd = ' ON DUPLICATE KEY UPDATE product_id = VALUES(product_id)';

        foreach ($productIds as $productId) {
            $rows[] = "('".implode("','", array($ruleId, $productId))."')";

            if (sizeof($rows) == 1000) {
                $sql = $queryStart.join(',', $rows).$queryEnd;
                $write->query($sql);
                $rows = array();
            }
        }

        if (!empty($rows)) {
            $sql = $queryStart.join(',', $rows).$queryEnd;
            $write->query($sql);
        }

        return $this;
    }

    public function getRuleProductIds($ruleId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getTable('feedexport/rule_product'), 'product_id')
            ->where('rule_id=?', $ruleId);
        return $read->fetchCol($select);
    }
}
