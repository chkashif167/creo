<?php
class Raveinfosys_Exporter_Block_Adminhtml_Exporter extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_exporter';
    $this->_blockGroup = 'exporter';
    $this->_headerText = Mage::helper('exporter')->__('Order Export');
    $this->_addButtonLabel = Mage::helper('exporter')->__('Export All Orders');
    parent::__construct();
  }
}