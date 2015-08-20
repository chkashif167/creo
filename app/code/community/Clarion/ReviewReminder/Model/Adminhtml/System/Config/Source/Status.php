<?php
/**
 * @category   Clarion
 * @package    Clarion_ReviewReminder
 * @created    6th Jan, 2015
 * @author     Clarion magento team <magento.team@clariontechnologies.co.in>
 * @purpose    Review Reminder order status source model
 * @copyright  Copyright (c) 2014 Clarion Technologies Pvt. Ltd.
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
class Clarion_ReviewReminder_Model_Adminhtml_System_Config_Source_Status
{
    public function toOptionArray($isMultiselect = false)
    {
        $options = array(
            array('value'=>'canceled', 'label'=>Mage::helper('clarion_reviewreminder')->__('Canceled')),
            array('value'=>'closed', 'label'=>Mage::helper('clarion_reviewreminder')->__('Closed')),
            array('value'=>'complete', 'label'=>Mage::helper('clarion_reviewreminder')->__('Complete')),
            array('value'=>'holded', 'label'=>Mage::helper('clarion_reviewreminder')->__('On Hold')),
            array('value'=>'pending', 'label'=>Mage::helper('clarion_reviewreminder')->__('Pending')),
            array('value'=>'processing', 'label'=>Mage::helper('clarion_reviewreminder')->__('Processing')),
        );
        
        /*
        $statuses = Mage::getModel('sales/order_status')->getResourceCollection()->getData();
        $options = array();
        if(!empty($statuses) && is_array($statuses)){
            foreach ($statuses as $status) {
                $options[] = array('value'=>$status['status'], 'label'=>Mage::helper('clarion_reviewreminder')->__($status['label']));
            }
        }
        */
        
        if(!$isMultiselect){
 
            array_unshift($options, array('value'=>'', 'label'=>Mage::helper('clarion_reviewreminder')->__('--Please Select--')));
 
        }
        return $options;
    }
}