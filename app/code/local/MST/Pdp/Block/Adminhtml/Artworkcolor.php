<?php
class MST_Pdp_Block_Adminhtml_Artworkcolor extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_pdp';
        $this->_blockGroup = 'pdp';
        $_helper = Mage::helper('pdp');
        parent::__construct(); 
    }
}