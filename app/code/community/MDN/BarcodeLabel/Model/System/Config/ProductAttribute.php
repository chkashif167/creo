<?php

class MDN_BarcodeLabel_Model_System_Config_ProductAttribute extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    public function getAllOptions() {
        if (!$this->_options) {
            $entityTypeId = Mage::getModel('eav/entity_type')->loadByCode('catalog_product')->getId();
            $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
                    ->setEntityTypeFilter($entityTypeId)
                    ->addFieldToFilter('backend_type', array('neq' => 'static'));

            //add empty
            $options[] = array(
                'value' => '',
                'label' => '',
            );

            //add sku entry
            $options[] = array(
                'value' => 'sku',
                'label' => 'sku',
            );


            foreach ($attributes as $attribute) {
                $options[] = array(
                    'value' => $attribute->getAttributeCode(),
                    'label' => $attribute->getName(),
                );
            }

            $this->_options = $options;
        }
        return $this->_options;
    }

    public function toOptionArray() {
        return $this->getAllOptions();
    }

}