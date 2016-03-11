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



class Mirasvit_FeedExport_Model_Resource_Feed extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('feedexport/feed', 'feed_id');
    }

    /**
     * Before save.
     *
     * @param Mirasvit_FeedExport_Model_Feed $object
     *
     * @return $this
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->isObjectNew() && !$object->hasCreatedAt()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }

        $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());

        if (!$object->getIsMassStatus()) {
            Mage::helper('feedexport/format')->preparePostData($object);

            if (is_array($object->getCronDay())) {
                $object->setCronDay(implode(',', $object->getCronDay()));
            }
            if (is_array($object->getCronTime())) {
                $object->setCronTime(implode(',', $object->getCronTime()));
            }
            if (is_array($object->getNotificationEvents())) {
                $object->setNotificationEvents(implode(',', $object->getNotificationEvents()));
            }
        }

        $this->saveRules($object);

        return parent::_beforeSave($object);
    }

    /**
     * @param Mirasvit_FeedExport_Model_Feed $object
     *
     * @return $this
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        Mage::helper('feedexport/format')->expandFormat($object);

        $object->setCronDay(explode(',', $object->getCronDay()));
        $object->setCronTime(explode(',', $object->getCronTime()));

        $this->loadRules($object);

        return parent::_afterLoad($object);
    }

    /**
     * @param Mirasvit_FeedExport_Model_Feed $object
     *
     * @return Mirasvit_FeedExport_Model_Feed
     */
    public function loadRules(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('feedexport/rule_feed'), array('*'))
            ->where('feed_id = ?', $object->getId());

        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $array = array();
            foreach ($data as $row) {
                $array[] = $row['rule_id'];
            }
            $object->setData('rule_ids', $array);
        }

        return $object;
    }

    /**
     * @param Mirasvit_FeedExport_Model_Feed $object
     */
    protected function saveRules(Mage_Core_Model_Abstract $object)
    {
        $table = $this->getTable('feedexport/rule_feed');
        $condition = $this->_getWriteAdapter()->quoteInto('feed_id = ?', $object->getId());

        $this->_getWriteAdapter()->delete($table, $condition);

        foreach ((array) $object->getData('rule_ids') as $ruleId) {
            $insertArray = array(
                'feed_id' => $object->getId(),
                'rule_id' => $ruleId,
            );
            $this->_getWriteAdapter()->insert($table, $insertArray);
        }
    }

    /**
     * @param Mirasvit_FeedExport_Model_Feed $object
     * @param array                          $productIds
     *
     * @return $this
     */
    public function saveProductIds(Mage_Core_Model_Abstract $object, $productIds)
    {
        $feedId = intval($object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('feedexport/feed_product'), 'feed_id = '.$feedId);

        $queryStart = 'INSERT INTO '.$this->getTable('feedexport/feed_product').' (
                feed_id, product_id) values ';
        $queryEnd = ' ON DUPLICATE KEY UPDATE product_id = VALUES(product_id)';

        foreach ($productIds as $productId) {
            $rows[] = "('".implode("','", array($feedId, $productId))."')";

            if (sizeof($rows) == 1000) {
                $sql = $queryStart.implode(',', $rows).$queryEnd;
                $this->_getWriteAdapter()->query($sql);
                $rows = array();
            }
        }

        if (!empty($rows)) {
            $sql = $queryStart.implode(',', $rows).$queryEnd;
            $this->_getWriteAdapter()->query($sql);
        }

        return $this;
    }
}
