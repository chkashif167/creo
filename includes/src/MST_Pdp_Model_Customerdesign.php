<?php
class MST_Pdp_Model_Customerdesign extends Mage_Core_Model_Abstract {
	public function _construct() {
		parent::_construct ();
		$this->_init ( 'pdp/customerdesign' );
	}
	public function saveTemplate($data) {
		$model = Mage::getModel('pdp/customerdesign');
		$model->setData($data);
		$model->save();
        return $model;
	}
	public function getCustomerDesign($customerId) {
		
	}
}