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



class Mirasvit_Advd_Block_Adminhtml_Widget_Order_Chart extends Mirasvit_Advd_Block_Adminhtml_Widget_Abstract_Chart
{
    public function getGroup()
    {
        return 'Sales';
    }

    public function getName()
    {
        return 'Chart';
    }

    protected function _metrics()
    {
        return array(
            'quantity'              => Mage::helper('advr')->__('Number of Orders'),
            'sum_total_qty_ordered' => Mage::helper('advr')->__('Number of Ordered Products'),
            'sum_grand_total'       => Mage::helper('advr')->__('Grand Total'),
            'sum_subtotal'          => Mage::helper('advr')->__('Subtotal'),
            'sum_discount_amount'   => Mage::helper('advr')->__('Discount'),
            'sum_total_invoiced'    => Mage::helper('advr')->__('Invoiced'),
            'sum_total_refunded'    => Mage::helper('advr')->__('Refunded'),
            'sum_shipping_amount'   => Mage::helper('advr')->__('Shipping Amount'),
            'sum_tax_amount'        => Mage::helper('advr')->__('Tax Amount'),

            'avg_total_qty_ordered' => Mage::helper('advr')->__('Average Number of Ordered Products'),
            'avg_grand_total'       => Mage::helper('advr')->__('Average Grand Total'),
            'avg_subtotal'          => Mage::helper('advr')->__('Average Subtotal'),
            'avg_discount_amount'   => Mage::helper('advr')->__('Average Discount'),
            'avg_total_invoiced'    => Mage::helper('advr')->__('Average Invoiced'),
            'avg_total_refunded'    => Mage::helper('advr')->__('Average Refunded'),
            'avg_shipping_amount'   => Mage::helper('advr')->__('Average Shipping Amount'),
            'avg_tax_amount'        => Mage::helper('advr')->__('Average Tax Amount'),
        );
    }

    public function prepareOptions()
    {
        $metrics = array();
        foreach ($this->_metrics() as $value => $label) {
            $metrics[] = array(
                'value' => $value,
                'label' => $label,
            );
        }
        $this->form->addField(
            'metrics',
            'multiselect',
            array(
                'name'   => 'metrics',
                'label'  => Mage::helper('advr')->__('Metric'),
                'values' => $metrics,
                'value'  => $this->getParam('metrics', array('sum_grand_total')),
            )
        );

        $this->form->addField(
            'range',
            'select',
            array(
                'name'   => 'range',
                'label'  => Mage::helper('advr')->__('Range'),
                'values' => array(
                    array(
                        'value' => '1d',
                        'label' => $this->__('Day')
                    ),
                    array(
                        'value' => '1w',
                        'label' => $this->__('Week')
                    ),
                    array(
                        'value' => '1m',
                        'label' => $this->__('Month')
                    ),
                    array(
                        'value' => '1y',
                        'label' => $this->__('Year')
                    ),
                ),
                'value'  => $this->getParam('range', '1d'),
            )
        );

        $this->form->addField(
            'limit',
            'text',
            array(
                'name'  => 'limit',
                'label' => Mage::helper('advr')->__('Number of values'),
                'value' => $this->getParam('limit', 5)
            )
        );

        return $this;
    }

    protected function _getCollection()
    {
        if (!$this->hasData('collection')) {
            $filterData = new Varien_Object(
                array(
                    'range'     => $this->getParam('range', '1d'),
                    'store_ids' => $this->getParam('store_ids'),
                )
            );

            $collection = Mage::getModel('advr/report_sales')
                ->setBaseTable('sales/order')
                ->selectColumns($this->getParam('metrics'))
                ->setFilterData($filterData)
                ->groupByColumn('period')
                ->selectColumns('period')
                ->setPageSize($this->getParam('limit', 5))
                ->setOrder('created_at', 'desc');

            $collection = array_reverse($collection->getItems());

            $this->setData('collection', $collection);
        }

        return $this->getData('collection');
    }

    public function getCategories()
    {
        $result = array();
        $collection = $this->_getCollection();

        foreach ($collection as $item) {
            $result[] = strtotime($item->getPeriod());
        }

        return $result;
    }

    public function getDataTable()
    {
        $table = array();

        $_metrics = $this->_metrics();
        $metrics = $this->getParam('metrics');

        if (!is_array($metrics)) {
            $metrics = array($metrics);
        }

        $header = array('datetime');
        foreach ($metrics as $m) {
            $header[] = $_metrics[$m];
        }

        $table[] = $header;

        $collection = $this->_getCollection();
        foreach ($collection as $item) {
            if ($this->getParam('range', '1d') == '1w') {
                $row = array(date('d M, Y', strtotime($item->getPeriod()) - 7 * 24 * 60 * 60));
            } else {
                $row = array(date('d M, Y', strtotime($item->getPeriod())));
            }
            foreach ($item->getData() as $serie => $value) {
                if (in_array($serie, $metrics)) {
                    $row[] = floatval($value);
                }
            }

            $table[] = $row;
        }

        return $table;
    }
}
