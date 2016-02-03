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



class Mirasvit_Email_Model_Event_Order_Status extends Mirasvit_Email_Model_Event_Abstract
{
    const EVENT_CODE = 'order_status|';

    public function getEventsGroup()
    {
        return Mage::helper('email')->__('Order');
    }

    public function getEvents()
    {
        $result = array();
        $result[self::EVENT_CODE] = __('Order obtained new status');

        $orderStatuses = Mage::getSingleton('sales/order_config')->getStatuses();
        foreach ($orderStatuses as $code => $name) {
            $result[self::EVENT_CODE.$code] = __("Order obtained '%s' status", $name);
        }

        return $result;
    }

    public function findEvents($eventCode, $timestamp)
    {
        $events = array();
        $fromDate = date('Y-m-d H:i:s', $timestamp);
        $resource = Mage::getSingleton('core/resource');

        $historyCollection = Mage::getModel('sales/order_status_history')->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('main_table.created_at', array('gt' => $fromDate))
            ->setOrder('main_table.created_at', 'asc');

        $historyCollection->getSelect()
            ->joinLeft(
                array('order' => $resource->getTableName('sales/order')),
                'main_table.parent_id = order.entity_id',
                array(
                    'customer_name' => 'CONCAT_WS(" ", order.customer_firstname, order.customer_lastname)',
                    'customer_email' => 'order.customer_email',
                    'customer_id' => 'order.customer_id',
                    'store_id' => 'order.store_id',
                )
            )
            ->group('CONCAT(main_table.parent_id, main_table.status)');

        foreach ($historyCollection as $history) {
            $code = self::EVENT_CODE.$history->getStatus();

            if ($code == $eventCode || $eventCode == self::EVENT_CODE) {
                $args = array(
                    'time' => strtotime($history->getCreatedAt()),
                    'created_at' => $history->getCreatedAt(),
                    'customer_email' => $history['customer_email'],
                    'customer_name' => $history['customer_name'],
                    'customer_id' => $history['customer_id'],
                    'order_id' => $history['parent_id'],
                    'store_id' => $history['store_id'],
                );

                $events[] = $args;
            }
        }

        return $events;
    }
}
