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



class Mirasvit_Advr_Block_Adminhtml_Order_Country extends Mirasvit_Advr_Block_Adminhtml_Order_Abstract
{
    public function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setHeaderText(Mage::helper('advr')->__('Sales By Country'));

        return $this;
    }

    protected function prepareChart()
    {
        $this->setChartType('geo');

        $this->initChart()
            ->resetColumns()
            ->addColumn('Country', 'country_id')
            ->addColumn('Grand Total', 'sum_grand_total', 'number');

        return $this;
    }

    protected function prepareGrid()
    {
        $this->initGrid()
            ->setDefaultSort('sum_grand_total')
            ->setDefaultDir('desc')
            ->setDefaultLimit(200)
            ->setPagerVisibility(false);

        return $this;
    }

    public function _prepareCollection()
    {
        $collection = Mage::getModel('advr/report_sales')
            ->setBaseTable('sales/order')
            ->setFilterData($this->getFilterData())
            ->selectColumns($this->getVisibleColumns())
            ->groupByColumn('country_id');

        return $collection;
    }

    public function getColumns()
    {
        $columns = array(
            'country_id' => array(
                'header'         => 'Country',
                'type'           => 'text',
                'frame_callback' => array(Mage::helper('advr/callback'), 'country'),
                'totals_label'   => 'Total',
                'filter'         => false,
            ),
        );

        $columns += $this->getOrderTableColumns(true);

        return $columns;
    }
}
