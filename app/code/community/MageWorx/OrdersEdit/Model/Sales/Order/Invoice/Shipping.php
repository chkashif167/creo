<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Model_Sales_Order_Invoice_Shipping extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * Collect shipping amount to be invoiced based on already invoiced amount
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return $this
     */
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $previousInvoices = $invoice->getOrder()->getInvoiceCollection();

        if ($invoice->getShippingAmount() > 0 || !count($previousInvoices)) {
            return $this;
        }

        $order = $invoice->getOrder();

        $shippingAmount        = $order->getShippingAmount() - $order->getShippingInvoiced() - $order->getShippingRefunded();
        $baseShippingAmount    = $order->getBaseShippingAmount() - $order->getBaseShippingInvoiced() - $order->getBaseShippingRefunded();

        $invoice->setShippingAmount($shippingAmount);
        $invoice->setBaseShippingAmount($baseShippingAmount);
        $invoice->setGrandTotal($invoice->getGrandTotal() + $shippingAmount);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseShippingAmount);

        return $this;
    }
}