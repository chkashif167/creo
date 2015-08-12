<?php

class Magebuzz_Featuredcategory_Model_Mysql4_Featuredcategory extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the featuredcategory_id refers to the key field in your database table.
        $this->_init('featuredcategory/featuredcategory', 'featuredcategory_id');
    }
}