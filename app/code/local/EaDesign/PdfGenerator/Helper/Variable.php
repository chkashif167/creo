<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Variable
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Helper_Variable extends Mage_Core_Helper_Abstract
{

    public function getTotalVariables()
    {

        $variables[] = array(
            'value' => '{{var grandtotalexcludingtax}}',
            'label' => Mage::helper('tax')->__('Grand Total (Excl. Tax)') . ':',
        );
        $variables[] = array(
            'value' => '{{var grandtotaltax}}',
            'label' => Mage::helper('tax')->__('Grand Total Tax') . ':',
        );
        $variables[] = array(
            'value' => '{{var grandtotalincludingtax}}',
            'label' => Mage::helper('tax')->__('Grand Total (Incl. Tax)') . ':',
        );

        $variables = array(
            'label' => Mage::helper('pdfgenerator')->__('Grand Totals'),
            'value' => $variables
        );
        return $variables;
    }

    public function getDiscountVariables()
    {
        $variables[] = array(
            'value' => '{{var discountammount}}',
            'label' => Mage::helper('pdfgenerator')->__('Discount amount'),
        );

        $variables = array(
            'label' => Mage::helper('pdfgenerator')->__('Discount'),
            'value' => $variables
        );
        return $variables;
    }

    public function getTaxVariables()
    {
        $variables = $this->_getTaxVariables();

        $variables[] = array(
            'value' => '{{var all_tax_ammount}}',
            'label' => Mage::helper('pdfgenerator')->__('All Tax Amount'),
        );

        $variables[] = array(
            'value' => '{{var pseudotaxtable_data}}',
            'label' => Mage::helper('pdfgenerator')->__('Tax pseudo table (<tr></tr> only)'),
        );

        $variables[] = array(
            'value' => '{{var taxtable_data}}',
            'label' => Mage::helper('pdfgenerator')->__('Tax table (class=table-rates-tax)'),
        );

        $variables = array(
            'label' => Mage::helper('pdfgenerator')->__('Tax'),
            'value' => $variables
        );

        return $variables;
    }

    public function getShippingVariables()
    {

        $variables[] = array(
            'value' => '{{var shipping_amount}}',
            'label' => Mage::helper('tax')->__('Shipping (Excl. Tax)'),
        );

        $variables[] = array(
            'value' => '{{var shipping_amountincltax}}',
            'label' => Mage::helper('tax')->__('Shipping (Incl. Tax)'),
        );

        $variables[] = array(
            'value' => '{{var shipping_tax}}',
            'label' => Mage::helper('tax')->__('Shipping Tax)'),
        );

        $variables = array(
            'label' => Mage::helper('pdfgenerator')->__('Shipping'),
            'value' => $variables
        );


        return $variables;
    }

    public function getSubtotalVariables()
    {
        $variables[] = array(
            'value' => '{{var subtotalexcludingtax}}',
            'label' => Mage::helper('tax')->__('Subtotal (Excl. Tax)') . ':',
        );
        $variables[] = array(
            'value' => '{{var subtotalincludingtax}}',
            'label' => Mage::helper('tax')->__('Subtotal (Incl. Tax)') . ':',
        );

        $variables = array(
            'label' => Mage::helper('pdfgenerator')->__('Subtotals'),
            'value' => $variables
        );
        return $variables;
    }

    public function getItemsVariables()
    {
        $productNonSystemAttributes = Mage::helper('pdfgenerator/product');

        $gettingTheVariablesFromArrayKey = array_keys($productNonSystemAttributes->getAllNonSystemAttributes());
        $gettingTheLabelsFromArrayKey = $productNonSystemAttributes->getAllNonSystemAttributes();

        foreach ($gettingTheVariablesFromArrayKey as $variables) {
            $itemVariables[] = array(
                'label' => $gettingTheLabelsFromArrayKey[$variables]['label'],
                'value' => '{{var ' . $variables . '}}'
            );
        }

        $itemVariables[] = array(
            'label' => 'Product list start',
            'value' => '##productlist_start##'
        );

        $itemVariables[] = array(
            'label' => 'Product list end',
            'value' => '##productlist_start##'
        );

        $itemVariables = array(
            'label' => Mage::helper('pdfgenerator')->__('Item Vriables'),
            'value' => $itemVariables
        );
        return $itemVariables;
    }

    public function getItemPriceVariables()
    {

        $itemVariables[] = array(
            'label' => Mage::helper('tax')->__('Price Excl. Tax'),
            'value' => '{{var ' . 'itemcarpticeexcl' . '}}'
        );

        $itemVariables[] = array(
            'label' => Mage::helper('tax')->__('Price Incl. Tax'),
            'value' => '{{var ' . 'itemcarptice' . '}}'
        );

        $itemVariables[] = array(
            'label' => Mage::helper('tax')->__('Subtotal Excl. Tax'),
            'value' => '{{var ' . 'itemsubtotal' . '}}'
        );

        $itemVariables[] = array(
            'label' => Mage::helper('tax')->__('Subtotal Incl. Tax'),
            'value' => '{{var ' . 'itemcarpticeicl' . '}}'
        );


        $itemVariables = array(
            'label' => Mage::helper('pdfgenerator')->__('Item Price Vriables'),
            'value' => $itemVariables
        );
        return $itemVariables;
    }

    public function getItemSystemVariables()
    {
        $itemVariables[] = array(
            'label' => Mage::helper('pdfgenerator')->__('Product Name'),
            'value' => '{{var ' . 'items_name' . '}}'
        );
        $itemVariables[] = array(
            'label' => Mage::helper('pdfgenerator')->__('Item Bundle Optiones Label'),
            'value' => '{{var ' . 'bundle_items_option' . '}}'
        );
        $itemVariables[] = array(
            'label' => Mage::helper('pdfgenerator')->__('SKU'),
            'value' => '{{var ' . 'items_sku' . '}}'
        );
        $itemVariables[] = array(
            'label' => Mage::helper('pdfgenerator')->__('Qty'),
            'value' => '{{var ' . 'items_qty' . '}}'
        );
        $itemVariables[] = array(
            'label' => Mage::helper('pdfgenerator')->__('Tax Amount'),
            'value' => '{{var ' . 'items_tax' . '}}'
        );
        $itemVariables[] = array(
            'label' => Mage::helper('pdfgenerator')->__('Tax Percent'),
            'value' => '{{var ' . 'items_tax_percent' . '}}'
        );
        $itemVariables[] = array(
            'label' => Mage::helper('pdfgenerator')->__('Discount Amount'),
            'value' => '{{var ' . 'items_discount' . '}}'
        );
        $itemVariables[] = array(
            'label' => Mage::helper('pdfgenerator')->__('Product image'),
            'value' => '{{var ' . 'productimage' . '}}'
        );
        $itemVariables[] = array(
            'label' => Mage::helper('pdfgenerator')->__('Product options'),
            'value' => '{{var ' . 'product_options' . '}}'
        );
        $itemVariables[] = array(
            'label' => Mage::helper('pdfgenerator')->__('Product weight'),
            'value' => '{{var ' . 'weight' . '}}'
        );
        $itemVariables[] = array(
            'label' => Mage::helper('pdfgenerator')->__('Product description'),
            'value' => '{{var ' . 'description' . '}}'
        );
        $itemVariables[] = array(
            'label' => Mage::helper('pdfgenerator')->__('Product short description'),
            'value' => '{{var ' . 'short_description' . '}}'
        );
        $itemVariables[] = array(
            'label' => Mage::helper('pdfgenerator')->__('Product url path'),
            'value' => '{{var ' . 'url_path' . '}}'
        );

        $itemVariables = array(
            'label' => Mage::helper('pdfgenerator')->__('Item System Variables'),
            'value' => $itemVariables
        );

        return $itemVariables;
    }

    public function getTheCustomerVariables()
    {
        $variables[] = array(
            'value' => '{{var customer_name}}',
            'label' => Mage::helper('sales')->__('Customer Name'),
        );
        $variables[] = array(
            'value' => '{{var customer_email}}',
            'label' => Mage::helper('sales')->__('Email'),
        );
        $variables[] = array(
            'value' => '{{var customer_group}}',
            'label' => Mage::helper('sales')->__('Customer Group'),
        );
        $variables[] = array(
            'value' => '{{var customer_firstname}}',
            'label' => Mage::helper('customer')->__('First Name'),
        );
        $variables[] = array(
            'value' => '{{var customer_lastname}}',
            'label' => Mage::helper('customer')->__('Last Name'),
        );
        $variables[] = array(
            'value' => '{{var customer_middlename}}',
            Mage::helper('customer')->__('Middle Name/Initial'),
        );
        $variables[] = array(
            'value' => '{{var customer_prefix}}',
            'label' => Mage::helper('customer')->__('Prefix'),
        );
        $variables[] = array(
            'value' => '{{var customer_suffix}}',
            'label' => Mage::helper('customer')->__('Suffix'),
        );
        $variables[] = array(
            'value' => '{{var customer_taxvat}}',
            'label' => Mage::helper('customer')->__('Tax/VAT number'),
        );
        $variables[] = array(
            'value' => '{{var customer_dob}}',
            'label' => Mage::helper('customer')->__('Date Of Birth'),
        );


        $variables = array(
            'label' => Mage::helper('pdfgenerator')->__('Customer'),
            'value' => $variables
        );

        return $variables;
    }

    public function getTheInfoVariables()
    {
        $variables[] = array(
            'value' => '{{var ea_logo_store}}',
            'label' => Mage::helper('tax')->__('Logo'),
        );
        $variables[] = array(
            'value' => '{{var ea_order_number}}',
            'label' => Mage::helper('sales')->__('Order # %s')
        );
        $variables[] = array(
            'value' => '{{var ea_purcase_from_website}}',
            'label' => Mage::helper('sales')->__('Purchased From')
        );
        $variables[] = array(
            'value' => '{{var ea_order_group}}',
            'label' => Mage::helper('sales')->__('Purchased From Store')
        );
        $variables[] = array(
            'value' => '{{var ea_order_store}}',
            'label' => Mage::helper('sales')->__('Purchased From Website')
        );
        $variables[] = array(
            'value' => '{{var ea_order_status}}',
            'label' => Mage::helper('sales')->__('Order Status')
        );
        $variables[] = array(
            'value' => '{{var ea_source_date}}',
            'label' => Mage::helper('sales')->__('Order Date')
        );
        $variables[] = array(
            'value' => '{{var ea_order_totalpaid}}',
            'label' => Mage::helper('sales')->__('Total Paid')
        );
        $variables[] = array(
            'value' => '{{var ea_order_totalrefunded}}',
            'label' => Mage::helper('sales')->__('Total Refunded')
        );
        $variables[] = array(
            'value' => '{{var ea_order_totaldue}}',
            'label' => Mage::helper('sales')->__('Total Due')
        );
        $variables[] = array(
            'value' => '{PAGENO}',
            'label' => Mage::helper('sales')->__('Page Number (Header/Footer)')
        );
        $variables[] = array(
            'value' => '{nbpg}',
            'label' => Mage::helper('sales')->__('Number of pages (Header/Footer)')
        );
        $variables[] = array(
            'value' => '{DATE j-m-Y}',
            'label' => Mage::helper('sales')->__('Print Date (j-m-Y - can be changed)')
        );
        $variables = array(
            'label' => Mage::helper('pdfgenerator')->__('Template'),
            'value' => $variables
        );
        return $variables;
    }

    public function getAddressVariables()
    {
        $variables[] = array(
            'value' => '{{var billing_address}}',
            'label' => Mage::helper('sales')->__('Billing Address'),
        );
        $variables[] = array(
            'value' => '{{var shipping_address}}',
            'label' => Mage::helper('sales')->__('Shipping Address'),
        );

        $variables = array(
            'label' => Mage::helper('pdfgenerator')->__('Address'),
            'value' => $variables
        );
        return $variables;
    }

    public function getShipPayVariables()
    {
        $variables[] = array(
            'value' => '{{var billing_method}}',
            'label' => Mage::helper('sales')->__('Billing Method'),
        );
        $variables[] = array(
            'value' => '{{var billing_method_currency}}',
            'label' => Mage::helper('sales')->__('Order was placed using'),
        );
        $variables[] = array(
            'value' => '{{var shipping_method}}',
            'label' => Mage::helper('sales')->__('Shipping Information'),
        );

        $variables = array(
            'label' => Mage::helper('pdfgenerator')->__('Shipping and Billing'),
            'value' => $variables
        );
        return $variables;
    }

    public function getInvoiceVariables()
    {
        $variables[] = array(
            'value' => '{{var ea_invoice_id}}',
            'label' => Mage::helper('pdfgenerator')->__('Invoice Id'),
        );
        $variables[] = array(
            'value' => '{{var ea_invoice_status}}',
            'label' => Mage::helper('pdfgenerator')->__('Invoice Status'),
        );
        $variables[] = array(
            'value' => '{{var ea_invoice_date}}',
            'label' => Mage::helper('pdfgenerator')->__('Invoice Date'),
        );
        $variables[] = array(
            'value' => '{{var ea_invoice_comments}}',
            'label' => Mage::helper('pdfgenerator')->__('Invoice Comments'),
        );

        $variables = array(
            'label' => Mage::helper('pdfgenerator')->__('Invoice'),
            'value' => $variables
        );
        return $variables;
    }

    private function _getTaxVariables()
    {
        $taxData = Mage::getSingleton('tax/calculation_rate')->getCollection()->getData();

        foreach ($taxData as $tax) {
            $taxTitle = Mage::Helper('core/string')->cleanString($tax['code']);
            $taxTitle = 'tax_' . ereg_replace("[^A-Za-z0-9]", "", $taxTitle);
            $taxTitle = strtolower($taxTitle);
            $percent = $tax['rate'] ? ' (' . round($tax['rate'], 2) . '%)' : '';

            $tax_info[] = array(
                'value' => '{{var ' . $taxTitle . '}}',
                'label' => Mage::helper('pdfgenerator')->__($tax['code']) . $percent . ' '
            );
        }

        return $tax_info;
    }

}
