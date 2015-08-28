<?php
class Mango_Attributeswatches_Block_Adminhtml_Attributeswatches extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_attributeswatches';
    $this->_blockGroup = 'attributeswatches';
    $this->_headerText = Mage::helper('attributeswatches')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('attributeswatches')->__('Add Item');
    parent::__construct();
    $this->removeButton("add");
  }
}