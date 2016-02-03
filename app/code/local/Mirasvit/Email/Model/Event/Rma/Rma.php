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


class Mirasvit_Email_Model_Event_Rma_Rma extends Mirasvit_Email_Model_Event_Abstract
{
    const EVENT_CODE = 'rma_created';

    public function getEventsGroup()
    {
        return Mage::helper('email')->__('Rma');
    }

    public function getEvents()
    {
        $result = array();
        $result[self::EVENT_CODE] = Mage::helper('email')->__('RMA created');

        return $result;
    }

    public function findEvents($eventCode, $timestamp)
    {
        $events   = array();
        $fromDate = date('Y-m-d H:i:s', $timestamp);

        $rmaCollection = Mage::getModel('rma/rma')->getCollection()
            ->addFieldToFilter('main_table.created_at', array('gt' => $fromDate))
            ->setOrder('main_table.created_at', 'asc');

        foreach ($rmaCollection as $rma) {
            $events[] = array(
                'time'              => strtotime($rma->getCreatedAt()),
                'customer_email'    => $rma->getEmail(),
                'customer_id'       => $rma->getCustomerId(),
                'rma_id'            => $rma->getId(),
                'order_id'          => $rma->getOrderId(),
                'store_id'          => $rma->getStoreId()
            );
        }

        return $events;
    }
}