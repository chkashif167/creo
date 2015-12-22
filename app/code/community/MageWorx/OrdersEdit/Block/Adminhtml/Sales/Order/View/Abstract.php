<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

if (MageWorx_OrdersEdit_Helper_Data::foeModuleCheck('MageTools_Pendingorders', 'MageTools_Pendingorders_Block_Sales_Order_View', 'Mage_Adminhtml_Block_Sales_Order_View')) {
    class MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_View_Abstract extends MageTools_Pendingorders_Block_Sales_Order_View {}
} elseif (MageWorx_OrdersEdit_Helper_Data::foeModuleCheck('IllApps_Shipsync', 'IllApps_Shipsync_Block_Adminhtml_Sales_Order_View', 'Mage_Adminhtml_Block_Sales_Order_View')) {
    class MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_View_Abstract extends  IllApps_Shipsync_Block_Adminhtml_Sales_Order_View {}
} elseif (MageWorx_OrdersEdit_Helper_Data::foeModuleCheck('AuIt_Pdf', 'AuIt_Pdf_Block_Adminhtml_Sales_Order_View', 'Mage_Adminhtml_Block_Sales_Order_View')) {
    class MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_View_Abstract extends AuIt_Pdf_Block_Adminhtml_Sales_Order_View {}
} elseif (MageWorx_OrdersEdit_Helper_Data::foeModuleCheck('Amasty_Email', 'Amasty_Email_Block_Adminhtml_Sales_Order_View', 'Mage_Adminhtml_Block_Sales_Order_View')) {
    class MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_View_Abstract extends  Amasty_Email_Block_Adminhtml_Sales_Order_View {}
}  else {
    class MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_View_Abstract extends Mage_Adminhtml_Block_Sales_Order_View {}
}