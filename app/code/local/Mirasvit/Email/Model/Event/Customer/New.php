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


class Mirasvit_Email_Model_Event_Customer_New extends Mirasvit_Email_Model_Event_Abstract
{
    const EVENT_CODE = 'customer_new';

    public function getEventsGroup()
    {
        return Mage::helper('email')->__('Customer');
    }

    public function getEvents()
    {
        $result = array();

        $result[self::EVENT_CODE] = Mage::helper('email')->__('New customer signup');

        return $result;
    }

    public function findEvents($eventCode, $from)
    {
        $events     = array();
        $resource   = Mage::getSingleton('core/resource');
        $collection = Mage::getModel('customer/customer')->getCollection();

        $collection->getSelect()
            ->where('created_at >= ?', date('Y-m-d H:i:s', $from - 40000));
        foreach ($collection as $customer) {
            $customer = Mage::getModel('customer/customer')->load($customer->getId());
            $event = array(
                'time'           => strtotime($customer->getCreatedAt()),
                'customer_email' => $customer->getEmail(),
                'customer_name'  => $customer->getFirstname().' '.$customer->getLastname(),
                'customer_id'    => $customer->getId(),
                'store_id'       => $customer->getStoreId(),
            );
            $events[] = $event;
        }

        return $events;
    }
}