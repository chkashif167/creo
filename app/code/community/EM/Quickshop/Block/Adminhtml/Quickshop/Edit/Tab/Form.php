<?php

class EM_Quickshop_Block_Adminhtml_Quickshop_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('quickshop_form', array('legend'=>Mage::helper('quickshop')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('quickshop')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('quickshop')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('quickshop')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('quickshop')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('quickshop')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('quickshop')->__('Content'),
          'title'     => Mage::helper('quickshop')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getQuickshopData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getQuickshopData());
          Mage::getSingleton('adminhtml/session')->setQuickshopData(null);
      } elseif ( Mage::registry('quickshop_data') ) {
          $form->setValues(Mage::registry('quickshop_data')->getData());
      }
      return parent::_prepareForm();
  }
}