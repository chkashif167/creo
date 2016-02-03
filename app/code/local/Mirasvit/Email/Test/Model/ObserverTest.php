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


class Mirasvit_Email_Test_Model_ObserverTest extends EcomDev_PHPUnit_Test_Case
{
    protected $_model;

    protected function setUp()
    {
        $this->_model =  Mage::getSingleton('email/observer');
    }

    /**
     * @test
     * @cover checkEvents
     *
     * @loadFixture triggers
     */
    public function checkEventsTest()
    {
        $this->assertTrue($this->_model->checkEvents());
    }

    /**
     * @test
     * @cover getActiveEvents
     *
     * @loadFixture triggers
     */
    public function getActiveEventsTest()
    {
        $events = array(
            'cart_abandoned',
            'order_status|new',
            'customer_activity',
            'customer_loggedin'
        );

        $this->assertEquals($events, $this->_model->getActiveEvents());
    }
}