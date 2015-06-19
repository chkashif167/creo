<?php
class MST_Pdp_Model_Mysql4_Colorimage extends Mage_Core_Model_Mysql4_Abstract
{

    public function _construct()
    {
        $this->_init('pdp/colorimage', 'id');
    }
}