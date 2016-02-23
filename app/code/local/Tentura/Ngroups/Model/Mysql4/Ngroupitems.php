<?php

class Tentura_Ngroups_Model_Mysql4_Ngroupitems extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the ngroups_id refers to the key field in your database table.
        $this->_init('ngroups/ngroupitems', 'id');
    }
    
}