<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_Changed_Coupons extends Mage_Adminhtml_Block_Widget
{
    /** @var string  */
    protected $_template = 'mageworx/ordersedit/changed/coupons.phtml';

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_coupons');
        $this->setTemplate('mageworx/ordersedit/changed/coupons.phtml');
    }

    /**
     * @return string
     */
    public function getCouponCode()
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $this->getQuote();

        return $quote->getCouponCode();
    }
}