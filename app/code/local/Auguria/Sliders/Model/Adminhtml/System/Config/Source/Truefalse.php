<?php
/**
 * @category   Auguria
 * @package    Auguria_Sliders
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */
class Auguria_Sliders_Model_Adminhtml_System_Config_Source_Truefalse
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'true', 'label'=>Mage::helper('auguria_sliders')->__('True')),
            array('value' => 'false', 'label'=>Mage::helper('auguria_sliders')->__('False')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'false' => Mage::helper('auguria_sliders')->__('False'),
            'true' => Mage::helper('auguria_sliders')->__('True'),
        );
    }
}
