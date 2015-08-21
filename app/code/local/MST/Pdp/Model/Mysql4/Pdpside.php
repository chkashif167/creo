<?php
class MST_Pdp_Model_Mysql4_Pdpside extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('pdp/pdpside', 'id');
    }
}