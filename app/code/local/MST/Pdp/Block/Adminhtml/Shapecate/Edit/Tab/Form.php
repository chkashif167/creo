<?php

class MST_Pdp_Block_Adminhtml_Shapecate_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('shape_category_form', array('legend' => Mage::helper('pdp')->__('Shape Category Information')));
        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('pdp')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ));
		$fieldset->addField('position', 'text', array(
            'label' => Mage::helper('pdp')->__('Position'),
            'required' => false,
            'name' => 'position',
        ));
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('pdp')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('pdp')->__('Enabled'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('pdp')->__('Disabled'),
                ),
            ),
        ));
        if (Mage::getSingleton('adminhtml/session')->getShapecateData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getShapecateData());
            Mage::getSingleton('adminhtml/session')->setShapecateData(null);
        } elseif (Mage::registry('shapecate_data')) {
            $form->setValues(Mage::registry('shapecate_data')->getData());
        }
        return parent::_prepareForm();
    }
}