<?php
class VES_Core_Block_Adminhtml_Key extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_key';
		$this->_blockGroup = 'ves_core';
		$this->_headerText = Mage::helper('ves_core')->__('License Key Manager');
		$this->_addButtonLabel 	= Mage::helper('ves_core')->__('Add License Key');
		parent::__construct();
	}
}