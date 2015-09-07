<?php

/**
 * Description of General
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Block_Adminhtml_Template_Pdf_Edit_Tabs_General extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('general_tabs');
        $this->setDestElementId('form');
        $this->setTitle(Mage::helper('pdfgenerator')->__('Item Information'));
    }

    /**
     * Add fields to form and create template info form
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model = mage::registry('pdfgenerator_template');

        $form = new Varien_Data_Form();


        $fieldset = $form->addFieldset('general_fieldset', array(
            'legend' => Mage::helper('pdfgenerator')->__('Template Information'),
            'class' => 'fieldset'
        ));

        $fieldset->addField('pdftemplate_name', 'text', array(
            'name' => 'pdftemplate_name',
            'label' => Mage::helper('pdfgenerator')->__('Template Name'),
            'required' => true,
        ));

        $fieldset->addField('pdftemplate_desc', 'text', array(
            'name' => 'pdftemplate_desc',
            'label' => Mage::helper('pdfgenerator')->__('Template Description'),
            'required' => false
        ));

        if ($model->getPdftemplateId()) {
            $fieldset->addField('pdftemplate_id', 'hidden', array(
                'name' => 'pdftemplate_id',
            ));
        }

        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('template_store_id', 'select', array(
                'name' => 'template_store_id',
                'label' => Mage::helper('cms')->__('Store View'),
                'title' => Mage::helper('cms')->__('Store View'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField('template_store_id', 'hidden', array(
                'name' => 'template_store_id',
                'value' => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $fieldset->addField('pdft_is_active', 'select', array(
            'label' => Mage::helper('cms')->__('Status'),
            'title' => Mage::helper('cms')->__('Status'),
            'name' => 'pdft_is_active',
            'required' => true,
            'options' => array(
                '1' => Mage::helper('cms')->__('Enabled'),
                '0' => Mage::helper('cms')->__('Disabled'),
            ),
        ));
        $fieldset->addField('pdft_default', 'select', array(
            'label' => Mage::helper('pdfgenerator')->__('Default'),
            'title' => Mage::helper('pdfgenerator')->__('Default'),
            'name' => 'pdft_default',
            'required' => false,
            'options' => array(
                '1' => Mage::helper('cms')->__('Yes'),
                '0' => Mage::helper('cms')->__('No'),
            ),
        ));

        if ($this->getThePdfType()) {
            $fieldset->addField('pdft_type', 'select', array(
                'label' => Mage::helper('pdfgenerator')->__('Type'),
                'title' => Mage::helper('pdfgenerator')->__('Type'),
                'name' => 'pdft_type',
                'options' => array(
                    /* review here */
                    '1' => $this->getThePdfType(),
                ),
                'value' => 1,
                'required' => true,
                'readonly' => true,
            ));
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getThePdfType()
    {
        $pdfType = $this->getRequest()->getParam('type');
        return $pdfType;
    }

}
