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



abstract class Mirasvit_Email_Model_Event_Abstract
{
    private $generatedMode = null;

    public function getGeneratedMode()
    {
        return $this->generatedMode;
    }

    public function setGeneratedMode($mode)
    {
        $this->generatedMode = $mode;

        return $this;
    }

    abstract public function getEvents();

    /**
     * Return name of event group, like Customer, Cart, Base, Wishlist etc.
     *
     * @return string
     */
    public function getEventsGroup()
    {
        return Mage::helper('email')->__('Base');
    }

    abstract public function findEvents($eventCode, $timestamp);

    public function check($eventCode, $timestamp = false)
    {
        $timeVar = 'last_check_'.$eventCode;

        if (!$timestamp) {
            $timestamp = Mage::helper('email')->getVar($timeVar);
            if (!$timestamp) {
                $timestamp = Mage::getSingleton('core/date')->gmtTimestamp();
            }
        }

        $events = $this->findEvents($eventCode, $timestamp);

        foreach ($events as $event) {
            $uniqKey = $this->getEventUniqKey($event);

            $this->saveEvent($eventCode, $uniqKey, $event);
        }

        Mage::helper('email')->setVar($timeVar, Mage::getSingleton('core/date')->gmtTimestamp());

        return true;
    }

    /**
     * default args
     * ! customer_name
     * ! customer_email
     * ! store_id
     * ? customer_id
     * ? customer
     * ? order.
     */
    public function saveEvent($code, $uniqKey, $args)
    {
        if (!isset($args['expire_after'])) {
            $args['expire_after'] = 3600;
        }
        if (!isset($args['time'])) {
            $args['time'] = time();
        }

        $args['generated_mode'] = ($this->getGeneratedMode() === Mirasvit_Email_Model_Queue::GENERATED_MODE_MANUAL)
            ? Mirasvit_Email_Model_Queue::GENERATED_MODE_MANUAL
            : Mirasvit_Email_Model_Queue::GENERATED_MODE_CRON;

        $gmtExpireAt = date('Y-m-d H:i:s', $args['time'] - $args['expire_after']);
        $event = Mage::getModel('email/event')->getCollection()
            ->addFieldToFilter('uniq_key', $uniqKey)
            ->addFieldToFilter('code', $code)
            ->addFieldToFilter('created_at', array('gt' => $gmtExpireAt))
            ->getFirstItem();

        if ($event->getId()) {
            return $event;
        }

        $event = Mage::getModel('email/event');
        $event->setCode($code)
            ->setUniqKey($uniqKey)
            ->setArgs($args)
            ->setStoreIds($args['store_id']);

        if (isset($args['time'])) {
            $gmtCreatedAt = date('Y-m-d H:i:s', $args['time']);
            $event->setCreatedAt($gmtCreatedAt);
        }

        $event->save();

        return $event;
    }

    public function getEventUniqKey($args)
    {
        $key = array();

        foreach ($args as $k => $v) {
            if (in_array($k, array('customer_email', 'customer_id', 'quote_id', 'order_id', 'store_id', 'wishlist_id'))) {
                $key[] = $v;
            }
        }

        return implode('|', $key);
    }
}
