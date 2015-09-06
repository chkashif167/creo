<?php

/**
 * Description of Settings
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Block_Adminhtml_Template_Pdf_Edit_Tabs_Settings extends Mage_Adminhtml_Block_Widget_Form
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


        $fieldset = $form->addFieldset('general_fieldset', array(
            'legend' => Mage::helper('pdfgenerator')->__('Template Information'),
            'class' => 'fieldset'
        ));

        $fieldset->addField('orig_template_variables', 'hidden', array(
            'name' => 'orig_template_variables',
        ));

        $fieldset->addField('variables', 'hidden', array(
            'name' => 'variables',
            'value' => Zend_Json::encode($this->getVariables())
        ));

        $fieldset->addField('template_variables', 'hidden', array(
            'name' => 'template_variables',
        ));

        /*
         * We will use this when needed.
         */

        $insertVariableButton = $this->getLayout()
            ->createBlock('adminhtml/widget_button', '', array(
                'type' => 'button',
                'label' => Mage::helper('pdfgenerator')->__('Insert Variable...'),
                'onclick' => 'MagentovariablePlugin.loadChooser(\'' . $this->getVariablesWysiwygActionUrl() . '\', \'pdft_filename\');'
            ));
        $fieldset->addField('insert_variable', 'note', array(
            'text' => $insertVariableButton->toHtml()
        ));

        $fieldset->addField('pdft_filename', 'text', array(
            'name' => 'pdft_filename',
            'label' => Mage::helper('pdfgenerator')->__('File Name'),
            'required' => true,
        ));
        $fieldset->addField('pdftp_format', 'select', array(
            'label' => Mage::helper('pdfgenerator')->__('Page format'),
            'title' => Mage::helper('pdfgenerator')->__('Page format'),
            'name' => 'pdftp_format',
            'required' => true,
            'options' => array(
                '5' => Mage::helper('pdfgenerator')->__('Legal'),
                '4' => Mage::helper('pdfgenerator')->__('Letter'),
                '3' => Mage::helper('pdfgenerator')->__('A6'),
                '2' => Mage::helper('pdfgenerator')->__('A5'),
                '1' => Mage::helper('pdfgenerator')->__('A3'),
                '0' => Mage::helper('pdfgenerator')->__('A4'),
            ),
        ));

        $fieldset->addField('pdftc_customchek', 'select', array(
            'label' => Mage::helper('pdfgenerator')->__('Custom format'),
            'name' => 'pdftc_customchek',
            'options' => array(
                '1' => Mage::helper('pdfgenerator')->__('Yes'),
                '0' => Mage::helper('pdfgenerator')->__('No')),
            'onclick' => "",
            'onchange' => "",
            'disabled' => false,
        ));

        $fieldset->addField('pdft_customwidth', 'text', array(
            'name' => 'pdft_customwidth',
            'class' => 'validate-zero-or-greater',
            'label' => Mage::helper('pdfgenerator')->__('Width (mm)'),
            'required' => false,
        ));
        $fieldset->addField('pdft_customheight', 'text', array(
            'name' => 'pdft_customheight',
            'class' => 'validate-zero-or-greater',
            'label' => Mage::helper('pdfgenerator')->__('Height (mm)'),
            'required' => false,
        ));

        $fieldset->addField('pdft_orientation', 'select', array(
            'label' => Mage::helper('pdfgenerator')->__('Page orientation'),
            'title' => Mage::helper('pdfgenerator')->__('Page orientation'),
            'name' => 'pdft_orientation',
            'required' => true,
            'options' => array(
                'portrait' => Mage::helper('pdfgenerator')->__('Portrait'),
                'landscape' => Mage::helper('pdfgenerator')->__('Landscape'),
            ),
        ));

        $fieldset->addField('pdftm_top', 'text', array(
            'name' => 'pdftm_top',
            'class' => 'validate-greater-than-zero',
            'label' => Mage::helper('pdfgenerator')->__('Top (mm)'),
            'required' => true,
        ));
        $fieldset->addField('pdftm_bottom', 'text', array(
            'name' => 'pdftm_bottom',
            'class' => 'validate-greater-than-zero',
            'label' => Mage::helper('pdfgenerator')->__('Bottom (mm)'),
            'required' => true,
        ));
        $fieldset->addField('pdftm_left', 'text', array(
            'name' => 'pdftm_left',
            'class' => 'validate-greater-than-zero',
            'label' => Mage::helper('pdfgenerator')->__('Left (mm)'),
            'required' => true,
        ));
        $fieldset->addField('pdftm_right', 'text', array(
            'name' => 'pdftm_right',
            'class' => 'validate-greater-than-zero',
            'label' => Mage::helper('pdfgenerator')->__('Right (mm)'),
            'required' => true,
        ));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getVariablesWysiwygActionUrl()
    {
        return Mage::getSingleton('adminhtml/url')->getUrl('*/adminhtml_variable/wysiwygPlugin');
    }

}
