<?php

class MDN_BarcodeLabel_Block_System_Config_Button_Export extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = $this->getUrl('BarcodeLabel/Admin/Export');

        $html = '';

        $html .= $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setType('button')
                ->setClass('scalable')
                ->setLabel($this->__('Export'))
                ->setOnClick("setLocation('$url')")
                ->toHtml();

        return $html;
    }
}