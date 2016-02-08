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


class Mirasvit_Email_Model_Event_Email_Click extends Mirasvit_Email_Model_Event_Abstract
{
    const EVENT_CODE = 'email_click|';

    public function getEventsGroup()
    {
        return Mage::helper('email')->__('Emails');
    }

    public function getEvents()
    {
        $result = array();

        $triggers = Mage::getModel('email/trigger')->getCollection();
        foreach ($triggers as $trigger) {
            $result[self::EVENT_CODE.$trigger->getId()] = Mage::helper('email')->__('Open link in email from trigger "'.$trigger->getTitle().'"');
        }

        return $result;
    }

    public function findEvents($eventCode, $timestamp)
    {
        $events   = array();

        $createdFrom = date('Y-m-d H:i:s', $timestamp);

        $collection = Mage::getModel('emailreport/click')->getCollection();
        $collection->getSelect()
            ->where('created_at >= ?', $createdFrom);

        foreach ($collection as $click) {
            $code = self::EVENT_CODE.$click->getTriggerId();
            
            if ($code == $eventCode) {
                $queue = Mage::getModel('email/queue')->load($click->getQueueId());
                $args = $queue->getArgs();


                $event = array(
                    'time'           => strtotime($click->getCreatedAt()),
                    'customer_email' => $args['customer_email'],
                    'customer_name'  => $args['customer_name'],
                    'customer_id'    => $args['customer_id'],
                    'store_id'       => $args['store_id'],
                );

                $events[] = $event;
            }
        }

        return $events;
    }
}