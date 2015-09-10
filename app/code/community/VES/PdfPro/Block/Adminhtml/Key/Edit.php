<?php

class VES_PdfPro_Block_Adminhtml_Key_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'pdfpro';
        $this->_controller = 'adminhtml_key';
        
        $this->_updateButton('save', 'label', Mage::helper('pdfpro')->__('Save API Key'));
        $this->_updateButton('delete', 'label', Mage::helper('pdfpro')->__('Delete API Key'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'submitAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function submitAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('key_data') && Mage::registry('key_data')->getId() ) {
            return Mage::helper('pdfpro')->__("Edit API Key '%s'", $this->htmlEscape(Mage::registry('key_data')->getId()));
        } else {
            return Mage::helper('pdfpro')->__('Add API Key');
        }
    }
}