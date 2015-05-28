<?php

class Mks_Bannerslider_Adminhtml_ImagegalleryController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("bannerslider/imagegallery")->_addBreadcrumb(Mage::helper("adminhtml")->__("Imagegallery  Manager"),Mage::helper("adminhtml")->__("Imagegallery Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Bannerslider"));
			    $this->_title($this->__("Manager Imagegallery"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Bannerslider"));
				$this->_title($this->__("Imagegallery"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("bannerslider/imagegallery")->load($id);
				if ($model->getId()) {
					Mage::register("imagegallery_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("bannerslider/imagegallery");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Imagegallery Manager"), Mage::helper("adminhtml")->__("Imagegallery Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Imagegallery Description"), Mage::helper("adminhtml")->__("Imagegallery Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("bannerslider/adminhtml_imagegallery_edit"))->_addLeft($this->getLayout()->createBlock("bannerslider/adminhtml_imagegallery_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("bannerslider")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Bannerslider"));
		$this->_title($this->__("Imagegallery"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("bannerslider/imagegallery")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("imagegallery_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("bannerslider/imagegallery");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Imagegallery Manager"), Mage::helper("adminhtml")->__("Imagegallery Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Imagegallery Description"), Mage::helper("adminhtml")->__("Imagegallery Description"));


		$this->_addContent($this->getLayout()->createBlock("bannerslider/adminhtml_imagegallery_edit"))->_addLeft($this->getLayout()->createBlock("bannerslider/adminhtml_imagegallery_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						
				 //save image
		try{

if((bool)$post_data['image']['delete']==1) {

	        $post_data['image']='';

}
else {

	unset($post_data['image']);

	if (isset($_FILES)){

		if ($_FILES['image']['name']) {

			if($this->getRequest()->getParam("id")){
				$model = Mage::getModel("bannerslider/imagegallery")->load($this->getRequest()->getParam("id"));
				if($model->getData('image')){
						$io = new Varien_Io_File();
						$io->rm(Mage::getBaseDir('media').DS.implode(DS,explode('/',$model->getData('image'))));	
				}
			}
						$path = Mage::getBaseDir('media') . DS . 'bannerslider' . DS .'imagegallery'.DS;
						$uploader = new Varien_File_Uploader('image');
						$uploader->setAllowedExtensions(array('jpg','png','gif'));
						$uploader->setAllowRenameFiles(false);
						$uploader->setFilesDispersion(false);
						$destFile = $path.$_FILES['image']['name'];
						$filename = $uploader->getNewFileName($destFile);
						$uploader->save($path, $filename);

						$post_data['image']='bannerslider/imagegallery/'.$filename;
		}
    }
}

        } catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
        }
//save image


						$model = Mage::getModel("bannerslider/imagegallery")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Imagegallery was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setImagegalleryData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setImagegalleryData($this->getRequest()->getPost());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					return;
					}

				}
				$this->_redirect("*/*/");
		}



		public function deleteAction()
		{
				if( $this->getRequest()->getParam("id") > 0 ) {
					try {
						$model = Mage::getModel("bannerslider/imagegallery");
						$model->setId($this->getRequest()->getParam("id"))->delete();
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
						$this->_redirect("*/*/");
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					}
				}
				$this->_redirect("*/*/");
		}

		
		public function massRemoveAction()
		{
			try {
				$ids = $this->getRequest()->getPost('ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("bannerslider/imagegallery");
					  $model->setId($id)->delete();
				}
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
			}
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}
			
		/**
		 * Export order grid to CSV format
		 */
		public function exportCsvAction()
		{
			$fileName   = 'imagegallery.csv';
			$grid       = $this->getLayout()->createBlock('bannerslider/adminhtml_imagegallery_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'imagegallery.xml';
			$grid       = $this->getLayout()->createBlock('bannerslider/adminhtml_imagegallery_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
