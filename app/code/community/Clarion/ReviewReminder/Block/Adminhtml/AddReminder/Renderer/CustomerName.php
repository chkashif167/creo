<?php
/**
 * @category   Clarion
 * @package    Clarion_ReviewReminder
 * @created    14th Jan, 2015
 * @author     Clarion magento team <magento.team@clariontechnologies.co.in>
 * @purpose    Customer name renderer block 
 * @copyright  Copyright (c) 2014 Clarion Technologies Pvt. Ltd.
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
class Clarion_ReviewReminder_Block_Adminhtml_Addreminder_Renderer_CustomerName extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render custome name
     * @param object $row order data object
     * @return string
     */ 
    public function render(Varien_Object $row)
    {
        $firstName = $row->getData('customer_firstname');
        $lastName = $row->getData('customer_lastname');
        if (!empty($firstName) || !empty($lastName)) {
            
            if (!empty($lastName)) {
                return $firstName . ' ' . $lastName;
            } else {
                return $firstName;
            }
        }
    }
}
?>
