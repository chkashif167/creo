<?php


class Mks_Responsivebannerslider_Block_Adminhtml_Responsivebannerslider extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_responsivebannerslider";
	$this->_blockGroup = "responsivebannerslider";
	$this->_headerText = Mage::helper("responsivebannerslider")->__("Responsivebannerslider Manager");
	$this->_addButtonLabel = Mage::helper("responsivebannerslider")->__("Add New Item");
	parent::__construct();
	
	}

}