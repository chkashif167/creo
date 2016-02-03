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


class Mirasvit_Email_Test_Model_QueueTest extends EcomDev_PHPUnit_Test_Case
{
    protected $_model;

    protected function setUp()
    {
        parent::setUp();

        $this->_model =  Mage::getModel('email/queue');
    }

    /**
     * @test
     * @cover getTrigger
     *
     * @loadFixture queue
     */
    public function getTriggerTest()
    {
        $this->_model->load(1);
        $this->assertEquals(1, $this->_model->getTrigger()->getId());
    }

    /**
     * @test
     * @cover getChain
     *
     * @loadFixture queue
     */
    public function getChainTest()
    {
        $this->_model->load(2);
        $this->assertEquals(2, $this->_model->getChain()->getId());
    }

    /**
     * @test
     * @cover getSubject
     *
     * @loadFixture queue
     *
     * @dataProvider getSubjectProvider
     */
    public function getSubjectTest($queueId, $result)
    {
        $this->_model->load($queueId);
        $this->assertEquals($result, $this->_model->getSubject());
    }

    public function getSubjectProvider()
    {
        return array(
            array(1, 'Predifined subject'),
            array(2, 'Hello Martin')
        );
    }

    /**
     * @test
     * @cover getContent
     *
     * @loadFixture queue
     *
     * @dataProvider getContentProvider
     */
    public function getContentTest($queueId, $result)
    {
        $this->_model->load($queueId);
        $this->assertEquals($result, $this->_model->getContent());
    }

    public function getContentProvider()
    {
        return array(
            array(1, 'Email: martin@example.com Name: Martin'),
            array(2, 'Email: martin@example.com Name: Martin')
        );
    }

    /**
     * @test
     * @cover delivery
     *
     * @loadFixture queue
     */
    public function deliveryTest()
    {
        $this->_model->load(1);
        $this->assertEquals(Mirasvit_Email_Model_Queue::STATUS_PENDING, $this->_model->getStatus());

        $this->_model->delivery();

        $this->_model->load(1);
        $this->assertEquals(Mirasvit_Email_Model_Queue::STATUS_DELIVERED, $this->_model->getStatus());
    }

    /**
     * @test
     * @cover missed
     *
     * @loadFixture queue
     */
    public function missedTest()
    {
        $this->_model->load(1);
        $this->assertEquals(Mirasvit_Email_Model_Queue::STATUS_PENDING, $this->_model->getStatus());

        $this->_model->missed();

        $this->_model->load(1);
        $this->assertEquals(Mirasvit_Email_Model_Queue::STATUS_MISSED, $this->_model->getStatus());
    }

    /**
     * @test
     * @cover cancel
     *
     * @loadFixture queue
     */
    public function cancelTest()
    {
        $this->_model->load(1);
        $this->assertEquals(Mirasvit_Email_Model_Queue::STATUS_PENDING, $this->_model->getStatus());

        $this->_model->cancel();

        $this->_model->load(1);
        $this->assertEquals(Mirasvit_Email_Model_Queue::STATUS_CANCELED, $this->_model->getStatus());
    }

    /**
     * @test
     * @cover unsubscribe
     *
     * @loadFixture queue
     */
    public function unsubscribeTest()
    {
        $this->_model->load(1);
        $this->assertEquals(Mirasvit_Email_Model_Queue::STATUS_PENDING, $this->_model->getStatus());

        $this->_model->unsubscribe();

        $this->_model->load(1);
        $this->assertEquals(Mirasvit_Email_Model_Queue::STATUS_UNSUBSCRIBED, $this->_model->getStatus());
    }

    /**
     * @test
     * @cover reset
     *
     * @loadFixture queue
     */
    public function resetTest()
    {
        $this->_model->load(1);
        $this->assertEquals(Mirasvit_Email_Model_Queue::STATUS_PENDING, $this->_model->getStatus());

        $this->_model->reset();

        $this->_model->load(1);
        $this->assertEquals(Mirasvit_Email_Model_Queue::STATUS_PENDING, $this->_model->getStatus());
    }
}