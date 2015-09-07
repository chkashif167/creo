<?php

/**
 * Description of Template
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Block_Adminhtml_Template_Template extends Mage_Adminhtml_Block_Template
{
    /*
     * Set the default grid view template
     */

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('pdfgenerator/template/list.phtml');
    }

    /*
     * Create blocks for grid and buttons and other stuff we need
     */

    protected function _prepareLayout()
    {
        $this->setChild('add_pdf_button', $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' => Mage::helper('pdfgenerator')->__('Add New PDF Template'),
                'onclick' => "window.location='" . $this->getCreateUrl() . "'",
                'class' => 'add'
            )));
        $this->setChild('grid', $this->getLayout()->createBlock('pdfgenerator/adminhtml_template_pdf_grid', 'pdf.grid.template'));
        return parent::_prepareLayout();
    }

    /*
     * New pdf template location
     */

    public function getCreateUrl()
    {
        return $this->getUrl('*/*/newpdf');
    }

    /*
     * Add the add template button to the list.phtml
     */

    public function getAddButtonHtml()
    {
        $this->setChild('add_pdf_button');
    }

    /*
     * Add the header text using the helper
     */

    public function getHeaderText()
    {
        return Mage::helper('pdfgenerator')->__('PDF Templates');
    }

}
