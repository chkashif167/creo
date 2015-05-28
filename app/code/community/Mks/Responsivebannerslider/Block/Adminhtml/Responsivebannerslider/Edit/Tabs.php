<?php
class Mks_Responsivebannerslider_Block_Adminhtml_Responsivebannerslider_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("responsivebannerslider_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("responsivebannerslider")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("responsivebannerslider")->__("Item Information"),
				"title" => Mage::helper("responsivebannerslider")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("responsivebannerslider/adminhtml_responsivebannerslider_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
