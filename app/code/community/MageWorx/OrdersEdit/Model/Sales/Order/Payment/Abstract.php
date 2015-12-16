<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

if (MageWorx_OrdersEdit_Helper_Data::foeModuleCheck('Gorilla_AuthorizenetCim', 'Gorilla_AuthorizenetCim_Model_Sales_Order_Payment', 'Mage_Sales_Model_Order_Payment')){
    class MageWorx_OrdersEdit_Model_Sales_Order_Payment_Abstract extends Gorilla_AuthorizenetCim_Model_Sales_Order_Payment {}
} else {
    class MageWorx_OrdersEdit_Model_Sales_Order_Payment_Abstract extends Mage_Sales_Model_Order_Payment {}
}
