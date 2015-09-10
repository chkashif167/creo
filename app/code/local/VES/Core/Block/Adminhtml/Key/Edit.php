<?php

class VES_Core_Block_Adminhtml_Key_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'ves_core';
        $this->_controller = 'adminhtml_key';
        if( Mage::registry('key_data') && Mage::registry('key_data')->getId() ) {
        	$this->_updateButton('save', 'label', Mage::helper('ves_core')->__('Update License Key'));
        }else{
        	$this->_updateButton('save', 'label', Mage::helper('ves_core')->__('Save License Key'));
        }
        $this->_updateButton('delete', 'label', Mage::helper('ves_core')->__('Delete License Key'));
    }

    public function getHeaderText()
    {
        if( Mage::registry('key_data') && Mage::registry('key_data')->getId() ) {
            return Mage::helper('ves_core')->__("View License Key '%s'", $this->htmlEscape(Mage::registry('key_data')->getLicenseKey()));
        } else {
            return Mage::helper('ves_core')->__('Add License Key');
        }
    }
}