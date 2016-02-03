<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Follow Up Email
 * @version   1.0.2
 * @build     564
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Email_Adminhtml_Email_QueueController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('email')
            ->_title(Mage::helper('email')->__('Follow Up Email'), Mage::helper('email')->__('Follow Up Email'))
            ->_title(Mage::helper('email')->__('Mail Log (Queue)'), Mage::helper('email')->__('Mail Log (Queue)'));

        return $this;
    }

    public function indexAction()
    {
        Mage::getSingleton('adminhtml/session')->addNotice($this->_getInfo());

        $this->_title(Mage::helper('email')->__('Mail Log (Queue)'));
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('email/adminhtml_queue'));
        $this->renderLayout();
    }

    public function viewAction()
    {
        $this->loadLayout();
        $this->_initAction();

        $this->getModel();
        
        $this->_addContent($this->getLayout()->createBlock('email/adminhtml_queue_view'));
        $this->renderLayout();
    }

    public function previewAction()
    {
        $this->loadLayout();

        $model = $this->getModel();

        $this->renderLayout();
    }

    public function dropAction()
    {
        $model = $this->getModel();

        $this->getResponse()->setBody($model->getEmailContent());
    }

    public function sendAction()
    {
        $this->getModel()->send();

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('email')->__('The mail was sent.'));
        
        $this->_redirect('*/*/');
    }

    public function cancelAction()
    {
        $this->getModel()->cancel('Manually change');

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('email')->__('The mail was canceled.'));

        $this->_redirect('*/*/');
    }

    public function resetAction()
    {
        $this->getModel()->reset('Manually change');

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('email')->__('The mail was reseted.'));
        
        $this->_redirect('*/*/');
    }

    public function massSendAction()
    {
        if (is_array($this->getRequest()->getParam('queue'))) {
            foreach ($this->getRequest()->getParam('queue') as $queueId) {
                $model = Mage::getModel('email/queue')->load($queueId);
                $model->send();
            }
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('email')->__('The mails was sent.'));

        $this->_redirect('*/*/');
    }

    public function massCancelAction()
    {
        if (is_array($this->getRequest()->getParam('queue'))) {
            foreach ($this->getRequest()->getParam('queue') as $queueId) {
                $model = Mage::getModel('email/queue')->load($queueId);
                $model->cancel('Manually change');
            }
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('email')->__('The mails was canceled.'));

        $this->_redirect('*/*/');
    }

    public function massStatusAction()
    {
        $status = $this->getRequest()->getParam('status');

        if (is_array($this->getRequest()->getParam('queue'))) {
            foreach ($this->getRequest()->getParam('queue') as $queueId) {
                $model = Mage::getModel('email/queue')->load($queueId);
                $model->setStatus($status)
                    ->setStatusMessage('Manually change')
                    ->save();
            }
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('email')->__('The mails was chaged status.'));

        $this->_redirect('*/*/');
    }

    public function getModel()
    {
        $model = Mage::getModel('email/queue');

        if ($this->getRequest()->getParam('id')) {
            $model->load($this->getRequest()->getParam('id'));
        }

        Mage::register('current_model', $model);

        return $model;
    }

    protected function _getInfo()
    {
        $html = array();

        $html[] = $this->__('Current Time: <b>%s</b>', Mage::getSingleton('core/date')->date('M d, Y g:i:s A'));


        return implode('<br>', $html);
    }

	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('email/email_queue');
	}
}