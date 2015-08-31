<?php
/**
* Magento Support Team.
* @category   MST
* @package    MST_Pdp
* @version    2.0
* @author     Magebay Developer Team <info@magebay.com>
* @copyright  Copyright (c) 2009-2013 MAGEBAY.COM. (http://www.magebay.com)
*/
class MST_Pdp_Model_Productstatus extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('pdp/productstatus');
    }
	public function setProductConfig($data) {
		$id = NULL;
		$collection = $this->getCollection();
		$collection->addFieldToFilter('product_id', $data['product_id']);
		if ($collection->count() > 0) {
			$id = $collection->getFirstItem()->getId();
		}
		$this->setData($data)->setId($id)->save();
	}
	public function getProductStatus($productId) {
		$productConfigs = $this->getProductConfig($productId);
		return $productConfigs['status'];
	}
	public function getConfigNote($productId) {
		$collection = $this->getCollection();
		$collection->addFieldToFilter('product_id', $productId);
		if ($collection->count() > 0) {
			$data = $collection->getFirstItem()->getData();
			$note = array();
			if ($data['note']) {
				$note = json_decode($data['note'], true);
                if(!isset($note['default_color']) || (isset($note['default_color']) && !$note['default_color'])) {
                    $note['default_color'] = Mage::getStoreConfig("pdp/design/default_object_color");
                }
                if(!isset($note['default_fontsize']) || (isset($note['default_fontsize']) && !$note['default_fontsize'])) {
                    $note['default_fontsize'] = Mage::getStoreConfig("pdp/design/default_object_fontsize");
                }
                if(!isset($note['default_fontheight']) || (isset($note['default_fontheight']) && !$note['default_fontheight'])) {
                    $note['default_fontheight'] = Mage::getStoreConfig("pdp/design/default_object_fontheight");
                }
			}
			//Check product status more details 
			//--Check product has side to design or not--
			$sideModel = Mage::getModel('pdp/pdpside')->getDesignSides($productId);
			$isPdpEnable = Mage::getStoreConfig('pdp/setting/enable');
			if (!$sideModel->count() || $isPdpEnable == 0) {
				$data['status'] = 0;
			}
			$finalArr = array_merge($data, $note);
			//End check status
			return $finalArr;
		}
		return null;
	}
	public function getProductConfig($productId) {
		$note = null;
		if ($this->getConfigNote($productId)) {
			$note = $this->getConfigNote($productId);
		}
		return $note;
	}
}