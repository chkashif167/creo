<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Model_AwOrdertags_Observer extends AW_Ordertags_Model_Observer {

    /**
     * @param $observer
     */
    public function orderStatusChanged($observer) {
        if (Mage::app()->getRequest()->getControllerName()!='ordersedit_order_edit') {
            parent::orderStatusChanged($observer);
        }
    }
}