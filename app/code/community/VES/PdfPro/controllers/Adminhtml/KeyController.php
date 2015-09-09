<?php
/**
 * VES_PdfPro_Adminhtml_KeyController
 *
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */

class VES_PdfPro_Adminhtml_KeyController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('pdfpro/api_key')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Easy PDF'), Mage::helper('adminhtml')->__('Manage Keys'));
		
		return $this;
	}
	
	public function indexAction(){		
		$this->_initAction();
		$this->renderLayout();
	}
	
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('pdfpro/key')->load($id);
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('key_data', $model);
			$this->loadLayout();
			$this->_setActiveMenu('pdfpro/api_key');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Easy PDF'), Mage::helper('adminhtml')->__('Easy PDF'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Add new API Key'), Mage::helper('adminhtml')->__('Add new API Key'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('pdfpro')->__('Group does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 	
	public function saveAction() {
			$data = $this->getRequest()->getParams();
			$data['api_key'] = trim($data['api_key']);
			$model = Mage::getModel('pdfpro/key');
			$data['store_ids']	= implode(',', $data['store_ids']);
			$data['customer_group_ids']	= implode(',', $data['customer_group_ids']);
			$model->setData($data)
			->setId($this->getRequest()->getParam('id'));
			Mage::dispatchEvent('ves_pdfpro_apikey_form_save_before',array('model'=>$model));
			try {
				$model->save();
				Mage::dispatchEvent('ves_pdfpro_apikey_form_save_after',array('model'=>$model));
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('pdfpro')->__('The API Key has been saved.'));
				Mage::getSingleton('adminhtml/session')->setGroupData(false);
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setGroupData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('pdfpro/key');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}
	
	public function checkforupdateAction(){
		try{
			$versionFile 	= Mage::getBaseDir('media').DS.'ves_pdfpro'.DS.'version.txt';
			$date 			= Mage::getModel('core/date')->date('Y-m-d');
			$serverVersion 	= Mage::helper('pdfpro')->getServerVersion();
			try{
				$fp				= fopen($versionFile, 'w');
				fwrite($fp, base64_encode(json_encode(array('date'=>$date,'version'=>$serverVersion))));
				fclose($fp);
			}catch(Exception $e){
				
			}
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('pdfpro')->__('Your current version of Easy PDF Invoice is "%s". The latest version from server is "%s".',Mage::helper('pdfpro')->getVersion(),$serverVersion));
		}catch (Exception $e){
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('pdfpro')->__($e->getMessage()));
		}
		$this->_redirect('*/*/');
	}
}