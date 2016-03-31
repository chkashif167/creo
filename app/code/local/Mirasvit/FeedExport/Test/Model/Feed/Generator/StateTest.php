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
 * @package   Advanced Product Feeds
 * @version   1.1.4
 * @build     702
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_FeedExport_Test_Model_Feed_Generator_StateTest extends EcomDev_PHPUnit_Test_Case
{
    protected function setUp()
    {
        return parent::setUp();
    }

    /**
     * @test
     * @cover save
     */
    public function saveTest()
    {
        $key = md5(microtime());

        $saveState = Mage::getModel('feedexport/feed_generator_state')
            ->setKey($key)
            ->setAction('product')
            ->setStatus('ready');

        $loadState = Mage::getModel('feedexport/feed_generator_state')
            ->setKey($key);

        $this->assertEquals($loadState->toArray(), $saveState->toArray());
    }

    /**
     * @test
     * @cover save
     */
    public function loadTest()
    {
        $key = md5(microtime());

        $saveState = Mage::getModel('feedexport/feed_generator_state')
            ->setKey($key)
            ->setAction('product')
            ->setStatus('ready');

        $loadState = Mage::getModel('feedexport/feed_generator_state')
            ->setKey($key)
            ->load();

        $this->assertEquals($saveState, $loadState);
    }

    /**
     * @test
     * @cover getData
     */
    public function getDataTest()
    {
        $key = md5(microtime());

        $stateA = Mage::getModel('feedexport/feed_generator_state')
            ->setKey($key);

        $stateB = Mage::getModel('feedexport/feed_generator_state')
            ->setKey($key)
            ->setStatus('ready');

        $this->assertEquals('ready', $stateA->getStatus());
    }
}