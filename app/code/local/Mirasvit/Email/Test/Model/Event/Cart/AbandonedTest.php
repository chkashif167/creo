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


class Mirasvit_Email_Test_Model_Event_Cart_AbandonedTest extends EcomDev_PHPUnit_Test_Case
{
    protected $_model;

    protected function setUp()
    {
        $this->_model =  Mage::getModel('email/event_cart_abandoned');
    }

    /**
     * @test
     * @cover findEvents
     *
     * @loadFixture quote
     *
     * @doNotIndex catalog_product_price
     */
    public function findEventsTest()
    {
        $expected = array(
            array(
                'time'           => 1380586200,
                'customer_email' => 'bob@example.com',
                'customer_name'  => 'Bob Smith',
                'customer_id'    => 0,
                'store_id'       => 2,
                'quote_id'       => 2,
            ),
            array(
                'time'           => 1380669000,
                'customer_email' => 'john@example.com',
                'customer_name'  => 'John Barham',
                'customer_id'    => 0,
                'store_id'       => 2,
                'quote_id'       => 1,
            ),
        );
        $time = strtotime('2013-10-02 00:00:00');
        $events = $this->_model->findEvents('cart_abandoned', $time);

        $this->assertEquals($expected, $events);
    }
}