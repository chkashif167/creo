<?php

class Progos_Creomob_Block_Configuration extends Mage_Core_Block_Template {


	protected function _construct(){

		parent::_construct();

		$this->_blockGroup = 'progos_creomob_adminhtml';

		$this->_controller = 'brand';

		$this->_headerText = Mage::helper('progos_creomob')->__('Creomob Configuration');
	}


	
}