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


class Mirasvit_Email_Block_Adminhtml_Trigger_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('email_trigger_grid');
        $this->setDefaultSort('trigger_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('email/trigger')->getCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('trigger_id', array(
            'header'    => Mage::helper('email')->__('ID'),
            'align'     => 'right',
            'width'     => '50px',
            'index'     => 'trigger_id',
        ));

        $this->addColumn('title', array(
            'header'    => Mage::helper('email')->__('Title'),
            'align'     => 'left',
            'index'     => 'title',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_ids', array(
                'header'     => Mage::helper('email')->__('Store'),
                'index'      => 'store_ids',
                'type'       => 'store',
                'store_all'  => true,
                'store_view' => true,
                'sortable'   => false,
                'filter_condition_callback' => array($this, '_filterStoreCondition'),
            ));
        }

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('email')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                1 => Mage::helper('email')->__('Enabled'),
                0 => Mage::helper('email')->__('Disabled'),
            ),
        ));

        // $this->addColumn('action',
        //     array(
        //         'header'  => Mage::helper('email')->__('Action'),
        //         'width'   => '100',
        //         'type'    => 'action',
        //         'getter'  => 'getId',
        //         'actions' => array(
        //             array(
        //                 'caption' => Mage::helper('email')->__('Edit'),
        //                 'url'     => array('base' => '*/*/edit'),
        //                 'field'   => 'id'
        //             ),
        //             array(
        //                 'caption' => Mage::helper('email')->__('Remove'),
        //                 'url'     => array('base' => '*/*/delete'),
        //                 'field'   => 'id'
        //             ),
        //         ),
        //         'filter'    => false,
        //         'sortable'  => false,
        //         'is_system' => true,
        //     )
        // );


        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('trigger_id');
        $this->getMassactionBlock()->setFormFieldName('trigger_id');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'    => Mage::helper('email')->__('Delete'),
            'url'      => $this->getUrl('*/*/massDelete'),
            'confirm'  => Mage::helper('email')->__('Are you sure?')
        ));

        $statuses = array(
            '1' => Mage::helper('email')->__('Enabled'),
            '0' => Mage::helper('email')->__('Disabled')
        );
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('email')->__('Change status'),
            'url'   => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name'   => 'status',
                    'type'   => 'select',
                    'class'  => 'required-entry',
                    'label'  => Mage::helper('email')->__('Status'),
                    'values' => $statuses
                )
            )
        ));

        $this->getMassactionBlock()->addItem('send_test', array(
            'label' => Mage::helper('email')->__('Send test email'),
            'url'   => $this->getUrl('*/*/massSend', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name'   => 'email',
                    'type'   => 'text',
                    'class'  => 'required-entry',
                    'style'  => 'width: 200px',
                    'label'  => Mage::helper('email')->__('Email'),
                    'value'  => Mage::getSingleton('email/config')->getTestEmail(),
                )
            )
        ));


        return $this;
    }

    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $collection->getSelect()->where('find_in_set(?, store_ids)', $value);
    }
}