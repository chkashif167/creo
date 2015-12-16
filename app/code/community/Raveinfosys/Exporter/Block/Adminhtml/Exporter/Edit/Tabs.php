<?php

class Raveinfosys_Exporter_Block_Adminhtml_Exporter_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('exporter_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('exporter')->__('Import Orders'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('exporter')->__('Import Orders'),
          'title'     => Mage::helper('exporter')->__('Import Orders'),
          'content'   => $this->getLayout()->createBlock('exporter/adminhtml_exporter_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}