<?php
/**
 * @category   Auguria
 * @package    Auguria_Sliders
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */
class Auguria_Sliders_Block_Adminhtml_Sliders_Edit_Tab_Content extends Mage_Adminhtml_Block_Widget_Form
{	
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('sliders_form', array('legend'=>Mage::helper('auguria_sliders')->__("Content")));
		
		$fieldset->addField('slider_id', 'hidden', array(
				'name'      => 'id',
		));
		
		$fieldset->addField('name', 'text', array(
				'label'     => Mage::helper('auguria_sliders')->__('Name'),
				'class'     => 'required-entry',
				'required'  => true,
				'name'      => 'name',
		));

		$fieldset->addField('image', 'image', array(
				'label'     => Mage::helper('auguria_sliders')->__('Image'),
				'required'  => false,
				'name'      => 'image',
		));
		
		$fieldset->addField('link', 'text', array(
				'label'     => Mage::helper('auguria_sliders')->__('Link'),
				'required'  => false,
				'name'      => 'link',
		));
		
		$config = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
		$config->setData('files_browser_window_url',Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index/'));
		$config->setData('directives_url',Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive'));
		$config->setData('directives_url_quoted', preg_quote($config->getData('directives_url')));
		$config->setData('widget_window_url',Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/widget/index'));
		
		$fieldset->addField('cms_content', 'editor', array(
				'label'     => Mage::helper('auguria_sliders')->__('Cms content'),
				'required'  => false,
				'name'      => 'cms_content',
				'config'	=> $config
		));
		
		$fieldset->addField('sort_order', 'text', array(
				'label'     => Mage::helper('auguria_sliders')->__('Sort order'),
				'required'  => false,
				'name'      => 'sort_order',
            	'class' => 'validate-digits'
		));
		
		$status = Mage::helper('auguria_sliders')->getIsActiveOptionArray();
		array_unshift($status, array('label'=>'', 'value'=>''));
		$fieldset->addField('is_active', 'select', array(
				'label'     => Mage::helper('auguria_sliders')->__('Status'),
				'required'  => true,
				'name'      => 'is_active',
				'values'    => $status
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