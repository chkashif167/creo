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


class Mirasvit_FeedExport_Test_Helper_RequestTest extends EcomDev_PHPUnit_Test_Case
{
    protected $_helper;

    protected function setUp()
    {
        $this->_helper =  Mage::helper('feedexport/request');

        return parent::setUp();
    }

    /**
     * @test
     * @cover getPhpBin
     */
    public function getPhpBinTest()
    {
        $this->assertEquals($this->_helper->getPhpBin(), '/usr/bin/php');
    }

    /**
     * @test
     * @cover pingShell
     */
    public function pingShellTest()
    {
        $this->assertEquals($this->_helper->pingShell(), true);
    }

    /**
     * @test
     * @cover exec
     */
    public function execTest()
    {
        $this->assertEquals($this->_helper->exec('echo 1'), '1');
    }

    /**
     * @test
     * @cover request
     */
    public function requestTest()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }
}