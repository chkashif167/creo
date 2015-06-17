<?php
class MST_Pdp_Model_Colorimage extends Mage_Core_Model_Abstract
{
	public function _construct()
    {
        parent::_construct();
        $this->_init('pdp/colorimage');
    }
}