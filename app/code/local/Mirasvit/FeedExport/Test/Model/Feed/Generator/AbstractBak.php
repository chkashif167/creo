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


class Mirasvit_FeedExport_Test_Model_Feed_Generator_AbstractTest extends EcomDev_PHPUnit_Test_Case
{
    protected function setUp()
    {
        return parent::setUp();
    }

    protected function _getMock($overwrite = array())
    {
        $overwrite[] = 'prepareValue';
        $overwrite[] = 'setFormat';
        return $this->getMock('Mirasvit_FeedExport_Model_Feed_Generator_Abstract', $overwrite);
    }

    /**
     * @test
     * @cover _processEntity
     *
     * @loadFixture feeds
     *
     * @dataProvider _processEntityProvider
     */
    public function _processEntityTest($collectionSize, $expected)
    {
        $collection = new Varien_Data_Collection();
        for ($i = 1; $i <= $collectionSize; $i++) {
            $obj = $this->getMock('Varien_Object', array('load'));
            $obj->expects($this->any())
                 ->method('load')
                 ->will($this->returnValue($obj));
            $obj->setId($i);
            $collection->addItem($obj);
        }

        $mock = $this->_getMock(array('getCollection'));
        $mock->expects($this->any())
             ->method('getCollection')
             ->will($this->returnValue($collection));


        $feed = Mage::getModel('feedexport/feed')->load(1);
        $feed->getState()->reset();
        $mock->setFeed($feed)
            ->init();

        $result = $content = '';
        while ($content !== false) {
            $content = $mock->_processEntity('product', '{id}.');
            $result .= $content;
        }

        $this->assertEquals($expected, $result);
        $this->assertEquals($collectionSize, $feed->getState()->getIdx());
    }

    public function _processEntityProvider()
    {
        return array(
            array(0, ''),
            array(1, '1.'),
            array(5, '1.2.3.4.5.'),
        );
    }
}