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


class Mirasvit_Email_Model_Event_Customer_Birthday extends Mirasvit_Email_Model_Event_Abstract
{
    const EVENT_CODE = 'customer_birthday';

    public function getEventsGroup()
    {
        return Mage::helper('email')->__('Customer');
    }

    public function getEvents()
    {
        $result = array();

        $result[self::EVENT_CODE] = Mage::helper('email')->__('Customer Birthday');

        return $result;
    }

    public function findEvents($eventCode, $timestamp)
    {
        $events = array();

        $resource = Mage::getSingleton('core/resource');
        $read     = $resource->getConnection('core_read');

        $entityTable        = $resource->getTableName('customer/entity');
        $datetimeTable      = $entityTable.'_datetime';
        $customerEntityType = Mage::getModel('eav/entity_type')->loadByCode('customer')->getId();
        $birhdayAttrId      = Mage::getModel('eav/entity_attribute')->loadByCode($customerEntityType, 'dob')->getId();

        $select = $read->select()
            ->from(array('dt' => $datetimeTable), array('entity_id', 'value'))
            ->join(array('customer' => $entityTable),
                'customer.entity_id=dt.entity_id AND customer.entity_type_id=dt.entity_type_id',
                'store_id'
            )
            ->where('dt.entity_type_id=?', $customerEntityType)
            ->where('dt.attribute_id=?', $birhdayAttrId)
            ->where('DATE_FORMAT(dt.value, "%m-%d") = ?', date('m-d', $timestamp));

        $collection = $read->fetchAll($select);

        foreach ($collection as $birthday) {
            $customer = Mage::getModel('customer/customer')->load($birthday['entity_id']);

            $event = array(
                'customer_email' => $customer->getEmail(),
                'customer_name'  => $customer->getName(),
                'customer_id'    => $customer->getId(),
                'store_id'       => $birthday['store_id'],
                'expire_after'   => 7 * 24 * 60 * 60
            );

            $events[] = $event;
        }

        return $events;
    }
}