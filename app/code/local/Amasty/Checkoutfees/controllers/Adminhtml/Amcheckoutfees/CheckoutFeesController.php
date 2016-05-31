<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */


class Amasty_Checkoutfees_Adminhtml_Amcheckoutfees_CheckoutFeesController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('sales/amcheckoutfees');
        $this->_addContent($this->getLayout()->createBlock('amcheckoutfees/adminhtml_fees'));
        $this->renderLayout();
    }

    protected function _setActiveMenu($menuPath)
    {
        $this->getLayout()->getBlock('menu')->setActive($menuPath);
        $this->_title($this->__('Sales'))->_title($this->__('Fees'));

        return $this;
    }

    public function newAction()
    {
        $this->editAction();
    }

    public function editAction()
    {
        $id    = (int)$this->getRequest()->getParam('id');
        $model = Mage::getModel('amcheckoutfees/fees')->load($id);

        if ($id && !$model->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amcheckoutfees')->__('Item does not exist'));
            $this->_redirect('*/*/');

            return;
        }

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('amcheckoutfees_fees', $model);

        $this->loadLayout();

        $head = $this->getLayout()->getBlock('head');
        $head->setCanLoadExtJs(1);
        $head->setCanLoadRulesJs(1);

        $this->_setActiveMenu('sales/amcheckoutfees');
        $this->_addContent($this->getLayout()->createBlock('amcheckoutfees/adminhtml_fees_edit'));
        $this->_addLeft($this->getLayout()->createBlock('amcheckoutfees/adminhtml_fees_edit_tabs'));

        $this->renderLayout();
    }

    public function saveAction()
    {
        $id    = $this->getRequest()->getParam('id');
        $data  = $this->getRequest()->getPost();
        $model = Mage::getModel('amcheckoutfees/fees');
        $newIds = array();

        if ($id !== null && $id <= 0) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amcheckoutfees')->__('Unable to find an item to save'));
            $this->_redirect('*/*/');
        } elseif ($data) {
            try {
                // prepare Fee title array for packed save
                if (isset($data['title'])) {
                    $data['title'] = serialize($data['title']);
                }
                // prepare Stores and CustomerGroups array to string conversion
                if (isset($data['stores'])) {
                    $data['stores'] = ',' . implode(',', $data['stores']) . ',';
                }
                if (isset($data['cust_groups'])) {
                    $data['cust_groups'] = ',' . implode(',', $data['cust_groups']) . ',';
                }

                // prepare Rule array for packed save
                if (isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                }
                if (isset($data['rule']['actions'])) {
                    $data['actions'] = $data['rule']['actions'];
                }
                unset($data['rule']);


                // save Fee data
                $model->setData($data);
                $model->loadPost($data);
                $model->setId($id);
                $model->save();

                // saved Fee id (if new was created)
                $id = $model->getId();

                // process saving for all options
                if (!empty($data['options']) && is_array($data['options'])) {
                    foreach ($data['options'] as $name => $option) {
                        // init fresh variable
                        $feeData = Mage::getModel('amcheckoutfees/feesData');
                        // name will be like "new_opt_ID"
                        list($optionType, $optionId) = explode('_', $name);

                        // check
                        if ($optionType == 'default') {
                            $default = $option['is_default'];
                        }

                        // check if already existing row
                        if ($optionType == 'op' && $optionId > 0) {
                            $option['fees_data_id'] = $optionId;
                        }

                        // check if option delete flag exists
                        if (isset($option['delete']) && $option['delete'] == 1) {
                            $feeData->setData($option);
                            $feeData->delete();
                            continue;
                        }

                        // check for required before save
                        if ((($optionType == 'op' && $optionId > 0) || ($optionType == 'new')) && (is_array($option['title']) && !empty($option['title'][0]))) {
                            // set option main data from form
                            $feeData->setData($option);
                            $feeData->setFeesId($id);
                            $feeData->setIsDefault(0);

                            // save option titles
                            if (isset($option['title']) && is_array($option['title'])) {
                                $feeData->setTitle(serialize($option['title']));
                            }

                            $feeData->save();
                            // save optionId for "isDefault" option
                            $newIds[$name] = $feeData->getFeesDataId();
                        }
                    }

                    // override default value
                    if (isset($default) && !empty($default) && isset($newIds[$default]) && !empty($newIds[$default])) {
                        $feeData = Mage::getModel('amcheckoutfees/feesData');
                        $feeData->setFeesDataId($newIds[$default]);
                        $feeData->setIsDefault(1);
                        $feeData->save();
                    }
                }


                Mage::getSingleton('adminhtml/session')->setFormData(false);
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amcheckoutfees')->__('Item has been successfully saved'));

                if ($this->getRequest()->getParam('continue')) {
                    $this->_redirect('*/*/edit', array('id' => $id));
                } else {
                    $this->_redirect('*/*');
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $id));
            }
        }
    }


    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('ids');
        if (!(is_numeric($ids) || is_array($ids))) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amcheckoutfees')->__('Please select records'));
            $this->_redirect('*/*/');

            return;
        }

        try {
            Mage::getModel('amcheckoutfees/fees')->massDelete($ids);
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('adminhtml')->__(
                    'Total of %d record(s) were successfully deleted', count($ids)
                )
            );
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/');

        return;
    }

    public function deleteAction()
    {
        $ids = $this->getRequest()->getParam('id');
        if (!is_numeric($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amcheckoutfees')->__('Please select records'));
            $this->_redirect('*/*/');

            return;
        }

        try {
            Mage::getModel('amcheckoutfees/fees')->massDelete(array($ids));
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('adminhtml')->__(
                    'Total of %d record(s) were successfully deleted', count(array($ids))
                )
            );
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/');

        return;
    }


    public function massEnableAction()
    {
        $ids = $this->getRequest()->getParam('ids');
        if (!(is_numeric($ids) || is_array($ids))) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amcheckoutfees')->__('Please select records'));
            $this->_redirect('*/*/');

            return;
        }

        try {
            Mage::getModel('amcheckoutfees/fees')->massEnable($ids);
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('adminhtml')->__(
                    'Total of %d record(s) were successfully enabled', count($ids)
                )
            );
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/');

        return;
    }


    public function massDisableAction()
    {
        $ids = $this->getRequest()->getParam('ids');
        if (!(is_numeric($ids) || is_array($ids))) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amcheckoutfees')->__('Please select records'));
            $this->_redirect('*/*/');

            return;
        }

        try {
            Mage::getModel('amcheckoutfees/fees')->massDisable($ids);
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('adminhtml')->__(
                    'Total of %d record(s) were successfully disabled', count($ids)
                )
            );
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/');

        return;
    }

    public function newConditionHtmlAction()
    {
        $this->newConditions('conditions');
    }

    public function newConditions($prefix)
    {
        $id      = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type    = $typeArr[0];

        $model = Mage::getModel($type)
                     ->setId($id)
                     ->setType($type)
                     ->setRule(Mage::getModel('salesrule/rule'))
                     ->setPrefix($prefix);
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

    public function newActionHtmlAction()
    {
        $this->newConditions('actions');
    }

    public function chooserAction()
    {
        $uniqId       = $this->getRequest()->getParam('uniq_id');
        $chooserBlock = $this->getLayout()->createBlock('adminhtml/promo_widget_chooser', '', array(
            'id' => $uniqId
        )
        );
        $this->getResponse()->setBody($chooserBlock->toHtml());
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/amcheckoutfees');
    }
}