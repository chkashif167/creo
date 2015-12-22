<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

if (MageWorx_OrdersEdit_Helper_Data::foeModuleCheck('Extended_Ccsave', 'Extended_Ccsave_Model_Payment_Method_Ccsave', 'Mage_Payment_Model_Method_Ccsave')){
    class MageWorx_OrdersEdit_Model_Payment_Method_Ccsave_Abstract extends Extended_Ccsave_Model_Payment_Method_Ccsave {}
} else {
    class MageWorx_OrdersEdit_Model_Payment_Method_Ccsave_Abstract extends Mage_Payment_Model_Method_Ccsave {}
}