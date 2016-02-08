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



class Mirasvit_Email_Model_Trigger extends Mage_Core_Model_Abstract
{
    protected $_chainCollection = null;

    protected function _construct()
    {
        $this->_init('email/trigger');
    }

    /**
     * Chain Collection sorted by delay.
     *
     * @return collection
     */
    public function getChainCollection()
    {
        if ($this->_chainCollection == null) {
            $this->_chainCollection = Mage::getModel('email/trigger_chain')->getCollection()
                ->addFieldToFilter('trigger_id', $this->getId())
                ->setOrder('delay', 'asc');
        }

        return $this->_chainCollection;
    }

    /**
     * List of triggering events.
     *
     * @return array
     */
    public function getTriggeringEvents()
    {
        return array($this->getData('event'));
    }

    /**
     * List of cancellation events.
     *
     * @return array
     */
    public function getCancellationEvents()
    {
        return array_filter(explode(',', $this->getData('cancellation_event')));
    }

    /**
     * List of all events (triggering + cancellation).
     *
     * @return array
     */
    public function getEvents()
    {
        return array_values(array_unique(array_merge($this->getTriggeringEvents(), $this->getCancellationEvents())));
    }

    public function getRunRule()
    {
        $rule = Mage::getModel('email/rule')->load($this->getRunRuleId());
        $rule->getConditions()->setJsFormObject('rule_run_fieldset');

        return $rule;
    }

    public function getStopRule()
    {
        Mage::throwException('depricated');
    }

    /**
     * Sender email specified for trigger or global.
     *
     * @return string
     */
    public function getSenderEmail($storeId = 0)
    {
        if ($this->getData('sender_email')) {
            return $this->getData('sender_email');
        }

        return Mage::getStoreConfig('trans_email/ident_general/email', $storeId);
    }

    /**
     * Sender name specified for trigger or global.
     *
     * @return string
     */
    public function getSenderName($storeId = 0)
    {
        if ($this->getData('sender_name')) {
            return $this->getData('sender_name');
        }

        return Mage::getStoreConfig('trans_email/ident_general/name', $storeId);
    }

    /**
     * Collection of events with status "new" for current trigger
     * (unprocesssed events for current trigger).
     *
     * @return collection
     */
    public function getNewEvents()
    {
        $collection = Mage::getModel('email/event')->getCollection()
            ->addFieldToFilter('code', array('in' => $this->getEvents()))
            ->addNewFilter($this->getId(), $this->getStoreIds())
            ->setOrder('main_table.created_at', 'asc');
        $collection->getSelect()->limit(100);

        return $collection;
    }

    /**
     * Processing all new events.
     *
     * @return $this
     */
    public function processNewEvents()
    {
        $collection = $this->getNewEvents();

        foreach ($collection as $event) {
            $this->processNewEvent($event);
            $event->addProcessedTriggerId($this->getId());
        }

        return $this;
    }

    /**
     * Processing one event.
     *
     * @param object $event
     * @param bool   $isTest
     *
     * @return $this
     */
    public function processNewEvent($event, $isTest = false)
    {
        if (in_array($event->getCode(), $this->getCancellationEvents())) {
            $this->cancelEvent($event, $isTest);
        }

        if (in_array($event->getCode(), $this->getTriggeringEvents())) {
            $this->triggerEvent($event, $isTest);
        }

        return $this;
    }

    public function validateRules($args)
    {
        $objArgs = new Varien_Object($args);

        $runRule = $this->getRunRule();
        $runRuleResult = $runRule->validate($objArgs);

        return $runRuleResult;
    }

    /**
     * Trigger Event!
     * Check run, stop rules
     * Generate mail chain.
     *
     * @param object $event
     * @param bool   $isTest
     *
     * @return $this
     */
    public function triggerEvent($event, $isTest = false)
    {
        $args = $event->getArgs();

        if (!$isTest) {
            if (!$this->validateRules($args)) {
                return false;
            }
        }

        foreach ($this->getChainCollection() as $chain) {
            $uniqKey = $event->getUniqKey().'|'.$this->getId().'|'.$chain->getId();
            $scheduledAt = $chain->getScheduledAt($args['time']);

            if ($isTest) {
                $args['is_test'] = true;
                $scheduledAt = time();
            }

            $gmtScheduledAt = date('Y-m-d H:i:s', $scheduledAt);

            $queueCollection = Mage::getModel('email/queue')->getCollection()
                ->addFieldToFilter('trigger_id', $this->getId())
                ->addFieldToFilter('chain_id', $chain->getId())
                ->addFieldToFilter('uniq_key', $uniqKey)
                ->addFieldToFilter('scheduled_at', $gmtScheduledAt);

            if ($queueCollection->count() != 0) {
                continue;
            }

            $queue = Mage::getModel('email/queue');
            $queue->setTriggerId($this->getId())
                ->setChainId($chain->getId())
                ->setUniqKey($uniqKey)
                ->setSenderEmail($this->getSenderEmail($args['store_id']))
                ->setSenderName($this->getSenderName($args['store_id']))
                ->setRecipientEmail($args['customer_email'])
                ->setRecipientName($args['customer_name'])
                ->setArgs($args)
                ->setScheduledAt($gmtScheduledAt)
                ->save();

            if ($isTest) {
                $queue->setTest(1);
                $queue->send();
            }
        }
    }

    public function cancelEvent($event, $isTest = false)
    {
        $args = $event->getArgs();

        $queueCollection = Mage::getModel('email/queue')->getCollection()
            ->addFieldToFilter('status', array('neq' => Mirasvit_Email_Model_Queue::STATUS_DELIVERED))
            ->addFieldToFilter('trigger_id', $this->getId())
            ->addFieldToFilter('recipient_email', $args['customer_email']);

        foreach ($queueCollection as $queue) {
            $queue->cancel('Cancelation Event');
        }

        return $this;
    }

    public function generate($timestamp = null)
    {
        if ($timestamp) {
            $queueCollection = Mage::getModel('email/queue')->getCollection()
                ->addFieldToFilter('trigger_id', $this->getId())
                ->addFieldToFilter('created_at', array('gteq' => Mage::getSingleton('core/date')->date(null, $timestamp)))
                ->addFieldToFilter('status', array('neq' => Mirasvit_Email_Model_Queue::STATUS_DELIVERED));
            foreach ($queueCollection as $queue) {
                $queue->delete();
            }
        }

        foreach ($this->getEvents() as $eventCode) {
            $oldEvents = Mage::getModel('email/event')->getCollection()->addFieldToFilter('code', $eventCode);
            foreach ($oldEvents as $event) {
                $event->delete();
            }
            $eventModel = Mage::helper('email/event')->getEventModel($eventCode);
            $eventModel->setGeneratedMode(Mirasvit_Email_Model_Queue::GENERATED_MODE_MANUAL)
                ->check($eventCode, $timestamp);
        }

        $this->processNewEvents();
    }

    public function sendTest($to = null)
    {
        $storeIds = $this->getStoreIds();
        if ($storeIds[0] == 0) {
            unset($storeIds[0]);
            foreach (Mage::app()->getStores() as $storeId => $store) {
                if ($store->getIsActive()) {
                    $storeIds[] = $storeId;
                }
            }
        }

        foreach ($storeIds as $storeId) {
            $args = Mage::helper('email/event')->getRandomEventArgs();
            $args['store_id'] = $storeId;
            if ($to !== null) {
                $args['customer_email'] = $to;
            }

            $event = Mage::getModel('email/event')
                ->setArgsSerialized(serialize($args))
                ->setUniqKey('test_'.time());

            ini_set('display_errors', 1);

            try {
                $this->triggerEvent($event, true);
            } catch (Exception $e) {
                throw $e;
            }
        }
    }
}
