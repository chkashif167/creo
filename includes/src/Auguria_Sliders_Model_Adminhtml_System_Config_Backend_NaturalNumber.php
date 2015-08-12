<?php
/**
 * @category   Auguria
 * @package    Auguria_Sliders
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */
class Auguria_Sliders_Model_Adminhtml_System_Config_Backend_NaturalNumber extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave()
    {
        $value = $this->getValue();        
    	if (!Zend_Validate::is($value, 'NotEmpty')) {
    		Mage::throwException(Mage::helper('auguria_sliders')->__("A value is required."));
        }
        if (!Zend_Validate::is($value, 'Digits')) {
        	Mage::throwException(Mage::helper('auguria_sliders')->__("'%s' is not a natural number.", $value));
        }
        $validator = new Zend_Validate_GreaterThan(-1);
		if (!$validator->isValid($value)) {
        	Mage::throwException(Mage::helper('auguria_sliders')->__("'%s' is not a natural number.", $value));
        }
        return $this;
    }
}