<?php

class Magestore_Fblogin_Model_Mysql4_Fblogin_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('fblogin/fblogin');
    }
}