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
 * @package   Follow Up Email
 * @version   1.0.2
 * @build     564
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Email_Model_Resource_Trigger_Chain extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('email/trigger_chain', 'chain_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->isObjectNew() && !$object->hasCreatedAt()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }

        $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());

        return parent::_beforeSave($object);
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        parent::_afterLoad($object);
        $object->prepareSerializedData();

        return $this;
    }

    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        $queueCollectionToDelete = Mage::getModel('email/queue')->getCollection()
            ->addFieldToFilter('status', Mirasvit_Email_Model_Queue::STATUS_PENDING)
            ->addFieldToFilter('chain_id', $object->getId());

        foreach ($queueCollectionToDelete as $queue) {
            $queue->delete();
        }

        return $this;
    }
}