<?php

/**
 * Description of Edit
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Block_Adminhtml_Template_Pdf_New extends Mage_Adminhtml_Block_Widget
{
    /*
     * The internal constructor to set the template for new templates!
     */

    protected function _construct()
    {
        $this->setTemplate('pdfgenerator/template/new/new.phtml');
        parent::_construct();
    }

    /**
     * Preparing the layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->setChild('back_button', $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                    'label' => Mage::helper('adminhtml')->__('Back'),
                    'onclick' => "window.location.href = '" . $this->getUrl('*/*') . "'",
                    'class' => 'back'
                )
            )
        );

        $this->setChild('select_button', $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                    'label' => Mage::helper('adminhtml')->__('Continue To PDF Template Type'),
                    'onclick' => "getSelectedItem()",
                    'type' => 'button',
                    'class' => 'select'
                )
            )
        );

        $this->setChild('form', $this->getLayout()->createBlock('pdfgenerator/adminhtml_template_pdf_edit_tabs')
        );

        return parent::_prepareLayout();
    }

    /*
     * New template back button
     * 
     */

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    /*
     * New template select type button
     */

    public function getSelectButtonHtml()
    {
        return $this->getChildHtml('select_button');
    }

    /**
     * Return header text for form
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('adminhtml')->__('Select New PDF Template Type');
    }

    /**
     * Return form block HTML
     *
     * @return string
     */
    public function getFormHtml()
    {
        return $this->getChildHtml('form');
    }

    /*
     * The first option to select the template for.
     * 
     * Need to make constants and to check the system!
     */

    public function getPdfTemplateOptions()
    {
        $options = array(
            array('value' => EaDesign_PdfGenerator_Model_Pdfgenerator::INVOICETEMPLATE, 'label' => Mage::helper('adminhtml')->__('Invoices')),
        );

        return $options;
    }

    /**
     * Get the current locale - need to check if needed
     *
     * @return string
     */
    public function getCurrentLocale()
    {
        return Mage::app()->getLocale()->getLocaleCode();
    }

}
