<?php

/**
 * Description of IndexController
 *
 * @author Ea Design
 * This is the url to folow: http://url/index.php/adminpdfgenerator/adminhtml_PdfGenerator
 */
class EaDesign_PdfGenerator_Adminhtml_PdfgeneratorController extends Mage_Adminhtml_Controller_Action
{

    /**
     * The index action on main extension pannel
     * @return
     */
    public function indexAction()
    {
        $this->_title($this->__('System'))->_title($this->__('PDF Templates'));

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->loadLayout();
        $this->_setActiveMenu('adminpdfgenerator/adminhtml_pdfgenerator');
        $this->_addBreadcrumb(Mage::helper('pdfgenerator')->__('PDF Templates'), Mage::helper('pdfgenerator')->__('PDF Templates'));

        $this->_addContent($this->getLayout()->createBlock('pdfgenerator/adminhtml_template_template', 'pdftemplate'));

        $this->renderLayout();
    }

    /*
     * We get the data for ajax soting and stuff
     */

    public function gridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('pdfgenerator/adminhtml_template_template')->toHtml());
    }

    /**
     * The new action system to get the type we need to adjust to.
     */
    public function newpdfAction()
    {
        $this->loadLayout();

        $pdfTemplate = $this->_initPdfTemplate('id');
        /*
         * Need to activate the menu - not working yet
         */
        $this->_setActiveMenu('adminpdfgenerator/adminhtml_pdfgenerator');
        $this->_title($this->__('EaDesign PDF Templates'))->_title($this->__('PDF Templates'));

        $this->_addBreadcrumb(Mage::helper('pdfgenerator')
            ->__('New EaDesign PDF Template'), Mage::helper('pdfgenerator')
            ->__('New EaDesign PDF Template'));

        /*
         * We have the type param form controller - we know how to manage.
         */

        if (!$this->getRequest()->getParam('type')) {
            /*
             * We are in first step
             */
            $this->_addContent(
                $this->getLayout()
                    ->createBlock('pdfgenerator/adminhtml_template_pdf_new', 'pdftemplate_new')
            );
        } else {
            /*
             * we are in second step - we could forward to edit - nut we will see
             */
            $this->_addContent(
                $this->getLayout()
                    ->createBlock('pdfgenerator/adminhtml_template_pdf_edit', 'edit_form')
            );
            $this->_addLeft(
                $this->getLayout()
                    ->createBlock('pdfgenerator/adminhtml_template_pdf_edit_tabs', 'pdftemplate_edit')
            );
        }
        $this->renderLayout();
    }

    /*
     * Edit the pdf template
     */

    public function editPdfAction()
    {
        $this->loadLayout();

        $pdfTemplate = $this->_initPdfTemplate('id');

        $this->_setActiveMenu('adminpdfgenerator/adminhtml_pdfgenerator');
        $this->_title($this->__('EaDesign PDF Templates'))->_title($this->__('Edit PDF Templates'));

        $this->_addBreadcrumb(Mage::helper('pdfgenerator')
            ->__('Edit EaDesign PDF Template'), Mage::helper('pdfgenerator')
            ->__('Edit EaDesign PDF Template'));
        /*
         * The edit form!!!
         */
        $this->_addContent(
            $this->getLayout()
                ->createBlock('pdfgenerator/adminhtml_template_pdf_edit', 'edit_form')
        );
        $this->_addLeft(
            $this->getLayout()
                ->createBlock('pdfgenerator/adminhtml_template_pdf_edit_tabs', 'pdftemplate_edit')
        );


        $this->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        // check if data sent

        $data = $this->getRequest()->getPost();
        if ($data = $this->getRequest()->getPost()) {
            $data = $this->_filterPostData($data);
            //init model and set data
            $model = $this->_initPdfTemplate('pdftemplate_id');

            if ($id = $this->getRequest()->getParam('id')) {
                $model->load($id);
            }

            $model->setData($data);

            if ($id = $this->getRequest()->getParam('id')) {
                $model->setData('update_time', now());
            } else {
                $model->setData('created_time', now());
                $model->setData('update_time', now());
            }
            Mage::dispatchEvent('pdf_template_prepare_save', array('pdftemplate' => $model, 'request' => $this->getRequest()));
            try {
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('cms')->__('The page has been saved.'));

                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/editpdf', array('id' => $model->getId(), '_current' => true));
                    return;
                }

                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e, Mage::helper('pdfgenerator')->__('An error occurred while saving the template.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('pdftemplate_id' => $this->getRequest()->getParam('pdftemplate_id')));
            return;
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('id')) {
            $title = "";
            try {
                // init model and delete
                $model = $this->_initPdfTemplate('pdftemplate_id');
                $model->load($id);

                $title = $model->getTitle();
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('pdfgenerator')->__('The template has been deleted.'));
                // go to grid
                Mage::dispatchEvent('adminhtml_pdftemplate_on_delete', array('title' => $title, 'status' => 'success'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::dispatchEvent('adminhtml_pdftemplate_on_delete', array('title' => $title, 'status' => 'fail'));
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('pdftemplate_id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('pdfgenerator')->__('Unable to find a template to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }

    /*
     * We will neable a download of the first entity that mathes the requerment for user ui tests
     */

    public function previewAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /*
     * Load the email template for processing
     * 
     * @param string $pdfFieldNameId;
     * @return the model object with the loaded id
     */

    protected function _initPdfTemplate($pdfFieldNameId = 'pdftemplate_id')
    {
        $this->_title($this->__('System'))->_title($this->__('PDF Templates'));
        $id = (int)$this->getRequest()->getParam($pdfFieldNameId);
        $model = Mage::getModel('eadesign/pdfgenerator');
        if ($id) {
            $model->load($id);
        } else {
            if ($typeId = $this->getRequest()->getParam('type')) {
                $model->setTypeId($typeId);
            }
        }
        if (!Mage::registry('pdfgenerator_template')) {
            Mage::register('pdfgenerator_template', $model);
        }
        if (!Mage::registry('current_pdfgenerator_template')) {
            Mage::register('current_pdfgenerator_template', $model);
        }
        return $model;
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('custom_theme_from', 'custom_theme_to'));
        return $data;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('pdfadmin_menu/first_page');
    }
}
