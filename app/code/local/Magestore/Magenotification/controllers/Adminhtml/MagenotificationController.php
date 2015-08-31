<?php

class Magestore_Magenotification_Adminhtml_MagenotificationController extends Mage_Adminhtml_Controller_Action
{	  

	public function readdetailAction()
	{
		$id = $this->getRequest()->getParam('id');
		$notice = Mage::getModel('magenotification/inbox')->load($id);
		$notice->setIsRead(1);
		$notice->save();
		return $this->_redirectUrl($notice->getUrl());
	}
	public function indexAction(){
		$this->_title($this->__('System'))->_title($this->__('Notifications'));

        $this->loadLayout()
            ->_setActiveMenu('system/magestore_extension')
            ->_addBreadcrumb(Mage::helper('magenotification')->__('Messages Inbox'), Mage::helper('magenotification')->__('Messages Inbox'))
            ->_addContent($this->getLayout()->createBlock('magenotification/adminhtml_notification_inbox'))
			//->_addContent($this->getLayout()->createBlock('magenotification/adminhtml_notification_inbox_grid'))
            ->renderLayout();	
	}
	public function gridAction(){
		$this->_title($this->__('System'))->_title($this->__('Notifications'));

        $this->loadLayout()
            ->_setActiveMenu('system/magestore_extension')
            ->_addBreadcrumb(Mage::helper('magenotification')->__('Messages Inbox'), Mage::helper('magenotification')->__('Messages Inbox'))
            ->_addContent($this->getLayout()->createBlock('magenotification/adminhtml_notification_inbox'))
			//->_addContent($this->getLayout()->createBlock('magenotification/adminhtml_notification_inbox_grid'))
            ->renderLayout();	
	}
	public function markAsReadAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $session = Mage::getSingleton('adminhtml/session');
            $model = Mage::getModel('magenotification/magenotification')
                ->load($id);

            if (!$model->getId()) {
                $session->addError(Mage::helper('magenotification')->__('Unable to proceed. Please, try again.'));
                $this->_redirect('*/*/');
                return ;
            }

            try {
                $model->setIsRead(1)
                    ->save();
                $session->addSuccess(Mage::helper('magenotification')->__('The message has been marked as read.'));
            } catch (Mage_Core_Exception $e) {
                $session->addError($e->getMessage());
            } catch (Exception $e) {
                $session->addException($e, Mage::helper('magenotification')->__('An error occurred while marking notification as read.'));
            }

            $this->_redirectReferer();
            return;
        }
        $this->_redirect('*/*/');
    }
	public function removeAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $session = Mage::getSingleton('adminhtml/session');
            $model = Mage::getModel('magenotification/magenotification')
                ->load($id);

            if (!$model->getId()) {
                $this->_redirect('*/*/');
                return ;
            }

            try {
                $model->setIsRemove(1)
                    ->save();
                $session->addSuccess(Mage::helper('magenotification')->__('The message has been removed.'));
            } catch (Mage_Core_Exception $e) {
                $session->addError($e->getMessage());
            } catch (Exception $e) {
                $session->addException($e, Mage::helper('magenotification')->__('An error occurred while removing the message.'));
            }

            $this->_redirect('*/*/');
            return;
        }
        $this->_redirect('*/*/');
    }
	public function massRemoveAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        $ids = $this->getRequest()->getParam('notification');
        if (!is_array($ids)) {
            $session->addError(Mage::helper('magenotification')->__('Please select messages.'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getModel('magenotification/magenotification')
                        ->load($id);
                    if ($model->getId()) {
                        $model->setIsRemove(1)
                            ->save();
                    }
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('magenotification')->__('Total of %d record(s) have been removed.', count($ids))
                );
            } catch (Mage_Core_Exception $e) {
                $session->addError($e->getMessage());
            } catch (Exception $e) {
                $session->addException($e, Mage::helper('magenotification')->__('An error occurred while removing messages.'));
            }
        }
        $this->_redirectReferer();
    }
	public function massMarkAsReadAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        $ids = $this->getRequest()->getParam('notification');
        if (!is_array($ids)) {
            $session->addError(Mage::helper('magenotification')->__('Please select messages.'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getModel('magenotification/magenotification')
                        ->load($id);
                    if ($model->getId()) {
                        $model->setIsRead(1)
                            ->save();
                    }
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('magenotification')->__('Total of %d record(s) have been marked as read.', count($ids))
                );
            } catch (Mage_Core_Exception $e) {
                $session->addError($e->getMessage());
            } catch (Exception $e) {
                $session->addException($e, Mage::helper('magenotification')->__('An error occurred while marking the messages as read.'));
            }
        }
        $this->_redirect('*/*/');
    }
	 protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('magenotification');
    }
}