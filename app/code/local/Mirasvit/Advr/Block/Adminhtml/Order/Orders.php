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
 * @package   Advanced Reports
 * @version   1.0.1
 * @build     539
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Advr_Block_Adminhtml_Order_Orders extends Mirasvit_Advr_Block_Adminhtml_Order_Abstract
{
    public function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setHeaderText(Mage::helper('advr')->__('Sales'));

        return $this;
    }

    protected function prepareChart()
    {
        $this->setChartType('column');

        $this->initChart()
            ->setXAxisType('datetime')
            ->setXAxisField('period');

        return $this;
    }

    protected function prepareGrid()
    {
        $this->initGrid()
            ->setDefaultSort('period')
            ->setDefaultDir('asc')
            ->setDefaultLimit(100000)
            ->setPagerVisibility(false);

        return $this;
    }

    protected function prepareToolbar()
    {
        $this->initToolbar()
            ->setRangesVisibility(true);

        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('advr/report_sales')
            ->setBaseTable('sales/order')
            ->setFilterData($this->getFilterData())
            ->selectColumns($this->getVisibleColumns())
            ->groupByColumn('period');

        return $collection;
    }

    public function getColumns()
    {
        $columns = array(
            'period' => array(
                'header'              => 'Period',
                'type'                => 'text',
                'frame_callback'      => array(Mage::helper('advr/callback'), 'period'),
                'totals_label'        => 'Total',
                'filter_totals_label' => 'Subtotal',
                'filter'              => false,
            ),
        );

        $columns += $this->getOrderTableColumns(true);

        $columns['actions'] = array(
            'header'  => 'Actions',
            'actions' => array(
                array(
                    'caption'  => Mage::helper('advr')->__('View Orders'),
                    'callback' => array($this, 'rowUrlCallback')
                ),
            ),
        );

        return $columns;
    }

    public function rowUrlCallback($row)
    {
        $row->setRange($this->getFilterData()->getRange());

        return Mage::helper('advr/callback')->rowUrl('*/*/plain', $row, array('period'));
    }
}
