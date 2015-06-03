<?php

class EM_Quickshop_Model_Mysql4_Quickshop extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the quickshop_id refers to the key field in your database table.
        $this->_init('quickshop/quickshop', 'quickshop_id');
    }
}