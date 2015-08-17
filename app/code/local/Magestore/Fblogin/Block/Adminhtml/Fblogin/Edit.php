<?php

class Magestore_Fblogin_Block_Adminhtml_Fblogin_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'fblogin';
        $this->_controller = 'adminhtml_fblogin';
        
        $this->_updateButton('save', 'label', Mage::helper('fblogin')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('fblogin')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('fblogin_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'fblogin_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'fblogin_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('fblogin_data') && Mage::registry('fblogin_data')->getId() ) {
            return Mage::helper('fblogin')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('fblogin_data')->getTitle()));
        } else {
            return Mage::helper('fblogin')->__('Add Item');
        }
    }
}