<?php
/**
 * MageWorx
 * Admin Order Grid  extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersGrid
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersGrid_Model_Resource_Order_Group extends Mage_Core_Model_Mysql4_Abstract {
    
    protected function _construct() 
    {        
        $this->_init('mageworx_ordersgrid/order_group', 'order_group_id');
    }
    
}