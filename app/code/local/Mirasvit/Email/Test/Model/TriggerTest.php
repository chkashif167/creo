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


class Mirasvit_Email_Test_Model_TriggerTest extends EcomDev_PHPUnit_Test_Case
{
    protected $_model;

    protected function setUp()
    {
        parent::setUp();

        $this->_model =  Mage::getModel('email/trigger');
    }

    /**
     * @test
     * @cover getTriggeringEvents
     *
     * @loadFixture triggers
     *
     * @dataProvider getTriggeringEventsProvider
     *
     * @doNotIndexAll 1
     */
    public function getTriggeringEventsTest($triggerId, $events)
    {
        $this->_model->load($triggerId);
        $this->assertEquals($events, $this->_model->getTriggeringEvents());
    }

    public function getTriggeringEventsProvider()
    {
        return array(
            array(1, array('cart_abandoned')),
            array(2, array('customer_new')),
        );
    }

    /**
     * @test
     * @cover getCancellationEvents
     *
     * @loadFixture triggers
     *
     * @dataProvider getCancellationEventsProvider
     *
     * @doNotIndexAll 1
     */
    public function getCancellationEventsTest($triggerId, $events)
    {
        $this->_model->load($triggerId);
        $this->assertEquals($events, $this->_model->getCancellationEvents());
    }

    public function getCancellationEventsProvider()
    {
        return array(
            array(1, array('order_placed', 'customer_loggedin')),
            array(2, array('customer_new', 'order_placed', 'customer_loggedin')),
        );
    }

    /**
     * @test
     * @cover getEvents
     *
     * @loadFixture triggers
     *
     * @dataProvider getEventsProvider
     *
     * @doNotIndexAll 1
     */
    public function getEventsTest($triggerId, $events)
    {
        $this->_model->load($triggerId);
        $this->assertEquals($events, $this->_model->getEvents());
    }

    public function getEventsProvider()
    {
        return array(
            array(1, array('cart_abandoned', 'order_placed', 'customer_loggedin')),
            array(2, array('customer_new', 'order_placed', 'customer_loggedin')),
        );
    }

    /**
     * @test
     * @cover getNewEventsTest
     *
     * @loadFixture trigger_event
     *
     * @dataProvider getNewEventsProvider
     *
     * @doNotIndexAll 1
     */
    public function getNewEventsTest($triggerId, $eventIds)
    {
        $this->_model->load($triggerId);
        $collection = $this->_model->getNewEvents();

        $this->assertEquals($eventIds, $collection->getAllIds());
        $this->assertEquals(count($eventIds), $collection->count());
    }

    public function getNewEventsProvider()
    {
        return array(
            array(1, array(1, 2, 4)),
            array(2, array(2, 4)),
        );
    }

    /**
     * @test
     * @cover processNewEvents
     *
     * @loadFixture trigger_event
     *
     * @dataProvider processNewEventsProvider
     *
     * @doNotIndexAll 1
     */
    public function processNewEventsTest($triggerId)
    {
        $this->_model->load($triggerId);
        $this->assertTrue($this->_model->getNewEvents()->count() > 0);

        $this->_model->processNewEvents();

        $this->assertEquals(0, $this->_model->getNewEvents()->count());
    }

    public function processNewEventsProvider()
    {
        return array(
            array(1),
            array(2),
        );
    }

    public function cancelEventTest()
    {

    }

    /**
     * @test
     * @cover triggerEvent
     *
     * @loadFixture trigger_chain
     *
     * @dataProvider triggerEventProvider
     *
     * @doNotIndexAll 1
     */
    public function triggerEventTest($triggerId, $eventId, $expectedQueue)
    {
        $event = Mage::getModel('email/event')->load($eventId);
        $trigger = $this->_model->load($triggerId);
        $trigger->triggerEvent($event);

        $queueCollection = Mage::getModel('email/queue')->getCollection()
            ->addFieldToFilter('trigger_id', $trigger->getId())
            ->addFieldToFilter('uniq_key', $event->getUniqKey());

        $queue = $queueCollection->toArray();

        foreach ($expectedQueue as $indx => $expectedItem) {
            foreach ($expectedItem as $key => $value) {
                $this->assertEquals($value, $queue['items'][$indx][$key]);
            }
        }
    }

    public function triggerEventProvider()
    {
        return array(
            array(1, 1, array(
                array(
                    'status'          => 'pending',
                    'trigger_id'      => 1,
                    'chain_id'        => 1,
                    'uniq_key'        => 'eventuniqkey',
                    'uniq_key_md5'    => '4e15f13def7ce7fc64afb6ed0328face',
                    'scheduled_at'    => '2013-10-01 00:00:00',
                    'recipient_email' => 'bob@example.com',
                    'recipient_name'  => 'Bob',
                ),
                array(
                    'status'          => 'pending',
                    'trigger_id'      => 1,
                    'chain_id'        => 2,
                    'uniq_key'        => 'eventuniqkey',
                    'uniq_key_md5'    => '4e15f13def7ce7fc64afb6ed0328face',
                    'scheduled_at'    => '2013-10-01 01:00:05',
                    'recipient_email' => 'bob@example.com',
                    'recipient_name'  => 'Bob',
                ),
            )),
        );
    }

    // /**
    //  * @test
    //  * @cover prepareArgs
    //  *
    //  * @dataProvider prepareArgsProvider
    //  */
    // public function prepareArgsTest($expected, $key, $args)
    // {
    //     $this->_model->prepareArgs($key, $args);
    //     $this->assertEquals($expected, $args);
    // }

    // public function prepareArgsProvider()
    // {
    //     return array(
    //         array()
    //     );
    // }
}