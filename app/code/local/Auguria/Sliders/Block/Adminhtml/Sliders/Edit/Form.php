<?php
/**
 * @category   Auguria
 * @package    Auguria_Sliders
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */
class Auguria_Sliders_Block_Adminhtml_Sliders_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareLayout()
	{
		parent::_prepareLayout();
		
		$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
		$this->getLayout()->getBlock('head')->addJs('mage/adminhtml/variables.js');
		$this->getLayout()->getBlock('head')->addJs('mage/adminhtml/wysiwyg/widget.js');
		$this->getLayout()->getBlock('head')->addJs('lib/flex.js');
		$this->getLayout()->getBlock('head')->addJs('lib/FABridge.js');
		$this->getLayout()->getBlock('head')->addJs('mage/adminhtml/flexuploader.js');
		$this->getLayout()->getBlock('head')->addJs('mage/adminhtml/browser.js');
		$this->getLayout()->getBlock('head')->addJs('extjs/ext-tree.js');
		$this->getLayout()->getBlock('head')->addJs('extjs/ext-tree-checkbox.js');

		$this->getLayout()->getBlock('head')->addItem('js_css','extjs/resources/css/ext-all.css');
		$this->getLayout()->getBlock('head')->addItem('js_css','extjs/resources/css/ytheme-magento.css');
		$this->getLayout()->getBlock('head')->addItem('js_css','prototype/windows/themes/default.css');
		$this->getLayout()->getBlock('head')->addCss('lib/prototype/windows/themes/magento.css');
	}
	
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array(
				'id' => 'edit_form',
				'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
				'method' => 'post',
				'enctype' => 'multipart/form-data'
		)
		);

		$form->setUseContainer(true);
		$this->setForm($form);
		return parent::_prepareForm();
	}
}