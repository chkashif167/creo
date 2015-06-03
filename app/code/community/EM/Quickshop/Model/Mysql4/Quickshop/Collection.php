<?php

class EM_Quickshop_Model_Mysql4_Quickshop_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('quickshop/quickshop');
    }
}