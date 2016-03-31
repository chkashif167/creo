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


class Mirasvit_FeedExport_Test_Model_Feed_Generator_Pattern_ProductTest extends EcomDev_PHPUnit_Test_Case
{
    protected $_model;

    protected function setUp()
    {
        $this->_model =  Mage::getModel('feedexport/feed_generator_pattern_product');

        return parent::setUp();
    }

    /**
     * Replace current domain to example.com
     * @param string $str
     *
     * @return string
     */
    protected function _prepareString($str)
    {
        $url = Mage::getBaseUrl();
        $url = str_replace('/index.php', '', $url);

        $str = str_replace('/index.php', '', $str);

        return str_replace($url, 'http://example.com/', $str);
    }

    /**
     * @test
     * @cover getPatternValue
     *
     * @dataProvider getPatternValueProvider
     *
     * @loadFixture products
     * @doNotIndex catalog_product_price
     */
    public function getPatternValueTest($pattern, $expected)
    {
        Mage::helper('feedexport')->setCurrentStore(Mage::getModel('core/store')->load(2));

        $product = Mage::getModel('catalog/product')->load(1);
        $result  = $this->_model->getValue($pattern, $product);
        $result  = $this->_prepareString($result);
        $this->assertEquals($expected, $result);
        Mage::helper('feedexport')->resetCurrentStore();
    }
    public function getPatternValue2Provider()
    {
    }

    public function getPatternValueProvider()
    {
        return array(
            // eval
            array('{(return $price * 1.05;)}', 12.99 * 1.05),
            array('{(return $price / 6;), [number_format 2]}', number_format(12.99 / 6, 2)),
            array('{(return $name.$sku;)}', 'Canon Digital Cameracanon-digital-camera'),

            array('{url}', 'http://example.com/catalog/product/view/id/1/s/canon-digital-camera/'),

            array('{image}', 'http://example.com/media/catalog/product/image.jpg'),
            array('{thumbnail}', ''),
            array('{small_image}', 'http://example.com/media/catalog/product/small_image.jpg'),

            array('{image2}', ''),
            array('{image3}', ''),
            array('{image4}', ''),
            array('{image5}', ''),

            array('{qty}', 100),
            array('{is_in_stock}', 1),

            // categories
            array('{category_id}', 4),
            array('{category}', 'Cameras'),
            array('{category_url}', 'http://example.com/catalog/category/view/s/cameras/id/4/'),
            array('{category_path}', 'Electronic > Cameras'),

            // prices
            array('{price}', 12.99),
            array('{final_price}', 12.99),
            // array('{store_price}', 12.99),
            array('{base_price}', 12.99),
            array('{tier_price}', null),

            // attributes
            array('{attribute_set_id}', 4),
            array('{attribute_set}', 'Default'),

            array('{status}', 'Enabled'),
            array('{visibility}', 'Catalog, Search'),
            array('{sku}', 'canon-digital-camera'),
            array('{name}', 'Canon Digital Camera'),
            array('{name, [substr 0 8], [...]}', 'Canon Di...'),
        );
    }
}