<?php

class Mango_Attributeswatches_Block_Adminhtml_Attributeswatches_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'attributeswatches';
        $this->_controller = 'adminhtml_attributeswatches';
        
        $this->_updateButton('save', 'label', Mage::helper('attributeswatches')->__('Save Item'));
        /*$this->_updateButton('delete', 'label', Mage::helper('attributeswatches')->__('Delete Item'));*/
		
        /*$this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);*/

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('attributeswatches_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'attributeswatches_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'attributeswatches_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('attributeswatches_data') && Mage::registry('attributeswatches_data')->getId() ) {
            return Mage::helper('attributeswatches')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('attributeswatches_data')->getValue()));
        } else {
            return Mage::helper('attributeswatches')->__('Add Item');
        }
    }
}