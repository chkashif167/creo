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


require_once 'abstract.php';

class Mirasvit_Shell_EmailTest extends Mage_Shell_Abstract
{
    public function run()
    {
        // $this->fillReportData();
        Mage::getModel('emailreport/aggregated')->aggregateAll();
    }

    public function fillReportData()
    {
        $queueIds = Mage::getModel('email/queue')->getCollection()->getAllIds();

        for ($i = 0; $i < 1000; $i++) {
            $queueId = $queueIds[rand(0, count($queueIds))];
            $queue = Mage::getModel('email/queue')->load($queueId);

            Mage::getModel('emailreport/open')
                ->setQueueId($queue->getId())
                ->setTriggerId($queue->getTrigger()->getId())
                ->setSessionId(microtime(true))
                ->setCreatedAt(Mage::getSingleton('core/date')->date(null, time() - rand(0, 365 * 24 * 60 * 60)))
                ->save();

            if (rand(0, 2) == 1) {
                Mage::getModel('emailreport/click')
                    ->setQueueId($queue->getId())
                    ->setTriggerId($queue->getTrigger()->getId())
                    ->setSessionId(microtime(true))
                    ->setCreatedAt(Mage::getSingleton('core/date')->date(null, time() - rand(0, 365 * 24 * 60 * 60)))
                    ->save();
            }


            if (rand(0, 20) == 1) {
                Mage::getModel('emailreport/review')
                    ->setQueueId($queue->getId())
                    ->setTriggerId($queue->getTrigger()->getId())
                    ->setSessionId(microtime(true))
                    ->setReviewId(0)
                    ->setCreatedAt(Mage::getSingleton('core/date')->date(null, time() - rand(0, 365 * 24 * 60 * 60)))
                    ->save();

            }

            if (rand(0, 25) == 1) {
                Mage::getModel('emailreport/order')
                    ->setQueueId($queue->getId())
                    ->setTriggerId($queue->getTrigger()->getId())
                    ->setSessionId(microtime(true))
                    ->setRevenue(rand(100, 1000))
                    ->setCreatedAt(Mage::getSingleton('core/date')->date(null, time() - rand(0, 365 * 24 * 60 * 60)))
                    ->save();
                echo '+'; 
            }

            echo '-';
        }
    }

    public function testEvents()
    {
        $eventObj = new Mirasvit_Email_Model_Event_Cart_Abandoned();
        $events = array();

        $events[] = array(
            'time'           => 1413387200,
            'customer_email' => 'bob1@example.com',
            'customer_name'  => 'Bob1 Joe1',
            'customer_id'    => null,
            'store_id'       => 1,
            'quote_id'       => 5,
        );

        foreach ($events as $event) {
            $uniqKey = $eventObj->getEventUniqKey($event);
            $eventObj->saveEvent(Mirasvit_Email_Model_Event_Cart_Abandoned::EVENT_CODE, $uniqKey, $event);
        }

        $triggers = Mage::getModel('email/trigger')->getCollection()
            ->addActiveFilter();

        foreach ($triggers as $trigger) {
            $trigger->processNewEvents();
        }
    }
}
$test = new Mirasvit_Shell_EmailTest();
$test->run();