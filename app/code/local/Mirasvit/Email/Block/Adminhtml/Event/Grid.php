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
 * @package   Follow Up Email
 * @version   1.0.2
 * @build     564
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Email_Block_Adminhtml_Event_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('email_event_grid');
        $this->setDefaultSort('event_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('email/event')->getCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('event_id', array(
            'header' => __('ID'),
            'align'  => 'right',
            'width'  => '50px',
            'index'  => 'event_id',
        ));

        $this->addColumn('uniq_key', array(
            'header'    => __('Event Unique Key'),
            'align'     => 'left',
            'index'     => 'uniq_key',
        ));

        $this->addColumn('code', array(
            'header'    => __('Event Code'),
            'align'     => 'left',
            'index'     => 'code',
        ));

        $this->addColumn('created_at', array(
            'header'    => __('Created At'),
            'align'     => 'left',
            'index'     => 'created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('updated_at', array(
            'header'    => __('Updated At'),
            'align'     => 'left',
            'index'     => 'updated_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('args_serialized', array(
            'header'    => __('Arguments'),
            'align'     => 'left',
            'index'     => 'args_serialized',
            'renderer'  => 'Mirasvit_Email_Block_Adminhtml_Event_Grid_Renderer_Args',
        ));

        $this->addColumn('triggers', array(
            'header'   => __('Triggers'),
            'align'    => 'left',
            'index'    => 'triggers',
            'renderer' => 'Mirasvit_Email_Block_Adminhtml_Event_Grid_Renderer_Triggers',
            'filter'   => false,
            'sortable' => false,
        ));

        $this->addColumn('action',
            array(
                'header'  => Mage::helper('email')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('email')->__('Reset & Process'),
                        'url'     => array('base' => '*/*/reset'),
                        'field'   => 'id',
                    ),
                    array(
                        'caption' => Mage::helper('email')->__('Remove'),
                        'url'     => array('base' => '*/*/remove'),
                        'field'   => 'id'
                    )
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
        return false;
    }
}