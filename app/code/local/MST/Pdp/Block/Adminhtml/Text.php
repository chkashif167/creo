<?php

class MST_Pdp_Block_Adminhtml_Text extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_text';
        $this->_blockGroup = 'pdp';
        $this->_headerText = Mage::helper('pdp')->__('Manage Texts');
        $this->_addButtonLabel = Mage::helper('pdp')->__('Add New Text');
        parent::__construct();
           $this->_addButton('importcolor', array(
        'label'     => $this->__('Import Text'),
        'onclick'   => 'setLocation(\'' . $this->getUrlImportText() .'\')',
        'class'     => 'importcolor',
    ));
    }
    function getUrlImportText()
    {
       
         return $this->getUrl('*/adminhtml_importtext/edit');
    }
}