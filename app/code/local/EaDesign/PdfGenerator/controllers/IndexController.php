<?php

/**
 * We need to chege this one to the front. We will see!!!
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_IndexController extends Mage_Sales_Controller_Abstract
{

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function invoicepdfgenratorAction()
    {

        $invoiceId = (int)$this->getRequest()->getParam('invoice_id');
        if ($invoiceId) {
            $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
            $order = $invoice->getOrder();
        } else {
            $orderId = (int)$this->getRequest()->getParam('order_id');
            $order = Mage::getModel('sales/order')->load($orderId);
        }


        if ($this->_canViewOrder($order)) {
            Mage::register('current_order', $order);
            if (isset($invoice)) {
                Mage::register('current_invoice', $invoice);
            }
        } else {
            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                $this->_redirect('sales/order/history/');
            } else {
                $this->_redirect('sales/guest/form');
            }
        }


        try {
            $pdfFile = Mage::getSingleton('eadesign/entity_invoicepdf')->getThePdf((int)$invoiceId);
            $this->_prepareDownloadResponse($pdfFile->getData('filename') .
                '.pdf', $pdfFile->getData('pdfbody'), 'application/pdf');
        } catch (Exception $e) {
            Mage::log($e->getMessage());
            return null;
        }
    }

}
