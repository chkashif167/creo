<?php

class EM_Quickshop_Model_Quickshop extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('quickshop/quickshop');
    }
}