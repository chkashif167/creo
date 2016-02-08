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
 * @package   Sphinx Search Ultimate
 * @version   2.3.2
 * @build     1290
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_SearchLandingPage_Adminhtml_Searchlandingpage_PageController extends Mage_Adminhtml_Controller_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        return $this;
    }

    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('search');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Landing Pages'));
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('searchlandingpage/adminhtml_page'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_getModel();

        $this->_title(Mage::helper('searchlandingpage')->__('Add Landing Page'));
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('searchlandingpage/adminhtml_page_edit'))
            ->renderLayout();
    }

    public function editAction()
    {
        $model = $this->_getModel();

        if ($model->getId()) {
            $this->_title(Mage::helper('searchlandingpage')->__('Edit Search Index "%s"', $model->getTitle()));

            $this->_initAction()
                ->_addContent($this->getLayout()->createBlock('searchlandingpage/adminhtml_page_edit'))
                ->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('action')->__('The search index does not exist.'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            try {
                $model = $this->_getModel();
                $model->addData($data)
                    ->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('searchlandingpage')->__('Landing Page "%s" saved', $model->getTitle()));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));

                    return;
                }

                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);

                $this->_redirect('*/*/');
            }
        }
    }

    public function deleteAction()
    {
        try {
            $model = $this->_getModel();
            $model->delete();

            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('searchlandingpage')->__('Landing Page was successfully deleted'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/');
    }

    protected function _getModel()
    {
        $model = Mage::getModel('searchlandingpage/page');

        if ($id = $this->getRequest()->getParam('id')) {
            $model->load($id);
        }

        Mage::register('current_model', $model);

        return $model;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('search/searchlandingpage_page');
    }
}
