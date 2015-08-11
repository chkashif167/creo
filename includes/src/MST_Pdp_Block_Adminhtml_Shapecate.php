<?php

class MST_Pdp_Block_Adminhtml_Shapecate extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_shapecate';
        $this->_blockGroup = 'pdp';
        $this->_headerText = Mage::helper('pdp')->__('Manage Shape Categories');
        $this->_addButtonLabel = Mage::helper('pdp')->__('Add New Category');
        parent::__construct();
    }
}