<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

if (MageWorx_OrdersEdit_Helper_Data::foeModuleCheck('CLS_Paypal', 'CLS_Paypal_Model_Paypal_Payflowpro', 'Mage_Paypal_Model_Payflowpro')){
    class MageWorx_OrdersEdit_Model_Paypal_Payflowpro_Abstract extends CLS_Paypal_Model_Paypal_Payflowpro {}
} else {
    class MageWorx_OrdersEdit_Model_Paypal_Payflowpro_Abstract extends Mage_Paypal_Model_Payflowpro {}
}