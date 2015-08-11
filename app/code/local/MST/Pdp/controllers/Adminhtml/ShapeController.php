<?php
/**
* Magento Support Team.
* @category   MST
* @package    MST_Pdp
* @version    2.0
* @author     Magebay Developer Team <info@magebay.com>
* @copyright  Copyright (c) 2009-2013 MAGEBAY.COM. (http://www.magebay.com)
*/
class MST_Pdp_Adminhtml_ShapeController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('pdp/pdp')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Design Manager'), Mage::helper('adminhtml')->__('Design Manager'));
        return $this;
    }
	public function indexAction()
    {
        //$this->_initAction();
		$this->loadLayout()->_setActiveMenu('pdp/pdp');
        $this->renderLayout();
    }
    public function uploadAction() {
        $request = $this->getRequest()->getPost();
        if(!isset($request['category']) || !$request['category']) {
            Mage::getSingleton('adminhtml/session')->addError("Please add shape category before upload shapes!");
            $this->_redirect('*/*/index');
            return;
        }
        $baseDir = Mage::getBaseDir('media') . DS . "pdp" . DS . "shapes" . DS;
        if (!file_exists($baseDir)) {
            mkdir($baseDir, 0777);
        }
        if(!file_exists($baseDir)) {
            Mage::getSingleton('adminhtml/session')->addError("Can not create shapes folder: media/pdp/shapes");
            $this->_redirect('*/*/index');
			return;
        }
        $shapeModel = Mage::getModel("pdp/shapes");
        foreach ($_FILES["shapes"]["error"] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {
                if(file_exists($baseDir)) {
                    $tmp_name = $_FILES["shapes"]["tmp_name"][$key];
                    //$tmp_name = "shape-" . time() . $key . ".svg";
                    $name = $_FILES["shapes"]["name"][$key];
                    $ext = substr($name, strrpos($name, '.') + 1);
                    if($ext != "svg") {
                        Mage::getSingleton('adminhtml/session')->addError("Please upload svg file only!");
				        $this->_redirect('*/*/index');
				        return;
                    }
                    $filename = "shape-" . time() . $key . "." . $ext;
                    $result = move_uploaded_file($tmp_name, $baseDir . $filename);
                    if($result) {
                        //Save data to db
                        $shape = array(
                            'filename' => $filename,
                            'original_filename' => $name,
                            'tag' => $request['tag'],
                            'category' => $request['category']
                        );
                        $shapeModel->setData($shape)->save();
                    }
                }
            }
        }
        $this->_redirect("*/*/index/");
    }
	public function deleteImageAction ()
	{
		$data = $this->getRequest()->getParams();
		if ($data['id'] != "") {
			$image = Mage::getModel('pdp/shape')->load($data['id']);
            $filename = $image->getFilename();
            $path = Mage::getBaseDir('media').DS.'pdp/shapes/';
            unlink($path . $filename);
            $image->delete();
			$this->getResponse()->setBody("delete_" . $data['id']);
		}
	}
}