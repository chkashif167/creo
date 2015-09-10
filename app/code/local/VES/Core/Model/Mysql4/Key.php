<?php

class VES_Core_Model_Mysql4_Key extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('ves_core/key', 'key_id');
    }
}