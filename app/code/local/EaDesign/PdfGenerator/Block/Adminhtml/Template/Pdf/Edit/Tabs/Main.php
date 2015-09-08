<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Form
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Block_Adminhtml_Template_Pdf_Edit_Tabs_Main extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Prepare layout.
     * Add files to use dialog windows
     *
     * @return Mage_Adminhtml_Block_System_Email_Template_Edit_Form
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
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
            'legend' => Mage::helper('pdfgenerator')->__('Template Body'),
            'class' => 'fieldset-wide'
        ));

        $editor = Mage::getSingleton('eadesign/wysiwyg_config')->getConfig(array('tab_id' => $this->getTabId()));

        $fieldset->addField('pdftemplate_body', 'editor', array(
            'name' => 'pdftemplate_body',
            'label' => Mage::helper('pdfgenerator')->__('Template Body'),
            'title' => Mage::helper('pdfgenerator')->__('Template Body'),
            'style' => 'width:700px; height:500px;',
            'config' => $editor,
            'wysiwyg' => true,
            'required' => true,
        ));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
