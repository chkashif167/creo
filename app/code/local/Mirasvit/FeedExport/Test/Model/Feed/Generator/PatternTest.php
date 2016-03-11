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


class Mirasvit_FeedExport_Test_Model_Feed__Generator_PatternTest extends EcomDev_PHPUnit_Test_Case
{
    protected $_model;

    protected function setUp()
    {
        $this->_model =  Mage::getModel('feedexport/feed_generator_pattern');
        $this->_feed  =  Mage::getModel('feedexport/feed')->load(2);
        $this->_model->setFeed($this->_feed);
        $this->_model->setStore($this->_feed->getStore());
    }

    /**
     * Replace current domain to example.com
     * @param string $str
     *
     * @return string
     */
    protected function _replaceDomain($str)
    {
        $url = Mage::getBaseUrl();
        $url = str_replace('/index.php', '', $url);

        $str = str_replace('/index.php', '', $str);

        return str_replace($url, 'http://example.com/', $str);
    }

    /**
     * @test
     * @cover parsePatternTest
     *
     * @dataProvider parsePatternProvider
     */
    public function parsePatternTest($pattern, $value)
    {
        $result = $this->_model->parsePattern($pattern);
        $this->assertEquals($value, $result);
    }

    public function parsePatternProvider()
    {
        return array(
            array(
                '{url|parent}',
                array(
                    'key'        => 'url',
                    'type'       => 'parent',
                    'formatters' => array(),
                    'additional' => '',
                ),
            ),
            array(
                '{image:100x150}',
                array(
                    'key'        => 'image',
                    'type'       => '',
                    'formatters' => array(),
                    'additional' => '100x150',
                ),
            ),
            array(
                '{(return $price * 10 / 12;), [number_format 2]}',
                array(
                    'key'        => '(return $price * 10 / 12;)',
                    'type'       => '',
                    'formatters' => array(
                        'number_format 2'
                    ),
                    'additional' => '',
                )
            ),
        );
    }

    /**
     * @test
     * @cover getPatternValue
     *
     * @dataProvider getPatternValueProvider
     *
     * @loadFixture products
     * @loadFixture categories
     * @loadFixture feeds
     * @doNotIndex catalog_product_price
     */
    public function getPatternValueTest($expected, $content, $scope, $objId)
    {
        $obj = null;
        if ($objId != null) {
            $obj = Mage::getModel('catalog/'.$scope)->load($objId);
        }

        $result = $this->_model->getPatternValue($content, $scope, $obj);

        $result = $this->_replaceDomain($result);

        $this->assertEquals($expected, $result);
    }

    public function getPatternValueProvider()
    {
        return array(
            array(
                25,
                '{php, [5 * 5]}',
                null,
                null
            ),
            array(
                'Samsung Gallaxy Phone SMG-GLX-6798',
                "{name}",
                'product',
                2,
            ),
            array(
                'USA Store Category',
                "{name}",
                'category',
                2,
            ),
        );
    }
}