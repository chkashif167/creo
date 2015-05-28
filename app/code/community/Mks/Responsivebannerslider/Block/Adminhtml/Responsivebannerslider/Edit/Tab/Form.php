<?php
class Mks_Responsivebannerslider_Block_Adminhtml_Responsivebannerslider_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("responsivebannerslider_form", array("legend"=>Mage::helper("responsivebannerslider")->__("Item information")));

				
						$fieldset->addField("title", "text", array(
						"label" => Mage::helper("responsivebannerslider")->__("Title"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "title",
						));
									
						$fieldset->addField('image', 'image', array(
						'label' => Mage::helper('responsivebannerslider')->__('Images'),
						'name' => 'image',
						'note' => '(*.jpg, *.png, *.gif)',
						));
						$fieldset->addField("description", "textarea", array(
						"label" => Mage::helper("responsivebannerslider")->__("Description"),
						"name" => "description",
						));
					
						$fieldset->addField("url", "text", array(
						"label" => Mage::helper("responsivebannerslider")->__("URL"),
						"name" => "url",
						));
					
						$fieldset->addField("imageorder", "text", array(
						"label" => Mage::helper("responsivebannerslider")->__("Images Order"),
						"name" => "imageorder",
						));
									
						 $fieldset->addField('status', 'select', array(
						'label'     => Mage::helper('responsivebannerslider')->__('Status'),
						'values'   => Mks_Responsivebannerslider_Block_Adminhtml_Responsivebannerslider_Grid::getValueArray5(),
						'name' => 'status',					
						"class" => "required-entry",
						"required" => true,
						));

				if (Mage::getSingleton("adminhtml/session")->getResponsivebannersliderData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getResponsivebannersliderData());
					Mage::getSingleton("adminhtml/session")->setResponsivebannersliderData(null);
				} 
				elseif(Mage::registry("responsivebannerslider_data")) {
				    $form->setValues(Mage::registry("responsivebannerslider_data")->getData());
				}
				return parent::_prepareForm();
		}
}
