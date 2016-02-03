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



class Mirasvit_Advd_Block_Adminhtml_Widget_Order_Metric extends Mirasvit_Advd_Block_Adminhtml_Widget_Abstract_Metric
{
    public function getGroup()
    {
        return 'Sales';
    }

    public function getName()
    {
        return 'Metrics';
    }

    public function getMetricValue()
    {
        $totals = $this->getTotals($this->getParam('interval'));

        $value = $totals->getData($this->getParam('metric'));

        return $value;
    }

    public function getMetricValueToCompare()
    {
        $totals = $this->getTotals($this->getParam('interval'), $this->getParam('compare_with'));

        $value = $totals->getData($this->getParam('metric'));

        return $value;
    }

    public function formatMetricValue($value)
    {
        switch ($this->getParams()->getMetric()) {
            case 'quantity':
            case 'sum_total_qty_ordered':
            case 'avg_total_qty_ordered':
                $type = 'number';
                break;

            default:
                $type = 'currency';
                break;
        }

        if ($type == 'currency') {
            $value = Mage::app()->getStore()->getBaseCurrency()->format($value);
        }

        return $value;
    }

    public function prepareOptions()
    {
        $this->form->addField(
            'metric',
            'select',
            array(
                'name'   => 'metric',
                'label'  => Mage::helper('advr')->__('Metric'),
                'value'  => $this->getParam('metric', 'quantity'),
                'values' => array(
                    'quantity'              => Mage::helper('advr')->__('Number of Orders'),
                    'sum_total_qty_ordered' => Mage::helper('advr')->__('Number of Ordered Products'),
                    'sum_grand_total'       => Mage::helper('advr')->__('Grand Total'),
                    'sum_subtotal'          => Mage::helper('advr')->__('Subtotal'),
                    'sum_discount_amount'   => Mage::helper('advr')->__('Discount'),
                    'sum_total_invoiced'    => Mage::helper('advr')->__('Invoiced'),
                    'sum_total_refunded'    => Mage::helper('advr')->__('Refunded'),
                    'sum_shipping_amount'   => Mage::helper('advr')->__('Shipping Amount'),
                    'sum_tax_amount'        => Mage::helper('advr')->__('Tax Amount'),
                    'sum_gross_profit'      => Mage::helper('advr')->__('Gross Profit'),

                    'avg_total_qty_ordered' => Mage::helper('advr')->__('Average Number of Ordered Products'),
                    'avg_grand_total'       => Mage::helper('advr')->__('Average Grand Total'),
                    'avg_subtotal'          => Mage::helper('advr')->__('Average Subtotal'),
                    'avg_discount_amount'   => Mage::helper('advr')->__('Average Discount'),
                    'avg_total_invoiced'    => Mage::helper('advr')->__('Average Invoiced'),
                    'avg_total_refunded'    => Mage::helper('advr')->__('Average Refunded'),
                    'avg_shipping_amount'   => Mage::helper('advr')->__('Average Shipping Amount'),
                    'avg_tax_amount'        => Mage::helper('advr')->__('Average Tax Amount'),
                ),
            )
        );

        $this->form->addField(
            'interval',
            'select',
            array(
                'name'   => 'interval',
                'label'  => Mage::helper('advr')->__('Period'),
                'value'  => $this->getParam('interval', Mirasvit_Advr_Helper_Date::LAST_24H),
                'values' => Mage::helper('advr/date')->getIntervals(true, true),
            )
        );

        $this->form->addField(
            'compare_with',
            'select',
            array(
                'name'   => 'compare_with',
                'label'  => Mage::helper('advr')->__('Compare with'),
                'value'  => $this->getParam('compare_with', '0'),
                'values' => array(
                    '0'   => Mage::helper('advr')->__('Previous period'),
                    '7'   => Mage::helper('advr')->__('Same period last week'),
                    '30'  => Mage::helper('advr')->__('Same period last month'),
                    '365' => Mage::helper('advr')->__('Same period last year'),
                ),
            )
        );

        return $this;
    }

    protected function getTotals($intervalCode, $compareWith = false)
    {
        if ($compareWith === false) {
            $interval = Mage::helper('advr/date')->getInterval($intervalCode, true);
        } else {
            $interval = Mage::helper('advr/date')->getPreviousInterval($intervalCode, $compareWith, true);
        }

        $filterData = new Varien_Object(array(
            'from'      => $interval->getFrom()->get(Varien_Date::DATETIME_INTERNAL_FORMAT),
            'to'        => $interval->getTo()->get(Varien_Date::DATETIME_INTERNAL_FORMAT),
            'store_ids' => $this->getParam('store_ids')
        ));

        $orders = Mage::getModel('advr/report_sales')
            ->setBaseTable('sales/order')
            ->selectColumns(array($this->getParam('metric')))
            ->setFilterData($filterData);

        $totals = $orders->getTotals();

        return $totals;
    }

    public function getWidgetTitle()
    {
        $hint = Mage::helper('advr/date')->getIntervalHint($this->getParam('interval'));

        return $this->getParam('title') . ' <small>' . $hint . '</small>';
    }
}
