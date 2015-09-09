<?php

class VES_Core_Block_Adminhtml_Key_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
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
	
		$fieldset = $form->addFieldset('apikey_form', array('legend'=>Mage::helper('ves_core')->__(''),'class'=>'fieldset-wide'));
		$fieldset->addField('license_key', 'text', array(
			'label'     => Mage::helper('ves_core')->__('License Key'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'license_key',
		));

		if ( Mage::getSingleton('adminhtml/session')->getFormData() ){
			$form->setValues(Mage::getSingleton('adminhtml/session')->getFormData());
			Mage::getSingleton('adminhtml/session')->setFormData(null);
		} elseif ( Mage::registry('key_data') ) {
			$form->setValues(Mage::registry('key_data')->getData());
			if(Mage::registry('key_data')->getId() ) {
				$licenseInfo = unserialize(Mage::getModel('ves_core/key')->decode(Mage::registry('key_data')->getLicenseInfo(),VES_Core_Model_Key::ENCODED_KEY));
				if(!$licenseInfo || !is_array($licenseInfo)){
					$form->getElement('license_key')->setData('note','<span style="color: #FF0000;">'.Mage::helper('ves_core')->__('Your license information is not valid.').'</span>');
					return;
				}
				$fieldset1 = $form->addFieldset('apikey_info', array('legend'=>Mage::helper('ves_core')->__('License Key information'),'class'=>'fieldset-wide'));				
				$fieldset1->addField('item_name', 'label', array(
					'label'     => Mage::helper('ves_core')->__('Extension'),
					'name'      => 'item_name',
				));
				$fieldset1->addField('type', 'label', array(
					'label'     => Mage::helper('ves_core')->__('License Type'),
					'name'      => 'type',
				));
				$fieldset1->addField('created_at', 'label', array(
					'label'     => Mage::helper('ves_core')->__('Created At'),
					'name'      => 'created_at',
				));
				$fieldset1->addField('expiry_at', 'label', array(
					'label'     => Mage::helper('ves_core')->__('Expiration date'),
					'name'      => 'expiry_at',
				));
				$fieldset1->addField('domains', 'label', array(
					'label'     => Mage::helper('ves_core')->__('Domains'),
					'name'      => 'domains',
				));
				$fieldset1->addField('status', 'label', array(
					'label'     => Mage::helper('ves_core')->__('Status'),
					'name'      => 'status',
				));
				foreach($licenseInfo as $key=>$value){
					if(in_array($key, array('created_at','expiry_at'))){
						$value = $value?Mage::app()->getLocale()->date(strtotime($value))->toString(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM)):'n/a';
					}elseif($key=='domains'){
						$value = implode(',', $licenseInfo['domains']);
					}elseif($key=='status'){
						switch($value){
							case 0: $value = Mage::helper('ves_core')->__('Expired');break;
							case 1: $value = Mage::helper('ves_core')->__('Pending');break;
							case 2: $value = Mage::helper('ves_core')->__('Active');break;
							case 3: $value = Mage::helper('ves_core')->__('Suspended');break;
						}
					}
					$element = $form->getElement($key);
					if($element) $element->setValue($value);
				}
			}
		}
	
		return parent::_prepareForm();
	}
}