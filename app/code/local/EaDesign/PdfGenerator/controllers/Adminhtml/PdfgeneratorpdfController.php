<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PdfgeneratorPdfConstroller
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Adminhtml_PdfgeneratorpdfController extends Mage_Adminhtml_Controller_Action
{

    public function invoicepdfgenratorAction()
    {

        if (!$invoiceId = $this->getRequest()->getParam('invoice_id')) {
            return false;
        }
        try {
            $pdfFile = Mage::getSingleton('eadesign/entity_invoicepdf')->getThePdf((int)$invoiceId, 999922222);
            $this->_prepareDownloadResponse($pdfFile->getData('filename') .
                '.pdf', $pdfFile->getData('pdfbody'), 'application/pdf');
        } catch (Exception $e) {
            Mage::log($e->getMessage());
            return null;
        }
    }

    public function invoicepdfmassAction()
    {
        $ids = $this->getRequest()->getPost('invoice_ids');
        $templateId = $this->getRequest()->getPost('template');

        if (!$templateId) {
            $this->_redirect('adminhtml/sales_invoice');
            $error = Mage::helper('sales')->__('You have no templates selected!');
            Mage::getSingleton('core/session')->addError($error);
            return;
        }

        $pdfData = Mage::getSingleton('eadesign/entity_masspdf')->getPdfData($ids, $templateId);
        $this->_prepareDownloadResponse('ea_invoice_mass_print' .
            '.pdf', $pdfData, 'application/pdf');
    }

    public function orderpdfmassAction()
    {

        $ids = $this->getRequest()->getPost('order_ids');
        $templateId = $this->getRequest()->getPost('template');

        if (!$templateId) {
            $this->_redirect('adminhtml/sales_order');
            $error = Mage::helper('sales')->__('You have no templates selected!');
            Mage::getSingleton('core/session')->addError($error);
            return;
        }

        $invoiceId = array();
        foreach ($ids as $id) {
            $order = Mage::getModel('sales/order')->load($id);
            if ($order->hasInvoices()) {
                foreach ($order->getInvoiceCollection() as $invoiceCollection) {
                    $invoiceId[] = $invoiceCollection->getData('entity_id');
                }
            }
        }

        if (empty($invoiceId)) {
            $this->_redirect('adminhtml/sales_order');
            $error = Mage::helper('sales')->__('You have no files to get');
            Mage::getSingleton('core/session')->addError($error);
            return;
        }

        $pdfData = Mage::getSingleton('eadesign/entity_masspdf')->getPdfData($invoiceId, $templateId);
        $this->_prepareDownloadResponse('ea_invoice_mass_print' .
            '.pdf', $pdfData, 'application/pdf');
    }

}
