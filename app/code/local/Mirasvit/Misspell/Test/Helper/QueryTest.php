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
 * @package   Sphinx Search Ultimate
 * @version   2.3.2
 * @build     1290
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Misspell_Test_Helper_QueryTest extends EcomDev_PHPUnit_Test_Case
{
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = Mage::helper('misspell/query');
        $this->index = Mage::helper('searchindex/index')->getIndex('mage_catalog_product');
        $this->index->reindexAll();
    }

    /**
     * @test
     * @cover suggestFallbackPhase
     *
     * @loadFixture  products
     * @dataProvider suggestFallbackPhaseProvider
     *
     * @doNotIndex catalog_product_price
     */
    public function suggestFallbackPhaseTest($fallback, $phase)
    {
        $result = $this->_helper->suggestFallbackPhase($phase, 2);
        $this->assertEquals($fallback, $result);
    }

    public function suggestFallbackPhaseProvider()
    {
        return array(
            array('samsung phone', 'samsung digital phone'),
            array('samsung phone', 'samsung nokia canon phone'),
            array(false, 'nokia kodak'),
        );
    }
}
