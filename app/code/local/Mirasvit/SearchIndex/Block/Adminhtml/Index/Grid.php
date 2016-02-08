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



class Mirasvit_SearchIndex_Block_Adminhtml_Index_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('searcindexgrid');
        $this->setDefaultSort('index_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);

        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('searchindex/index')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('title', array(
            'header' => Mage::helper('searchindex')->__('Title'),
            'align' => 'left',
            'index' => 'title',
            'frame_callback' => array($this, '_renderTitle'),
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('searchindex')->__('State'),
            'align' => 'left',
            'index' => 'status',
            'width' => '150px',
            'renderer' => 'Mirasvit_SearchIndex_Block_Adminhtml_Index_Renderer_State',
            'filter' => false,
        ));

        $this->addColumn('action',
            array(
                'header' => Mage::helper('searchindex')->__('Action'),
                'width' => '100',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('searchindex')->__('Edit'),
                        'url' => array('base' => '*/*/edit'),
                        'field' => 'id',
                    ),
                    array(
                        'caption' => Mage::helper('searchindex')->__('Run Reindex'),
                        'url' => array('base' => '*/*/reindex'),
                        'field' => 'id',
                    ),
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
            )
        );

        return parent::_prepareColumns();
    }

    public function _renderTitle($renderedValue, $item, $column, $isExport)
    {
        return $item->getTitle();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('index_id');
        $this->getMassactionBlock()->setFormFieldName('index_id');

        $this->getMassactionBlock()->addItem('reindex', array(
            'label' => Mage::helper('searchindex')->__('Run Reindex'),
            'url' => $this->getUrl('*/*/massReindex'),
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
