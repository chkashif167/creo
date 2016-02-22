<?php

class Tentura_Ngroups_Block_Adminhtml_Ngroups_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'ngroups';
        $this->_controller = 'adminhtml_ngroups';
        
        $this->_updateButton('save', 'label', Mage::helper('ngroups')->__('Save Item'));
        $this->_updateButton('save', 'onclick', 'saveGroup()');
        
        $this->_updateButton('delete', 'label', Mage::helper('ngroups')->__('Delete Item'));


        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('ngroups_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'ngroups_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'ngroups_content');
                }
            }

            function clearRequired()
            {
                $('subscriberGrid_massaction-select').removeClassName('required-entry');
                 try{
                $('oldsubscriberGrid_massaction-select').removeClassName('required-entry');
                 }catch(e){}
            }

            Event.observe(window, 'load', clearRequired);


            function saveGroup(){
                clearRequired();
                $('customers').value = subscriberGrid_massactionJsObject.checkedString;
                try{
                $('deletecustomers').value = oldsubscriberGrid_massactionJsObject.checkedString;
                }catch(e){}
                editForm.submit($('edit_form').action);
            }
            function saveAndContinueEdit(){
                clearRequired();
                $('customers').value = subscriberGrid_massactionJsObject.checkedString;
                try{
                $('deletecustomers').value = oldsubscriberGrid_massactionJsObject.checkedString;
                }catch(e){}
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('ngroups_data') && Mage::registry('ngroups_data')->getId() ) {
            return Mage::helper('ngroups')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('ngroups_data')->getTitle()));
        } else {
            return Mage::helper('ngroups')->__('Add Item');
        }
    }
}