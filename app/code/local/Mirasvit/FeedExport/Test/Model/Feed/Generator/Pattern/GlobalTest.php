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


class Mirasvit_FeedExport_Test_Model_Feed_Generator_Pattern_GlobalTest extends EcomDev_PHPUnit_Test_Case
{
    protected $_model;

    protected function setUp()
    {
        $this->_model =  Mage::getModel('feedexport/feed_generator_pattern_global');

        return parent::setUp();
    }

    /**
     * @test
     * @cover getPatternValue
     *
     * @dataProvider getPatternValueProvider
     */
    public function getPatternValueTest($expected, $pattern)
    {
        $result = $this->_model->getPatternValue($pattern);
        $this->assertEquals($expected, $result);
    }

    public function getPatternValueProvider()
    {
        return array(
            array(
                'owner@example.com',
                '{base_email}',
            ),
            array(
                'http://magento1702.alx/',
                '{base_url}',
            ),
            array(
                date('d.m.Y'),
                "{php, [date('d.m.Y')]}",
            ),
        );
    }
}