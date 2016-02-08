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



class Mirasvit_SearchSphinx_Test_Helper_InflectPtTest extends EcomDev_PHPUnit_Test_Case
{
    protected $_helper;

    protected function mockConfigMethod($methods)
    {
    }

    protected function setUp()
    {
        $this->_helper = Mage::helper('searchsphinx/inflectPt');
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
            array('boataria', 'boat'),
            array('boate', 'boat'),
            array('boates', 'boat'),
            array('boatos', 'boat'),
            array('bobinho', 'bobinh'),
            array('bobinhos', 'bobinh'),
            array('quimioterápicos', 'quimioteráp'),
        );
    }
}
