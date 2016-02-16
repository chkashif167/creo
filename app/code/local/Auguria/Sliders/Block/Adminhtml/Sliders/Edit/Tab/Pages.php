<?php
/**
 * @category   Auguria
 * @package    Auguria_Sliders
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */
class Auguria_Sliders_Block_Adminhtml_Sliders_Edit_Tab_Pages extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('sliders_form', array('legend'=>Mage::helper('auguria_sliders')->__("Cms pages")));
		 
		$fieldset->addField('pages', 'multiselect', array(
			'label'     => Mage::helper('auguria_sliders')->__('Slider visible in'),
			'required'  => false,
			'name'      => 'pages[]',
			'values'    => Mage::getSingleton('auguria_sliders/adminhtml_system_config_source_cms_page')->toOptionArray()
		));
		
		if (Mage::getSingleton('adminhtml/session')->getSlidersData()) {
			$form->setValues(Mage::getSingleton('adminhtml/session')->getSlidersData());
			Mage::getSingleton('adminhtml/session')->setSlidersData(null);
		}
		elseif (Mage::registry('sliders_data')) {
			$form->setValues(Mage::registry('sliders_data')->getData());
		}
		return parent::_prepareForm();
	}
}