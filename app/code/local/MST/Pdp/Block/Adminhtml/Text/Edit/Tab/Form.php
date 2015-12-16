<?php

class MST_Pdp_Block_Adminhtml_Text_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('artwork_category_form', array('legend' => Mage::helper('pdp')->__('Artwork Category Information')));
        $fieldset->addField('text', 'textarea', array(
            'label' => Mage::helper('pdp')->__('Text'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'text',
        ));
        $fieldset->addField('tags', 'text', array(
        		'label' => Mage::helper('pdp')->__('Tags'),
        		'class' => '',
        		'required' => false,
        		'name' => 'tags',
        		'note' => 'Separate by comma: love,like,good'
        ));
        $fieldset->addField('is_popular', 'select', array(
        		'label' => Mage::helper('pdp')->__('Is Popular'),
        		'name' => 'is_popular',
        		'values' => array(
        				array(
        						'value' => 2,
        						'label' => Mage::helper('pdp')->__('No'),
        				),
        				array(
        						'value' => 1,
        						'label' => Mage::helper('pdp')->__('Yes'),
        				),
        		),
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
        if (Mage::getSingleton('adminhtml/session')->getTextData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getTextData());
            Mage::getSingleton('adminhtml/session')->setTextData(null);
        } elseif (Mage::registry('text_data')) {
            $form->setValues(Mage::registry('text_data')->getData());
        }
        return parent::_prepareForm();
    }
}