<?php

class MDN_BarcodeLabel_Helper_Export extends Mage_Core_Helper_Abstract {

    public function getContent()
    {
        $barcodeAttribute = Mage::helper('BarcodeLabel')->getBarcodeAttribute();
        $collection = Mage::getModel('catalog/product')->getCollection()
                                        ->addAttributeToSelect('name')
                                        ->addAttributeToSelect($barcodeAttribute);
        $content = '';
        $separator = ';';
        $lineReturn = "\n";
        foreach($collection as $item)
        {
            $line = $item->getData($barcodeAttribute).$separator.$item->getSku().$separator.$item->getName().$lineReturn;
            $content .= $line;
        }

        return $content;
    }

}
