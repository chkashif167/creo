<?php

class MDN_BarcodeLabel_Model_List extends Mage_Core_Model_Abstract {
    
    public function _construct(){
        $this->_init('BarcodeLabel/List', 'bll_id');
    }

    public function appendIfNotExist($barcode)
    {
        $item = $this->getCollection()->addFieldToFilter('bll_barcode', $barcode)->getFirstItem();
        if ($item->getbll_barcode() == $barcode)
            return false;

        $item = Mage::getModel('BarcodeLabel/List')->setbll_barcode($barcode)->save();
        return $item;
    }
}
