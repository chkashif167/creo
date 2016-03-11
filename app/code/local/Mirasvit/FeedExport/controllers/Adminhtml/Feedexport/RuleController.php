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



class Mirasvit_FeedExport_Adminhtml_Feedexport_RuleController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        if ($this->getRequest()->getParam('popup')) {
            $this->loadLayout('popup');
        } else {
            $this->loadLayout()
                ->_setActiveMenu('catalog')
                ->_title(__('Feed Export'))
                ->_title(__('Filters'));
        }

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('feedexport/adminhtml_rule'));
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

        if ($this->getRequest()->getParam('type')) {
            $model->setType($this->getRequest()->getParam('type'));
        }

        if ($id && !$model->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(__('This item not exists.'));
            $this->_redirect('*/*/');

            return;
        }

        $this->_title($id ? $model->getName() : __('New Filter'));
        $this->_initAction();

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('feedexport/adminhtml_rule_edit'))
            ->_addLeft($this->getLayout()->createBlock('feedexport/adminhtml_rule_edit_tabs'));

        $this->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = $this->_initModel();
            $model->addData($data['data']);

            if (isset($data['rule'])) {
                $model->loadPost($data['rule']);
            }

            try {
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(__('Filter was successfully saved'));

                if ($this->getRequest()->getParam('popup')) {
                    $this->_redirect('feedexport/adminhtml_feed/addRule', array(
                        'group' => $this->getRequest()->getParam('group'),
                        'rule' => $model->getId(),
                        '_current' => true,
                    ));

                    return;
                } elseif ($this->getRequest()->getParam('back') == 'edit') {
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

            Mage::getSingleton('adminhtml/session')->addSuccess(__('Item was successfully deleted'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));

            return;
        }

        $this->_redirect('*/*/');
    }

    public function exportAction()
    {
        $model = $this->_initModel();
        $path = $model->export();

        Mage::getSingleton('adminhtml/session')->addSuccess(__('Filter rule exported to %s', $path));

        $this->_redirect('*/*/');
    }

    public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];
        if (isset($typeArr[2])) {
            $type .= '_'.$typeArr[2];
        }

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('feedexport/rule'))
            ->setPrefix('conditions');

        if (!empty($typeArr[1])) {
            $attribute = str_replace(Mirasvit_FeedExport_Model_Rule_Condition_Combine_Parent::ATTR_CODE_PREFIX, '', $typeArr[1]);
            $model->setAttribute($attribute);
        }

        if ($model instanceof Mirasvit_FeedExport_Model_Rule_Condition_Product_Parent) {
            $model->setRulePrefix('_parent');
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

    public function _initModel()
    {
        $model = Mage::getModel('feedexport/rule');

        if ($this->getRequest()->getParam('id')) {
            $model->load($this->getRequest()->getParam('id'));
        }

        if ($this->getRequest()->getParam('type')) {
            $model->setType($this->getRequest()->getParam('type'));
        }

        if ($this->getRequest()->getParam('feed')) {
            $model->setFeedIds(array($this->getRequest()->getParam('feed')));
        }

        Mage::register('current_model', $model);

        return $model;
    }

    public function duplicateAction()
    {
        try {
            $model = $this->_initModel();
            $model->duplicate();
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/');

            return;
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('feedexport')->__('Rule was successfully duplicated'));
        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/feedexport/feedexport_rule');
    }
}
