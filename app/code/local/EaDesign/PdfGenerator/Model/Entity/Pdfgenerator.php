<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Pdfgenerator
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Model_Entity_Pdfgenerator extends EaDesign_PdfGenerator_Model_Entity_Abstract
{

    const THE_START = '##productlist_start##';
    const THE_END = '##productlist_end##';
    const PAPER_FORMAT_A4 = 'A4';
    const PAPER_FORMAT_A3 = 'A3';
    const PAPER_FORMAT_A5 = 'A5';
    const PAPER_FORMAT_A6 = 'A6';
    const PAPER_FORMATA_LETTER = 'Letter';
    const PAPER_FORMATA_LEGAL = 'Legal';
    const PAPER_ORIENTATION_P = '';
    const PAPER_ORIENTATION_L = '-L';

    /**
     *
     * @var type object pdf generator collection
     */
    public $pdfCollection;

    /**
     * The pdf proceesed template
     * @var string
     */
    protected $_pdfProcessedTemplate;

    /**
     * Load the pdf system
     * @return Mpdf Object - lib
     */
    protected function _construct()
    {
        $this->setPdfCollection();
        parent::_construct();
    }

    public function setTheSourceId($invoiceId)
    {
        $this->_sourceId = $invoiceId;
        return $this->_sourceId;
    }

    public function getTheSourceId()
    {
        return $this->_sourceId;
    }

    public function loadPdf()
    {
        $top = $this->createTemplate()->getData('pdftm_top');
        $bottom = $this->createTemplate()->getData('pdftm_bottom');
        $left = $this->createTemplate()->getData('pdftm_left');
        $right = $this->createTemplate()->getData('pdftm_right');

        $pdf = new Mpdf_Mpdfstart('', $this->pdfPaperFormat(), 8, '', $left, $right, $top, $bottom);
        $pdf->shrink_tables_to_fit = 0;
        $pdf->useOnlyCoreFonts = true;
        return $pdf;
    }

    public function pdfPaperFormat()
    {
        $isCustom = $this->createTemplate()->getData('pdftc_customchek');
        $orientation = $this->createTemplate()->getData('pdft_orientation');

        if ($orientation && !$isCustom) {
            $format = $this->createTemplate()->getData('pdftp_format');

            if ($format == 0 && $orientation == 'portrait') {
                return self::PAPER_FORMAT_A4 . self::PAPER_ORIENTATION_P;
            }
            if ($format == 1 && $orientation == 'portrait') {
                return self::PAPER_FORMAT_A3 . self::PAPER_ORIENTATION_P;
            }
            if ($format == 2 && $orientation == 'portrait') {
                return self::PAPER_FORMAT_A5 . self::PAPER_ORIENTATION_P;
            }
            if ($format == 3 && $orientation == 'portrait') {
                return self::PAPER_FORMAT_A6 . self::PAPER_ORIENTATION_P;
            }
            if ($format == 4 && $orientation == 'portrait') {
                return self::PAPER_FORMATA_LETTER . self::PAPER_ORIENTATION_P;
            }
            if ($format == 5 && $orientation == 'portrait') {
                return self::PAPER_FORMATA_LEGAL . self::PAPER_ORIENTATION_P;
            }
            if ($format == 0 && $orientation == 'landscape') {
                return self::PAPER_FORMAT_A4 . self::PAPER_ORIENTATION_L;
            }
            if ($format == 1 && $orientation == 'landscape') {
                return self::PAPER_FORMAT_A3 . self::PAPER_ORIENTATION_L;
            }
            if ($format == 2 && $orientation == 'landscape') {
                return self::PAPER_FORMAT_A5 . self::PAPER_ORIENTATION_L;
            }
            if ($format == 3 && $orientation == 'landscape') {
                return self::PAPER_FORMAT_A6 . self::PAPER_ORIENTATION_L;
            }
            if ($format == 4 && $orientation == 'landscape') {
                return self::PAPER_FORMATA_LETTER . self::PAPER_ORIENTATION_L;
            }
            if ($format == 5 && $orientation == 'landscape') {
                return self::PAPER_FORMATA_LEGAL . self::PAPER_ORIENTATION_L;
            }
            return '';
        } elseif ($isCustom) {
            $pdftCustomwidth = $this->createTemplate()->getData('pdft_customwidth');
            $pdftCustomheight = $this->createTemplate()->getData('pdft_customheight');
            if ($pdftCustomwidth && $pdftCustomheight) {
                return array($pdftCustomwidth, $pdftCustomheight);
            }
        }
        return '';
    }

    public function setPdfCollection()
    {
        $this->pdfCollection = Mage::getModel('eadesign/pdfgenerator')->getCollection();
        return $this;
    }

    public function getPdfCollection()
    {
        return $this->pdfCollection;
    }

    public function getTheStoreId()
    {
        if ($storeId = $this->getTheInvoice()->getOrder()->getStore()->getId()) {
            return array(0, $storeId);
        }
        return array(0);
    }

    public function getFilteredCollection()
    {
        try {
            if ($this->templateId) {
                $pdfGeneratorTemplate = $this->getPdfCollection()
                    ->addFieldToSelect('*')
                    ->addFieldToFilter('pdftemplate_id', $this->templateId);
            } else {
                $pdfGeneratorTemplate = $this->getPdfCollection()
                    ->addFieldToSelect('*')
                    ->addFieldToFilter('pdft_is_active', 1)
                    ->addFieldToFilter('template_store_id', $this->getTheStoreId());
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage());
            return null;
        }
        return $pdfGeneratorTemplate;
    }

    public function createTemplate()
    {
        $pdfCollection = $this->getFilteredCollection();
        foreach ($pdfCollection as $pdf) {
            $dataTemplate = $pdf;
        }

        if ($dataTemplate) {
            return $dataTemplate;
        }
        return false;
    }

    /**
     * Create the header of the pdf
     *
     */
    public function getFileName()
    {
        if ($fileName = $this->createTemplate()->getData('pdft_filename')) {
            $templateVars = $this->getVars();
            $headerTemplate = Mage::helper('pdfgenerator')->setTheTemplateLayout($fileName);
            $processedTemplate = $headerTemplate->getProcessedTemplate($templateVars);

            $cleanString = Mage::helper('core/string')->cleanString($processedTemplate);
            $cleanString = str_replace(array(' ', '.', ':'), '-', $processedTemplate);
            return $cleanString;
        }
        return 'invoice - ';
    }

    /**
     * Create the header of the pdf
     *
     */
    public function getHeader()
    {
        if ($header = $this->createTemplate()->getData('pdfth_header')) {
            $templateVars = $this->getVars();
            $headerTemplate = Mage::helper('pdfgenerator')->setTheTemplateLayout($header);
            $processedTemplate = $headerTemplate->getProcessedTemplate($templateVars);
            return $processedTemplate;
        }
        return false;
    }

    /**
     * Create the pdf footer
     *
     */
    public function getFooter()
    {
        if ($footer = $this->createTemplate()->getData('pdftf_footer')) {
            $templateVars = $this->getVars();
            $headerTemplate = Mage::helper('pdfgenerator')->setTheTemplateLayout($footer);
            $processedTemplate = $headerTemplate->getProcessedTemplate($templateVars);
            return $processedTemplate;
        }
        return false;
    }

    public function getCss()
    {
        if ($css = $this->createTemplate()->getData('pdft_css')) {
            return $css;
        }
        return false;
    }

    /**
     * Create the body of the pdf
     */
    public function getBody()
    {

        if ($body = $this->createTemplate()->getData('pdftemplate_body')) {
            return $body;
        }
        return false;
    }

    /**
     * Get the template body from used in the backend with the varables and add the item variables.
     * @return string
     */
    public function getTheTemplateBodyWithItems()
    {
        $itemsHelper = Mage::helper('pdfgenerator/items');
        $templateToProcessForItems = $this->getBody();

        $items = Mage::getModel('eadesign/entity_items')
            ->setSource($this->getTheInvoice())->setOrder($this->getTheInvoice()->getOrder());
        $itemsData = $items->processAllVars();

        if (!$itemsHelper->substrCount($templateToProcessForItems, self::THE_START) === $itemsHelper->substrCount($templateToProcessForItems, self::THE_END)) {
            return false;
        }

        $result = Mage::helper('pdfgenerator/items')
            ->getTheItemsFromBetwin($templateToProcessForItems, self::THE_START, self::THE_END);

        $i = 1;
        foreach ($itemsData as $templateVars) {
            $itemPosition = array('items_position' => $i++);
            $templateVars = array_merge($itemPosition, $templateVars);

            $pdfProcessTemplate = Mage::getModel('core/email_template');
            $itemProcess = $pdfProcessTemplate->setTemplateText($result)->getProcessedTemplate($templateVars);
            $finalItems .= $itemProcess . '<br>';
        }
        $templateWithItemsProcessed = str_replace($result, $finalItems, $templateToProcessForItems);


        $tempmplateForHtmlProcess = '<html>' . $templateWithItemsProcessed . '</html>';

        $htmlTemplateWithItems = Mage::helper('pdfgenerator/items')->processHtml($tempmplateForHtmlProcess);
        return $htmlTemplateWithItems;
    }

    /**
     * Load the default information for the template processing
     * @return object Mail template object
     */
    public function mainVariableProcess()
    {
        $templateText = $this->getTheTemplateBodyWithItems();
        $theVariableProcessor = Mage::helper('pdfgenerator')->setTheTemplateLayout($templateText);
        return $theVariableProcessor;
    }

    /**
     * The vars for the entity
     * @return type
     */
    public function getTheProcessedTemplate()
    {
        $templateVars = $this->getVars();
        $processedTemplate = $this->mainVariableProcess()->getProcessedTemplate($templateVars);
        return $processedTemplate;
    }

    public function getPdf()
    {
        $mailPdf = new Varien_Object;

        $pdf = $this->loadPdf();

        $templateBody = $this->getTheProcessedTemplate();

        /* Html mass data */
        $mailPdf->setData('htmltemplate', $templateBody);
        $mailPdf->setData('htmlheader', $this->getHeader());
        $mailPdf->setData('htmlfooter', $this->getFooter());
        $mailPdf->setData('htmlcss', $this->getCss());

        $pdf->SetHTMLHeader($this->getHeader());
        $pdf->SetHTMLFooter($this->getFooter());

        $pdf->WriteHTML($this->getCss(), 1);
        $pdf->WriteHTML($templateBody);


        $output = $pdf->Output($this->getFileName(), 'S');

        $mailPdf->setData('pdfbody', $output);
        $mailPdf->setData('filename', $this->getFileName());

        return $mailPdf;
    }

}
