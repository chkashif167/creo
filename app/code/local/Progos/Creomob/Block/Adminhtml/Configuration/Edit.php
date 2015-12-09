<?php


class Progos_Creomob_Block_Adminhtml_Configuration_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'progos_creomob_adminhtml';
        $this->_controller = 'configuration';

        
        $this->_mode = 'edit';

        $newOrEdit = $this->getRequest()->getParam('id')
            ? $this->__('Edit')
            : $this->__('New');
        $this->_headerText =  $newOrEdit . ' ' . $this->__('Configuration');
    }
}