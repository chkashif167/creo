<?php
/**
* Magento Support Team.
* @category   MST
* @package    MST_Pdp
* @version    2.0
* @author     Magebay Developer Team <info@magebay.com>
* @copyright  Copyright (c) 2009-2013 MAGEBAY.COM. (http://www.magebay.com)
*/
class MST_Pdp_Model_Pdp extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('pdp/pdp');
    }
	
	public function setDesignImage ($data) 
	{
		$model = Mage::getModel('pdp/images');
		$collection = $model->getCollection();
		$collection->addFieldToFilter('filename', $data['filename']);
		$collection->addFieldToFilter('image_type', $data['image_type']);
		$imageObj = $collection->getFirstItem()->getData('filename');
		if (count($imageObj) == "") {
			if ($data['filename'] != "" && $data['image_type'] != "") {
				$model->setData($data);
				$model->save();
			}
		}
        $returnData = $model->getData();
		return $returnData;
	}
	public function getImageCollection ()
	{
		$images = Mage::getModel('pdp/images')->getCollection()
        ->addFieldToFilter('image_type', 'custom')
		->setOrder('image_id', 'DESC')
		->setOrder('image_type');
		return $images;
	}
	public function getImageCollectionByCategory ($category)
	{
		if ($category === "0") {
			$images = Mage::getModel('pdp/images')->getCollection()
			->addFieldToFilter('image_type', 'custom')
			->setOrder('position', 'DESC')
			->setOrder('image_id', 'DESC');
		} else {
			/* $category_fillter = array('like'=>'%'. $category .'%');
			$images = Mage::getModel('pdp/images')->getCollection()
			->addFieldToFilter('image_type', 'custom')
			->addFieldToFilter('category', array($category_fillter))
			->setOrder('image_id', 'DESC')
			->setOrder('image_type'); */
			$images = Mage::getModel('pdp/images')->getCollection()
			->addFieldToFilter('image_type', 'custom')
			->addFieldToFilter('category', $category)
			->setOrder('position', 'DESC')
			->setOrder('image_id', 'DESC');
		}
		
		return $images;
	}
    
	public function getFilename ($image_id)
	{	
		$filename = Mage::getModel('pdp/images')->load($image_id)->getFilename();
		return $filename;
	}
	
	public function setDesignFont ($data) 
	{
		$model = Mage::getModel('pdp/fonts');
		$collection = $model->getCollection();
		$collection->addFieldToFilter('name', $data['name']);
		$collection->addFieldToFilter('ext', $data['ext']);
		$fontObj = $collection->getFirstItem()->getData('name');
		if (count($fontObj) == "") {
			if ($data['name'] != "" && $data['ext'] != "") {
				$model->setData($data);
				$model->save();
			}
		}
		$returnData = $model->getData();
		return $returnData;
	}
	
    public function getFontCollection ()
	{
		$fonts = Mage::getModel('pdp/fonts')->getCollection()
		->setOrder('name', 'ASC');
		return $fonts;
	}
	public function deleteDesign ($id)
	{
		$model = Mage::getModel('pdp/design')->load($id)->delete();
	}
	public function deleteImageByFilename($filename)
	{
		$images = Mage::getModel('pdp/images')->getCollection();
		$images->addFieldToFilter('filename', $filename);
		if ( count($images) > 0) {
			$id = $images->getFirstItem()->getId();
			Mage::getModel('pdp/images')->load($id)->delete();
		}
	}
	public function deleteImageById($id)
	{
		$image = Mage::getModel('pdp/images')->load($id);
		$filename = $image->getFilename();
		$isRemoved = Mage::helper('pdp')->removeImageFile($filename);
		$image->delete();
		
	}
	public function deleteFontById($id)
	{
		$font = Mage::getModel('pdp/fonts')->load($id);
		$filename = $font->getName() . '.' . $font->getExt();
		$isRemoved = Mage::helper('pdp')->removeFontFile($filename);
		$font->delete();
	}
	public function getImageInfo($imageId)
	{
		$images = Mage::getModel('pdp/images')->load($imageId);
		$categoryTitle = Mage::getModel('pdp/artworkcate')->load($images->getCategory())->getTitle();
		
		$colorImage = $this->getColorImage($imageId);
		$data['image'] = $images->getData();
		$data['colorimage'] = $colorImage;
		$data['category_title'] = $categoryTitle;
		return $data;
	}
	public function updateImageInfo($data)
	{
		if ($data['image_id']) {
			$model = Mage::getModel('pdp/images')->setData($data)->setId($data['image_id'])->save();
		}
	}
	public function addColorImage($data) 
	{
		$model = Mage::getModel('pdp/colorimage');
		$model->setData($data);
		$model->save();
	}
	public function getColorImage($imageId)
	{
		$collection = Mage::getModel('pdp/colorimage')->getCollection();
		$collection->addFieldToFilter('image_id', $imageId);
		
		$data = array();
		foreach ($collection as $item) {
			$data[] = join('--', $item->getData());
		}
		return join(",,", $data);
	}
	public function getColorImageFrontend($imageId)
	{
		$collection = Mage::getModel('pdp/colorimage')->getCollection();
		$collection->addFieldToFilter('image_id', $imageId);
		
		$data = array();
		foreach ($collection as $item) {
			$img_color = array($item->getColor(), $item->getFilename());
			$data[] = join('__', $img_color);
		}
		return join(',', $data);
	}
	public function deleteColorImage ($id)
	{
		$imgColor = Mage::getModel('pdp/colorimage')->load($id);
		//Remove image file
		$filename = $imgColor->getFilename();
		$isRemoved = Mage::helper('pdp')->removeImageFile($filename);
		$imgColor->delete();
	}
	public function duplicate($currentProductId, $newProductId) {
		//die("OLD PRODUCTA $currentProductId, New Product $newProductId");
		//Duplicate sides
		$this->duplicateSides($currentProductId, $newProductId);
		//Duplicate product config
		$this->duplicateProductConfig($currentProductId, $newProductId);
		//Duplicate sample data
		$this->duplicateSampleData($currentProductId, $newProductId);
	}
	protected function duplicateSides($currentProductId, $newProductId) {
		$sideModel = Mage::getModel('pdp/pdpside');
		$sides = $sideModel->getDesignSides($currentProductId);
		if($sides->count()) {
			foreach($sides as $side) {
				$sideData = $side->getData();
				$sideData['id'] = null;
				$sideData['product_id'] = $newProductId;
				$sideModel->saveProductSide($sideData);
			}
		}
	}
	protected function duplicateProductConfig($currentProductId, $newProductId) {
		$productStatusModel = Mage::getModel('pdp/productstatus');
		$productConfigCollection = $productStatusModel->getCollection()->addFieldToFilter("product_id", $currentProductId);
		if($productConfigCollection->count()) {
			$currentConfigData = $productConfigCollection->getFirstItem()->getData();
			//New data
			$currentConfigData['product_id'] = $newProductId;
			unset($currentConfigData['id']);
			$productStatusModel->setProductConfig($currentConfigData);
		}
	}
	protected function duplicateSampleData($currentProductId, $newProductId) {
		$sampleModel = Mage::getModel('pdp/admintemplate');
		$sampleOfCurrentProduct = $sampleModel->getCollection()->addFieldToFilter("product_id", $currentProductId);
		if($sampleOfCurrentProduct->count()) {
			$sampleData = $sampleOfCurrentProduct->getFirstItem()->getData();
			//New data
			$sampleData['id'] = null;
			$sampleData['product_id'] = $newProductId;
			$sampleModel->saveAdminTemplate($sampleData);
		}
	}
	public function checkDuplicateRequest($newProduct) {
		$requestData = Mage::getSingleton("core/session")->getDuplicatePdcConfig();
		if($requestData != "" && $requestData != null) {
			//Check if new product is duplicated product
			$sku = $newProduct->getSku();
			$status = $newProduct->getStatus();
			if($sku == null && $status == "2") {
				$oldProduct = Mage::getModel("catalog/product")->load($requestData["original_product_id"]);
				if($oldProduct->getName() == $newProduct->getName()) {
					try {
						$this->duplicate($oldProduct->getId(), $newProduct->getId());
						Mage::getSingleton("core/session")->setDuplicatePdcConfig("");
					} catch (Exception $e) {
						//Reset duplicate session if have any exception
						Mage::getSingleton("core/session")->setDuplicatePdcConfig("");
					}
				}
			}
		}
	}
}