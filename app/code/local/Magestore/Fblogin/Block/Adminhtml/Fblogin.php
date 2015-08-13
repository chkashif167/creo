<?php
class Magestore_Fblogin_Block_Adminhtml_Fblogin extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_fblogin';
    $this->_blockGroup = 'fblogin';
    $this->_headerText = Mage::helper('fblogin')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('fblogin')->__('Add Item');
    parent::__construct();
  }
}