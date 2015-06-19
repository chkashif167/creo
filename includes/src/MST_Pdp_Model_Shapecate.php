<?php
class MST_Pdp_Model_Shapecate extends Mage_Core_Model_Abstract {
	public function _construct() {
		parent::_construct ();
		$this->_init ( 'pdp/shapecate' );
	}
	public function getOptionArray() {
		$arr_status = array (
				array ('value' => 1, 'label' => Mage::helper ( 'pdp' )->__ ( 'Enabled' ) ),
				array ('value' => 2, 'label' => Mage::helper ( 'pdp' )->__ ( 'Disabled' ) ) 
		);
		return $arr_status;
	}
	public function getArtworkCateCollection() {
		$collection = $this->getCollection();
		$collection->addFieldToFilter('status', 1);
		$collection->setOrder('position', 'ASC');
		return $collection;
	}
	public function getCategoryOptions() {
		$options = $this->getArtworkCateCollection();
		$data = array();
		foreach ($options as $option) {
			$data[$option->getId()] = $option->getTitle();
		}	
		return $data;
	}
	public function getCategoryFilterOptions() {
		$options = $this->getArtworkCateCollection();
		$data = array();
		$data[0] = 'All';
		foreach ($options as $option) {
			$data[$option->getId()] = $option->getTitle();
		}	
		return $data;
	}
	public function getDefaultArtCate () {
		$cateId = $this->getArtworkCateCollection()->getFirstItem()->getId();
		return $cateId;
	}
}