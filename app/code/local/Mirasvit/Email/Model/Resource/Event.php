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


class Mirasvit_Email_Model_Resource_Event extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('email/event', 'event_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->isObjectNew() && !$object->hasCreatedAt()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }

        $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());

        if ($object->hasData('args')) {
            $object->setArgsSerialized(serialize($object->getData('args')));
        }
        
        return parent::_beforeSave($object);
    }

    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $connection->delete($this->getTable('email/event_trigger'), 'event_id = '.$object->getId());

        return parent::_beforeDelete($object);
    }

    public function addProcessedTriggerId($eventId, $triggerId)
    {
        $data = array(
            'trigger_id' => $triggerId,
            'event_id'   => $eventId,
            'status'     => 'done',
            'created_at' => Mage::getSingleton('core/date')->gmtDate(),
            'updated_at' => Mage::getSingleton('core/date')->gmtDate(),
        );

        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

        $connection->delete($this->getTable('email/event_trigger'),
            'event_id = '.$eventId.' AND trigger_id='.$triggerId);

        $connection->insert(
            $this->getTable('email/event_trigger'),
            $data
        );
    }

    public function getProcessedTriggerIds($eventId)
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

        $select = $connection->select()->from($this->getTable('email/event_trigger'))->where('event_id=?', $eventId);

        return $connection->fetchAll($select);
    }

    public function removeProcessedTriggers($eventId)
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

        $connection->delete($this->getTable('email/event_trigger'),
            'event_id = '.$eventId);

        return true;
    }
}