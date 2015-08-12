<?php
/**
* Magento Support Team.
* @category   MST
* @package    MST_Pdp
* @version    2.0
* @author     Magebay Developer Team <info@magebay.com>
* @copyright  Copyright (c) 2009-2013 MAGEBAY.COM. (http://www.magebay.com)
*/
class MST_Pdp_Adminhtml_PdpController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('pdp/pdp')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Design Manager'), Mage::helper('adminhtml')->__('Design Manager'));
        return $this;
    }
    public function uploadAction()
    {
        //$this->_initAction();
		$this->loadLayout()->_setActiveMenu('pdp/pdp');
        $this->renderLayout();
    }
	
	public function imageAction()
    {
        //$this->_initAction();
		$this->loadLayout()->_setActiveMenu('pdp/pdp');
        $this->renderLayout();
    }
	public function fontAction()
    {
        //$this->_initAction();
		$this->loadLayout()->_setActiveMenu('pdp/pdp');
        $this->renderLayout();
    }
	public function designAction()
    {
        //$this->_initAction();
		$this->loadLayout()->_setActiveMenu('pdp/pdp');
        $this->renderLayout();
    }
	public function deleteImageAction ()
	{
		$data = $this->getRequest()->getParams();
		if ($data['image_id'] != "") {
			$image = Mage::getModel('pdp/images')->load($data['image_id']);
            $filename = $image->getFilename();
            $path = Mage::getBaseDir('media').DS.'pdp/images/';
            unlink($path . $filename);
            $image->delete();
			$this->getResponse()->setBody("delete_" . $data['image_id']);
		}
	}
    
    public function deleteFontAction ()
	{
		$data = $this->getRequest()->getParams();
		if ($data['font_id'] != "") {
			$font = Mage::getModel('pdp/fonts')->load($data['font_id']);
            $filename = $font->getFilename();
            $path = Mage::getBaseDir('media').DS.'pdp/fonts/';
            unlink($path . $filename);
            $font->delete();
			$this->getResponse()->setBody("delete_" . $data['font_id']);
		}
	}
	public function saveColorAction()
	{
		$image_id = $_POST['image_id_color'];
		$hexCode = str_replace('#', '', $_POST['color']);
		if ($hexCode == "" || $image_id == "" || $_FILES['color_image']['name'] == "") {
			$url = Mage::helper("adminhtml")->getUrl("pdp/adminhtml_pdp/image/");
			Mage::app()->getResponse()->setRedirect($url);
			return;
		}
		if (!empty($_FILES['color_image']['name'])) {
			try {
				$imageName = $_FILES['color_image']['name'];
				$ext = substr($imageName, strrpos($imageName, '.') + 1);
				$filename = "ColorImage_" . time() . '.' . $ext;
				$uploader = new Varien_File_Uploader('color_image');
				$uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png', 'bmp', 'svg+xml')); // or pdf or anything
				/* $size=filesize($_FILES['image']['tmp_name']);
				$test=getimagesize($_FILES['image']['tmp_name']); */
				$uploader->setAllowRenameFiles(false);
				$uploader->setFilesDispersion(false);
				$path = Mage::getBaseDir('media').DS.'pdp/images/';
				$uploader->save($path, $filename);
				$data['filename'] = $filename;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/image');
				return;
			}
		}
		$data['image_id'] = $image_id;
		$data['color'] = $hexCode;
		$data['filename'];
		Mage::getModel('pdp/pdp')->addColorImage($data);
		$url = Mage::helper("adminhtml")->getUrl("pdp/adminhtml_pdp/image/");
		Mage::app()->getResponse()->setRedirect($url);
	}
	public function saveArtworkColorAction() 
	{
		$data = $_REQUEST;
		$colorImage = $data['color-image'];
		$pdpModel = Mage::getModel('pdp/pdp');
		foreach ($colorImage as $key => $value) {
			$designColor = array();
			$inputName = 'artworkimage_' . $key;
            $artworkPath = "pdp" . DS . "images" . DS . "artworks" . DS;
			try {
				$filename = Mage::helper('pdp')->saveImage($inputName, $artworkPath);
				if ($filename != "") {
					$designColor['filename'] = $filename;
					$designColor['image_id'] = $data['image_id'];
					$designColor['sort'] = $data['sort'][$key];
					$designColor['color'] = $colorImage[$key];
					$pdpModel->addColorImage($designColor);
				}
			} catch(Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError("Can not upload cliparts");
				$url = Mage::helper("adminhtml")->getUrl("pdp/adminhtml_pdp/artworkcolor/image_id/" . $data['image_id']);
				Mage::app()->getResponse()->setRedirect($url);
				return;
			}
		}
		$url = Mage::helper("adminhtml")->getUrl("pdpadmin/adminhtml_pdp/image/");
		Mage::app()->getResponse()->setRedirect($url);
	}
	
	public function artworkColorAction() 
	{		
		$this->loadLayout()->_setActiveMenu('pdp/pdp');
		$this->renderLayout();
	}
	public function artworkColorInfoAction() 
	{
		$imageId = $this->getRequest()->getParam('image_id');
		if ($imageId != "") {
			$info = Mage::getModel('pdp/pdp')->getImageInfo($imageId);
			$info['add_color_url'] = Mage::helper("adminhtml")->getUrl("pdpadmin/adminhtml_pdp/artworkcolor/",array("image_id"=> $imageId));
			$this->getResponse()->setBody(json_encode($info));
		}
	}
    public function updateStatusAction() {
        $request = $this->getRequest()->getParams();
        Mage::getModel('pdp/productstatus')->setProductStatus($request);
    }
}