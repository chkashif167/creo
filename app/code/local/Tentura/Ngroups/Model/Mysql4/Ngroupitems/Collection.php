<?php

class Tentura_Ngroups_Model_Mysql4_Ngroupitems_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('ngroups/ngroupitems');
    }
}