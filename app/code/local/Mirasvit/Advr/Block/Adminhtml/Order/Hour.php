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



class Mirasvit_Advr_Block_Adminhtml_Order_Hour extends Mirasvit_Advr_Block_Adminhtml_Order_Abstract
{
    public function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setHeaderText(Mage::helper('advr')->__('Sales By Hour'));

        return $this;
    }

    protected function prepareChart()
    {
        $this->setChartType('column');

        $this->initChart()
            ->setXAxisType('category')
            ->setXAxisField('hour_of_day');

        return $this;
    }

    protected function prepareGrid()
    {
        $this->initGrid()
            ->setDefaultSort('hour_of_day')
            ->setDefaultDir('asc')
            ->setDefaultLimit(24)
            ->setPagerVisibility(false);

        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('advr/report_sales')
            ->setBaseTable('sales/order')
            ->setFilterData($this->getFilterData())
            ->selectColumns($this->getVisibleColumns())
            ->groupByColumn('hour_of_day');

        return $collection;
    }

    public function getColumns()
    {
        $columns = array(
            'hour_of_day' => array(
                'header'              => 'Hour',
                'type'                => 'text',
                'frame_callback'      => array(Mage::helper('advr/callback'), 'hour'),
                'export_callback'     => array(Mage::helper('advr/callback'), 'hour'),
                'totals_label'        => 'Total',
                'filter_totals_label' => 'Subtotal',
                'filter'              => false,
            ),
        );

        $columns += $this->getOrderTableColumns(true);

        return $columns;
    }
}
