<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Observer
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Model_Observer
{

    public $invoiceId;

    public function beforeSendInvoice($observer)
    {
        $invoiceId = $observer->getEvent()->getObject()->getId();

        $this->invoiceId = $invoiceId;

        if ($this->getPdfSituation()) {


            if ($invoiceId) {
                $pdfFile = Mage::getSingleton('eadesign/entity_invoicepdf')->getThePdf((int)$invoiceId, 999922222);
            }


            $mailObj = $observer->getEvent()->getTemplate();

            $mailObj->getMail()->createAttachment(
                $pdfFile->getData('pdfbody')
                , 'application/pdf'
                , Zend_Mime::DISPOSITION_ATTACHMENT
                , Zend_Mime::ENCODING_BASE64
                , $pdfFile->getData('filename') . '.pdf'
            );
        }
    }

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
        $invoice = Mage::getModel('sales/order_invoice')->load($this->invoiceId);

        if ($storeId = $invoice->getStore()->getId()) {
            return array(0, $storeId);
        }
        return array(0);
    }

}

