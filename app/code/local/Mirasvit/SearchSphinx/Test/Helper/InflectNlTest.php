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



class Mirasvit_SearchSphinx_Test_Helper_InflectNlTest extends EcomDev_PHPUnit_Test_Case
{
    protected $_helper;

    protected function mockConfigMethod($methods)
    {
    }

    protected function setUp()
    {
        $this->_helper = Mage::helper('searchsphinx/inflectNl');
    }

    /**
     * @test
     * @cover singularize
     *
     * @dataProvider singularizeProvider
     */
    public function singularizeTest($string, $expected)
    {
        $result = $this->_helper->singularize($string);
        $this->assertEquals($expected, $result);
    }

    public function singularizeProvider()
    {
        return array(
            array('lichere', 'licher'),
            array('koplampen', 'koplamp'),
            array('lichamelijke', 'licham'),
            array('lichamelijkheden', 'licham'),
            array('lichamen', 'licham'),
            array('lichtzinnige', 'lichtzinn'),
            array('ophalers', 'ophaler'),
            array('ophalend', 'ophal'),
        );
    }
}
