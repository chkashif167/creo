<?php

class EM_Quickshop_Block_Adminhtml_Quickshop_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'quickshop';
        $this->_controller = 'adminhtml_quickshop';
        
        $this->_updateButton('save', 'label', Mage::helper('quickshop')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('quickshop')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('quickshop_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'quickshop_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'quickshop_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('quickshop_data') && Mage::registry('quickshop_data')->getId() ) {
            return Mage::helper('quickshop')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('quickshop_data')->getTitle()));
        } else {
            return Mage::helper('quickshop')->__('Add Item');
        }
    }
}