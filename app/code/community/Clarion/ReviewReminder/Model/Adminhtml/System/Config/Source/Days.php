<?php
/**
 * @category   Clarion
 * @package    Clarion_ReviewReminder
 * @created    6th Jan, 2015
 * @author     Clarion magento team <magento.team@clariontechnologies.co.in>
 * @purpose    Review reminder number of days source model
 * @copyright  Copyright (c) 2014 Clarion Technologies Pvt. Ltd.
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
class Clarion_ReviewReminder_Model_Adminhtml_System_Config_Source_Days
{
    public function toOptionArray()
    {
        //create option array for days
        $options = array();
        $days = range(1, 20);
        if(!empty($days)){
            foreach ($days as $day) {
                $options[]= array('value'=>$day, 'label'=>Mage::helper('clarion_reviewreminder')->__($day));
            }
        }
        return $options;
    }
}