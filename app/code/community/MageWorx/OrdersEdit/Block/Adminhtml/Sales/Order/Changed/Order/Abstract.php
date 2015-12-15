<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_Changed_Order_Abstract extends Mage_Adminhtml_Block_Sales_Order_View_Info
{
    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        return $this;
    }
}