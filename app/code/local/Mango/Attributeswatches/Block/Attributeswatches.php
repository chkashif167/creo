<?php

class Mango_Attributeswatches_Block_Attributeswatches extends Mage_Core_Block_Template {

    protected $_product = null;

    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function getAttributeswatches() {
        if (!$this->hasData('attributeswatches')) {
            $this->setData('attributeswatches', Mage::registry('attributeswatches'));
        }
        return $this->getData('attributeswatches');
    }

    public function setProduct($product) {
        $this->_product = $product;
        return $this;
    }

    public function getProduct() {
        return $this->_product;
    }

    public function showSwatches() {
        $_product = $this->getProduct();
        if ($_product->isConfigurable()) {
            $att = $_product->loadByAttribute('sku', $_product->getSku())->getTypeInstance(true)->getConfigurableAttributes($_product);
            foreach ($att as $attribute) {
                if ($attribute->getProductAttribute()->getAttributeCode() != "color")
                    return false;
            }
            return true;
        }
        return false;
    }

    public function showSwatchesParent($_product) {

        if ($_product->isConfigurable()) {
            $att = $_product->loadByAttribute('sku', $_product->getSku())->getTypeInstance(true)->getConfigurableAttributes($_product);
            foreach ($att as $attribute) {
                if ($attribute->getProductAttribute()->getAttributeCode() != "color")
                    return false;
            }
            return true;
        }
        return false;
    }


/*    public function showSwatchesMultipleAttributes() {
        $_product = $this->getProduct();
        if ($_product->isConfigurable()) {
            $att = $_product->loadByAttribute('sku', $_product->getSku())->getTypeInstance(true)->getConfigurableAttributes($_product);
            foreach ($att as $attribute) {
                if ($attribute->getProductAttribute()->getAttributeCode() == "color")
                    return true;
            }
            return false;
        }
        return false;
    }*/
    
    
     public function showSwatchesInList() {
        $_product = $this->getProduct();
        $_configurable_attribute = Mage::getStoreConfig("attributeswatches/productlist/attributes");
        if ($_product->isConfigurable()) {
            return Mage::getResourceModel('attributeswatches/attributes')->hasConfigurableAttribute($_configurable_attribute , $_product->getId());
        }
        return false;
    }

    

}
