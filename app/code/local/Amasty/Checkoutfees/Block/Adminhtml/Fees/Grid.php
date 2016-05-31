<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */
class Amasty_Checkoutfees_Block_Adminhtml_Fees_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('feesGrid');
        $this->setDefaultSort('fees_id');
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('amcheckoutfees/fees')->getCollection();
        /*
         * just do something here with collection
         * apply filters \ sorting or etc
        */

        $this->setDefaultSort('sort');
        $this->setDefaultDir('asc');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $hlp = Mage::helper('amcheckoutfees');
        $this->addColumn('fees_id', array(
                'header' => $hlp->__('ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index' => 'fees_id',
            )
        );

        $this->addColumn('name', array(
                'header' => $hlp->__('Fee Name'),
                'index'  => 'name',
                'width' => '200px',
            )
        );
        $this->addColumn('position_cart', array(
                'header'  => $hlp->__('Display on cart'),
                'index'   => 'position_cart',
                'align'   => 'center',
                'width'   => '80px',
                'type'    => 'options',
                'options' => array(
                    '0' => $this->__('No'),
                    '1' => $this->__('Yes'),
                ),
            )
        );
        $this->addColumn('position_checkout', array(
                'header'  => $hlp->__('Display on checkout'),
                'index'   => 'position_checkout',
                'align'   => 'center',
                'width'   => '80px',
                'type'    => 'options',
                'options' => array(
                    '0' => $this->__('No'),
                    '1' => $this->__('Shipping'),
                    '2' => $this->__('Payment'),
                ),
            )
        );

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('stores', array(
                    'header'   => Mage::helper('cms')->__('Store View'),
                    'index'    => 'stores',
                    'type'     => 'stores',
                    'sortable' => false,
                    'renderer' => 'amcheckoutfees/adminhtml_fees_grid_renderer_stores',
                    'filter'   => 'amcheckoutfees/adminhtml_fees_grid_filter_stores',
                    'width'    => '200px',
                )
            );
        }

        $this->addColumn('cust_groups', array(
                'header'   => $hlp->__('Affected Customer Groups'),
                'align'    => 'center',
                'index'    => 'cust_groups',
                'type'     => 'options',
                'options'  => Mage::getResourceModel('customer/group_collection')->load()->toOptionHash(),
                'renderer' => 'amcheckoutfees/adminhtml_fees_grid_renderer_custGroups',
                'filter'   => 'amcheckoutfees/adminhtml_fees_grid_filter_custGroups',
                'width'    => '200px',
            )
        );

        $this->addColumn('sort', array(
                'header' => $hlp->__('Sort'),
                'index'  => 'sort',
                'width'  => '40px',
            )
        );
        $this->addColumn('enabled', array(
                'header'  => $hlp->__('Enabled'),
                'align'   => 'center',
                'width'   => '80px',
                'index'   => 'enabled',
                'type'    => 'options',
                'options' => array(
                    '0' => $this->__('No'),
                    '1' => $this->__('Yes'),
                ),
            )
        );

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('fees_id');
        $this->getMassactionBlock()->setFormFieldName('ids');

        $actions = array(
            'massEnable'  => 'Enable',
            'massDisable' => 'Disable',
            'massDelete'  => 'Delete',
        );
        foreach ($actions as $code => $label) {
            $this->getMassactionBlock()->addItem($code, array(
                    'label'   => Mage::helper('amcheckoutfees')->__($label),
                    'url'     => $this->getUrl('*/*/' . $code),
                    'confirm' => ($code == 'massDelete' ? Mage::helper('amcheckoutfees')->__('Are you sure?') : null),
                )
            );
        }

        return $this;
    }
}