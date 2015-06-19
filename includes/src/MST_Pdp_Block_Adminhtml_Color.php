<?php



class MST_Pdp_Block_Adminhtml_Color extends Mage_Adminhtml_Block_Widget_Grid_Container

{



    public function __construct()

    {

        $this->_controller = 'adminhtml_color';

        $this->_blockGroup = 'pdp';

        $this->_headerText = Mage::helper('pdp')->__('Manage Colors');

        $this->_addButtonLabel = Mage::helper('pdp')->__('Add New Color');

        parent::__construct();
          $this->_addButton('importcolor', array(
        'label'     => $this->__('Import Color'),
        'onclick'   => 'setLocation(\'' . $this->getImportColor() .'\')',
        'class'     => 'importcolor',
    ));
    }
     function getImportColor()
    {
       
         return $this->getUrl('*/adminhtml_importcolor/edit');
    }

}