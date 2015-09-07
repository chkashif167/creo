<?php

class MDN_BarcodeLabel_Block_System_Config_Button_ManageList extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = $this->getUrl('BarcodeLabel/Admin/ManageList');

        $html = '';
        
        $listHelper = Mage::helper('BarcodeLabel/List');
        $html .= $this->__('Barcode count : %s', $listHelper->getCount());
        $html .= '<br>'.$this->__('Available : %s', $listHelper->getAvailableCount());
        
        $html .= '<br>'.$this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('scalable')
                    ->setLabel($this->__('Manage list'))
                    ->setOnClick("setLocation('$url')")
                    ->toHtml();

        return $html;
    }
}