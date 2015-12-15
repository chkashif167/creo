<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

if (MageWorx_OrdersEdit_Helper_Data::foeModuleCheck('AW_Sarp', 'AW_Sarp_Model_Payment_Method_Core_Authorizenet', 'Mage_Paygate_Model_Authorizenet')){
    class MageWorx_OrdersEdit_Model_Paygate_Authorizenet_Abstract extends AW_Sarp_Model_Payment_Method_Core_Authorizenet {}
} else {
    class MageWorx_OrdersEdit_Model_Paygate_Authorizenet_Abstract extends Mage_Paygate_Model_Authorizenet {}
}