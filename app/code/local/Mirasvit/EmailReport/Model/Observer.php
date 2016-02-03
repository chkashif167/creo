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



class Mirasvit_EmailReport_Model_Observer extends Varien_Object
{
    public function onControllerActionPredispatch($observer)
    {
        if (Mage::app()->getRequest()->getParam('emqc')) {
            $queueKey = Mage::app()->getRequest()->getParam('emqc');

            $queue = Mage::getModel('email/queue')->loadByUniqKeyMd5($queueKey);
            $sessionId = Mage::getSingleton('core/session')->getSessionId();
            $triggerId = ($queue) ? $queue->getTriggerId() : null;

            if ($queue && $queue->getId() && !$this->isReported('click', $queue->getId(), $triggerId, $sessionId)) {
                Mage::getModel('emailreport/click')
                    ->setQueueId($queue->getId())
                    ->setTriggerId($triggerId)
                    ->setSessionId($sessionId)
                    ->save();

                Mage::helper('emailreport')->setQueueId($queue->getId());
            }
        }
    }

    public function onReviewSaveAfter($observer)
    {
        if ($observer->getObject()
            && $observer->getObject()->getReviewId()
            && Mage::helper('emailreport')->getQueueId()) {
            $queue = Mage::getModel('email/queue')->load(Mage::helper('emailreport')->getQueueId());
            $sessionId = Mage::getSingleton('core/session')->getSessionId();
            $triggerId = ($queue) ? $queue->getTriggerId() : null;

            if ($queue && $queue->getId() && !$this->isReported('review', $queue->getId(), $triggerId, $sessionId)) {
                Mage::getModel('emailreport/review')
                    ->setQueueId($queue->getId())
                    ->setTriggerId($triggerId)
                    ->setSessionId($sessionId)
                    ->setReviewId($observer->getObject()->getReviewId())
                    ->save();
            }
        }
    }

    public function onSalesOrderPlaceAfter($observer)
    {
        if ($observer->getOrder()
            && Mage::helper('emailreport')->getQueueId()) {
            $order = $observer->getOrder();
            $queue = Mage::getModel('email/queue')->load(Mage::helper('emailreport')->getQueueId());
            $sessionId = Mage::getSingleton('core/session')->getSessionId();
            $triggerId = ($queue) ? $queue->getTriggerId() : null;

            if ($queue && $queue->getId() && !$this->isReported('order', $queue->getId(), $triggerId, $sessionId)) {
                Mage::getModel('emailreport/order')
                    ->setQueueId($queue->getId())
                    ->setTriggerId($triggerId)
                    ->setSessionId($sessionId)
                    ->setRevenue($order->getBaseGrandTotal())
                    ->setCoupon($order->getCouponCode())
                    ->save();
            }
        }
    }

    public function onEmailQueueGetContentAfter($observer)
    {
        $queue = $observer->getQueue();

        Mage::helper('emailreport')->prepareQueueContent($queue);
    }

    public function isReported($reportType, $queueId, $triggerId, $sessionId)
    {
        $reports = Mage::getModel('emailreport/'.$reportType)->getCollection()
            ->addFieldToFilter('queue_id', $queueId)
            ->addFieldToFilter('trigger_id', $triggerId)
            ->addFieldToFilter('session_id', $sessionId);

        return (bool) $reports->count();
    }
}
