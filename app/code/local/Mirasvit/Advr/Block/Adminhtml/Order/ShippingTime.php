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



class Mirasvit_Advr_Block_Adminhtml_Order_ShippingTime extends Mirasvit_Advr_Block_Adminhtml_Order_Abstract
{
    public function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setHeaderText(Mage::helper('advr')->__('Average Shipping Time'));

        return $this;
    }

    protected function prepareChart()
    {
        return $this;
    }

    protected function prepareGrid()
    {
        $this->initGrid()
            ->setDefaultSort('period')
            ->setDefaultDir('asc')
            ->setDefaultLimit(100000)
            ->setPagerVisibility(false)
            ->setFilterVisibility(false);

        return $this;
    }

    protected function prepareToolbar()
    {
        $this->initToolbar()
            ->setRangesVisibility(true);

        return $this;
    }

    public function _prepareCollection()
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
            'period'                       => array(
                'header'         => 'Period',
                'type'           => 'text',
                'index'          => 'period',
                'frame_callback' => array(Mage::helper('advr/callback'), 'period'),
                'totals_label'   => 'Total',
                'grouped'        => true,
                'filter'         => false,
            ),

            'avg_shipping_time'            => array(
                'header'         => 'Average Shipping Time',
                'type'           => 'number',
                'totals_label'   => '',
                'frame_callback' => array(Mage::helper('advr/callback'), 'time'),
            ),

            'quantity_shipping_time_0_1'   => array(
                'header'         => 'Number of orders (< 1 hour)',
                'type'           => 'number',
                'frame_callback' => array(Mage::helper('advr/callback'), 'percentOf'),
                'percent_of'     => 'quantity'
            ),

            'quantity_shipping_time_1_24'  => array(
                'header'         => '1 - 24 hours',
                'type'           => 'number',
                'frame_callback' => array(Mage::helper('advr/callback'), 'percentOf'),
                'percent_of'     => 'quantity'
            ),

            'quantity_shipping_time_24_48' => array(
                'header'         => '24 - 48 hours',
                'type'           => 'number',
                'frame_callback' => array(Mage::helper('advr/callback'), 'percentOf'),
                'percent_of'     => 'quantity'
            ),

            'quantity_shipping_time_48_72' => array(
                'header'         => '48 - 72 hours',
                'type'           => 'number',
                'frame_callback' => array(Mage::helper('advr/callback'), 'percentOf'),
                'percent_of'     => 'quantity'
            ),

            'quantity_shipping_time_72_'   => array(
                'header'         => '> 72 hours',
                'type'           => 'number',
                'frame_callback' => array(Mage::helper('advr/callback'), 'percentOf'),
                'percent_of'     => 'quantity'
            ),

            'quantity'                     => array(
                'header' => 'Number of Orders',
                'type'   => 'number',
                'index'  => 'quantity',
            ),
        );

        return $columns;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function percent($value, $row, $column)
    {
        $a = $row->getData('is_new');
        $b = $row->getData('is_returning');

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
