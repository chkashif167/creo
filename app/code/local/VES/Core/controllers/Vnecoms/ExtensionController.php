<?php
class VES_Core_Vnecoms_ExtensionController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
    	$this->loadLayout();
    	$this->_setActiveMenu('system/vnecoms');
    	$block = $this->getLayout()->createBlock('ves_core/adminhtml_key','license_key_grid');
    	$this->_addContent($block);
    	$this->renderLayout();
    }
    
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('ves_core/key')->load($id);
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('key_data', $model);
			$this->loadLayout();
			$this->_setActiveMenu('system/vnecoms');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Easy PDF'), Mage::helper('adminhtml')->__('Easy PDF'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Add new API Key'), Mage::helper('adminhtml')->__('Add new API Key'));
			$this->_addContent($this->getLayout()->createBlock('ves_core/adminhtml_key_edit','license_key_edit'))
				->_addLeft($this->getLayout()->createBlock('ves_core/adminhtml_key_edit_info','license_key_edit_info'));
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ves_core')->__('Group does not exist'));
			$this->_redirect('*/*/');
		}
	}
	
	
	public function newAction() {
		$this->_forward('edit');
	}
	
	
	public function saveAction() {
		$licenseKey = $this->getRequest()->getParam('license_key');
		try {
			$model = Mage::getModel('ves_core/key');
			
			/*Check if the license key is already exist in the database*/
			if(!$this->getRequest()->getParam('id')){
				$model->load($licenseKey,'license_key');			
				if($model->getId()) throw new Exception(Mage::helper('ves_core')->__('The license key %s is already exist.',$licenseKey));
			}
			
			$result = $model->getKeyInfo($licenseKey);
			if(!isset($result['success']) || !$result['success']){
				if(isset($result['msg']) && $result['msg']) throw new Exception($result['msg']);
				else throw new Exception(Mage::helper('ves_core')->__('Could not retrieve license key info.'));
			}
			
			$licenseInfo = unserialize($model->decode($result['result'], VES_Core_Model_Key::ENCODED_KEY));
			if(!is_array($licenseInfo)){
				throw new Exception(Mage::helper('ves_core')->__('Could not retrieve license key info.'));
			}
			
			$model->setData(array('license_key'=>$licenseKey, 'license_info'=>$result['result']));
			if($this->getRequest()->getParam('id')){
				$model->setId($this->getRequest()->getParam('id'));
			}
			$model->save();
			Mage::getSingleton('adminhtml/session')->unsetData('check_ves_notification_message');
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ves_core')->__('The license key has been saved.'));
			Mage::getSingleton('adminhtml/session')->setGroupData(false);
			if ($this->getRequest()->getParam('back')) {
				$this->_redirect('*/*/edit', array('id' => $model->getId()));
				return;
			}
			$this->_redirect('*/*/');
			return;
		} catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			return;
		}
		
	}
	
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('ves_core/key');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
				Mage::getSingleton('adminhtml/session')->unsetData('check_ves_notification_message');
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}
}