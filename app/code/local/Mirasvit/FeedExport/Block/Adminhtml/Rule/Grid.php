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


class Mirasvit_FeedExport_Block_Adminhtml_Rule_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('feedexport_rule_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('feedexport/rule')->getCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('rule_id', array(
            'header' => __('ID'),
            'align'  => 'right',
            'width'  => '50px',
            'index'  => 'rule_id',
        ));

        $this->addColumn('name', array(
            'header' => __('Name'),
            'align'  => 'left',
            'index'  => 'name',
        ));

        $this->addColumn('type', array(
            'header'  => __('Type'),
            'align'   => 'left',
            'index'   => 'type',
            'type'    => 'options',
            'options' => Mage::getSingleton('feedexport/system_config_source_ruleType')->toOptions(),
        ));

        $this->addColumn('conditions', array(
            'header'   => __('Conditions'),
            'align'    => 'left',
            'filter'   => false,
            'sortable' => false,
            'renderer' => 'Mirasvit_FeedExport_Block_Adminhtml_Rule_Renderer_Conditions'
        ));

        $this->addColumn('is_active', array(
            'header'  => __('Status'),
            'align'   => 'left',
            'width'   => '80px',
            'index'   => 'is_active',
            'type'    => 'options',
            'options' => array(
                1 => __('Enabled'),
                0 => __('Disabled'),
            ),
        ));

        $this->addColumn('action',
            array(
                'header'  => __('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => __('Edit'),
                        'url'     => array('base' => '*/*/edit'),
                        'field'   => 'id'
                    ),
                     array(
                        'caption' => Mage::helper('feedexport')->__('Duplicate'),
                        'url'     => array('base' => '*/*/duplicate'),
                        'field'   => 'id'
                    ),
                    array(
                        'caption' => __('Delete'),
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