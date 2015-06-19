<?php
class MST_Pdp_Model_Adminhtml_Sidemode extends Mage_Core_Model_Config_Data {
    public function toOptionArray()
    {
        $options = array(
            array('value' => 'side_hoz_tab', 'label' => 'Hozironal Tab'),
            array('value' => 'side_ver_tab', 'label' => 'Vertical Tab with Thumbnail')
        );
        return $options;
    }
}
