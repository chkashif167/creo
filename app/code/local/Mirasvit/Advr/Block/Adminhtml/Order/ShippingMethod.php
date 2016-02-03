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



class Mirasvit_Advr_Block_Adminhtml_Order_ShippingMethod extends Mirasvit_Advr_Block_Adminhtml_Order_Abstract
{
    public function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setHeaderText(Mage::helper('advr')->__('Sales By Shipping Method'));

        return $this;
    }

    protected function prepareChart()
    {
        $this->setChartType('pie');

        $this->initChart()
            ->setNameField('shipping_method')
            ->setValueField('sum_grand_total');

        return $this;
    }

    protected function prepareGrid()
    {
        $this->initGrid()
            ->setDefaultSort('sum_grand_total')
            ->setDefaultDir('desc')
            ->setDefaultLimit(100)
            ->setPagerVisibility(false);

        return $this;
    }

    public function _prepareCollection()
    {
        $collection = Mage::getModel('advr/report_sales')
            ->setBaseTable('sales/order')
            ->setFilterData($this->getFilterData())
            ->selectColumns($this->getVisibleColumns())
            ->groupByColumn('shipping_method');

        return $collection;
    }

    public function getColumns()
    {
        $columns = array(
            'shipping_method' => array(
                'header'              => 'Shipping Method',
                'type'                => 'text',
                'frame_callback'      => array(Mage::helper('advr/callback'), 'shippingMethod'),
                'totals_label'        => 'Total',
                'filter_totals_label' => 'Subtotal',
                'filter'              => false,
            ),
        );

        $columns += $this->getOrderTableColumns(true);

        return $columns;
    }
}
