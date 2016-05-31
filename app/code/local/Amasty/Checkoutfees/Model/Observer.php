<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */
class Amasty_Checkoutfees_Model_Observer
{
    public function insertHtml($observer)
    {
        if (!Mage::getStoreConfig('amcheckoutfees/general/enabled')) {
            return false;
        }

        $block = $observer->getBlock();
        $class = get_class($block);
        // prepare Email totals
        if (strpos($class, '_Email_') !== false && !Mage::registry('amcheckoutfees_email_template_style')) {
            Mage::register('amcheckoutfees_email_template_style', true);
        }

        // insert totals in Email block only
        if (
            Mage::registry('amcheckoutfees_email_template_style') &&
            (
                $block instanceof Mage_Sales_Block_Order_Totals ||
                $block instanceof Mage_Sales_Block_Order_Creditmemo_Totals ||
                $block instanceof Mage_Sales_Block_Order_Invoice_Totals
            )
        ) {
            $html = $observer->getTransport()->getHtml();
            $html = $this->_prepareTotalsHtml($html);
            $observer->getTransport()->setHtml($html);
        }

        return $observer;
    }

    /**
     * @param $html
     *
     * @return mixed
     */
    private function _prepareTotalsHtml($html)
    {
        $order = $this->getDataByParams();
        if (!$order) {
            return $html;
        }

        $amount              = $order->getBaseAmcheckoutfeesAmount();
        $baseCurrencyCode    = Mage::app()->getStore()->getBaseCurrencyCode();
        $currentCurrencyCode = $order->getOrderCurrencyCode() ? $order->getOrderCurrencyCode() : $order->getQuoteCurrencyCode();
        if ($currentCurrencyCode && $baseCurrencyCode != $currentCurrencyCode) {
            $amount = Mage::helper('directory')->currencyConvert($amount, $baseCurrencyCode, $currentCurrencyCode);
            $amount = Mage::app()->getLocale()->currency($currentCurrencyCode)->toCurrency($amount);
        } else {
            $amount = Mage::helper('core')->currency($amount, true, true);
        }

        $html_block
            = '
            <tr class="">
                <td colspan="3" align="right" style="padding:3px 9px">' . Mage::helper('amcheckoutfees')->__('Checkout Fees') . '</td>
                <td colspan="3" align="right" style="padding:3px 9px"><span class="price">' . $amount . '</span></td>
            </tr>
        ';

        if ($amount) {
            $html = str_replace(
                '<tr class="grand_total',
                $html_block . "\r\n" . ' <tr class="grand_total',
                $html
            );
        }

        return $html;
    }

    /**
     * @return bool|Mage_Core_Model_Abstract|Varien_Object
     */
    private function getDataByParams()
    {
        $data   = false;
        $params = Mage::app()->getRequest()->getParams();
        /*
         * case: invoice view/edit/create
         */
        if (isset($params['come_from']) && $params['come_from'] == 'invoice') {
            $data = Mage::getModel('sales/order_invoice')->load($params['invoice_id']);
        } /*
         * case: order edit
         */
        elseif (isset($params['come_from']) && $params['come_from'] == 'order') {
            $data = Mage::getModel('sales/order')->load($params['order_id']);
        } /*
         * case: order view
         */
        elseif (!isset($params['come_from']) && isset($params['order_id'])) {
            $data = Mage::getModel('sales/order')->load($params['order_id']);
        }  /*
         * case: creditmemo view print
         */
        elseif (!isset($params['come_from']) && isset($params['creditmemo_id'])) {
            $data = Mage::getModel('sales/order_creditmemo')->load($params['creditmemo_id']);
        }  /*
         * case: invoice view print
         */
        elseif (!isset($params['come_from']) && isset($params['invoice_id'])) {
            $data = Mage::getModel('sales/order_invoice')->load($params['invoice_id']);
        } /*
         * case: Order Email Template Processing
         */
        else {
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $data  = $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
            if ($quote->getQuoteCurrencyCode()) {
                $data->setQuoteCurrencyCode($quote->getQuoteCurrencyCode());
            } else if ($quote->getOrderCurrencyCode()) {
                $data->setOrderCurrencyCode($quote->getOrderCurrencyCode());
            }

        }

        return $data;
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function invoiceSaveAfter(Varien_Event_Observer $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        if ($invoice->getBaseAmcheckoutfeesAmount()) {
            $order = $invoice->getOrder();
            $order->setAmcheckoutfeesAmountInvoiced($order->getAmcheckoutfeesAmountInvoiced() + $invoice->getAmcheckoutfeesAmount());
            $order->setBaseAmcheckoutfeesAmountInvoiced($order->getBaseAmcheckoutfeesAmountInvoiced() + $invoice->getBaseAmcheckoutfeesAmount());
        }

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function creditmemoSaveAfter(Varien_Event_Observer $observer)
    {
        /* @var $creditmemo Mage_Sales_Model_Order_Creditmemo */
        $creditmemo = $observer->getEvent()->getCreditmemo();
        if ($creditmemo->getAmcheckoutfeesAmount()) {
            $order = $creditmemo->getOrder();
            $order->setAmcheckoutfeesAmountRefunded($order->getAmcheckoutfeesAmountRefunded() + $creditmemo->getAmcheckoutfeesAmount());
            $order->setBaseAmcheckoutfeesAmountRefunded($order->getBaseAmcheckoutfeesAmountRefunded() + $creditmemo->getBaseAmcheckoutfeesAmount());
        }

        return $this;
    }

    /**
     * @param $evt
     */
    public function updatePaypalTotal($evt)
    {
        $cart   = $evt->getPaypalCart();
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $data = $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
        $amount = $data->getBaseAmcheckoutfeesAmount();
        if ($amount) {
            $cart->addItem(Mage::helper('amcheckoutfees')->__('Checkout Fees'), 1, $amount);
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    function convertQuoteToOrder(Varien_Event_Observer $observer)
    {
        $observer->getOrder()->setData('amcheckoutfees_fees', $observer->getQuote()->getAmcheckoutfeesFees());

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function checkShoppingCart(Varien_Event_Observer $observer)
    {
        $post = Mage::app()->getRequest()->getPost('update_cart_action');
        if ($post == 'empty_cart') {
            $quote = Mage::helper('checkout/cart')->getQuote(); //quote
            $quote->setAmcheckoutfeesFees(0);
            $quote->save();
            $quote->setTotalsCollectedFlag(false)->collectTotals();
        } else {
            if (!Mage::registry('amasty_checkoutfees_checkShoppingCart')) {
                // save global var to prevent infinite loop
                Mage::register('amasty_checkoutfees_checkShoppingCart', true, true);

                // prepare data
                $quote  = Mage::getSingleton('checkout/session')->getQuote();
                $params = Mage::app()->getRequest()->getParams();

                // get all AutoApplied fees
                $fees = $this->getAutoapplyFees();

                // add AutoApply Fees as post params
                if (!empty($fees) && is_array($fees) && count($fees)) {
                    foreach ($fees as $fee) {
                        if ($fee['fee']->getAutoapply() && $fee['default']) {
                            $params['amcheckoutfees_' . $fee['fee']->getFeesId()] = $fee['default'];
                        }
                    }
                }

                // save current params into Quote
                $this->saveQuoteParams($params);

                // remove processing flag
                Mage::unregister('amasty_checkoutfees_checkShoppingCart');
            }
        }
    }

    private function getAutoapplyFees()
    {
        $fees     = array();
        $defaults = array();

        // get all fees for payment
        $allFees = Mage::getModel('amcheckoutfees/fees')
                       ->getCollection()
                       ->addFieldToFilter('enabled', 1)
                       ->addFieldToFilter('autoapply', 1)
                       ->setOrder('sort', 'ASC');

        // remove all fees that does not match rule for current Quote
        $allFees->validateAllFees();

        // process fees collections
        if ($allFees->getSize()) {
            foreach ($allFees as $fee) {
                // get all options for fees
                $feeOptions = Mage::getModel('amcheckoutfees/feesData')
                                  ->getCollection()
                                  ->addFieldToFilter('fees_id', array('eq' => $fee->getFeesId()))
                                  ->addFieldToFilter('is_default', array('eq' => 1))
                                  ->setOrder('sort', 'ASC')
                                  ->getItems();

                // replace default values with saved
                if ($feeOptions) {
                    foreach ($feeOptions as &$feeOption) {
                        $defaults[$feeOption->getFeesId()] = $feeOption->getFeesDataId();
                    }
                }
                // save all fee data for render
                $fees[$fee->getFeesId()] = array(
                    'fee'     => $fee,
                    'default' => isset($defaults[$fee->getFeesId()]) ? $defaults[$fee->getFeesId()] : 0
                );
            }
        }

        return $fees;
    }

    private function saveQuoteParams($params)
    {
        $data  = array();
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        // extract params from filtered POST
        if (is_array($params) && count($params) > 0) {
            // parse params into one structured array
            foreach ($params as $param => $value) {
                if (strpos($param, 'amcheckoutfees_') !== false) {
                    $paramData = explode('_', $param);
                    $paramId   = isset($paramData[1]) ? $paramData[1] : 0;
                    if ($paramId > 0) {
                        if (is_array($value)) {
                            $data[$paramId] = implode(',', array_values($value));
                        } else {
                            $data[$paramId] = $value;
                        }
                    }
                }
            }

            // merge saved data and newly added
            $savedData = $quote->getData('amcheckoutfees_fees') ? unserialize($quote->getData('amcheckoutfees_fees')) : array();
            $data      = $data + $savedData;
            $data      = serialize($data);

            // save into quote
            $quote->setData('amcheckoutfees_fees', $data);
            $quote->save();
            $quote->setTotalsCollectedFlag(false)->collectTotals();
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function convertOrderToQuote(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();
        $quote->setData('amcheckoutfees_fees', $order->getAmcheckoutfeesFees());
        $quote->save();
        $quote->setTotalsCollectedFlag(false)->collectTotals();

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function mergeQuotes(Varien_Event_Observer $observer)
    {
        $source = $observer->getSource();
        $quote  = $observer->getQuote();

        $quote->setAmcheckoutfeesFees($source->getAmcheckoutfeesFees());
        $quote->setTotalsCollectedFlag(false)->collectTotals();
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return bool
     */
    public function  onCheckoutStepSaveMethod(Varien_Event_Observer $observer)
    {
        if (!Mage::getStoreConfig('amcheckoutfees/general/enabled')) {
            return $observer;
        }

        // prepare data
        $data   = array();
        $params = Mage::app()->getRequest()->getParams();

        // extract params from filtered POST
        if (is_array($params) && count($params) > 0) {
            // parse params into one structured array
            foreach ($params as $param => $value) {
                if (strpos($param, 'amcheckoutfees_') !== false) {
                    $paramData = explode('_', $param);
                    $paramId   = isset($paramData[1]) ? $paramData[1] : 0;
                    if ($paramId > 0) {
                        if (is_array($value)) {
                            $data[$paramId] = implode(',', array_values($value));
                        } else {
                            $data[$paramId] = $value;
                        }
                    }
                }
            }

            // merge saved data and newly added
            $quote     = Mage::getSingleton('checkout/session')->getQuote();
            $savedData = $quote->getData('amcheckoutfees_fees') ? unserialize($quote->getData('amcheckoutfees_fees')) : array();
            $data      = $data + $savedData;
            $data      = serialize($data);

            // save into quote
            $quote->setData('amcheckoutfees_fees', $data);
            $quote->save();
            $quote->setTotalsCollectedFlag(false)->collectTotals();
        }

        return $observer;
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return bool
     */
    public function beforePaymentMethodSave($observer)
    {
        if (!Mage::registry('amasty_checkoutfees_beforePaymentMethodSave')) {
            // save global var to prevent infinite loop
            Mage::register('amasty_checkoutfees_beforePaymentMethodSave', true, true);

            // prepare data
            $quote  = Mage::getSingleton('checkout/session')->getQuote();
            $params = Mage::app()->getRequest()->getParams();

            // apply payment data
            $controller = Mage::getControllerInstance('Mage_Checkout_OnepageController', Mage::app()->getRequest(), Mage::app()->getResponse());
            $postData   = Mage::app()->getRequest()->getPost('payment', array());
            $controller->getOnepage()->savePayment($postData);

            // recalculate totals
            $quote->setTotalsCollectedFlag(false)->collectTotals();

            // get all AutoApplied fees
            $fees = $this->getAutoapplyFees();

            // add AutoApply Fees as post params
            if (!empty($fees) && is_array($fees) && count($fees)) {
                foreach ($fees as $fee) {
                    if ($fee['fee']->getAutoapply() && $fee['default']) {
                        $params['amcheckoutfees_' . $fee['fee']->getFeesId()] = $fee['default'];
                    }
                }
            }

            // save current params into Quote
            $this->saveQuoteParams($params);

            // remove processing flag
            Mage::unregister('amasty_checkoutfees_beforePaymentMethodSave');
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return bool
     */
    public function beforeShippingMethodSave($observer)
    {
        if (!Mage::registry('amasty_checkoutfees_beforeShippingMethodSave')) {
            // save global var to prevent infinite loop
            Mage::register('amasty_checkoutfees_beforeShippingMethodSave', true, true);

            // prepare data
            $quote  = Mage::getSingleton('checkout/session')->getQuote();
            $params = Mage::app()->getRequest()->getParams();

            // save Shipping data
            $controller = Mage::getControllerInstance('Mage_Checkout_OnepageController', Mage::app()->getRequest(), Mage::app()->getResponse());
            $addressId  = Mage::app()->getRequest()->getPost('shipping_address_id', false);
            $postData   = Mage::app()->getRequest()->getPost('shipping', array());
            $controller->getOnepage()->saveShipping($postData, $addressId);

            // recalculate totals
            $quote->setTotalsCollectedFlag(false)->collectTotals();

            // get all AutoApplied fees
            $fees = $this->getAutoapplyFees();

            // add AutoApply Fees as post params
            if (!empty($fees) && is_array($fees) && count($fees)) {
                foreach ($fees as $fee) {
                    if ($fee['fee']->getAutoapply()) {
                        $params['amcheckoutfees_' . $fee['fee']->getFeesId()] = $fee['default'];
                    }
                }
            }

            // save current params into Quote
            $this->saveQuoteParams($params);

            // remove processing flag
            Mage::unregister('amasty_checkoutfees_beforeShippingMethodSave');
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return bool
     */
    public function  onCollectTotalsAfter(Varien_Event_Observer $observer)
    {
        if (!Mage::registry('amasty_checkoutfees_collectTotalsBefore')) {
            Mage::register('amasty_checkoutfees_collectTotalsBefore', true, true);
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $data  = $quote->getData('amcheckoutfees_fees');
            $data  = $data ? unserialize($data) : array();
            foreach ($data as $k => $v) {
                $fee = Mage::getModel('amcheckoutfees/fees')->load($k);
                if (!$fee->validateFee()) {
                    unset($data[$k]);
                }
            }
            $data = serialize($data);
            $quote->setData('amcheckoutfees_fees', $data);
            $quote->save();
            Mage::unregister('amasty_checkoutfees_collectTotalsBefore');
        }

        return $observer;
    }
}