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



class Mirasvit_FeedExport_Adminhtml_Feedexport_FeedController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog')
            ->_addBreadcrumb(Mage::helper('feedexport')->__('Feed Export'), Mage::helper('feedexport')->__('Feed Export'));

        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Manage Feeds'));
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('feedexport/adminhtml_feed'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_testWritable();

        $model = $this->getModel();
        $this->_title($this->__('New Feed'));

        $this->_initAction();

        $this->_addContent($this->getLayout()->createBlock('feedexport/adminhtml_feed_edit'))
            ->_addLeft($this->getLayout()->createBlock('feedexport/adminhtml_feed_edit_tabs'));

        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_testWritable();

        $model = $this->getModel();

        if ($model->getId()) {
            $this->_title($model->getName());

            $this->_initAction();

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('feedexport/adminhtml_feed_edit'))
                ->_addLeft($this->getLayout()->createBlock('feedexport/adminhtml_feed_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('feedexport')->__('The feed does not exist.'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = $this->getModel();

            $feed = $data['feed'];
            unset($data['feed']);

            $feed['rule_ids'] = isset($feed['rule_ids']) ? $feed['rule_ids'] : array();

            $ruleIds = array();
            foreach ($feed['rule_ids'] as $key => $value) {
                $ruleIds[] = $key;
            }
            $feed['rule_ids'] = $ruleIds;

            $data = array_merge($data, $feed);

            if (isset($data['template_id'])) {
                $model->fromTemplate($data['template_id']);
            }

            $model->addData($data);

            try {
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('feedexport')->__('Feed was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back') == 'edit') {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));

                    return;
                } elseif ($this->getRequest()->getParam('back') == 'delivery') {
                    $this->_redirect('*/*/delivery', array('id' => $model->getId()));

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

        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('feedexport')->__('Unable to find feed to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        try {
            $model = $this->getModel();
            $model->delete();
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));

            return;
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('feedexport')->__('Feed was successfully deleted'));
        $this->_redirect('*/*/');
    }

    public function stateAction()
    {
        $html = Mage::app()->getLayout()
            ->createBlock('feedexport/adminhtml_feed_generator_loader')
            ->setFeed($this->getModel())
            ->toHtml();

        $this->getResponse()->clearBody();
        $this->getResponse()->setBody($html);
    }

    public function addRuleAction()
    {
        $this->_getSession()->addNotice(
            Mage::helper('feedexport')->__('Please click on the Close Window button if it is not closed automatically.')
        );
        $this->loadLayout('popup');
        $this->getModel();
        $this->_addContent(
            $this->getLayout()->createBlock('feedexport/adminhtml_rule_new_created')
        );
        $this->renderLayout();
    }

    public function deliveryAction()
    {
        $feed = $this->getModel();

        try {
            $feed->delivery();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('feedexport')->__('Feed was successfully delivered'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('feedexport')->__('Unable to delivery feed <br>'.$e->getMessage()));
        }

        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
    }

    public function getModel()
    {
        $model = Mage::getModel('feedexport/feed');

        if ($this->getRequest()->getParam('id')) {
            $model->load($this->getRequest()->getParam('id'));
        }

        Mage::register('current_model', $model);

        return $model;
    }

    public function historyGridAction()
    {
        $this->getModel();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('feedexport/adminhtml_feed_edit_tab_history_grid')->toHtml()
        );
    }

    public function resetProductsAction()
    {
        $feed = $this->getModel();

        try {
            $resource = Mage::getSingleton('core/resource');
            $connection = $resource->getConnection('core_write');
            $feedProductTable = $resource->getTableName('feedexport/feed_product');

            $connection->query('DELETE FROM '.$feedProductTable.' WHERE `feed_id`='.$feed->getId());

            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('feedexport')->__('Products was successfully reseted'));
        } catch (Exception $e) {
        }

        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
    }

    public function massProductsExportAction()
    {
        $feedId = $this->getRequest()->getParam('feed_id');
        $productIds = $this->getRequest()->getParam('product');

        $feed = Mage::getModel('feedexport/feed')->load($feedId);
        $feed->getResource()->saveProductIds($feed, $productIds);

        $this->_redirect('*/*/edit', array('id' => $feed->getId(), 'generate' => 1));
    }

    protected function _testWritable()
    {
        $path = Mage::getSingleton('feedexport/config')->getBasePath();

        // if (!Mage::helper('feedexport')->isWritable($path)) {
        //     Mage::getSingleton('adminhtml/session')->addError(Mage::helper('feedexport')->__('The path "%s" is not writable!', $path));
        // }
    }

    public function duplicateAction()
    {
        try {
            $model = $this->getModel();
            $model->duplicate();
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/');

            return;
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('feedexport')->__('Feed was successfully duplicated'));
        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/feedexport');
    }
}
