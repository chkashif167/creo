<?php
class MST_Pdp_Model_Mysql4_Jsonfile_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('pdp/jsonfile');
    }

}