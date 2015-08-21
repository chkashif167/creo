<?php
class MST_Pdp_Model_Displaytype extends Mage_Core_Model_Config_Data {
    public function toOptionArray()
    {
        $themes = array(
            array('value' => 'link', 'label' => 'Show customize design in popup '),
            array('value' => 'full', 'label' => 'Show full customize design'),
        );
        return $themes;
    }
}