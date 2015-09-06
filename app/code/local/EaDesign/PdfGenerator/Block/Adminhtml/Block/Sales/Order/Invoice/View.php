<?php

/**
 * Description of View
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Block_Adminhtml_Block_Sales_Order_Invoice_View extends Mage_Adminhtml_Block_Sales_Order_Invoice_View
{
    /*
     * The constructor to get the template
     */

    public function __construct()
    {
        parent::__construct();
        $this->_addButton('printea', array(
                'label' => Mage::helper('pdfgenerator')->__('EaDesign Print PDF'),
                'class' => 'saveea',
                'onclick' => $this->getThePdfSituation()
            )
        );
    }

    /*
     * The url for the print template system
     */

    public function getEaPrintUrl()
    {
        return $this->getEaDesignPrintInvoiceUrl();
    }

    private function getEaDesignPrintInvoiceUrl()
    {
        return $this->getUrl('adminpdfgenerator/adminhtml_pdfgeneratorpdf/invoicepdfgenrator', array(
            'invoice_id' => $this->getInvoice()->getId()
        ));
    }

    private function getThePdfSituation()
    {
        $templateCollection = Mage::getModel('eadesign/pdfgenerator')->getCollection();
        $templateCollection->addFieldToSelect('*')
            ->addFieldToFilter('template_store_id', $this->getCurrentInvoiceOrderStore())
            ->addFieldToFilter('pdft_is_active', 1);

        $templateId = $templateCollection->getData('pdftemplate_id');

        $checkMbstrings = extension_loaded('mbstring');

        if (!$checkMbstrings) {
            $messege = Mage::helper('pdfgenerator')->__('You do not have mbstrings library active. You will get the default Magento Invoice');
            return $location = "confirmSetLocation('{$messege}', '{$this->getPrintUrl()}')";
        }

        if (!empty($templateId)) {
            return $location = 'setLocation(\'' . $this->getEaPrintUrl() . '\')';
        }
        $messege = Mage::helper('pdfgenerator')->__('You do not have a template selected for this invoice. You will get the default Magento Invoice');
        return $location = "confirmSetLocation('{$messege}', '{$this->getPrintUrl()}')";
    }

    private function getCurrentInvoiceOrderStore()
    {
        if ($storeId = $this->getInvoice()->getOrder()->getStore()->getId()) {
            return array(0, $storeId);
        }
        return array(0);
    }

}
