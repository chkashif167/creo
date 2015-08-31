<?php

class Mango_Attributeswatches_Block_Adminhtml_Attributeswatches_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('attributeswatches_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('attributeswatches')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('attributeswatches')->__('Item Information'),
          'title'     => Mage::helper('attributeswatches')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('attributeswatches/adminhtml_attributeswatches_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}