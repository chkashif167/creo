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



/**
 * @category Mirasvit
 */
class Mirasvit_SearchSphinx_Adminhtml_Searchsphinx_SynonymController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('search')
            ->_title($this->__('Dictionary of synonyms'));
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('searchsphinx/adminhtml_synonym'));
        $this->renderLayout();
    }

    public function importAction()
    {
        $this->_initAction()
            ->_title($this->__('Import Dictionary'));

        $this->_addContent($this->getLayout()->createBlock('searchsphinx/adminhtml_synonym_import'));

        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_getModel();

        $this->_title(Mage::helper('searchsphinx')->__('Add Synonyms'));
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('searchsphinx/adminhtml_synonym_edit'))
            ->renderLayout();
    }

    public function editAction()
    {
        $model = $this->_getModel();

        if ($model->getId()) {
            $this->_title(Mage::helper('searchsphinx')->__('Edit Synonyms'));

            $this->_initAction()
                ->_addContent($this->getLayout()->createBlock('searchsphinx/adminhtml_synonym_edit'))
                ->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('searchsphinx')->__('The synonym does not exist.'));
            $this->_redirect('*/*/');
        }
    }

    public function deleteAction()
    {
        try {
            $model = $this->_getModel();
            $model->delete();

            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('searchsphinx')->__('Synonym was successfully deleted'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/');
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            try {
                $model = $this->_getModel();
                $model->addData($data)
                    ->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('searchsphinx')->__('Synonym is saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));

                    return;
                }

                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);

                if ($model->getId()) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                } else {
                    $this->_redirect('*/*/new');
                }
            }
        }
    }

    public function massImportAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            try {
                $result = Mage::getSingleton('searchsphinx/synonym')->import($data['file'], $data['store']);

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('searchsphinx')->__('Imported %s synonyms', $result));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);

                $this->_redirect('*/*/import');
            }
        }
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('synonym');

        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('searchsphinx')->__('Please select synonym(s)'));
        } else {
            try {
                foreach ($ids as $itemId) {
                    $model = Mage::getModel('searchsphinx/synonym')->setIsMassDelete(true)
                        ->load($itemId);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('searchsphinx')->__('Total of %d record(s) were successfully deleted', count($ids))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    protected function _getModel()
    {
        $model = Mage::getModel('searchsphinx/synonym');

        if ($id = $this->getRequest()->getParam('id')) {
            $model->load($id);
        }

        Mage::register('current_model', $model);

        return $model;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('search/searchsphinx_synonyms');
    }
}
