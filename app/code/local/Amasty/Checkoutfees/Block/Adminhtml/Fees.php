<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */
class Amasty_Checkoutfees_Block_Adminhtml_Fees extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_fees';
        $this->_blockGroup = 'amcheckoutfees';
        $this->_headerText = Mage::helper('amcheckoutfees')->__('Manage Fees');
        parent::__construct();
    }
}