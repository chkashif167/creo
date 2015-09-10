<?php

class VES_PdfPro_Block_Adminhtml_Key_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('apikey_form', array('legend'=>Mage::helper('pdfpro')->__('API Key information')));
	  Mage::dispatchEvent('ves_pdfpro_apikey_form_prepare_before',array('fieldset'=>$fieldset));
      $fieldset->addField('api_key', 'text', array(
          'label'     => Mage::helper('pdfpro')->__('API Key'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'api_key',
      ));
      
 		 /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_ids', 'multiselect', array(
                'name'      => 'store_ids[]',
                'label'     => Mage::helper('cms')->__('Store View'),
                'title'     => Mage::helper('cms')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
            //$renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            //$field->setRenderer($renderer);
        }
        else {
            $fieldset->addField('store_ids', 'hidden', array(
                'name'      => 'store_ids[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            Mage::registry('key_data')->setStoreIds(Mage::app()->getStore(true)->getId());
        }
        
        
        $customerGroups = Mage::getResourceModel('customer/group_collection')->load()->toOptionArray();
        $found = false;

        foreach ($customerGroups as $group) {
            if ($group['value']==0) {
                $found = true;
            }
        }
        if (!$found) {
            array_unshift($customerGroups, array(
                'value' => 0,
                'label' => Mage::helper('salesrule')->__('NOT LOGGED IN'))
            );
        }

        $fieldset->addField('customer_group_ids', 'multiselect', array(
            'name'      => 'customer_group_ids[]',
            'label'     => Mage::helper('salesrule')->__('Customer Groups'),
            'title'     => Mage::helper('salesrule')->__('Customer Groups'),
            'required'  => true,
            'values'    => Mage::getResourceModel('customer/group_collection')->toOptionArray(),
        ));
        $fieldset->addField('comment', 'editor', array(
          'label'     => Mage::helper('pdfpro')->__('Comment'),
          'required'  => false,
          'name'      => 'comment',
      	));
        $fieldset->addField('priority', 'text', array(
          'label'     => Mage::helper('pdfpro')->__('Priority'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'priority',
      ));
	  Mage::dispatchEvent('ves_pdfpro_apikey_form_prepare_after',array('fieldset'=>$fieldset));
      if ( Mage::getSingleton('adminhtml/session')->getFormData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getFormData());
          Mage::getSingleton('adminhtml/session')->setFormData(null);
      } elseif ( Mage::registry('key_data') ) {
          $form->setValues(Mage::registry('key_data')->getData());
      }
      return parent::_prepareForm();
  }
}