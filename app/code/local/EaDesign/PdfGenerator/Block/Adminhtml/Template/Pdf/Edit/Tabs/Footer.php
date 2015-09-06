<?php

/**
 * Description of Footer
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Block_Adminhtml_Template_Pdf_Edit_Tabs_Footer extends Mage_Adminhtml_Block_Widget_Form
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
            'legend' => Mage::helper('pdfgenerator')->__('Template Footer'),
            'class' => 'fieldset-wide'
        ));

        $editor = Mage::getSingleton('eadesign/wysiwyg_config')->getConfig(array('tab_id' => $this->getTabId()));
        $fieldset->addField('pdftf_footer', 'editor', array(
            'name' => 'pdftf_footer',
            'label' => Mage::helper('pdfgenerator')->__('Template Footer'),
            'title' => Mage::helper('pdfgenerator')->__('Template Footer'),
            'style' => 'width:700px; height:300px;',
            'config' => $editor,
            'wysiwyg' => true,
            'required' => false,
        ));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
