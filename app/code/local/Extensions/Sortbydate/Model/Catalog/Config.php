<?php
 
class Extensions_Sortbydate_Model_Catalog_Config extends Mage_Catalog_Model_Config
{
    public function getAttributeUsedForSortByArray()
    {
        $options = array(
            'created_at' => Mage::helper('catalog')->__('New')
        );
 
        foreach ($this->getAttributesUsedForSortBy() as $attribute) {
            $options[$attribute->getAttributeCode()] = $attribute->getStoreLabel();
        }
 
        return $options;
    }
}