<?php

class Magestore_Fblogin_Model_Mysql4_Fblogin extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the fblogin_id refers to the key field in your database table.
        $this->_init('fblogin/fblogin', 'fblogin_id');
    }
}