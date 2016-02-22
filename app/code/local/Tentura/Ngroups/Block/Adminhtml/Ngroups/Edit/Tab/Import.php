<?php

class Tentura_Ngroups_Block_Adminhtml_Ngroups_Edit_Tab_Import extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $this->setTemplate('ngroups/form.phtml');

      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('ngroups_form', array('legend'=>Mage::helper('ngroups')->__('Import Subscribers')));
     
//      $fieldset->addField('filename', 'file', array(
//          'label'     => Mage::helper('ngroups')->__('Import CSV file'),
//          'required'  => false,
//          'name'      => 'filename'
//      ));

      $fieldset->addField('emails', 'editor', array(
          'label'     => Mage::helper('ngroups')->__('Add Subscribers emails'),
          'class'     => '',
          'style'     => 'width:274px; height:250px;',
          'name'      => 'emails',
          'after_element_html' => '<p class="note"><small>one email per line</small></p>',
      ));

      $fieldset->addField('import', 'file', array(
          'label'     => Mage::helper('ngroups')->__('Import subscribers via CSV file'),
          'class'     => '',
          'name'      => 'uploadFile',
          'after_element_html' => '<p class="note"><small>one email per line</small></p>', 
      ));
      
      if(Mage::helper('ngroups')->getStoresNumber() > 1) {
        $fieldset->addField('store_id', 'select', array(
                'name' => 'store_id',
                'label' => Mage::helper('ngroups')->__('Apply new emails to store view'),
                'title' => Mage::helper('ngroups')->__('Store View'),
                'required' => false,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, false),
        ));
      }
     
      if ( Mage::getSingleton('adminhtml/session')->getNgroupsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getNgroupsData());
          Mage::getSingleton('adminhtml/session')->setNgroupsData(null);
      } elseif ( Mage::registry('ngroups_data') ) {
          $form->setValues(Mage::registry('ngroups_data')->getData());
      }
      return parent::_prepareForm();
  }
}