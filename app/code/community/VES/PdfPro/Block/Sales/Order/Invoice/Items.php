<?php
/**
 * VES_PdfPro_Block_Sales_Order_Invoice_Items
 *
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */
class VES_PdfPro_Block_Sales_Order_Invoice_Items extends Mage_Sales_Block_Order_Invoice_Items
{
    public function getPrintInvoiceUrl($invoice)
    {
        if(!Mage::getStoreConfig('pdfpro/config/enabled') || !Mage::getStoreConfig('pdfpro/config/allow_customer_print') || !Mage::helper('pdfpro')->getDefaultApiKey()) return parent::getPrintInvoiceUrl($invoice);
    	if(Mage::getSingleton('customer/session')->isLoggedIn()) return Mage::getUrl('pdfpro/print/invoice', array('invoice_id' => $invoice->getId()));
    	return Mage::getUrl('pdfpro/guest/printInvoice', array('invoice_id' => $invoice->getId()));
    }

    public function getPrintAllInvoicesUrl($order)
    {
    	if(!Mage::getStoreConfig('pdfpro/config/enabled') || !Mage::getStoreConfig('pdfpro/config/allow_customer_print') || !Mage::helper('pdfpro')->getDefaultApiKey()) return parent::getPrintAllInvoicesUrl($order);
        if(Mage::getSingleton('customer/session')->isLoggedIn()) return Mage::getUrl('pdfpro/print/invoices', array('order_id' => $order->getId()));
        return Mage::getUrl('pdfpro/guest/printInvoices', array('order_id' => $order->getId()));
    }
}
