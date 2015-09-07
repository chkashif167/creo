<?php

class MDN_BarcodeLabel_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Check that module is properly configured
     */
    public function checkConfiguration() {

        if ( strlen($this->getBarcodeAttribute() ) == 0) throw new Exception('Barcode attribute not set, create an attribute an save it : in system > configuration > barcodelabel');

        return true;
    }

    /**
     * Return barcode attribute
     * @return <type>
     */
    public function getBarcodeAttribute() {
        $barcodeAttribute = Mage::getStoreConfig('barcodelabel/general/attribute');
        return $barcodeAttribute;
    }
    
    /**
     * Return barcode attribute id
     * @return type
     */
    public function getBarcodeAttributeId()
    {
        $attributeCode = $this->getBarcodeAttribute();
        $attributeId = Mage::getSingleton('eav/entity_attribute')->getIdByCode(
            Mage_Catalog_Model_Product::ENTITY,
            $attributeCode
        );
        return $attributeId;
    }

    /**
     * Check if we log changes for entity type
     *
     * @param unknown_type $objectType
     * @return unknown
     */
    public function considerObjectType($objectType) {
        //register object types for optimization
        if (!Mage::registry('adminlogger_ignored_object_types')) {
            if (mage::getStoreConfig('adminlogger/general/enable_log') == 1)
                mage::log('Load ignored object types in registry');
            $ignoredObjectTypes = mage::getStoreConfig('adminlogger/advanced/object_to_ignore');
            $t_ignoredObjectTypes = explode("\n", $ignoredObjectTypes);

            for ($i = 0; $i < count($t_ignoredObjectTypes); $i++)
                $t_ignoredObjectTypes[$i] = trim($t_ignoredObjectTypes[$i]);

            Mage::register('adminlogger_ignored_object_types', $t_ignoredObjectTypes);
        }

        //check if object type is managed
        if (in_array($objectType, Mage::registry('adminlogger_ignored_object_types'))) {
            if (mage::getStoreConfig('adminlogger/general/enable_log') == 1)
                mage::log('Object type ' . $objectType . ' ignored ');
            return false;
        }
        else {
            if (mage::getStoreConfig('adminlogger/general/enable_log') == 1)
                mage::log('Object type ' . $objectType . ' considered ');
            return true;
        }
    }

    /**
     * Return object type
     *
     * @param unknown_type $object
     */
    public function getObjectType($object) {
        $retour = '';
        $resourceName = $object->getResourceName();
        $resourceName = strtolower($resourceName);

        return strtolower($resourceName);
    }

    /**
     * Convert cm to pixels
     *
     * @param unknown_type $value
     * @return unknown
     */
    public function cmToPixels($value) {
        return $value * 28.33;
    }

    /**
     * return a array with the height and height of the label
     */
    public function getLabelSize() {

        $topMargin = $this->cmToPixels(mage::getStoreConfig('barcodelabel/pdf/top_margin'));
        $leftMargin = $this->cmToPixels(mage::getStoreConfig('barcodelabel/pdf/left_margin'));
        $rightMargin = $this->cmToPixels(mage::getStoreConfig('barcodelabel/pdf/right_margin'));
        $bottomMargin = $this->cmToPixels(mage::getStoreConfig('barcodelabel/pdf/bottom_margin'));

        $verticalSpacing = $this->cmToPixels(mage::getStoreConfig('barcodelabel/pdf/vertical_inter_margin'));
        $horizontalSpacing = $this->cmToPixels(mage::getStoreConfig('barcodelabel/pdf/horizontal_inter_margin'));

        $labelsPerRow = mage::getStoreConfig('barcodelabel/pdf/labels_per_row');
        $rowCount = mage::getStoreConfig('barcodelabel/pdf/row_count');

        $widthCm = mage::getStoreConfig('barcodelabel/pdf/paper_width');
        $heightCm = mage::getStoreConfig('barcodelabel/pdf/paper_height');
        $pageWidth = $this->cmToPixels($widthCm);
        $pageHeight = $this->cmToPixels($heightCm);

        $usableWidth = ($pageWidth - $leftMargin - $rightMargin);
        $usableHeight = ($pageHeight - $topMargin - $bottomMargin);

        $labelHeight = ($usableHeight - (($rowCount - 1) * $verticalSpacing)) / $rowCount;

        $labelWidth = ($usableWidth - (($labelsPerRow - 1) * $horizontalSpacing)) / $labelsPerRow;

        $labelSize = array("height" => $labelHeight, "width" => $labelWidth);

        return $labelSize;
    }

}