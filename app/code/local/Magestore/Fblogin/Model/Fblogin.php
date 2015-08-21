<?php

class Magestore_Fblogin_Model_Fblogin extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('fblogin/fblogin');
    }
}