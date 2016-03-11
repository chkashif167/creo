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


class Mirasvit_FeedExport_Test_Model_Feed_Generator_Pattern_CategoryTest extends EcomDev_PHPUnit_Test_Case
{
    protected $_model;

    protected function setUp()
    {
        $this->_model =  Mage::getModel('feedexport/feed_generator_pattern_category');
        $this->_model->setStore(Mage::getModel('core/store')->load(2));

        return parent::setUp();
    }

    /**
     * @test
     * @cover getPatternValue
     *
     * @dataProvider getPatternValueProvider
     *
     * @loadFixture categories
     * @doNotIndex catalog_product_price
     */
    public function getPatternValueTest($pattern, $expected)
    {
        $obj = Mage::getModel('catalog/category')->load(4);
        $result  = $this->_model->getValue($pattern, $obj);

        $this->assertEquals($expected, $result);
    }

    public function getPatternValueProvider()
    {
        return array(
            array('{name}', 'USA Cameras'),
        );
    }
}