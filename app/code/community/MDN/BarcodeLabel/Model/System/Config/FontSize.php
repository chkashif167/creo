<?php

class MDN_BarcodeLabel_Model_System_Config_FontSize extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    public function getAllOptions() {
        if (!$this->_options) {
            $entityTypeId = Mage::getModel('eav/entity_type')->loadByCode('catalog_product')->getId();
            $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
                            ->setEntityTypeFilter($entityTypeId);

            //add empty
            for($i=4;$i<64;$i++)
            {
                $options[] = array(
                    'value' => $i,
                    'label' => $i,
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