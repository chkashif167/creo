<?php
class MST_Pdp_Model_Pdpside extends Mage_Core_Model_Abstract {
	public function _construct() {
		parent::_construct ();
		$this->_init ( 'pdp/pdpside' );
	}
	public function saveProductSide($data) {
		$id = NULL;
		if ($data['id'] != "") {
			$id = $data['id'];
		}
		$sideInfo = $this->setData($data)->setId($id)->save();
		return $sideInfo->getId();
	}
	public function getDesignSides($productId) {
		$collection = $this->getCollection();
		$collection->addFieldToFilter('product_id', $productId);
		$collection->setOrder('position', 'ASC');
		//$collection->setOrder('label', 'ASC');
		return $collection;
	}
    //If side using color as background
    public function getDesignSideColors($productId) {
		$collection = $this->getCollection();
		$collection->addFieldToFilter('product_id', $productId);
        $collection->addFieldToFilter('background_type', "color");
		$collection->setOrder('position', 'ASC');
		//$collection->setOrder('label', 'ASC');
		return $collection;
	}
	public function getActiveDesignSides($productId) {
		$collection = $this->getDesignSides($productId);
		$collection->addFieldToFilter('status', 1);
		return $collection;
	}
	public function inlineUpdate($params) {
		if ($params['side_id'] != "" && $params['update-info'] != "") {
			$sideInfo = $this->load($params['side_id']);
			$fieldInfo = explode('-', $params['update-info']);
			switch ($fieldInfo[0]) {
				case "status" : 
					$sideInfo->setStatus($fieldInfo[1]);
					break;
				case "position" :
					$sideInfo->setPosition($fieldInfo[1]);
			}
			$sideInfo->save();
		}
	}
}