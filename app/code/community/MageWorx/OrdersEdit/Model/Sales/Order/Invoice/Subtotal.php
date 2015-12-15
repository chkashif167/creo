<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Model_Sales_Order_Invoice_Subtotal extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * Collect invoice subtotal
     *
     * @param   Mage_Sales_Model_Order_Invoice $invoice
     * @return  Mage_Sales_Model_Order_Invoice_Total_Subtotal
     */
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        /// Below is the original magento code instead of marked lines
        $subtotal       = 0;
        $baseSubtotal   = 0;
        $subtotalInclTax= 0;
        $baseSubtotalInclTax = 0;

        $totalWeeeDiscount = 0;
        $totalBaseWeeeDiscount = 0;

        $order = $invoice->getOrder();

        foreach ($invoice->getAllItems() as $item) {
            if ($item->getOrderItem()->isDummy()) {
                continue;
            }

            $item->calcRowTotal();

            $subtotal       += $item->getRowTotal();
            $baseSubtotal   += $item->getBaseRowTotal();
            $subtotalInclTax+= $item->getRowTotalInclTax();
            $baseSubtotalInclTax += $item->getBaseRowTotalInclTax();
            $totalWeeeDiscount += $item->getOrderItem()->getDiscountAppliedForWeeeTax();
            $totalBaseWeeeDiscount += $item->getOrderItem()->getBaseDiscountAppliedForWeeeTax();
        }


        ///// Changes!!! Deducted refunded amount from allowed subtotal
        $allowedSubtotal = $order->getSubtotal() - $order->getSubtotalInvoiced() + $order->getSubtotalRefunded();
        $baseAllowedSubtotal = $order->getBaseSubtotal() - $order->getBaseSubtotalInvoiced()  + $order->getBaseSubtotalRefunded();
        $allowedSubtotalInclTax = $allowedSubtotal + $order->getHiddenTaxAmount() + $totalWeeeDiscount
            + $order->getTaxAmount() - $order->getTaxInvoiced() - $order->getHiddenTaxInvoiced() + $order->getTaxRefunded() + $order->getHiddenTaxRefunded();
        $baseAllowedSubtotalInclTax = $baseAllowedSubtotal + $order->getBaseHiddenTaxAmount() + $totalBaseWeeeDiscount
            + $order->getBaseTaxAmount() - $order->getBaseTaxInvoiced() - $order->getBaseHiddenTaxInvoiced() + $order->getBaseTaxRefunded() + $order->getBaseHiddenTaxRefunded();

        /**
         * Check if shipping tax calculation is included to current invoice.
         */
        $includeShippingTax = true;
        foreach ($invoice->getOrder()->getInvoiceCollection() as $previousInvoice) {
            if ($previousInvoice->getShippingAmount() && !$previousInvoice->isCanceled()) {
                $includeShippingTax = false;
                break;
            }
        }

        if ($includeShippingTax) {
            $allowedSubtotalInclTax     -= $order->getShippingTaxAmount();
            $baseAllowedSubtotalInclTax -= $order->getBaseShippingTaxAmount();
        } else {
            $allowedSubtotalInclTax     += $order->getShippingHiddenTaxAmount();
            $baseAllowedSubtotalInclTax += $order->getBaseShippingHiddenTaxAmount();
        }

        if ($invoice->isLast()) {
            $subtotal = $allowedSubtotal;
            $baseSubtotal = $baseAllowedSubtotal;
            $subtotalInclTax = $allowedSubtotalInclTax;
            $baseSubtotalInclTax  = $baseAllowedSubtotalInclTax;
        } else {
            $subtotal = min($allowedSubtotal, $subtotal);
            $baseSubtotal = min($baseAllowedSubtotal, $baseSubtotal);
            $subtotalInclTax = min($allowedSubtotalInclTax, $subtotalInclTax);
            $baseSubtotalInclTax = min($baseAllowedSubtotalInclTax, $baseSubtotalInclTax);
        }

        $invoice->setSubtotal($subtotal);
        $invoice->setBaseSubtotal($baseSubtotal);
        $invoice->setSubtotalInclTax($subtotalInclTax);
        $invoice->setBaseSubtotalInclTax($baseSubtotalInclTax);

        $invoice->setGrandTotal($invoice->getGrandTotal() + $subtotal);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseSubtotal);
        return $this;
    }
}
