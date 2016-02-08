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



class Mirasvit_Email_Adminhtml_Email_TriggerController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('email')
            ->_title(Mage::helper('email')->__('Follow Up Email'), Mage::helper('email')->__('Follow Up Email'))
            ->_title(Mage::helper('email')->__('Manage Triggers'), Mage::helper('email')->__('Manage Triggers'));

        return $this;
    }

    public function indexAction()
    {
        $this->_validate();

        $this->_title(Mage::helper('email')->__('Manage Triggers'));
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('email/adminhtml_trigger'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $model = $this->getModel();
        $this->_initAction();
        $this->_title(Mage::helper('email')->__('New Trigger'));

        $this->_addContent($this->getLayout()->createBlock('email/adminhtml_trigger_edit'))
            ->_addLeft($this->getLayout()->createBlock('email/adminhtml_trigger_edit_tabs'));

        $this->renderLayout();
    }

    public function editAction()
    {
        $model = $this->getModel();

        if ($model->getId()) {
            $this->_initAction();
            $this->_title(Mage::helper('email')->__("Edit Trigger '%s'", $model->getTitle()));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('email/adminhtml_trigger_edit'))
                ->_addLeft($this->getLayout()->createBlock('email/adminhtml_trigger_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('email')->__('The trigger does not exist.'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction()
    {
        $back = $this->getRequest()->getParam('back');

        if ($data = $this->getRequest()->getPost()) {
            $data = $this->_filterPostData($data);

            $model = $this->getModel();

            if ($back == 'send') {
                $model->sendTest($this->getRequest()->getParam('test_email'));

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('email')->__('Test email was successfully sent'));

                $this->_redirect('*/*/edit', array('id' => $model->getId()));

                return;
            } elseif ($back == 'generate') {
                $model->generate(Mage::app()->getLocale()->date($data['generate_from'])->get(Zend_Date::TIMESTAMP));

                $this->_redirect('*/*/edit', array('id' => $model->getId()));

                return;
            }

            try {
                $model->addData($data);
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('email')->__('Trigger was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($back == 'edit') {
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

        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('email')->__('Unable to find trigger to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        try {
            $model = $this->getModel();
            $model->delete();
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));

            return;
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('email')->__('Trigger was successfully deleted'));
        $this->_redirect('*/*/');
    }

    public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('email/rule'))
            ->setPrefix('conditions');

        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

    public function massStatusAction()
    {
        $triggerIds = $this->getRequest()->getParam('trigger_id');
        $status = $this->getRequest()->getParam('status');

        if (!is_array($triggerIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('email')->__('Please select trigger(s)'));
        } else {
            try {
                foreach ($triggerIds as $triggerId) {
                    $trigger = Mage::getModel('email/trigger')->load($triggerId);
                    $trigger->setIsMassAction(true); // Do not save chain if it is a mass action

                    $trigger->setIsActive($status)
                        ->save();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('email')->__('Total of %d record(s) were updated', count($triggerIds))
                );
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $triggerIds = $this->getRequest()->getParam('trigger_id');

        if (!is_array($triggerIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('email')->__('Please select trigger(s)'));
        } else {
            try {
                foreach ($triggerIds as $triggerId) {
                    $trigger = Mage::getModel('email/trigger')->load($triggerId);

                    $trigger->delete();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('email')->__('Total of %d record(s) were deleted', count($triggerIds))
                );
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/');
    }

    public function massSendAction()
    {
        $triggerIds = $this->getRequest()->getParam('trigger_id');
        $email = $this->getRequest()->getParam('email');

        if (!is_array($triggerIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('email')->__('Please select trigger(s)'));
        } else {
            try {
                foreach ($triggerIds as $triggerId) {
                    $trigger = Mage::getModel('email/trigger')->load($triggerId);

                    $trigger->sendTest($email);
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('email')->__('Total of %d email(s) were sent', count($triggerIds))
                );
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/');
    }

    public function getModel()
    {
        $model = Mage::getModel('email/trigger');

        if ($this->getRequest()->getParam('id')) {
            $model->load($this->getRequest()->getParam('id'));
        }

        Mage::register('current_model', $model);

        return $model;
    }

    protected function _filterPostData($data)
    {
        $data = $this->_filterDateTime($data, array('active_from', 'active_to'));

        return $data;
    }

    protected function _validate()
    {
        // validate cron
        $cron = Mage::getSingleton('email/config')->validateCron();
        if ($cron !== true) {
            Mage::getSingleton('adminhtml/session')->addNotice($cron);
        }
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('email/email_trigger');
    }
}
