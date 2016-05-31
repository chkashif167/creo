<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */


class Amasty_Checkoutfees_Model_Sales_Order_Total_Invoice_Amcheckoutfees extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $order                        = $invoice->getOrder();
        $amcheckoutfeesAmountLeft     = $order->getAmcheckoutfeesAmount() - $order->getAmcheckoutfeesAmountInvoiced();
        $baseAmcheckoutfeesAmountLeft = $order->getBaseAmcheckoutfeesAmount() - $order->getBaseAmcheckoutfeesAmountInvoiced();

        $invoice->setGrandTotal($invoice->getGrandTotal() + $amcheckoutfeesAmountLeft);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseAmcheckoutfeesAmountLeft);

        $invoice->setAmcheckoutfeesAmount($amcheckoutfeesAmountLeft);
        $invoice->setBaseAmcheckoutfeesAmount($baseAmcheckoutfeesAmountLeft);

        return $this;
    }
}
