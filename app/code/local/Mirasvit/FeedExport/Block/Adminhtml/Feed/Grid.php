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


class Mirasvit_FeedExport_Block_Adminhtml_Feed_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('feedexport_feed_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('feedexport/feed')->getCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('feed_id', array(
            'header'    => Mage::helper('feedexport')->__('ID'),
            'align'     => 'right',
            'width'     => '50px',
            'index'     => 'feed_id',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('feedexport')->__('Name'),
            'align'     => 'left',
            'index'     => 'name',
        ));

        $this->addColumn('feed_type', array(
            'header'  => Mage::helper('feedexport')->__('Type'),
            'align'   => 'left',
            'index'   => 'type',
            'type'    => 'options',
            'options' => array(
                'csv' => Mage::helper('feedexport')->__('CSV'),
                'txt' => Mage::helper('feedexport')->__('TXT'),
                'xml' => Mage::helper('feedexport')->__('XML'),
            ),
        ));

        $this->addColumn('file', array(
            'header'   => Mage::helper('feedexport')->__('File'),
            'align'    => 'left',
            'index'    => 'name',
            'renderer' => 'Mirasvit_FeedExport_Block_Adminhtml_Feed_Renderer_Link'
        ));

        $this->addColumn('last_generated', array(
            'header'  => Mage::helper('feedexport')->__('Last Generated At'),
            'align'   => 'left',
            'type'    => 'datetime',
            'index'   => 'generated_at',
        ));

        $this->addColumn('feed_status', array(
            'header'   => Mage::helper('feedexport')->__('Status'),
            'align'    => 'left',
            'filter'   => false,
            'sortable' => false,
            'width'    => '150px',
            'renderer' => 'Mirasvit_FeedExport_Block_Adminhtml_Feed_Renderer_Status'
        ));

        $this->addColumn('action',
            array(
                'header'  => Mage::helper('feedexport')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('feedexport')->__('Edit'),
                        'url'     => array('base' => '*/*/edit'),
                        'field'   => 'id'
                    ),
                    array(
                        'caption' => Mage::helper('feedexport')->__('Duplicate'),
                        'url'     => array('base' => '*/*/duplicate'),
                        'field'   => 'id'
                    ),
                    array(
                        'caption' => Mage::helper('feedexport')->__('Remove'),
                        'url'     => array('base' => '*/*/delete'),
                        'field'   => 'id',
                        'confirm' => Mage::helper('feedexport')->__('Are you sure?')
                    ),
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true,
            )
        );


        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}