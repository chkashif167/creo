<?php

class Tentura_Ngroups_Block_Adminhtml_Ngroups extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_ngroups';
        $this->_blockGroup = 'ngroups';
        $this->_headerText = Mage::helper('ngroups')->__('Newsletter Groups');
        $this->_addButtonLabel = Mage::helper('ngroups')->__('Add Group');
        parent::__construct();
    }

}
