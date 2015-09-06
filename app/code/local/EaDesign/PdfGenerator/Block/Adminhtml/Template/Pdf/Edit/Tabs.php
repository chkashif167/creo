<?php

/**
 * Description of Tabs
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Block_Adminhtml_Template_Pdf_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    /**
     * Initializa the tab system
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('pdfgenerator_tabs');
        $this->setDestElementId('pdf_template_new_form');
        $this->setTitle(Mage::helper('pdfgenerator')->__('PDF Settings'));
    }

    /**
     * Generate the teab system to send tot he template.
     */
    public function _beforeToHtml()
    {
        $this->addTab('general_section', array(
            'label' => Mage::helper('pdfgenerator')->__('Template information'),
            'title' => Mage::helper('pdfgenerator')->__('Template information'),
            'content' => $this->getLayout()->createBlock('pdfgenerator/adminhtml_template_pdf_edit_tabs_general')->toHtml(),
        ));
        $this->addTab('main_section', array(
            'label' => Mage::helper('pdfgenerator')->__('Page contents'),
            'title' => Mage::helper('pdfgenerator')->__('Page contents'),
            'content' => $this->getLayout()->createBlock('pdfgenerator/adminhtml_template_pdf_edit_tabs_main')->toHtml(),
        ));
        $this->addTab('header_section', array(
            'label' => Mage::helper('pdfgenerator')->__('Header contents'),
            'title' => Mage::helper('pdfgenerator')->__('Header contents'),
            'content' => $this->getLayout()->createBlock('pdfgenerator/adminhtml_template_pdf_edit_tabs_header')->toHtml(),
        ));
        $this->addTab('footer_section', array(
            'label' => Mage::helper('pdfgenerator')->__('Footer contents'),
            'title' => Mage::helper('pdfgenerator')->__('Footer contents'),
            'content' => $this->getLayout()->createBlock('pdfgenerator/adminhtml_template_pdf_edit_tabs_footer')->toHtml(),
        ));
        $this->addTab('settings_css', array(
            'label' => Mage::helper('pdfgenerator')->__('Template Css'),
            'title' => Mage::helper('pdfgenerator')->__('Template Css'),
            'content' => $this->getLayout()->createBlock('pdfgenerator/adminhtml_template_pdf_edit_tabs_css')->toHtml(),
        ));
        $this->addTab('settings_section', array(
            'label' => Mage::helper('pdfgenerator')->__('Settings'),
            'title' => Mage::helper('pdfgenerator')->__('Settings'),
            'content' => $this->getLayout()->createBlock('pdfgenerator/adminhtml_template_pdf_edit_tabs_settings')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

}

?>
