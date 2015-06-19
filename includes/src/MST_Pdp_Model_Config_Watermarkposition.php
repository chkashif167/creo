<?php
class MST_Pdp_Model_Config_Watermarkposition {
	public function toOptionArray()
    {
        return array(
            array('value'=> 'top_left', 'label'=>Mage::helper('pdp')->__('Top Left')),
			array('value'=> 'right_top', 'label'=>Mage::helper('pdp')->__('Top Right')),
			array('value'=> 'left_bottom', 'label'=>Mage::helper('pdp')->__('Bottom Left')),
			array('value'=> 'bottom_right', 'label'=>Mage::helper('pdp')->__('Bottom Right')),
			array('value'=> 'center_center', 'label'=>Mage::helper('pdp')->__('Center')),
        );
    }
}