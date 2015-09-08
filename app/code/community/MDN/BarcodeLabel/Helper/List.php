<?php

class MDN_BarcodeLabel_Helper_List extends Mage_Core_Helper_Abstract {
    
    /**
     * 
     */
    public function getCount()
    {
       return Mage::getModel('BarcodeLabel/List')->getCollection()->getSize(); 
    }
    
    /**
     * 
     */
    public function getAvailableCount()
    {
        $total = $this->getAvailableCode(99999999);
        return count($total);
    }

    /**
     * @param $filePath
     * @param $mode
     */
    public function import($filePath, $mode)
    {
        //truncate table if mode is replace
        if ($mode == 'replace')
            $this->truncateTable();

        //import file
        $lines = file($filePath);
        $count = 0;
        foreach($lines as $line)
        {
            $barcode = $this->cleanBarcode($line);
            if  (Mage::getModel('BarcodeLabel/List')->appendIfNotExist($barcode))
                $count++;
        }

        return $count;
    }

    /**
     * Clean a barcode removing leading chars
     * @param $line
     * @return mixed
     */
    public function cleanBarcode($line)
    {
        $line = str_replace("\n", "", $line);
        $line = str_replace("\r", "", $line);
        $line = str_replace("\t", "", $line);
        $line = trim($line);
        return $line;
    }

    /**
     * Truncate all barcodes
     */
    public function truncateTable()
    {
        $collection = Mage::getModel('BarcodeLabel/List')->getCollection();
        foreach($collection as $item)
            $item->delete();
    }

    /**
     * return first available code
     */
    public function getAvailableCode($max = 1)
    {
        $prefix = Mage::getConfig()->getTablePrefix();
        $barcodeAttributeId = Mage::helper('BarcodeLabel')->getBarcodeAttributeId();
        if ($barcodeAttributeId)
        {
            $sql = 'select bll_barcode from '.$prefix.'barcode_label_list  where bll_barcode not in (select value from '.$prefix.'catalog_product_entity_varchar where `value` is not null and `value` <> "" and attribute_id = '.$barcodeAttributeId.') limit 0, '.$max;
            if ($max == 1)
                $result = mage::getResourceModel('sales/order_item_collection')->getConnection()->fetchOne($sql);
            else
                $result = mage::getResourceModel('sales/order_item_collection')->getConnection()->fetchCol($sql);
            return $result;
        }
        else
            return array();
    }
}
