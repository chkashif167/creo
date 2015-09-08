<?php

class MDN_BarcodeLabel_Helper_BarcodePicture extends Mage_Core_Helper_Abstract {

    /**
     * Return barcode image
     */
    public function getImage($barcode) {
        
        $barcodeStandard = Mage::getStoreConfig('barcodelabel/general/standard');
     
        // WARNING option withChecksum = false is ignored for EAN 13 ! we have to cut the barcode if ean13 is enable
        if ($barcodeStandard == "Ean13") { $barcode = substr($barcode, 0, 12); }
        
        $barcodeOptions = array('text' => $barcode); // barcode attribut (not sku!)
        $rendererOptions = array(); // default = empty

        $factory = Zend_Barcode::factory($barcodeStandard, 'image', $barcodeOptions, $rendererOptions);

        $image = $factory->draw();

        return $image;
          
    }

}
