<?php
/**
 * @category   Auguria
 * @package    Auguria_Sliders
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */
class Auguria_Sliders_Model_Adminhtml_System_Config_Source_Effect
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'slide', 'label'=>Mage::helper('auguria_sliders')->__('Slide')),
            array('value' => 'fade', 'label'=>Mage::helper('auguria_sliders')->__('Fade')),
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
            'fade' => Mage::helper('auguria_sliders')->__('Fade'),
            'slide' => Mage::helper('auguria_sliders')->__('Slide'),
        );
    }
}
