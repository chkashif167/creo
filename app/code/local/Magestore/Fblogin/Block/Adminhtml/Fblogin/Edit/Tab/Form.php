<?php

class Magestore_Fblogin_Block_Adminhtml_Fblogin_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('fblogin_form', array('legend'=>Mage::helper('fblogin')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('fblogin')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('fblogin')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('fblogin')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('fblogin')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('fblogin')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('fblogin')->__('Content'),
          'title'     => Mage::helper('fblogin')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getFbloginData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getFbloginData());
          Mage::getSingleton('adminhtml/session')->setFbloginData(null);
      } elseif ( Mage::registry('fblogin_data') ) {
          $form->setValues(Mage::registry('fblogin_data')->getData());
      }
      return parent::_prepareForm();
  }
}