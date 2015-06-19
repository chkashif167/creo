<?php
class FacileCheckout_OnestepCheckout_Adminhtml_Model_System_Config_Source_AddressOptions
{
    public function toOptionArray()
    {
    	$help_obj	= Mage::helper('onestepcheckout');
    	$options	= array(
            array('value' => 'optional','label' => $help_obj->__('Optional')),
            array('value' => 'required','label' => $help_obj->__('Required')),
            array('value' => 'hidden',	'label' => $help_obj->__('Hidden'))
        );
        return $options;
    }
}
