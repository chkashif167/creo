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


class Mirasvit_EmailSmtp_Adminhtml_Emailsmtp_MailController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction ()
    {
        $this->loadLayout()->_setActiveMenu('email');

        return $this;
    }

    public function indexAction ()
    {
        $this->_title($this->__('Mail Manager'));
        $this->_initAction();
        $this->_addContent($this->getLayout()
            ->createBlock('emailsmtp/adminhtml_mail'));
        $this->renderLayout();
    }

    public function viewAction()
    {
        $this->loadLayout();
        $this->_initAction();

        $this->getModel();

        $this->_addContent($this->getLayout()->createBlock('emailsmtp/adminhtml_mail_view'));
        $this->renderLayout();
    }

    public function previewAction()
    {
        $model = $this->getModel();

        $this->getResponse()->setBody($model->getBody());
    }

    public function getModel()
    {
        $model = Mage::getModel('emailsmtp/mail');

        if ($this->getRequest()->getParam('id')) {
            $model->load($this->getRequest()->getParam('id'));
        }

        Mage::register('current_model', $model);


        return $model;
    }

	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('email/email_system/emailsmtp');
	}
}