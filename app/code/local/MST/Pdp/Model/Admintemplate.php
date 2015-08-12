<?php
class MST_Pdp_Model_Admintemplate extends Mage_Core_Model_Abstract {
	public function _construct() {
		parent::_construct ();
		$this->_init ( 'pdp/admintemplate' );
	}
	public function saveAdminTemplate($data) {
		$model = Mage::getModel('pdp/admintemplate');
		$collection = $this->getCollection();
		$collection->addFieldToFilter('product_id', $data['product_id']);
		if (count($collection) > 0) {
			$tempId = $collection->getFirstItem()->getId();
			$model->setData($data)->setId($tempId)->save();
		} else {
			$model->setData($data);
			$model->save();
		}
        return $model;
	}
}