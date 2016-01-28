<?php

class FreeLunchLabs_CloudFront_Block_Adminhtml_Testconnection extends Mage_Adminhtml_Block_System_Config_Form_Field {

    protected function _prepareLayout() {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('cloudfront/testconnection.phtml');
        }
        return $this;
    }

    public function render(Varien_Data_Form_Element_Abstract $element) {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {

        $this->addData(array(
            'button_label' => 'Create New Distribution',
            'html_id' => $element->getHtmlId(),
            'ajax_url' => Mage::getSingleton('adminhtml/url')->getUrl('*/testconnection/ping')
        ));

        return $this->_toHtml();
    }

}
