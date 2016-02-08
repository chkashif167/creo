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


class Mirasvit_Email_Test_Model_Event_AbstractTest extends EcomDev_PHPUnit_Test_Case
{
    protected $_model;

    protected function setUp()
    {
        $this->_model =  $this->getMockForAbstractClass('Mirasvit_Email_Model_Event_Abstract');
    }

    /**
     * @test
     * @cover check
     */
    public function checkTest()
    {
        foreach (Mage::helper('email/event')->getEventCodes() as $eventCode) {
            $eventModel = Mage::helper('email/event')->getEventModel($eventCode);
            $this->assertTrue($eventModel->check($eventCode));
        }
    }

    /**
     * @test
     * @cover getEventUniqKey
     *
     * @dataProvider getEventUniqKeyProvider
     */
    public function getEventUniqKeyTest($key, $args)
    {
        $this->assertEquals($key, $this->_model->getEventUniqKey($args));
    }

    public function getEventUniqKeyProvider()
    {
        return array(
            array('bob@example.com',
                array(
                    'customer_email' => 'bob@example.com',
                    'time'           => time(),
                )
            ),
            array('james@example.com_12_15',
                array(
                    'customer_email' => 'james@example.com',
                    'order_id'       => 15,
                    'quote_id'       => 12,
                    'time'           => time(),
                )
            ),
        );
    }
}