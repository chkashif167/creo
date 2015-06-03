<?php
class EM_Quickshop_Block_Adminhtml_Quickshop extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_quickshop';
    $this->_blockGroup = 'quickshop';
    $this->_headerText = Mage::helper('quickshop')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('quickshop')->__('Add Item');
    parent::__construct();
  }
}