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



class Mirasvit_Advr_Block_Adminhtml_Order_NewVsReturning extends Mirasvit_Advr_Block_Adminhtml_Order_Abstract
{

    public function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setHeaderText(Mage::helper('advr')->__('New vs Returning Customers'));

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
            ->setPagerVisibility(false)// ->setFilterVisibility(false)
        ;

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
            'period'                                 => array(
                'header'         => 'Period',
                'type'           => 'text',
                'index'          => 'period',
                'frame_callback' => array(Mage::helper('advr/callback'), 'period'),
                'totals_label'   => 'Total',
                'filter'         => false,
            ),

            'quantity_by_new_customers'              => array(
                'header' => 'Orders by new customers',
                'type'   => 'number',
                'chart'  => true,
            ),

            'sum_grand_total_by_new_customers'       => array(
                'header' => 'Grand Total by new customers',
                'type'   => 'currency',
            ),

            'percent_new'                            => array(
                'header'         => 'Percent of new',
                'type'           => 'percent',
                'index'          => 'quantity_by_new_customers',
                'frame_callback' => array($this, 'percent'),
            ),

            'quantity_by_returning_customers'        => array(
                'header' => 'Orders by returning customers',
                'type'   => 'number',
                'chart'  => true,
            ),

            'sum_grand_total_by_returning_customers' => array(
                'header' => 'Grand Total by returning customers',
                'type'   => 'currency',
            ),

            'percent_returning'                      => array(
                'header'         => 'Percent of returning',
                'type'           => 'percent',
                'index'          => 'quantity_by_returning_customers',
                'frame_callback' => array($this, 'percent'),
            ),
        );

        return $columns;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function percent($value, $row, $column)
    {
        $a = $row->getData('quantity_by_new_customers');
        $b = $row->getData('quantity_by_returning_customers');

        if ($b > 0) {
            $result = $a / ($a + $b);
        } else {
            $result = 1;
        }

        if ($b == $value) {
            $result = 1 - $result;
        }

        return sprintf("%.1f %%", $result * 100);
    }
}
