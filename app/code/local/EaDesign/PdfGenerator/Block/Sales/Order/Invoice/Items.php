<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Invoice
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Block_Sales_Order_Invoice_Items extends Mage_Sales_Block_Order_Invoice_Items
{

    public function getPdfPrintInvoiceUrl($invoice)
    {
        if ($this->getPdfSituation()) {
            return Mage::getUrl('pdfgenerator/index/invoicepdfgenrator', array('invoice_id' => $invoice->getId()));
        }
    }

    /**
     * Need to move the check to a helper - also added to mail
     * @return type
     */
    public function getPdfSituation()
    {
        return $this->_getThePdfSituation();
    }

    private function _getThePdfSituation()
    {
        $templateCollection = Mage::getModel('eadesign/pdfgenerator')->getCollection();
        $templateCollection->addFieldToSelect('*')
            ->addFieldToFilter('template_store_id', $this->_getCurrentInvoiceOrderStore())
            ->addFieldToFilter('pdft_is_active', 1);

        $templateId = $templateCollection->getData('pdftemplate_id');

        $checkMbstrings = extension_loaded('mbstring');

        if (!$checkMbstrings) {
            return false;
        }

        if (!empty($templateId)) {
            return true;
        }
        return false;
    }

    private function _getCurrentInvoiceOrderStore()
    {
        $order = Mage::registry('current_order');
        if ($storeId = $order->getStore()->getId()) {
            return array(0, $storeId);
        }
        return array(0);
    }

}

?>
