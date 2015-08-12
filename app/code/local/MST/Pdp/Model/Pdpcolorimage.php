<?php
class MST_Pdp_Model_Pdpcolorimage extends Mage_Core_Model_Abstract {
	public function _construct() {
		parent::_construct ();
		$this->_init ( 'pdp/pdpcolorimage' );
	}
	public function saveProductColorImage($data) {
		$id = NULL;
		if (isset($data['id']) && $data['id'] != "") {
			$id = $data['id'];
		}
		$result = $this->setData($data)->setId($id)->save();
		return $result->getId();
	}
	public function getProductColorImage($productId, $productColorId) {
		$sideCollection = Mage::getModel('pdp/pdpside')->getCollection();
		$sideCollection->addFieldToFilter('product_id', $productId);
		$collection = $this->getCollection();
		$collection->addFieldToFilter('product_color_id', $productColorId);
		
		$collection->getSelect()->join(
			array ('t2' => $sideCollection->getMainTable()),
			'main_table.side_id = t2.id AND t2.status = 1',
			't2.label'	
		);
		return $collection;
	}
}