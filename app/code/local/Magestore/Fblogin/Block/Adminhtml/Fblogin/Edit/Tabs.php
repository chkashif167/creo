<?php

class Magestore_Fblogin_Block_Adminhtml_Fblogin_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('fblogin_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('fblogin')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('fblogin')->__('Item Information'),
          'title'     => Mage::helper('fblogin')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('fblogin/adminhtml_fblogin_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}