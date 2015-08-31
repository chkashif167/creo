<?php

class Mango_Attributeswatches_Block_Adminhtml_Attributeswatches_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('attributeswatches_form', array('legend'=>Mage::helper('attributeswatches')->__('Item information')));
     
      $this->_addElementTypes($fieldset);
      
      /*$fieldset->addField('value', 'text', array(
          'label'     => Mage::helper('attributeswatches')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'value',


      ));*/
      
      $_modevalues = Mage::getModel("attributeswatches/system_config_source_swatchesmode")->toOptionArray();
      
      $fieldset->addField('mode', 'select', array(
          'label'     => Mage::helper('attributeswatches')->__('Mode'),
          'name'      => 'mode',
          'values'    => $_modevalues
      ));
      

      
      
      $_costhtml = '<script type="text/javascript" src="'. Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS) .'attributeswatches/jscolor/jscolor.js"></script>';
      
      $fieldset->addField('color', 'text', array(
          'label'     => Mage::helper('attributeswatches')->__('Color'),
          'required'  => false,
          'name'      => 'color',
           'class' => "color"
          
	  ))->setAfterElementHtml($_costhtml);
      
      
    $fieldset->addField('filename', 'image', array(
          //'value' =>  
          'label'     => Mage::helper('attributeswatches')->__('File'),
          'required'  => false,
          'name'      => 'filename',
          
	  ));
		
      
     
      /*$fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('attributeswatches')->__('Content'),
          'title'     => Mage::helper('attributeswatches')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));*/
     
      if ( Mage::getSingleton('adminhtml/session')->getAttributeswatchesData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getAttributeswatchesData());
          Mage::getSingleton('adminhtml/session')->setAttributeswatchesData(null);
      } elseif ( Mage::registry('attributeswatches_data') ) {
          $form->setValues(Mage::registry('attributeswatches_data')->getData());
      }
      return parent::_prepareForm();
  }
  
  
  /**
     * Retrieve Additional Element Types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'image' => Mage::getConfig()->getBlockClassName('attributeswatches/adminhtml_attributeswatches_helper_image'),
  //          'textarea' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_helper_form_wysiwyg')
        );
    }
  
}
