<?php
class MST_Pdp_Model_Config_Sizeunit {
	public function toOptionArray()
    {
        return array(
            array('value'=> 'B', 'label'=>Mage::helper('pdp')->__('Bytes')),
            array('value'=> 'k', 'label'=>Mage::helper('pdp')->__('Kilobytes (k)')),
            array('value'=> 'M', 'label'=>Mage::helper('pdp')->__('Megabytes (M)')),                                   
        );
    }
}