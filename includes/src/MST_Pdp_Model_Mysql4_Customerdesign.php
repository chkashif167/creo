<?php
class MST_Pdp_Model_Mysql4_Customerdesign extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('pdp/customerdesign', 'id');
    }
}