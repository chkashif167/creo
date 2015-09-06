<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Invoicepdf
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Model_Entity_Invoicepdf extends EaDesign_PdfGenerator_Model_Entity_Pdfgenerator
{

    /**
     * The id of the invoice
     * @var int
     */
    public $invoiceId;

    public $templateId;

    private $storeId;

    public function getTheInvoice()
    {
        $invoice = Mage::getModel('sales/order_invoice')->load($this->invoiceId);
        $this->storeId = $invoice->getOrder()->getStore()->getId();
        return $invoice;
    }

    /**
     * Get the invoice id and create the vars for teh invoice
     * @param type $invoiceId The invoice id
     */
    public function getThePdf($invoiceId, $templateId)
    {
        $this->invoiceId = $invoiceId;
        $this->setVars(Mage::helper('pdfgenerator')->processAllVars($this->collectVars()));

        if ($templateId === 999922222) {
            $this->templateId = $this->getDefaultTemplates();
        } else {
            $this->templateId = $templateId;
        }
        return $this->getPdf();
    }

    /**
     * Collect the vars for the template to be processed
     * @return array
     */
    public function collectVars()
    {
        $grandTotal = Mage::getModel('eadesign/entity_totals_grandtotal')
            ->setSource($this->getTheInvoice())->setOrder($this->getTheInvoice()->getOrder())
            ->getTotalsForDisplay();
        $subTotal = Mage::getModel('eadesign/entity_totals_subtotal')
            ->setSource($this->getTheInvoice())->setOrder($this->getTheInvoice()->getOrder())
            ->getTotalsForDisplay();
        $shippingTotal = Mage::getModel('eadesign/entity_totals_shipping')
            ->setSource($this->getTheInvoice())->setOrder($this->getTheInvoice()->getOrder())
            ->getTotalsForDisplay();
        // need to check the tax system 
        $taxTotal = Mage::getModel('eadesign/entity_totals_tax')
            ->setSource($this->getTheInvoice())->setOrder($this->getTheInvoice()->getOrder())
            ->getTotalsForDisplay();
        //need to check the discount system
        $discountTotal = Mage::getModel('eadesign/entity_totals_discount')
            ->setSource($this->getTheInvoice())->setOrder($this->getTheInvoice()->getOrder())
            ->getTotalsForDisplay();

        $leftInfoBlock = Mage::getModel('eadesign/entity_additional_info')
            ->setSource($this->getTheInvoice())
            ->setOrder($this->getTheInvoice()->getOrder())
            ->getTheInfoMergedVariables();

        $vars = array_merge($subTotal, $grandTotal, $shippingTotal, $taxTotal, $discountTotal, $leftInfoBlock);

        return $vars;
    }

    public function getDefaultTemplates()
    {
        $storeid = $this->storeId;

        $template = Mage::getModel('eadesign/pdfgenerator')->getCollection()
            ->addFieldToSelect('pdftemplate_id')
            ->addFieldToFilter('template_store_id', $storeid)
            ->addFieldToFilter('pdft_is_active', 1)
            ->addFieldToFilter('pdft_default', 1);

        $templateByStore = $template->getData();

        if (empty($templateByStore)) {
            $templateAll = Mage::getModel('eadesign/pdfgenerator')->getCollection()
                ->addFieldToSelect('pdftemplate_id')
                ->addFieldToFilter('pdft_default', 1)
                ->addFieldToFilter('pdft_is_active', 1);

            $dataNd = $templateAll->getData();

            if (empty($dataNd)) {
                $templateAllLast = Mage::getModel('eadesign/pdfgenerator')->getCollection()
                    ->addFieldToSelect('pdftemplate_id')
                    ->addFieldToFilter('pdft_is_active', 1);
                $dataNda = $templateAllLast->getData();
                return $dataNda[0]['pdftemplate_id'];
            } else {
                return $dataNd[0]['pdftemplate_id'];
            }

        } else {
            $data = $template->getData();
            return $data[0]['pdftemplate_id'];
        }
    }

}
