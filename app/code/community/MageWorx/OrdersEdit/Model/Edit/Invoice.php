<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_OrdersEdit_Model_Edit_Invoice extends Mage_Core_Model_Abstract
{
    /**
     * List of order totals that can be changed
     *
     * @var array
     */
    protected $_availableTotals = array(
        'shipping_tax_amount',
        'base_shipping_tax_amount',
        'base_shipping_tax_amount',
        'subtotal',
        'base_subtotal',
        'subtotal_incl_tax',
        'base_subtotal_incl_tax',
        'grand_total',
        'base_grand_total',
        'tax_amount',
        'base_tax_amount',
        'discount_amount',
        'base_discount_amount',
        'shipping_amount',
        'base_shipping_amount',
        'shipping_incl_tax',
        'base_shipping_incl_tax',
        'hidden_tax_amount',
        'base_hidden_tax_amount'
    );

    /**
     * Create invoice for order changes
     *
     * @param Mage_Sales_Model_Order $origOrder
     * @param Mage_Sales_Model_Order $newOrder
     * @param $changes
     * @return $this
     */
    public function invoiceChanges(Mage_Sales_Model_Order $origOrder, Mage_Sales_Model_Order $newOrder, $changes)
    {
        /** @var Mage_Sales_Model_Order_Invoice $invoice */
        $invoice = Mage::getModel('sales/service_order', $newOrder)->prepareInvoice();

        foreach ($this->_availableTotals as $code) {
            $diff = $newOrder->getData($code) - $origOrder->getData($code);
            if (!$diff) {
                continue;
            }

            $invoice->setData($code, $diff);
        }

        $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
        $invoice->register();

        $transaction = Mage::getModel('core/resource_transaction')
            ->addObject($invoice)
            ->addObject($invoice->getOrder());

        $transaction->save();

        $this->updateInvoicedItems($newOrder);

        return $this;
    }

    /**
     * Set correct totals for the items which have just been invoiced
     *
     * @param Mage_Sales_Model_Order $order
     * @return $this
     */
    public function updateInvoicedItems(Mage_Sales_Model_Order $order)
    {
        /** @var Mage_Sales_Model_Order_Item $orderItem */
        foreach ($order->getAllItems() as $orderItem) {
            $orderItem->setTaxInvoiced($orderItem->getTaxAmount());
            $orderItem->setBaseTaxInvoiced($orderItem->getBaseTaxAmount());

            $orderItem->setRowInvoiced($orderItem->getRowTotal());
            $orderItem->setBaseRowInvoiced($orderItem->getBaseRowTotal());

            $orderItem->save();
        }

        return $this;
    }
}