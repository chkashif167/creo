<?php

class MDN_BarcodeLabel_Helper_Generation extends Mage_Core_Helper_Abstract {

    /**
     * Generate barcodes for all products where attribute 'barcode' are empty
     */
    public function generateForAllProducts() {


        //config check for 
        Mage::helper('BarcodeLabel')->checkConfiguration();

        // array to stock the id of product filled (with barcode)
        $productsIdWithEan = array();

        // get product with EAN filled (if only one attribute has been set )
        $barcodeAttribute = Mage::helper('BarcodeLabel')->getBarcodeAttribute();
        if ($barcodeAttribute == 'sku')
            throw new Exception('You can not generate gencode when sku attribute is used'); 
        $productWithEan = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToFilter($barcodeAttribute, array('neq' => ''));

        // we stock each products id
        $productsIdWithEan = $productWithEan->getAllIds();
        
        // if no one products has been set (first click after install)
        if (count($productsIdWithEan) == 0)
            $productsIdWithEan = array('-1'); // define fake product id fo the not in select

        // getting all products without previous id ( not in )
        $productIdsWithoutEan = Mage::getModel('catalog/product')
                ->getCollection()
                ->addFieldToFilter('entity_id', array('nin' => $productsIdWithEan))
                ->getAllIds();
  

        //generate barcodes
        foreach ($productIdsWithoutEan as $productId) {
            $this->storeBarcode($productId);
        }
        
        

    }

    /**
     * Generate (and save) barcode for one product
     * @param <type> $productId
     */
    public function storeBarcode($productId) {

        //generate barcode
        $barcode = $this->getBarcodeForProduct($productId);

        //save into product
        $barcodeAttribute = Mage::helper('BarcodeLabel')->getBarcodeAttribute();
        if ($barcodeAttribute != 'sku')
        {
            Mage::getSingleton('catalog/product_action')
                    ->updateAttributes(array($productId), array(Mage::helper('BarcodeLabel')->getBarcodeAttribute() => $barcode), 0);
        }
        
        return $barcode;
    }

    /**
     * Generate barcode for product for EAN 13
     *
     * 1. make 12 barcode charactere (if product id=9 then fill 11 zero before > 000000000009)
     * zend framework add automatically the 13 character.
     */
    protected function getBarcodeForProduct($productId) {
        switch($this->getMethod())
        {
            case 'list':
                $barcode = Mage::helper('BarcodeLabel/List')->getAvailableCode();
                break;
            default:
                $barcode = str_pad($productId, 12, '0', STR_PAD_LEFT);
                $barcode .= $this->getControlKey($barcode);
                break;
        }
        return $barcode;
 }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return Mage::getStoreConfig('barcodelabel/general/generation_method');
    }

    /**
     * dont need to exist Zend framwork auto complete the checksum!!
     * 
     * Return control key for barcode
     * @param <type> $ean13
     * @return <type>
     */
    protected function getControlKey($ean13) {

        $sum = 0;

        for ($index = 0; $index < 12; $index++) {
            $number = (int) $ean13[$index];
            if (($index % 2) != 0)
                $number *= 3;
            $sum += $number;
        }

        $resteDivision = $sum % 10;

        if ($resteDivision == 0)
            return 0;
        else
            return 10 - $resteDivision;
    }

}