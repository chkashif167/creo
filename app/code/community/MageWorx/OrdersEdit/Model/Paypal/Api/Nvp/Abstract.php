<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

if (MageWorx_OrdersEdit_Helper_Data::foeModuleCheck('CLS_Paypal', 'CLS_Paypal_Model_Paypal_Api_Nvp', 'Mage_Paypal_Model_Api_Nvp')){
    class MageWorx_OrdersEdit_Model_Paypal_Api_Nvp_Abstract extends CLS_Paypal_Model_Paypal_Api_Nvp {}
} else {
    class MageWorx_OrdersEdit_Model_Paypal_Api_Nvp_Abstract extends Mage_Paypal_Model_Api_Nvp {}
}