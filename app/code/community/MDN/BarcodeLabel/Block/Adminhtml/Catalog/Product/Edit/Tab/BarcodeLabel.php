<?php

class MDN_BarcodeLabel_Block_Adminhtml_Catalog_Product_Edit_Tab_BarcodeLabel extends Mage_Adminhtml_Block_Widget implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    protected function _construct() {
        parent::_construct();
        $this->setTemplate('BarcodeLabel/Catalog/Product/View/Tab/BarcodeLabel.phtml');
    }

    public function getProduct()
    {
        return Mage::registry('product');
    }

    public function getLabelImageUrl()
    {
        return $this->getUrl('BarcodeLabel/Admin/LabelPreview', array('product_id' => $this->getProduct()->getId()));
    }

    public function getPrintUrl()
    {
        return $this->getUrl('BarcodeLabel/Admin/PrintProductLabels', array('product_id' => $this->getProduct()->getId()))."count/";
    }

    /**
     * get the URL of a configurable product
     * @return type 
     */
    public function getChildrenPrintUrl()
    {
        return $this->getUrl('BarcodeLabel/Admin/PrintChildrenProductLabels', array('product_id' => $this->getProduct()->getId()))."count/";
    }
    
    /**
     * check if product is configurable (return true) or not (return false) 
     */
    public function isConfigurableProduct(){
        
       $productType = $this->getProduct()->getTypeId();
       
       if( $productType == 'configurable' ){
           return true;
       }
       else { 
           return false;
           
       }
    }
    
    
    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel() {
        return Mage::helper('BarcodeLabel')->__('Barcode Label');
    }

    public function getTabTitle() {
        return Mage::helper('BarcodeLabel')->__('Barcode Label');
    }

    public function canShowTab() {
        return true;
    }

    public function isHidden() {
        return false;
    }

}