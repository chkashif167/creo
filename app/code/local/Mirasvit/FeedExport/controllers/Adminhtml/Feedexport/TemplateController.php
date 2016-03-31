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
 * @package   Advanced Product Feeds
 * @version   1.1.4
 * @build     702
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_FeedExport_Adminhtml_Feedexport_TemplateController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog')
            ->_title(__('Feed Export'))
            ->_title(__('Templates'));

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('feedexport/adminhtml_template'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_initModel();

        if ($id && !$model->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(__('This item not exists.'));
            $this->_redirect('*/*/');

            return;
        }

        $this->_title($id ? $model->getName() : __('New Template'));
        $this->_initAction();

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('feedexport/adminhtml_template_edit'))
            ->_addLeft($this->getLayout()->createBlock('feedexport/adminhtml_template_edit_tabs'));

        $this->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            if (isset($data['import'])) {
                return $this->doimportAction();
            }

            $model = $this->_initModel();
            $model->addData($data);

            try {
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(__('Template was successfully saved'));

                if ($this->getRequest()->getParam('back') == 'edit') {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));

                    return;
                } elseif ($this->getRequest()->getParam('back') == 'export') {
                    $this->_redirect('*/*/export', array('id' => $model->getId()));

                    return;
                } elseif ($this->getRequest()->getParam('back') == 'createfeed') {
                    $this->_redirect('*/*/createfeed', array('id' => $model->getId()));

                    return;
                }

                $this->_redirect('*/*/');

                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));

                return;
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError(__('Unable to find item to save'));
            $this->_redirect('*/*/');
        }
    }

    public function deleteAction()
    {
        try {
            $model = $this->_initModel();
            $model->delete();

            Mage::getSingleton('adminhtml/session')->addSuccess(__('Template was successfully deleted'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));

            return;
        }

        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        foreach ($this->getRequest()->getParam('template') as $id) {
            try {
                Mage::getModel('feedexport/template')->load($id)->delete();
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/');

                return;
            }

            Mage::getSingleton('adminhtml/session')->addSuccess(__('Template was successfully deleted'));
        }

        $this->_redirect('*/*/');
    }

    public function importAction()
    {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('feedexport/adminhtml_template_import'));
        $this->renderLayout();
    }

    public function doimportAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            foreach ($data['template'] as $template) {
                $model = Mage::getModel('feedexport/template')->import($template);
                Mage::getSingleton('adminhtml/session')->addSuccess(__('Template %s imported', $model->getName()));
            }
        }

        $this->_redirect('*/*/');
    }

    public function exportAction()
    {
        $model = $this->_initModel();
        $path = $model->export();

        Mage::getSingleton('adminhtml/session')->addSuccess(__('Template exported to %s', $path));
        $this->_redirect('*/*/');
    }

    public function massExportAction()
    {
        foreach ($this->getRequest()->getParam('template') as $templateId) {
            $path = Mage::getModel('feedexport/template')->load($templateId)->export();
            Mage::getSingleton('adminhtml/session')->addSuccess(__('Template exported to %s', $path));
        }

        $this->_redirect('*/*/');
    }

    public function _initModel()
    {
        $model = Mage::getModel('feedexport/template');

        if ($this->getRequest()->getParam('id')) {
            $model->load($this->getRequest()->getParam('id'));
        }

        Mage::register('current_model', $model);

        return $model;
    }

    public function getRowAction()
    {
        $attributeSelect = Mage::helper('feedexport/html')->getAttributeSelectHtml('csv[mapping][value_attribute][]', 'attribute', 'width:180px;display:block');
        $outputType = Mage::helper('feedexport/html')->getFormattersHtml('csv[mapping][formatters][]');
        $output = array('value' => $attributeSelect, 'type' => $outputType);

        $this->getResponse()
            ->clearHeaders()
            ->setHeader('content-type', 'application/json')
            ->setBody(json_encode($output));
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/feedexport/feedexport_template');
    }
}
