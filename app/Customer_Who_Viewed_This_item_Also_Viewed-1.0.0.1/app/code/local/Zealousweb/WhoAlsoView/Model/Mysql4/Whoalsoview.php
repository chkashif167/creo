<?php
class Zealousweb_WhoAlsoView_Model_Mysql4_Whoalsoview extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("whoalsoview/whoalsoview", "id");
    }
}