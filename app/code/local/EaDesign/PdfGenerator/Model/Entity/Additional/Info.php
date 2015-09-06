<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Info
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Model_Entity_Additional_Info extends EaDesign_PdfGenerator_Model_Entity_Pdfgenerator
{

    public function getStoreId()
    {
        return $this->getOrder()->getStoreId();
    }

    public function getTheInfoVariables()
    {
        $order = $this->getOrder();
        $store = Mage::app()->getStore($this->getStoreId());
        $image = Mage::getStoreConfig('sales/identity/logo', $this->getStoreId());
        $sourceDate = Mage::helper('core')->formatDate($this->getSource()->getCreatedAt(), 'medium', false);
        $variables = array(
            'ea_logo_store' => array(
                'value' => '<img src="' . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . '/sales/store/logo/' . $image . '" />',
            ),
            'ea_order_number' => array(
                'value' => $this->getOrder()->getRealOrderId(),
                'label' => Mage::helper('sales')->__('Order # %s')
            ),
            'ea_purcase_from_website' => array(
                'value' => $store->getWebsite()->getName(),
                'label' => Mage::helper('sales')->__('Purchased From')
            ),
            'ea_order_group' => array(
                'value' => $store->getGroup()->getName(),
                'label' => Mage::helper('pdfgenerator')->__('Purchased From Store')
            ),
            'ea_order_store' => array(
                'value' => $store->getName(),
                'label' => Mage::helper('sales')->__('Purchased From Website')
            ),
            'ea_order_status' => array(
                'value' => $this->getOrder()->getStatus(),
                'label' => Mage::helper('sales')->__('Order Status')
            ),
            'ea_source_date' => array(
                'value' => $sourceDate,
                'label' => Mage::helper('sales')->__('Order Date')
            ),
            'ea_order_totalpaid' => array(
                'value' => $order->formatPriceTxt($this->getOrder()->getTotalPaid()),
                'label' => Mage::helper('sales')->__('Total Paid')
            ),
            'ea_order_totalrefunded' => array(
                'value' => $order->formatPriceTxt($this->getOrder()->getTotalRefunded()),
                'label' => Mage::helper('sales')->__('Total Refunded')
            ),
            'ea_order_totaldue' => array(
                'value' => $order->formatPriceTxt($this->getOrder()->getTotalDue()),
                'label' => Mage::helper('sales')->__('Total Due')
            ),
        );

        return $variables;
    }

    public function getTheInvoiceVariables()
    {
        $invoice = $this->getSource();


        $comments = $invoice->getCommentsCollection()->getItems();
        if (count($comments)) {
            foreach ($comments as $comment) {
                $commentArr[] = $comment->getData('comment');
            }
            $commentData = implode('<br><br>', $commentArr);
        }

        $variables = array(
            'ea_invoice_id' => array(
                'value' => $invoice->getIncrementId(),
                'label' => Mage::helper('pdfgenerator')->__('Invoice Id'),
            ),
            'ea_invoice_status' => array(
                'value' => $invoice->getStateName(),
                'label' => Mage::helper('pdfgenerator')->__('Invoice Status'),
            ),
            'ea_invoice_date' => array(
                'value' => Mage::helper('core')->formatDate($invoice->getCreatedAtDate(), 'medium', false),
                'label' => Mage::helper('pdfgenerator')->__('Invoice Date'),
            ),
            'ea_invoice_comments' => array(
                'value' => $commentData,
                'label' => Mage::helper('pdfgenerator')->__('Comments'),
            ),
        );


        return $variables;
    }

    public function getTheCustomerVariables()
    {
        $order = $this->getSource()->getOrder();

        $variables = array(
            'customer_name' => array(
                'value' => $order->getData('customer_lastname') . ' ' . $order->getData('customer_firstname'),
                'label' => Mage::helper('sales')->__('Customer Name'),
            ),
            'customer_email' => array(
                'value' => $order->getCustomerEmail(),
                'label' => Mage::helper('sales')->__('Email'),
            ),
            'customer_group' => array(
                'value' => $order->getData('customer_group_id'),
                'label' => Mage::helper('sales')->__('Customer Group'),
            ),
            'customer_firstname' => array(
                'value' => $order->getData('customer_firstname'),
                'label' => Mage::helper('customer')->__('First Name'),
            ),
            'customer_lastname' => array(
                'value' => $order->getData('customer_lastname'),
                'label' => Mage::helper('customer')->__('Last Name'),
            ),
            'customer_middlename' => array(
                'value' => $order->getData('customer_middlename'),
                'label' => Mage::helper('customer')->__('Middle Name/Initial'),
            ),
            'customer_prefix' => array(
                'value' => $order->getData('customer_prefix'),
                'label' => Mage::helper('customer')->__('Prefix'),
            ),
            'customer_suffix' => array(
                'value' => $order->getData('customer_suffix'),
                'label' => Mage::helper('customer')->__('Suffix'),
            ),
            'customer_taxvat' => array(
                'value' => $order->getData('customer_taxvat'),
                'label' => Mage::helper('customer')->__('Tax/VAT number'),
            ),
            'customer_dob' => array(
                'value' => Mage::helper('core')->formatDate($order->getData('customer_dob'), 'medium', false),
                'label' => Mage::helper('customer')->__('Date Of Birth'),
            ),
        );

        return $variables;
    }

    public function getTheAddresInfo()
    {
        $order = $this->getOrder();
        $billingInfo = $order->getBillingAddress()->getFormated(true);
        if ($order->getShippingAddress()) {
            $shippingInfo = $order->getShippingAddress()->getFormated(true);
        } else {
            $shippingInfo = '';
        }
        $variables = array(
            'billing_address' => array(
                'value' => $billingInfo,
                'label' => Mage::helper('sales')->__('Billing Address'),
            ),
            'shipping_address' => array(
                'value' => $shippingInfo,
                'label' => Mage::helper('sales')->__('Shipping Address'),
            )
        );
        return $variables;
    }

    public function getThePaymentInfo()
    {
        $order = $this->getOrder();
        $paymentInfo = $order->getPayment()->getMethodInstance()->getTitle();

        if ($order->getPayment()) {
            $paymentInfo = $order->getPayment()->getMethodInstance()->getTitle();
        }

        $paymentInfoBlock = Mage::helper('payment')->getInfoBlock($order->getPayment());
        $bilSafeBlock = $paymentInfoBlock->toHtml();

        $variables = array(
            'billing_method' => array(
                'value' => $paymentInfo,
                'label' => Mage::helper('sales')->__('Billing Method'),
            ),
            'billing_method_currency' => array(
                'value' => $order->getOrderCurrencyCode(),
                'label' => Mage::helper('sales')->__('Order was placed using'),
            ),
            'ea_payment' => array(
                'value' => $bilSafeBlock,
                'label' => Mage::helper('sales')->__('Bilsafe'),
            ),
        );
        return $variables;
    }

    public function getTheShippingInfo()
    {
        $order = $this->getOrder();


        if ($order->getShippingDescription()) {
            $shippingInfo = $order->getShippingDescription();
        } else {
            $shippingInfo = '';
        }

        $variables = array(
            'shipping_method' => array(
                'value' => $shippingInfo,
                'label' => Mage::helper('sales')->__('Shipping Information'),
            ),
        );
        return $variables;
    }

    public function getTheInfoMergedVariables()
    {
        $vars = array_merge(
            $this->getTheInfoVariables()
            , $this->getTheCustomerVariables()
            , $this->getTheAddresInfo()
            , $this->getThePaymentInfo()
            , $this->getTheShippingInfo()
            , $this->getTheInvoiceVariables()
        );
        $processedVars = Mage::helper('pdfgenerator')->arrayToStandard($vars);

        return $processedVars;
    }

}
