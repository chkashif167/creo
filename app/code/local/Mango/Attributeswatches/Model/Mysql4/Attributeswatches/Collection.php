<?php

class Mango_Attributeswatches_Model_Mysql4_Attributeswatches_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('attributeswatches/attributeswatches');
    }
}