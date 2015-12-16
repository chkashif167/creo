<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

if (MageWorx_OrdersEdit_Helper_Data::foeModuleCheck('Amasty_Rules', 'Amasty_Rules_Model_SalesRule_Quote_Discount', 'Mage_SalesRule_Model_Quote_Discount')) {
    class MageWorx_OrdersEdit_Model_SalesRule_Quote_Discount_Abstract extends Amasty_Rules_Model_SalesRule_Quote_Discount {}
} elseif (MageWorx_OrdersEdit_Helper_Data::foeModuleCheck('Amasty_Promo', 'Amasty_Promo_Model_SalesRule_Quote_Discount', 'Mage_SalesRule_Model_Quote_Discount')) {
    class MageWorx_OrdersEdit_Model_SalesRule_Quote_Discount_Abstract extends Amasty_Promo_Model_SalesRule_Quote_Discount {}
} else {
    class MageWorx_OrdersEdit_Model_SalesRule_Quote_Discount_Abstract extends Mage_SalesRule_Model_Quote_Discount {}
}