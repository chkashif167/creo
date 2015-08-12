<?php

class Magebuzz_Featuredcategory_Model_Mysql4_Featuredcategory_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('featuredcategory/featuredcategory');
    }
}