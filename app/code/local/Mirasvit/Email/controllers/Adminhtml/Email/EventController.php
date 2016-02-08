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


class Mirasvit_Email_Adminhtml_Email_EventController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('email')
            ->_title(Mage::helper('email')->__('Follow Up Email'), Mage::helper('email')->__('Follow Up Email'))
            ->_title(Mage::helper('email')->__('Event Log'), Mage::helper('email')->__('Event Log'));

        return $this;
    }

    public function indexAction()
    {
        $this->_title(Mage::helper('email')->__('Mail Log'));
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('email/adminhtml_event'));
        $this->renderLayout();
    }

    public function checkAction()
    {
        Mage::getSingleton('email/observer')->checkEvents();

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('email')->__('Completed'));
        
        $this->_redirect('*/*/index');
    }

    public function resetAction()
    {
        $this->getModel()->removeProcessedTriggers();

        $triggers = Mage::getModel('email/trigger')->getCollection()
            ->addActiveFilter();

        foreach ($triggers as $trigger) {
            $trigger->processNewEvents();
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('email')->__('The triggers for events are reseted.'));
        
        $this->_redirect('*/*/');
    }

    public function removeAction()
    {
        $this->getModel()->delete();

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('email')->__('The event is removed.'));
        
        $this->_redirect('*/*/');
    }

    public function getModel()
    {
        $model = Mage::getModel('email/event');

        if ($this->getRequest()->getParam('id')) {
            $model->load($this->getRequest()->getParam('id'));
        }

        Mage::register('current_model', $model);

        return $model;
    }

	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('email/email_system/email_event');
	}
}