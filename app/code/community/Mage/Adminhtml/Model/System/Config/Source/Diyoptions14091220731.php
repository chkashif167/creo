<?php
class Mage_Adminhtml_Model_System_Config_Source_Diyoptions14091220731
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
		
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Slide (Responsive)')),
            array('value' => 2, 'label'=>Mage::helper('adminhtml')->__('Fade (Responsive)')),
        );
    }

}
