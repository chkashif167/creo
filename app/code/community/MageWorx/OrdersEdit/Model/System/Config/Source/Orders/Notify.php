<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Model_System_Config_Source_Orders_Notify
{
    /**
     * @return array
     */
    public function toOptionArray() {
        $helper = Mage::helper('mageworx_ordersedit');
        $options = array(
            array('value'=>0, 'label'=>$helper->__('Disable')),
            array('value'=>1, 'label'=>$helper->__('Admin Only')),
            array('value'=>2, 'label'=>$helper->__('Customer and Admin'))
        );
        return $options;
    }
}