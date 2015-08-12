<?php
class MST_Pdp_Model_Color extends Mage_Core_Model_Abstract {
	public function _construct() {
		parent::_construct ();
		$this->_init ( 'pdp/color' );
	}
	public function getOptionArray() {
		$arr_status = array (
				array ('value' => 1, 'label' => Mage::helper ( 'pdp' )->__ ( 'Enabled' ) ),
				array ('value' => 2, 'label' => Mage::helper ( 'pdp' )->__ ( 'Disabled' ) ) 
		);
		return $arr_status;
	}
	public function getColors() {
		$collection = Mage::getSingleton('pdp/color')->getCollection();
		$collection->addFieldToFilter('status', 1);
		return $collection;
	}
}