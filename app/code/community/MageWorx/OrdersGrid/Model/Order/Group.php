<?php
/**
 * MageWorx
 * Admin Order Grid  extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersGrid
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersGrid_Model_Order_Group extends Mage_Core_Model_Abstract {

    protected function _construct() {
        parent::_construct();
        $this->_init('mageworx_ordersgrid/order_group');
    }
}