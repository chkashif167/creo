<?php
/**
 * @category   Auguria
 * @package    Auguria_Sliders
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */
class Auguria_Sliders_Block_Adminhtml_Sliders_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('sliders_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('auguria_sliders')->__('Slider detail'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('content_section', array(
          'label'     => Mage::helper('auguria_sliders')->__('Content'),
          'title'     => Mage::helper('auguria_sliders')->__('Content'),
          'content'   => $this->getLayout()->createBlock('auguria_sliders/adminhtml_sliders_edit_tab_content')->toHtml(),
      ));
      
      $this->addTab('cms_pages_section', array(
          'label'     => Mage::helper('auguria_sliders')->__('Cms pages'),
          'title'     => Mage::helper('auguria_sliders')->__('Cms pages'),
          'content'   => $this->getLayout()->createBlock('auguria_sliders/adminhtml_sliders_edit_tab_pages')->toHtml(),
      ));
      
      $this->addTab('categories_section', array(
          'label'     => Mage::helper('auguria_sliders')->__('Categories'),
          'title'     => Mage::helper('auguria_sliders')->__('Categories'),
          'content'   => $this->getLayout()->createBlock('auguria_sliders/adminhtml_sliders_edit_tab_categories')->toHtml(),
      ));
      
      $this->addTab('stores_section', array(
          'label'     => Mage::helper('auguria_sliders')->__('Stores'),
          'title'     => Mage::helper('auguria_sliders')->__('Stores'),
          'content'   => $this->getLayout()->createBlock('auguria_sliders/adminhtml_sliders_edit_tab_stores')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}