<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */


class Amasty_Checkoutfees_Model_Sales_Order_Total_Creditmemo_Amcheckoutfees extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $order                        = $creditmemo->getOrder();
        $amcheckoutfeesAmountLeft     = $order->getAmcheckoutfeesAmountInvoiced() - $order->getAmcheckoutfeesAmountRefunded();
        $baseAmcheckoutfeesAmountLeft = $order->getBaseAmcheckoutfeesAmountInvoiced() - $order->getBaseAmcheckoutfeesAmountRefunded();
        if ($baseAmcheckoutfeesAmountLeft != 0) {
            $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $amcheckoutfeesAmountLeft);
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseAmcheckoutfeesAmountLeft);
            $creditmemo->setAmcheckoutfeesAmount($amcheckoutfeesAmountLeft);
            $creditmemo->setBaseAmcheckoutfeesAmount($baseAmcheckoutfeesAmountLeft);
        }

        return $this;
    }
}
