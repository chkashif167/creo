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



class Mirasvit_SearchLandingPage_Block_Adminhtml_Page_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('searchLandingPageGrid');
        $this->setDefaultSort('page_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);

        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('searchlandingpage/page')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('page_id', array(
            'header' => Mage::helper('searchlandingpage')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'page_id',
        ));

        $this->addColumn('query_text', array(
            'header' => Mage::helper('searchlandingpage')->__('Search Phase'),
            'align' => 'left',
            'index' => 'query_text',
        ));

        $this->addColumn('url_key', array(
            'header' => Mage::helper('searchlandingpage')->__('Landing Url'),
            'align' => 'left',
            'index' => 'url_key',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
