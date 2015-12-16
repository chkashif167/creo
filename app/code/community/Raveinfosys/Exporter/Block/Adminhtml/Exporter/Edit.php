<?php

class Raveinfosys_Exporter_Block_Adminhtml_Exporter_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'exporter';
        $this->_controller = 'adminhtml_exporter';
        
        $this->_updateButton('save', 'label', Mage::helper('exporter')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('exporter')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('exporter_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'exporter_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'exporter_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('exporter_data') && Mage::registry('exporter_data')->getId() ) {
            return Mage::helper('exporter')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('exporter_data')->getTitle()));
        } else {
            return Mage::helper('exporter')->__('Add Item');
        }
    }
}