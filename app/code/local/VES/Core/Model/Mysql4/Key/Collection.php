<?php

class VES_Core_Model_Mysql4_Key_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('ves_core/key');
    }
}