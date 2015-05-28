<?php
class Mks_Bannerslider_Block_Adminhtml_Imagegallery_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("imagegallery_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("bannerslider")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("bannerslider")->__("Item Information"),
				"title" => Mage::helper("bannerslider")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("bannerslider/adminhtml_imagegallery_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
