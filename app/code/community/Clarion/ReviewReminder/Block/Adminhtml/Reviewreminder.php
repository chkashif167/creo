<?php
/**
 * @category   Clarion
 * @package    Clarion_ReviewReminder
 * @created    6th Jan, 2015
 * @author     Clarion magento team <magento.team@clariontechnologies.co.in>
 * @purpose    Grid container block
 * @copyright  Copyright (c) 2014 Clarion Technologies Pvt. Ltd.
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
class Clarion_ReviewReminder_Block_Adminhtml_Reviewreminder extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        /*both these variables tell magento the location of our Grid.php(grid block) file.
         * $this->_blockGroup.'/' . $this->_controller . '_grid'
         * i.e  clarion_reviewreminder/adminhtml_reviewreminder_grid
         * $_blockGroup - is your module's name.
         * $_controller - is the path to your grid block. 
         */
        $this->_controller = 'adminhtml_reviewreminder';
        $this->_blockGroup = 'clarion_reviewreminder';
        $this->_headerText = Mage::helper('clarion_reviewreminder')->__('Manage Reminders');
        $this->_addButtonLabel = Mage::helper('clarion_reviewreminder')->__('Add Reminders Manually');
        parent::__construct();
    }
}

