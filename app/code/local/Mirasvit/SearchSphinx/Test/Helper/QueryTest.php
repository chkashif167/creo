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



class Mirasvit_SearchSphinx_Test_Helper_QueryTest extends EcomDev_PHPUnit_Test_Case
{
    protected $_helper;

    protected function mockConfigMethod($methods)
    {
        $config = $this->getModelMock('searchsphinx/config', array_keys($methods));
        foreach ($methods as $method => $value) {
            $config->expects($this->any())
                ->method($method)
                ->will($this->returnValue($value));
        }
        $this->replaceByMock('singleton', 'searchsphinx/config', $config);
    }

    protected function setUp()
    {
        $this->_helper = Mage::helper('searchsphinx/query');
    }

    /**
     * @test
     * @cover buildQuery
     *
     * @loadFixture synonyms
     *
     * @dataProvider buildQuerySynonymsProvider
     */
    public function buildQuerySynonymsTest($phase, $expected)
    {
        $this->mockConfigMethod(array('isAllowedWildcard' => true));

        $result = $this->_helper->buildQuery($phase, 2);
        $this->assertEquals($expected, $result);
    }

    public function buildQuerySynonymsProvider()
    {
        return array(
            array(
                'British',
                array(
                    'like' => array(
                        'and' => array(
                            'british' => array(
                                'or' => array(
                                    'british' => 'british',
                                    'england' => 'england',
                                    'gb' => 'gb',
                                    'uk' => 'uk',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'UK',
                array(
                    'like' => array(
                        'and' => array(
                            'uk' => array(
                                'or' => array(
                                    'british' => 'british',
                                    'england' => 'england',
                                    'gb' => 'gb',
                                    'uk' => 'uk',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );
    }

    /**
     * @test
     * @cover buildQuery
     *
     * @loadFixture stopwords
     *
     * @dataProvider buildQueryStopwordsProvider
     */
    public function buildQueryStopwordsTest($phase, $expected)
    {
        $this->mockConfigMethod(array('isAllowedWildcard' => true));

        $result = $this->_helper->buildQuery($phase, 2);
        $this->assertEquals($expected, $result);
    }

    public function buildQueryStopwordsProvider()
    {
        return array(
            array(
                'The British',
                array(
                    'like' => array(
                        'and' => array(
                            'british' => array(
                                'or' => array(
                                    'british' => 'british',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'UK and London',
                array(
                    'like' => array(
                        'and' => array(
                            'uk' => array(
                                'or' => array(
                                    'uk' => 'uk',
                                ),
                            ),
                            'london' => array(
                                'or' => array(
                                    'london' => 'london',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );
    }

    /**
     * @test
     * @cover buildQuery
     *
     * @dataProvider buildQueryWildcardsProvider
     */
    public function buildQueryWildcardsTest($phase, $expected, $allow)
    {
        $this->mockConfigMethod(array('isAllowedWildcard' => $allow));

        $result = $this->_helper->buildQuery($phase, 2);
        $this->assertEquals($expected, $result);
    }

    public function buildQueryWildcardsProvider()
    {
        return array(
            array(
                'British',
                array(
                    'like' => array(
                        'and' => array(
                            'british' => array(
                                'or' => array(
                                    'british' => 'british',
                                ),
                            ),
                        ),
                    ),
                ),
                true,
            ),
            array(
                'UK London',
                array(
                    'like' => array(
                        'and' => array(
                            'uk' => array(
                                'or' => array(
                                    ' uk ' => ' uk ',
                                ),
                            ),
                            'london' => array(
                                'or' => array(
                                    ' london ' => ' london ',
                                ),
                            ),
                        ),
                    ),
                ),
                false,
            ),
        );
    }

    /**
     * @test
     * @cover buildQuery
     */
    public function buildQueryWildcardExceptionsTest()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @test
     * @cover buildQuery
     */
    public function buildQueryNotWordsTest()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @test
     * @cover buildQuery
     */
    public function buildQueryReplaceWordsTest()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    // /**
    // * @test
    // * @loadFixture stopword
    // * @loadFixture synonym
    // */
    // public function buildQueryNotwords()
    // {
    //     $this->mockConfigMethod(array('isAllowedWildcard' => false, 'getNotwords' => array('not')));

    //     $expectedResult = array(
    //         'like' => array(
    //             'and' => array(
    //                 'british' => array(
    //                     'or' => array(
    //                         ' british ' => ' british ',
    //                         ' england ' => ' england ',
    //                         ' uk '      => ' uk '
    //                     )
    //                 ),

    //             ),
    //         ),
    //         'not like' => array(
    //             'and' => array(
    //                 'australia' => array(
    //                     'and' => array(
    //                         ' australia ' => ' australia '
    //                     ),
    //                 ),
    //             )
    //         ),
    //     );

    //     $result = $this->_helper->buildQuery('British not Australia', 2);
    //     $this->assertEquals($expectedResult, $result);
    // }

    // /**
    //  * @test
    //  * @loadFixture stopword
    //  */
    // public function isStopwordTest()
    // {
    //     $this->assertEquals(1, $this->_helper->isStopword('or', 2));
    //     $this->assertEquals(1, $this->_helper->isStopword('and', 2));
    //     $this->assertEquals(0, $this->_helper->isStopword('o', 2));
    // }
}
