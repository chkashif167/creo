<?php
/**
 * MageWorx
 * Admin Order Grid  extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersGrid
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersGrid_Model_Resource_Order_Group_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
        
    protected function _construct() {        
        $this->_init('mageworx_ordersgrid/order_group');
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {               
        $data = array('0'=>Mage::helper('mageworx_ordersgrid')->__('Actual'));
        foreach ($this as $item) {
            $data[$item->getOrderGroupId()] = $item->getTitle();
        }
        return $data;
    }
}