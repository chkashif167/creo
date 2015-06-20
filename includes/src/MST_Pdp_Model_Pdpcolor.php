<?php
class MST_Pdp_Model_Pdpcolor extends Mage_Core_Model_Abstract {
	public function _construct() {
		parent::_construct ();
		$this->_init ( 'pdp/pdpcolor' );
	}
	public function saveProductColor($data) {
		$id = NULL;
		if (isset($data['id']) && $data['id'] != "") {
			$id = $data['id'];
		}
		$result = $this->setData($data)->setId($id)->save();
		return $result->getId();
	}
	public function getProductColors ($productId) {
		$collection = $this->getCollection();
		$collection->addFieldToFilter('product_id', $productId);
		$collection->setOrder('position', 'ASC');
		return $collection;
	}
	public function getProductColorCollection($productId) {
		$colors = Mage::getModel('pdp/color')->getColors();
		$productColors = Mage::getModel('pdp/pdpcolor')->getProductColors($productId);
		/*$productColors->getSelect()->join(
				array ('t2' => $colors->getMainTable()),
				'main_table.color_id = t2.color_id',
				array ('t2.color_name', 't2.color_code')
		);*/
		return $productColors;
	}
	public function deleteProductColor($id) {
		$productColor = $this->load($id);
		$helper = Mage::helper('pdp');
		try {
			//Remove image file in pdpcolorimage table
			$colorImageModel = Mage::getModel('pdp/pdpcolorimage');
			$colorImageCollection = $colorImageModel->getCollection();
			$colorImageCollection->addFieldToFilter('product_color_id', $id);
			foreach ($colorImageCollection as $colorImage) {
				$_colorImgInfo = $colorImageModel->load($colorImage->getId());
				$helper->removeImageFile($_colorImgInfo->getFilename());
				$_colorImgInfo->delete();
				
			}
			$productColor->delete();
		} catch (Exception $e) {
			Zend_Debug::dump($e);
		}
	}
}