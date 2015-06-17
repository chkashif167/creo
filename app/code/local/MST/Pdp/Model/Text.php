<?php
class MST_Pdp_Model_Text extends Mage_Core_Model_Abstract {
	public function _construct() {
		parent::_construct ();
		$this->_init ( 'pdp/text' );
	}
	public function getOptionArray() {
		$arr_status = array (
				array ('value' => 1, 'label' => Mage::helper ( 'pdp' )->__ ( 'Enabled' ) ),
				array ('value' => 2, 'label' => Mage::helper ( 'pdp' )->__ ( 'Disabled' ) ) 
		);
		return $arr_status;
	}
}