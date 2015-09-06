<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Masspdf
 *
 * @author eadesignpc
 */
class EaDesign_PdfGenerator_Model_Entity_Masspdf extends EaDesign_PdfGenerator_Model_Entity_Pdfgenerator
{

    public $templateId;

    public function getPdfData($invoiceIds, $templateId)
    {
        $this->templateId = $this->getDefaultTemplates();

        if (isset($invoiceIds)) {
            $pdf = $this->loadPdf();

            foreach ($invoiceIds as $invoiceId) {
                $pdfData = Mage::getModel('eadesign/entity_invoicepdf')->getThePdf((int)$invoiceId, (int)$templateId);
                $pdf->SetHTMLHeader($pdfData->getData('htmlheader'));
                $pdf->SetHTMLFooter($pdfData->getData('htmlfooter'));
                $pdf->WriteHTML($pdfData->getData('htmlcss'), 1);

                $pagebreak = '<pagebreak>';
                if ($invoiceId === end($invoiceIds)) {
                    $pagebreak = '';
                }
                $pdf->WriteHTML($pdfData->getData('htmltemplate') . $pagebreak);
            }
        }

        return $pdf->Output('', 'S');
    }

    public function getDefaultTemplates()
    {
        $collection = Mage::getModel('eadesign/pdfgenerator')->getCollection();

        $template = $collection
            ->addFieldToSelect('pdftemplate_id')
            ->addFieldToFilter('pdft_is_active', 1);
        $data = $template->getData();

        if (empty($template)) {
            return 0;
        }
        return $data[0]['pdftemplate_id'];
    }

}
