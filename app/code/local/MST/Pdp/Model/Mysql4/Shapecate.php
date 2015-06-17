<?php
class MST_Pdp_Model_Mysql4_Shapecate extends Mage_Core_Model_Mysql4_Abstract
{

    public function _construct()
    {
        $this->_init('pdp/shapecate', 'id');
    }

}