<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Css
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Block_Adminhtml_Template_Pdf_Edit_Tabs_Css extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('general_tabs');
        $this->setDestElementId('form');
        $this->setTitle(Mage::helper('pdfgenerator')->__('Item Information'));
    }

    protected function _prepareForm()
    {
        $model = mage::registry('pdfgenerator_template');

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('header_fieldset', array(
            'legend' => Mage::helper('pdfgenerator')->__('Template Css'),
            'class' => 'fieldset-wide'
        ));

        $fieldset->addField('pdft_css', 'editor', array(
            'name' => 'pdft_css',
            'label' => Mage::helper('pdfgenerator')->__('Template Css'),
            'title' => Mage::helper('pdfgenerator')->__('Template Css'),
            'style' => 'width:700px; height:300px;',
            'wysiwyg' => false,
            'required' => false,
        ));


        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

}

?>
