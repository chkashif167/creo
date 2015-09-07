<?php

/**
 * Description of Edit
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Block_Adminhtml_Template_Pdf_Edit extends Mage_Adminhtml_Block_Widget
{

    /**
     * Initialize
     */
    protected function _construct()
    {
        $this->setTemplate('pdfgenerator/template/edit/edit.phtml');
        parent::_construct();
    }

    /**
     * Preparing the layout
     */
    protected function _prepareLayout()
    {
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);

            $this->getLayout()->getBlock('head')->addJs('pdfgenerator/variables.js');
            $this->getLayout()->getBlock('head')->addJs('lib/flex.js');
            $this->getLayout()->getBlock('head')->addJs('lib/FABridge.js');
            $this->getLayout()->getBlock('head')->addJs('mage/adminhtml/flexuploader.js');
            $this->getLayout()->getBlock('head')->addJs('mage/adminhtml/browser.js');
            $this->getLayout()->getBlock('head')->addJs('extjs/ext-tree.js');
            $this->getLayout()->getBlock('head')->addJs('extjs/ext-tree-checkbox.js');

            $this->getLayout()->getBlock('head')->addItem('js_css', 'extjs/resources/css/ext-all.css');
            $this->getLayout()->getBlock('head')->addItem('js_css', 'extjs/resources/css/ytheme-magento.css');
            $this->getLayout()->getBlock('head')->addItem('js_css', 'prototype/windows/themes/default.css');
            $this->getLayout()->getBlock('head')->addJs('pdfgenerator/window.js')
                ->addItem('js_css', 'pdfgenerator/magento.css');
        }
        $this->setChild('back_button', $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                    'label' => Mage::helper('pdfgenerator')->__('Back'),
                    'onclick' => "window.location.href = '" . $this->getUrl('*/*') . "'",
                    'class' => 'back'
                )
            )
        );

        $this->setChild('reset_button', $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                    'label' => Mage::helper('pdfgenerator')->__('Reset'),
                    'onclick' => 'window.location.href = window.location.href'
                )
            )
        );

        $this->setChild('delete_button', $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                    'label' => Mage::helper('pdfgenerator')->__('Delete Template'),
                    'onclick' => 'templateControl.deleteTemplate();',
                    'class' => 'delete'
                )
            )
        );

        $this->setChild('save_button', $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                    'label' => Mage::helper('pdfgenerator')->__('Save Template'),
                    'onclick' => 'templateControl.save();',
                    'class' => 'save'
                )
            )
        );
        $this->setChild('save_button_continue', $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                    'label' => Mage::helper('adminhtml')->__('Save and Continue Edit'),
                    'onclick' => 'templateControl.saveandcontinue();',
                    'class' => 'save'
                )
            )
        );

        $this->setChild('duplicate_button', $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                    'label' => Mage::helper('pdfgenerator')->__('Duplicate Template'),
                    'onclick' => 'templateControl.duplicate();',
                    'type' => 'button',
                    'class' => 'duplicate'
                )
            )
        );

        return parent::_prepareLayout();
    }

    /**
     * Back button return to the etension root
     */
    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    /**
     * Reset template - will rest all fields
     */
    public function getResetButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    /**
     * Save current template button
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * Save current template button
     */
    public function getSaveContinueButtonHtml()
    {
        return $this->getChildHtml('save_button_continue');
    }

    /**
     * Not sure we will use this one
     */
    public function getPreviewButtonHtml()
    {
        return $this->getChildHtml('preview_button');
    }

    /**
     * Detele button
     */
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    /**
     * Duplicate template button
     */
    public function getDuplicateButtonHtml()
    {
        return $this->getChildHtml('duplicate_button');
    }

    /**
     * Return edit flag for block
     *
     * @return boolean
     */
    public function getEditMode()
    {
        if (!$this->getRequest()->getParam('type')) {
            return true;
        }
        return false;
    }

    /**
     * Return header text for form
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getEditMode()) {
            return Mage::helper('pdfgenerator')->__('Edit PDF Template');
        }

        return Mage::helper('pdfgenerator')->__('New PDF Template');
    }

    /**
     * Return form block HTML
     *
     * @return string
     */
    public function getFormHtml()
    {
        return $this->getChildHtml('form');
    }

    /**
     * Return action url for form
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current' => true));
    }

    public function getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current' => true,
            'back' => 'edit',
        ));
    }

    public function getAjaxVarUrl()
    {
        return $this->getUrl('*/adminhtml_variable/wysiwygPlugin/?isAjax=true', array(
            '_current' => false,
        ));
    }

    /**
     * Return preview action url for form
     *
     * @return string
     */
    public function getPreviewUrl()
    {
        return $this->getUrl('*/*/preview');
    }

    /**
     * Return delete url for customer group
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('_current' => true));
    }


    public function getLocaleOptions()
    {
        return Mage::app()->getLocale()->getOptionLocales();
    }


    public function getCurrentLocale()
    {
        return Mage::app()->getLocale()->getLocaleCode();
    }

}
