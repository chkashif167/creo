<?php

class MDN_BarcodeLabel_Block_Adminhtml_System_Config_Preview extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    /**
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return type
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {

        $productId = Mage::getStoreConfig('barcodelabel/preview/product_id');
        $product = Mage::getModel('catalog/product')->load($productId);

        if ($product->getSku())
            $html = '<img src="'.$this->getUrl('BarcodeLabel/Admin/LabelPreview', array('product_id' => $productId)).'">';
        else
            $html = $this->__('Please set an existing product id above');

        return $html;
    }
}