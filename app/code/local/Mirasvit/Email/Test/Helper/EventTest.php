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


class Mirasvit_Email_Test_Helper_EventTest extends EcomDev_PHPUnit_Test_Case
{
    protected $_helper;

    protected function setUp()
    {
        $this->_helper =  Mage::helper('email/event');
    }

    /**
     * @test
     * @cover getEventModel
     *
     * @dataProvider getEventModelProvider
     */
    public function getEventModelText($eventClass, $eventCode)
    {
        $this->assertEquals($eventClass, get_class($this->_helper->getEventModel($eventCode)));
    }

    public function getEventModelProvider()
    {
        return array(
            array('Mirasvit_Email_Model_Event_Cart_Abandoned', 'cart_abandoned'),
            array('Mirasvit_Email_Model_Event_Customer_New', 'customer_new'),
            array('Mirasvit_Email_Model_Event_Order_Status', 'order_status|pending'),
        );
    }

    /**
     * @test
     * @cover getEventCodes
     */
    public function getEventCodesText()
    {
        $this->assertEquals(19, count($this->_helper->getEventCodes()));

        foreach ($this->_helper->getEventCodes() as $eventCode) {
            $this->assertTrue($this->_helper->getEventModel($eventCode) !== false);
        }

        foreach ($this->_helper->getEventCodes() as $eventCode) {
            $this->assertFalse($this->_helper->getEventModel('bad'.$eventCode));
        }
    }
}