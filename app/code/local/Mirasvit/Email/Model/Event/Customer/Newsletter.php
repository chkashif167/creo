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


class Mirasvit_Email_Model_Event_Customer_Newsletter extends Mirasvit_Email_Model_Event_Abstract
{
    const EVENT_CODE = 'customer_newsletter|';

    public function getEventsGroup()
    {
        return Mage::helper('email')->__('Customer');
    }

    public function getEvents()
    {
        $result = array();

        $result[self::EVENT_CODE.'subscribed'] = Mage::helper('email')->__('Newsletter subscription');
        $result[self::EVENT_CODE.'unsubscribed'] = Mage::helper('email')->__('Newsletter unsubscription');

        return $result;
    }

    public function findEvents($eventCode, $from)
    {
        return array();
    }

    public function observer($eventCode, $observer)
    {
        $subsriber = $observer->getDataObject();

        $customer = Mage::getModel('customer/customer')->load($subsriber->getCustomerId());

        $event = array(
            'time'              => time(),
            'customer_email'    => $subsriber->getSubscriberEmail(),
            'customer_name'     => $customer->getName(),
            'customer_id'       => $customer->getId(),
            'store_id'          => $subsriber->getStoreId(),
        );

        $this->saveEvent($eventCode, $this->getEventUniqKey($event), $event);

        return $this;
    }
}