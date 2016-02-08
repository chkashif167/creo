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



class Mirasvit_Advr_Block_Adminhtml_Order_Abstract extends Mirasvit_Advr_Block_Adminhtml_Block_Container
{
    public function getTotals()
    {
        return $this->getCollection()->getTotals();
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getOrderTableColumns($includePercentOfTotal = false)
    {
        $columns = array();

        if ($includePercentOfTotal) {
            $columns['percent'] = array(
                'header'          => 'Number Of Orders, %',
                'type'            => 'percent',
                'filter'          => false,
                'index'           => 'quantity',
                'frame_callback'  => array(Mage::helper('advr/callback'), 'percent'),
                'export_callback' => array(Mage::helper('advr/callback'), '_percent'),
            );
        }

        $columns['quantity'] = array(
            'header' => 'Number Of Orders',
            'type'   => 'number',
        );
        $columns['sum_total_qty_ordered'] = array(
            'header' => 'Items Ordered',
            'type'   => 'number',
        );
        $columns['sum_discount_amount'] = array(
            'header'         => 'Discount',
            'type'           => 'currency',
            'frame_callback' => array(Mage::helper('advr/callback'), 'discount'),
            'discount_from'  => 'sum_subtotal',
        );
        $columns['sum_shipping_amount'] = array(
            'header' => 'Shipping',
            'type'   => 'currency',
        );
        $columns['sum_tax_amount'] = array(
            'header' => 'Tax',
            'type'   => 'currency',
        );
        $columns['sum_shipping_tax_amount'] = array(
            'header' => 'Shipping Tax',
            'type'   => 'currency',
            'hidden' => true,
        );
        $columns['sum_total_invoiced'] = array(
            'header' => 'Invoiced',
            'type'   => 'currency',
            'hidden' => true,
        );
        $columns['sum_total_refunded'] = array(
            'header' => 'Refunded',
            'type'   => 'currency',
        );
        $columns['sum_subtotal'] = array(
            'header' => 'Subtotal',
            'type'   => 'currency',
        );
        $columns['sum_grand_total'] = array(
            'header' => 'Grand Total',
            'type'   => 'currency',
            'chart'  => true,
        );
        $columns['sum_total_invoiced_cost'] = array(
            'header' => 'Invoiced Cost',
            'type'   => 'currency',
            'hidden' => true,
        );
        $columns['sum_gross_profit'] = array(
            'header'         => 'Gross Profit',
            'type'           => 'currency',
            'hidden'         => true,
            'frame_callback' => array(Mage::helper('advr/callback'), 'discount'),
            'discount_from'  => 'sum_grand_total',
        );
        $columns['avg_total_qty_ordered'] = array(
            'header' => 'Average Items Ordered',
            'type'   => 'number',
            'hidden' => true,
        );
        $columns['avg_discount_amount'] = array(
            'header'         => 'Average Discount',
            'type'           => 'currency',
            'hidden'         => true,
            'frame_callback' => array(Mage::helper('advr/callback'), 'discount'),
            'discount_from'  => 'avg_subtotal',
        );
        $columns['avg_shipping_amount'] = array(
            'header' => 'Average Shipping',
            'type'   => 'currency',
            'hidden' => true,
        );
        $columns['avg_tax_amount'] = array(
            'header' => 'Average Tax',
            'type'   => 'currency',
            'hidden' => true,
        );
        $columns['avg_shipping_tax_amount'] = array(
            'header' => 'Average Shipping Tax',
            'type'   => 'currency',
            'hidden' => true,
        );
        $columns['avg_total_invoiced'] = array(
            'header' => 'Average Invoiced',
            'type'   => 'currency',
            'hidden' => true,
        );
        $columns['avg_total_refunded'] = array(
            'header' => 'Average Refunded',
            'type'   => 'currency',
            'hidden' => true,
        );
        $columns['avg_grand_total'] = array(
            'header' => 'Average Grand Total',
            'type'   => 'currency',
            'hidden' => true,
        );

        return $columns;
    }
}
