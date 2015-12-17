<?php

class MST_Pdp_Block_Adminhtml_Artworkcate extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_artworkcate';
        $this->_blockGroup = 'pdp';
        $this->_headerText = Mage::helper('pdp')->__('Manage Artwork Categories');
        $this->_addButtonLabel = Mage::helper('pdp')->__('Add New Category');
        parent::__construct();
    }
}