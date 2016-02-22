<?php

class Tentura_Ngroups_Block_Adminhtml_Ngroups_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('ngroups_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('ngroups')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('ngroups')->__('Group Information'),
          'title'     => Mage::helper('ngroups')->__('Group Information'),
          'content'   => $this->getLayout()->createBlock('ngroups/adminhtml_ngroups_edit_tab_form')->toHtml(),
      ));

      if ($this->getRequest()->getParam('id') > 0){
      
          $this->addTab('users_section', array(
              'label'     => Mage::helper('ngroups')->__('Subscribers in Group'),
              'title'     => Mage::helper('ngroups')->__('Subscribers in Group'),
              'content'   => $this->getLayout()->createBlock('ngroups/adminhtml_oldsubscriber_grid')->toHtml(),
          ));

      }

      $this->addTab('new_users_section', array(
          'label'     => Mage::helper('ngroups')->__('Add Subscribers to Group'),
          'title'     => Mage::helper('ngroups')->__('Add Subscribers to Group'),
          'content'   => $this->getLayout()->createBlock('ngroups/adminhtml_subscriber_grid')->toHtml(),
      ));

      $this->addTab('import_section', array(
          'label'     => Mage::helper('ngroups')->__('Import Subscribers to Group'),
          'title'     => Mage::helper('ngroups')->__('Import Subscribers to Group'),
          'content'   => $this->getLayout()->createBlock('ngroups/adminhtml_ngroups_edit_tab_import')->toHtml(),
      ));
      
      $this->addTab('categories_section', array(
          'label'     => Mage::helper('ngroups')->__('Categories Association'),
          'title'     => Mage::helper('ngroups')->__('Categories Association'),
          'content'   => $this->getLayout()->createBlock('ngroups/adminhtml_ngroups_edit_tab_categories')->toHtml(),
      ));

      return parent::_beforeToHtml();
  }
}